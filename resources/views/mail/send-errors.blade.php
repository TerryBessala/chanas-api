<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ $data['subject'] }}</title>
    <link href="https://fonts.googleapis.com/css?family=Muli&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style type="text/css">

        body {
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Muli', sans-serif;
        }
        div.link{
            width: 100%;
            justify-content: center !important;
            display: flex;
            line-height: 2;
        }
        a{
            cursor: pointer;
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
            text-decoration: none !important;
            color: #fff !important;
            background-color: #20a8d8;
            border-color: #20a8d8;
            text-align: center;
            padding: .375rem .75rem;
            font-size: 1rem;
            border-radius: 0.25rem;
            font-family: 'Muli', sans-serif;
            display: block;
            width: 195px;
            margin : 0 auto;
        }
        td{
            padding: 5px;
        }
        .text-center{
            text-align: center !important;
        }
        .text-left{
            text-align: left !important;
        }
        .text-right{
            text-align: right !important;
        }
        .text-justify{
            text-align: justify !important;
        }
        .text-white {
            color: #fff !important;
        }
        .row{
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        .col{
            -webkit-box-flex: 0;
            -ms-flex: 0 0 25%;
            flex: 0 0 25%;
            width: 25%;
            padding-right: 5px;
            padding-left: 5px;
            font-size: 13px;
        }
        @media (max-width:768px) {
            /*.col{
                flex: 0 0 100%;
                max-width: 100%;
            }*/
        }
        .bg-primary {
            background-color: #20a8d8 !important;
            border-color: #20a8d8 !important;
        }
        .bg-success {
            background-color: #379457 !important;
            border-color: #379457 !important;
        }
        .bg-danger {
            background-color: #f5302e !important;
            border-color: #f5302e !important;
        }
        .shadow {
            -webkit-box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
        }
        .card {
            position: relative;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid #c8ced3;
            border-radius: .25rem;
        }
        .card-body {
            -webkit-box-flex: 1;
            -ms-flex: 1 1 auto;
            flex: 1 1 auto;
            padding: 1.25rem;
        }
        .text-value-lg {
            font-size: 1.53125rem;
            font-weight: 600;
        }
        .font-weight-bold {
            font-weight: 700 !important;
        }
        .text-uppercase {
            text-transform: uppercase !important;
        }
    </style>
</head>
<body style="font-family: 'Maven Pro', sans-serif">
<div style="background: #fff !important; color: #212121 !important;">
    <table width="100%" border="1" class="table">
        @foreach ($data['data'] as $key => $dat)
            <tr>
                <th><b>{{ $key }}</b></th> <td><pre>{{ json_encode($dat) }}</pre></td>
            </tr>
        @endforeach
    </table>
</div>
</body>
</html>
