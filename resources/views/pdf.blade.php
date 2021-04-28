<!DOCTYPE html>
<html>
<head>
	<title>LAPORAN DATA WARGA DUSUN SEMILIR</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
	integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" c
	rossorigin="anonymous">
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 9pt;
		}
	</style>
	<center>
		<h3>DATA WARGA DUSUN SEMILIR</h3>
		<h5>TERDATA DI E-Family</h5>
		<img src="https://gerardoleonel.com/efamily.png">
        @php echo'<h5> Terbit pada: </h5>'; echo date('d-m-Y');@endphp
	</center>
    
    <br><br>

	<table class='table table-bordered'>
		<thead>
			<tr>
				<th>No</th>
				<th>Nomor Transaksi</th>
				<th>Metode Pembayaran</th>
				
			</tr>
		</thead>
		<tbody>
			@php $i=1 @endphp
			@foreach($transaksi as $p)
			<tr>
				<td>{{ $i++ }}</td>
				<td>{{$p->nomor_transaksi}}</td>
				<td>{{$p->metode_pembayaran}}</td>
				
			</tr>
			@endforeach
		</tbody>
	</table>
 
</body>
</html>