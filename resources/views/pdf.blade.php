<!DOCTYPE html>
<html>
<head>
	<title>STRUK TRANSAKSI</title>
	<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
	integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" c
	rossorigin="anonymous"> -->
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 9pt;
		},


	</style>
	
	<center>
		<img src="https://dbakbresto.ezraaudivano.com/public/akb_nota.jpg" style="width: 500px">
	</center>
    
	<hr style="border-top: 5px dashed"></hr>
	<table style="width: 100%">
		<tr>
			<th>Receipt # </th>
			<td>{{$transaksi->nomor_transaksi}}</td>
			<th>Date </th>
			<td>{{$transaksi->tanggal_transaksi}}</td>
		</tr>
		<tr>
			<th>Waiter</th>
			<td>{{$dataWaiter->name}}</td>
			<th>Time</th>
			<td>{{$dataLain['waktu']}}</td>
		</tr>

		<tr>
			<td>
			<hr style="border-top: 5px dashed "></hr>
			</td>
			<td>
			<hr style="border-top: 5px dashed "></hr>
			</td>
			<td>
			<hr style="border-top: 5px dashed "></hr>
			</td>
			<td>
			<hr style="border-top: 5px dashed "></hr>
			</td>
		</tr>
		<tr>
			<th>Table # </th>
			<td>{{$transaksi->nomor_meja}}</td>
			<th>Customer </th>
			<td>{{$transaksi->nama_customer}}</td>
		</tr>
	</table>
		<hr style="border-top: 5px double "></hr>
	<table style="width: 100%">
		<tr>
			<th>Qty</th>
			<th style="text-align: left">Item Menu </th>
			<th style="text-align: right">Harga </th>
			<th style="text-align: right">Subtotal </th>
		</tr>
		<tr>
			<th colspan="4" style="border: 5px"><hr></th>
		</tr>
			@foreach($dataDetail as $p)
		<tr>
			<td style="text-align: center">{{$p->jumlah}}</td>
			<td style="text-align: left">{{$p->nama_menu}} </td>
			<td style="text-align: right">{{$p->harga}} </td>
			<td style="text-align: right">{{$p->subtotal}} </td>
		</tr>
			@endforeach
		<tr>
			<th colspan="4"><hr style="border-top: 5px dashed"></hr></th>
		</tr>
		<tr>
			<th colspan="3" style="text-align: right">Subtotal </th>
			<td style="text-align: right">{{$transaksi->total_harga}} </td>
		</tr>
		<tr>
			<th colspan="3" style="text-align: right">Service 5% </th>
			<td style="text-align: right">{{$transaksi->pajakservice}} </td>
		</tr>
		<tr>
			<th colspan="3" style="text-align: right">Tax 10% </th>
			<td style="text-align: right">{{$transaksi->pajaktax}} </td>
		</tr>
		<tr>
			<th colspan="4"><hr style="border-top: 5px dashed"></hr></th>
		</tr>
		<tr>
			<th colspan="3" style="text-align: right">Total </th>
			<th style="text-align: right">{{$transaksi->total}}</th>
		</tr>
		<tr>
			<th colspan="4"><hr style="border-top: 5px double"></hr></th>
		</tr>
		<tr>
			<td colspan="3" style="text-align: right">Total Qty</td>
			<td style="text-align: right">{{$dataLain['jumlah_total']}}</td>
		</tr>
		<tr>
			<td colspan="3" style="text-align: right">Total Item</td>
			<td style="text-align: right">{{$dataLain['jumlah_item']}}</td>
		</tr>
		
		<tr>
			<th colspan="3" style="text-align: right">Printed </th>
			<th style="text-align: right">{{$dataLain['printed']}}</th>
		</tr>
		<tr>
			<td colspan="3" style="text-align: right">Cashier : </td>
			<td style="text-align: right">{{$transaksi->name}}</td>
		</tr>
	</table>
	<br>
	<br>
	<table style="position: absolute; bottom: 0" width="100%">
		<tr>
			<th style="text-align: center; border-top: 2px dotted black; border-bottom: 2px dotted black">THANK YOU FOR YOUR VISIT</th>
		</tr>
	</table>
</body>
</html>