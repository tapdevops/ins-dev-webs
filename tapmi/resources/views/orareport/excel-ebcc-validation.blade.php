<table>
	<tr>
		<th style="text-align: left;background-color: #ff9933;">Kode EBCC Validation</th>
		<th style="text-align: left;background-color: #ff9933;">Kode BA</th>
		<th style="text-align: left;background-color: #ff9933;">Business Area</th>
		<th style="text-align: left;background-color: #ff9933;">AFD</th>
		<th style="text-align: left;background-color: #ff9933;">Kode Block</th>
		<th style="text-align: left;background-color: #ff9933;">Block Deskripsi</th>
		<th style="text-align: left;background-color: #ff9933;">TPH</th>
		<th style="text-align: left;background-color: #ff9933;">Inputan TPH</th>
		<th style="text-align: left;background-color: #ff9933;">Alasan Input Manual</th>
		<th style="text-align: left;background-color: #ff9933;">Tanggal Validasi</th>
		<th style="text-align: left;background-color: #ff9933;">NIK Validator</th>
		<th style="text-align: left;background-color: #ff9933;">Nama Validator</th>
		<th style="text-align: left;background-color: #ff9933;">Jabatan Validator</th>
		<th style="text-align: left;background-color: #ff9933;">Maturity Status</th>
		<th style="text-align: left;background-color: #ff9933;">Periode</th>
		<th style="text-align: left;background-color: #ff9933;">Lat</th>
		<th style="text-align: left;background-color: #ff9933;">Long</th>
		@foreach($head as $hd)
			<th style="text-align: left;background-color: #ff9933;">{{ $hd->nama_kualitas }}</th>
		@endforeach
		<th style="text-align: left;background-color: #ff9933;">Total Janjang Panen</th>
	</tr>
	@if ( count( $data ) > 0 )
	@foreach ( $data as $dt )
	@php
		$dt = (array) $dt;
		
	@endphp
	<tr>
		<td style="text-align: left;">{{ $dt['val_ebcc_code'] }}</td>
		<td style="text-align: left;">{{ $dt['val_werks'] }}</td>
		<td style="text-align: left;">{{ $dt['val_est_name'] }}</td>
		<td style="text-align: left;">{{ $dt['val_afd_code'] }}</td>
		<td style="text-align: left;">{{ $dt['val_block_code'] }}</td>
		<td style="text-align: left;">{{ $dt['val_block_name'] }}</td>
		<td style="text-align: left;">{{ $dt['val_tph_code'] }}</td>
		<td style="text-align: left;">{{ $dt['val_status_tph_scan'] }}</td>
		<td style="text-align: left;">{{ $dt['val_alasan_manual'] }}</td>
		<td style="text-align: left;">{{ $dt['val_date_time'] }}</td>
		<td style="text-align: left;">{{ $dt['val_nik_validator'] }}</td>
		<td style="text-align: left;">{{ $dt['val_nama_validator'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jabatan_validator'] }}</td>
		<td style="text-align: left;">{{ $dt['val_maturity_status'] }}</td>
		<td style="text-align: left;">{{ $periode }}</td>
		<td style="text-align: left;">{{ $dt['val_lat_tph'] }}</td>
		<td style="text-align: left;">{{ $dt['val_lon_tph'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_1'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_2'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_3'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_4'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_6'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_15'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_16'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_5'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_8'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_7'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_9'] }}</td>
		<td style="text-align: left;">{{ ( $dt['val_jml_10'] == '1' ? 'Ada' : 'Tidak ada' ) }}</td>
		<td style="text-align: left;">{{ $dt['val_total_jjg'] }}</td>
	</tr>
	@endforeach
	@endif
</table>