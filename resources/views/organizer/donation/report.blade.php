<!DOCTYPE html>
<html>

<head>

    <title>Donation Report</title>

    <style>
        body {
            font-family: DejaVu Sans;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }

        h1 {
            text-align: center;
        }

        .summary {
            margin-bottom: 20px;
        }
    </style>

</head>

<body>

    <h1>SelangorCareConnect+ Donation Report</h1>

    <div class="summary">

        <p>
            Total Collections:
            RM {{ number_format($totalCollections, 2) }}
        </p>

        <p>
            Total Allocated:
            RM {{ number_format($totalAllocated, 2) }}
        </p>

        <p>
            Pending Verifications:
            {{ $pendingVerifications }}
        </p>

    </div>

    <table>

        <thead>

            <tr>

                <th>Date</th>
                <th>Contributor</th>
                <th>Campaign</th>
                <th>Amount</th>
                <th>Status</th>

            </tr>

        </thead>

        <tbody>

            @foreach($donations as $donation)

                <tr>

                    <td>{{ $donation['date'] }}</td>

                    <td>{{ $donation['contributor'] }}</td>

                    <td>{{ $donation['campaign'] }}</td>

                    <td>
                        RM {{ number_format((float) $donation['amount'], 2) }}
                    </td>

                    <td>{{ $donation['status'] }}</td>

                </tr>

            @endforeach

        </tbody>

    </table>

</body>

</html>