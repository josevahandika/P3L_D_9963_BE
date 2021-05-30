<!DOCTYPE html>
<html>
<head>
	<title>LAPORAN PENGELUARAN</title>
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
    <h3 style="text-align: center">LAPORAN PENGELUARAN</h3>
	<hr style="border-top: 5px dashed"></hr>
    @if($dataLain['status']=='Bulanan')
    <h4 style="text-align: left">Tahun :{{$dataLain['tahun']}}</h4>
		<hr style="border-top: 5px "></hr>
    @endif
    @if($dataLain['status']=='Tahunan')
    <h4 style="text-align: left">Periode :{{$dataLain['periode']}}</h4>
		<hr style="border-top: 5px "></hr>
    @endif   
	<table style="width: 100%">
		<tr>
			<td style="text-align: center">No</td>
            @if($dataLain['status']=='Bulanan')
			<td style="text-align: left">Bulan </td>
            @endif
            @if($dataLain['status']=='Tahunan')
			<td style="text-align: left">Tahun </td>
            @endif
			<td style="text-align: right">Makanan Utama </td>
			<td style="text-align: right">Side Dish </td>
			<td style="text-align: right">Minuman </td>
			<td style="text-align: right">Total Pendapatan </td>
		</tr>
        @php $i=1 @endphp
        @foreach($menu as $p)
        <tr>
			<td style="text-align: center">{{$i++}}</td>
			<td style="text-align: left">{{$p['bulan']}}</td>
			<td style="text-align: right">Rp {{number_format($p['MakananUtama'],0,',','.')}} </td>
			<td style="text-align: right">Rp {{number_format($p['SideDish'],0,',','.')}} </td>
			<td style="text-align: right">Rp {{number_format($p['Minuman'],0,',','.')}} </td>
			<td style="text-align: right">Rp {{number_format($p['TotalPendapatan'],0,',','.')}} </td>
		</tr>
        @endforeach
		<tr>
			<th colspan="6"><hr style="border-top: 5px double"></hr></th>
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