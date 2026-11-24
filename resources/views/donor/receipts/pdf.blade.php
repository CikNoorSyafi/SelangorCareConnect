<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <title>
        Donation Receipt
    </title>

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        td {
            border: 1px solid #ddd;
            padding: 12px;
        }

        .label {
            font-weight: bold;
            width: 35%;
        }
    </style>

</head>

<body>

    <div class="header">

        <div class="title">
            SelangorCareConnect+
        </div>

        <p>
            Official Donation Receipt
        </p>

    </div>

    <table>

        <tr>

            <td class="label">
                Donor Name
            </td>

            <td>
                {{ $donorName ?? 'Donor' }}
            </td>

        </tr>

        <tr>

            <td class="label">
                Donation Amount
            </td>

            <td>
                RM {{ number_format($amount, 2) }}
            </td>

        </tr>

        <tr>

            <td class="label">
                Payment Method
            </td>

            <td>
                {{ $method }}
            </td>

        </tr>

        <tr>
            <td class="label">Contribution Frequency</td>
            <td>{{ ($frequency ?? 'one_time') === 'monthly' ? 'Monthly preference' : 'One-Time' }}</td>
        </tr>

        @if(!empty($receiptEmail))
            <tr>
                <td class="label">Receipt Email</td>
                <td>{{ $receiptEmail }}</td>
            </tr>
        @endif

        @if(!empty($dedication))
            <tr>
                <td class="label">Dedication / Note</td>
                <td>{{ $dedication }}</td>
            </tr>
        @endif

        <tr>

            <td class="label">
                Transaction Date
            </td>

            <td>
                {{ $datetime->format('d M Y h:i A') }}
            </td>

        </tr>

        <tr>

            <td class="label">
                Status
            </td>

            <td>
                {{ $status }}
            </td>

        </tr>

        <tr>

            <td class="label">
                Fund
            </td>

            <td>
                {{ $campaign }}
            </td>

        </tr>

    </table>

    <p style="margin-top:40px">

        Thank you for supporting
        SelangorCareConnect+.

    </p>

</body>

</html>