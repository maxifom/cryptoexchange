<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{env("APP_NAME")}} IP confirmation</title>
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
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        a
        {
            text-decoration: none;
        }
        .card-title
        {
            font-size:18px;
        }
        .card-text
        {
            font-size:16px;
        }
        .btn
        {
            font-size:16px;
        }
        .card
        {
            padding:15px;
            border:1px solid #bbb;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="card" style="padding: 15px;border: 1px solid #bbb;-webkit-border-radius: 10px;-moz-border-radius: 10px;border-radius: 10px;">
    <div class="card-body">
        <h5 class="card-title" style="font-size: 18px;">{{env("APP_NAME")}} IP confirmation</h5>
        <p class="card-text" style="font-size: 16px;">Dear {{$name}}, <br> if you have requested IP confirmation for {{$ip}} please click the button below</p>
        <a href="{{route('confirmIpToken',$token)}}" class="btn btn-primary" style="text-decoration: none;display: inline-block;font-weight: 400;text-align: center;white-space: nowrap;vertical-align: middle;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;border: 1px solid transparent;padding: .375rem .75rem;font-size: 16px;line-height: 1.5;border-radius: .25rem;transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;color: #fff;background-color: #007bff;border-color: #007bff;">Confirm IP</a>
    </div>
</div>
<p>Or enter this token: {{$token}}</p>
<pre>Anti-phishing code: {{$code}}</pre>
</body>
</html>
