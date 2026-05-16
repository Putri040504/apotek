<!DOCTYPE html>
<html>

<head>

    <title>Data Pengguna</title>

    <style>
        body {
            font-family: Arial;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
        }

        h2 {
            text-align: center;
        }
    </style>

</head>

<body>

    <h2>APOTEK ZEMA</h2>
    <h3 style="text-align:center">Data Pengguna</h3>

    <table>

        <thead>

            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
            </tr>

        </thead>

        <tbody>

            @foreach ($users as $user)
                <tr>

                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>

                </tr>
            @endforeach

        </tbody>

    </table>

</body>

</html>
