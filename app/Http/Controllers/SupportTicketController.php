<?php

namespace App\Http\Controllers;

use App\Jobs\SupportTicketMailJob;
use App\Mail\SupportTicketMail;
use Illuminate\Http\Request;
use App\SupportTicket;
use App\SupportTicketText;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SupportTicketController extends Controller
{
    public function createTicket()
    {

        return view('support.create');
    }

    public function createTicketPOST(Request $request)
    {
        $validated_data = $request->validate([
            'subject' => 'required|max:255',
            'question' => 'required|max:1000',
            'captcha'=>'required|captcha'
        ]);
        $ticket_count = SupportTicket::where('user_id', Auth::id())->where('status', '!=', 'closed')->count();
        if ($ticket_count >= 5) {
            return redirect()->route('supportTickets')->with(['status' => 'Only 5 active tickets allowed']);
        }
        $subject = $validated_data['subject'];
        $subject = strip_tags($subject);
        $question = $validated_data['question'];
        $question = strip_tags($question);

        $db = DB::transaction(function () use ($subject, $question) {
            $s = SupportTicket::create([
                'user_id' => Auth::id(),
                'subject' => $subject,
            ]);
            SupportTicketText::create([
                'ticket_id' => $s->id,
                'text' => $question
            ]);
            $status = ['status' => 1, 'id' => $s->id];
            return $status;
        },1);
        if ($db['status'] == 1) {
            SupportTicketMailJob::dispatch("opened",$db['id']);
            SupportTicketMailJob::dispatch("opened",$db['id'],1);
            /*Mail::to(Auth::user()->email)->send(new SupportTicketMail("opened", $db['id'],Auth::user()->name));
            Mail::to(env("ADMIN_EMAIL"))->send(new SupportTicketMail("opened", $db['id'],Auth::user()->name,1));*/
            return redirect()->route('ticket', [$db['id']]);
        }
        return redirect()->route('supportTickets');

    }

    public function ticket($ticket)
    {
        $ticket = SupportTicket::where('id', $ticket)->first();
        if ($ticket && $ticket->user_id == Auth::id()) {
            $ticket_texts = SupportTicketText::where('ticket_id', $ticket->id)->orderByDesc('id')->get();
            foreach ($ticket_texts as $text) {
                $text = $text->formatDates();
            }
            return view('support.ticket')->with(['ticket' => $ticket, 'ticket_texts' => $ticket_texts]);
        }
        return redirect()->route('wallets');
    }

    public function addToTicket(Request $request)
    {
        $validated_data = $request->validate([
            'text' => 'required|max:1000',
            'captcha'=>'required|captcha'
        ]);
        $text = $validated_data['text'];
        $text = strip_tags($text);
        $ticket_id = $request->post('ticket_id');
        $t = SupportTicket::find($ticket_id);
        if ($t->status == 'closed') {
            return redirect()->route('ticket', [$t->id])->with(['status' => 'ticket closed']);
        }
        if ($t->user_id == Auth::id()) {
            $db = DB::transaction(function () use ($text, $ticket_id) {
                $t = SupportTicketText::create([
                    'text' => $text,
                    'ticket_id' => $ticket_id
                ]);
                $t->status == 'opened';
                $t->save();
                return 1;
            },1);
            if ($db) {
                SupportTicketMailJob::dispatch("added",$ticket_id);
                SupportTicketMailJob::dispatch("added",$ticket_id,1);
                //Mail::to(Auth::user()->email)->send(new SupportTicketMail("added", $ticket_id,Auth::user()->name));
                return redirect()->route('ticket', [$ticket_id])->with(['status'=>"Successfully added"]);
            }

        }
        return redirect()->route('wallets');

    }

    public function supportTickets($page=1)
    {
        $tickets = SupportTicket::where('user_id', Auth::id())->orderByDesc('id')->offset(10*($page-1))->limit(10)->get();
        foreach ($tickets as $ticket) {
            $ticket = $ticket->formatDates();
        }
        $pages = SupportTicket::where('user_id',Auth::id())->count();
        if ($pages%10==0)
        {
            $pages=(int)($pages/10);
        }
        else
        {
            $pages=(int)($pages/10)+1;
        }
        if ($page==0)
        {
            $page=1;
        }
        if ($pages==0)
        {
            $pages=1;
        }
        return view('support.tickets')->with(['tickets' => $tickets,'page'=>$page,'pages'=>$pages]);
    }

    public function closeTicket(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required',
        ]);
        $ticket_id = $request->post('ticket_id');
        $ticket = SupportTicket::find($ticket_id);
        if ($ticket->status!='closed')
        if ($ticket && $ticket->user_id == Auth::id()) {
            DB::transaction(function () use ($ticket) {
                $ticket->status = 'closed';
                $ticket->save();
                SupportTicketMailJob::dispatch("closed",$ticket->id);
                //Mail::to(Auth::user()->email)->send(new SupportTicketMail("closed", $ticket->id));
                return redirect()->route('supportTickets')->with(['status' => 'Ticket ' . $ticket->id . ' closed successfully']);
            },1);
        }
        return redirect()->route('supportTickets');
    }
}
