<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="referrer" content="origin">
    <title>Invoice</title>

    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Dosis', sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .invoice-box.rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box.rtl table {
            text-align: right;
        }

        .invoice-box.rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>

<body>
<div class="invoice-box">
    <h2 style="text-align: center"> Reçu de paiement </h2>
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2">
                <table style="width: 100%">
                    <tr>
                        <td class="title" style="width: 30%">
                            <img src="https://nxh.cm/uploads/1678936800logochanas.webp" style="width: 100%; object-fit: cover" />
                        </td>
                        <td style="width: 35%">
                        </td>
                        <td>
                            <b>Txn ID #:</b> {{ $invoice["Transaction_id"] }}<br />
                            <b>Date :</b> {{ $invoice["date"] }}<br />
                            <br />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="information">
            <td colspan="2">
                <table>
                    <tr>
                        <td style="color:#0e338f">
                            <b>CHANAS ASSURANCES S.A</b><br />
                             B.P. 109 Bonanjo, <br>
                            fax :  233 42 99 60 <br>
                        </td>
                        <td>
                            <b>{{ $invoice["name"] }}</b> <br />
                            {{ $invoice["phone"] }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="heading">
            <td>Méthode de paiement</td>

            <td></td>
        </tr>
        <tr class="details">
            <td style="color: darkblue; font-weight: bold;">NEXAH PAY</td>

            <td></td>
        </tr>
        <tr class="heading">
            <td>Service/Produit</td>

            <td>Montant</td>
        </tr>
        @foreach($invoice["services"] as $service)
            <tr class="item">
                <td>{{ $service["name"] }}</td>

                <td>{{ $service["amount"] }} FCFA</td>
            </tr>
        @endforeach
        <tr class="total">
            <td></td>
            <td><h2>Total: {{ $invoice["total"] }} FCFA</h2></td>
        </tr>
        <tr>
            <img src="{{ base_path("public/public/uploads/".$name) }}" alt=""/>
        </tr>
        <tr class="total" >
            <td></td>
            <div>

            </div></td>
        </tr>
    </table>
</div>
</body>
</html>
