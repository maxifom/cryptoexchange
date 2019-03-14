        <!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @if ($type=='added')
        <title>{{env("APP_NAME")}} | {{$type}} to Support ticket </title>

    @else
        <title>{{env("APP_NAME")}} Support ticket {{$type}} </title>
    @endif
    <style>
        .btn {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            border: 1px solid transparent;
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: .25rem;
            transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }

        a {
            text-decoration: none;
        }

        .card-title {
            font-size: 18px;
        }

        .card-text {
            font-size: 16px;
        }

        .card {
            padding: 15px;
            border: 1px solid #bbb;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
        }

    </style>
</head>
<body>
<div class="card"
     style="padding: 15px;border: 1px solid #bbb;-webkit-border-radius: 10px;-moz-border-radius: 10px;border-radius: 10px;">
    <div class="card-body">
        @if ($type=='added')
            <h5 class="card-title" style="font-size: 18px;">{{env("APP_NAME")}} | {{$type}} to support ticket </h5>
        @else
            <h5 class="card-title" style="font-size: 18px;">{{env("APP_NAME")}} | Support ticket {{$type}}</h5>
        @endif
        <p class="card-text" style="font-size: 16px;">Dear {{$name}}, <br> @if ($type=='added' || $type=='opened')you have
            successfully {{$type}} @if ($type=='added') to @endif
            support ticket.@endif @if ($type=='answered') your ticket was answered. @endif
            @if ($type=='closed') your ticket was closed. @endif
            <br>
            @if (!$admin)
                <a href="{{route('ticket',$ticket_id)}}"
                   style="text-decoration: none;display: inline-block;font-weight: 400;text-align: center;white-space: nowrap;vertical-align: middle;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;border: 1px solid transparent;padding: .375rem .75rem;font-size: 16px;line-height: 1.5;border-radius: .25rem;transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;color: #fff;background-color: #007bff;border-color: #007bff;">Show
                    ticket</a>
            @else
                <a href="{{route('admin_ticket',$ticket_id)}}"
                   style="text-decoration: none;display: inline-block;font-weight: 400;text-align: center;white-space: nowrap;vertical-align: middle;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;border: 1px solid transparent;padding: .375rem .75rem;font-size: 16px;line-height: 1.5;border-radius: .25rem;transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;color: #fff;background-color: #007bff;border-color: #007bff;">Show
                    ticket</a>
            @endif
        </p></div>
</div>
<pre>Anti-phishing code: {{$code}}</pre>
</body>
</html>
