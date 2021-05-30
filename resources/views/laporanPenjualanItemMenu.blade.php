<!DOCTYPE html>
<html>
<head>
	<title>LAPORAN PENJUALAN ITEM MENU</title>
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
		<img src="{{public_path('assets/akb_nota.jpg')}}" style="width: 500px">
	</center>
    <h3 style="text-align: center">LAPORAN PENJUALAN ITEM MENU</h3>
	<hr style="border-top: 5px dashed"></hr>
    <h4 style="text-align: left">Periode :{{$dataLain['periode']}}</h4>
	<table style="width: 100%">
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
            <td>
			<hr style="border-top: 5px dashed "></hr>
			</td>
		</tr>
	</table>
		<hr style="border-top: 5px "></hr>
	<table style="width: 100%">
		<tr>
			<th colspan="5" style="text-align: left">Makanan Utama</th>
		</tr>
		<tr>
			<th colspan="5" style="border: 5px"><hr></th>
		</tr>
		
		<tr>
			<td style="text-align: center">No</td>
			<td style="text-align: left">Item Menu </td>
			<td style="text-align: right">Unit </td>
			<td style="text-align: right">Penjualan Harian Tertinggi </td>
			<td style="text-align: right">Total Penjualan </td>
		</tr>
        @php $i=1 @endphp
        @foreach($menu as $p)
        @if($p->kategori=='Makanan Utama')
        <tr>
			<td style="text-align: center">{{$i++}}</td>
			<td style="text-align: left">{{$p->nama_menu}}</td>
			<td style="text-align: right">{{$p->unit}} </td>
			<td style="text-align: right">{{$p->penjualanTertinggi}} </td>
			<td style="text-align: right">{{$p->totalPenjualan}} </td>
		</tr>
        @endif
        @endforeach
		<tr>
			<th colspan="5"><hr style="border-top: 5px double"></hr></th>
		</tr>
        <tr>
			<th colspan="5" style="text-align: left">Side Dish</th>
		</tr>
		<tr>
			<th colspan="5" style="border: 5px"><hr></th>
		</tr>
			
		<tr>
			<td style="text-align: center">No</td>
			<td style="text-align: left">Item Menu </td>
			<td style="text-align: right">Unit </td>
			<td style="text-align: right">Penjualan Harian Tertinggi </td>
			<td style="text-align: right">Total Penjualan </td>
		</tr>
        @php $i=1 @endphp
        @foreach($menu as $p)
        @if($p->kategori=='Side Dish')
        <tr>
            <td style="text-align: center">{{$i++}}</td>
			<td style="text-align: left">{{$p->nama_menu}}</td>
			<td style="text-align: right">{{$p->unit}} </td>
			<td style="text-align: right">{{$p->penjualanTertinggi}} </td>
			<td style="text-align: right">{{$p->totalPenjualan}} </td>
		</tr>
        @endif
        @endforeach
        <tr>
			<th colspan="5"><hr style="border-top: 5px double"></hr></th>
		</tr>
        <tr>
			<th colspan="5" style="text-align: left">Minuman</th>
		</tr>
		<tr>
			<th colspan="5" style="border: 5px"><hr></th>
		</tr>
			
		<tr>
			<td style="text-align: center">No</td>
			<td style="text-align: left">Item Menu </td>
			<td style="text-align: right">Unit </td>
			<td style="text-align: right">Penjualan Harian Tertinggi </td>
			<td style="text-align: right">Total Penjualan </td>
		</tr>
        @php $i=1 @endphp
        @foreach($menu as $p)
        @if($p->kategori=='Minuman')
        <tr>
            <td style="text-align: center">{{$i++}}</td>
			<td style="text-align: left">{{$p->nama_menu}}</td>
			<td style="text-align: right">{{$p->unit}} </td>
			<td style="text-align: right">{{$p->penjualanTertinggi}} </td>
			<td style="text-align: right">{{$p->totalPenjualan}} </td>
		</tr>
        @endif
        @endforeach
        <tr>
			<th colspan="5"><hr style="border-top: 5px double"></hr></th>
		</tr>
		
		
	</table>
	<br>
	<br>
	<table style="position: absolute; bottom: 0" width="100%">
        <tr>
			<th style="text-align: center"> {{$dataLain['printed']}} </th>
		</tr>
		<tr>
			<td style="text-align: center">Printed By : <u>{{$dataLain['nama_karyawan']}}</u></td>
		</tr>
	</table>
</body>
</html>