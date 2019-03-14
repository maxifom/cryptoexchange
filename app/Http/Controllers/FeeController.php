<?php

namespace App\Http\Controllers;

use App\Coin;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function fees()
    {
        $coins = Coin::where('status','confirmed')->get();
        return view('fees')->with(['coins'=>$coins]);
    }
}
