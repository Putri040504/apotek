<!DOCTYPE html>
<html>
<head>
<title>Data Kategori</title>
<style>

table{
width:100%;
border-collapse:collapse;
}

table,th,td{
border:1px solid black;
}

th,td{
padding:8px;
text-align:center;
}

</style>
</head>

<body>

<h2 style="text-align:center;">Data Kategori</h2>

<table>

<tr>
<th>No</th>
<th>Kode Kategori</th>
<th>Nama Kategori</th>
</tr>

@foreach($kategori as $k)

<tr>
<td>{{ $loop->iteration }}</td>
<td>{{ $k->kode_kategori }}</td>
<td>{{ $k->nama_kategori }}</td>
</tr>

@endforeach

</table>

</body>
</html>