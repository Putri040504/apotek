<!DOCTYPE html>
<html>

<head>

    <title>Data Supplier</title>

    <style>
        body {
            font-family: Arial, sans-serif;
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
            margin-bottom: 0;
        }

        h4 {
            text-align: center;
            margin-top: 5px;
        }
    </style>

</head>

<body>

    <h2>APOTEK ZEMA</h2>
    <h4>Data Supplier</h4>

    <table>

        <thead>

            <tr>

                <th>No</th>
                <th>Kode Supplier</th>
                <th>Nama Supplier</th>
                <th>Nama Obat</th>
                <th>Alamat</th>
                <th>Email</th>
                <th>No Telp</th>

            </tr>

        </thead>

        <tbody>

            @foreach ($supplier as $s)
                <tr>

                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $s->kode_supplier }}</td>
                    <td>{{ $s->nama_supplier }}</td>
                    <td>{{ $s->obat->nama_obat ?? '-' }}</td>
                    <td>{{ $s->alamat }}</td>
                    <td>{{ $s->email }}</td>
                    <td>{{ $s->no_telp }}</td>

                </tr>
            @endforeach

        </tbody>

    </table>

</body>

</html>
