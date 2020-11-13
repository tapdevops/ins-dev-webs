<table border="1">
	<tr>
		<th>Bisnis Area</th>
		<th>Afdeling</th>
		<th>PIC Sampling</th>
		<th>Role</th>
		<th>Janjang Panen<br>Versi Validasi Sampling</th>
		<th>Janjang Panen Versi<br>Sistem - Validasi Otomatis</th>
		<th>Selisih Janjang</th>
		<th>Keterangan</th>
	</tr>
	@foreach($data_header as $data)
	@if($data->COUNT_DIFF>6 || $data->COUNT_DIFF<-6)
	<tr>
		<td style="text-align: center">{{$data->WERKS}}</td>
		<td style="text-align: center">{{$data->AFD_CODE}}</td>
		<td>{{$data->INSERT_USER}}</td>
		<td>{{$data->ROLE}}</td>
		<td style="text-align: center">{{$data->COUNT_VALIDATION}}</td>
		<td style="text-align: center">{{$data->COUNT_AI}}</td>
		<td style="text-align: center">{{$data->COUNT_DIFF}}</td>
		<td><span class="text-{{$data->KETERANGAN=='BELUM DIVERIFIKASI'?'danger':'success'}}"><b>{{$data->KETERANGAN}}</b></span></td>
	</tr>
	@endif
	@endforeach
</table>