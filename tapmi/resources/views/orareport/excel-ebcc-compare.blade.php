<table>
	<tr>
		<th style="text-align: left;back">VAL_EBCC_CODE</th>
		<th style="text-align: left;back">VAL_WERKS</th>
		<th style="text-align: left;back">VAL_NIK_VALIDATOR</th>
		<th style="text-align: left;back">VAL_NAMA_VALIDATOR</th>
		<th style="text-align: left;back">VAL_JABATAN_VALIDATOR</th>
		<th style="text-align: left;back">VAL_DATE_TIME</th>
		<th style="text-align: left;back">VAL_AFD_CODE</th>
		<th style="text-align: left;back">VAL_BLOCK_CODE</th>
		<th style="text-align: left;back">VAL_BLOCK_NAME</th>
		<th style="text-align: left;back">VAL_TPH_CODE</th>
		<th style="text-align: left;back">VAL_DELIVERY_TICKET</th>
		<th style="text-align: left;back">VAL_JML_BM</th>
		<th style="text-align: left;back">VAL_JML_BK</th>
		<th style="text-align: left;back">VAL_JML_MS</th>
		<th style="text-align: left;back">VAL_JML_OR</th>
		<th style="text-align: left;back">VAL_JML_BB</th>
		<th style="text-align: left;back">VAL_JML_JK</th>
		<th style="text-align: left;back">VAL_JML_BA</th>
		<th style="text-align: left;back">VAL_JML_BRD</th>
		<th style="text-align: left;back">VAL_JJG_PANEN</th>
		<th style="text-align: left;back">EBCC_ID_RENCANA</th>
		<th style="text-align: left;back">EBCC_NO_BCC</th>
		<th style="text-align: left;back">EBCC_WERKS</th>
		<th style="text-align: left;back">EBCC_NIK_KERANI_BUAH</th>
		<th style="text-align: left;back">EBCC_NAMA_KERANI_BUAH</th>
		<th style="text-align: left;back">EBCC_JABATAN_KERANI_BUAH</th>
		<th style="text-align: left;back">EBCC_DATE_TIME</th>
		<th style="text-align: left;back">EBCC_AFD_CODE</th>
		<th style="text-align: left;back">EBCC_BLOCK_CODE</th>
		<th style="text-align: left;back">EBCC_BLOCK_NAME</th>
		<th style="text-align: left;back">EBCC_TPH_CODE</th>
		<th style="text-align: left;back">EBCC_JML_BM</th>
		<th style="text-align: left;back">EBCC_JML_BK</th>
		<th style="text-align: left;back">EBCC_JML_MS</th>
		<th style="text-align: left;back">EBCC_JML_OR</th>
		<th style="text-align: left;back">EBCC_JML_BB</th>
		<th style="text-align: left;back">EBCC_JML_JK</th>
		<th style="text-align: left;back">EBCCJML_BA</th>
		<th style="text-align: left;back">EBCC_JML_BRD</th>
		<th style="text-align: left;back">EBCC_JJG_PANEN</th>
		
		
	</tr>
	@if ( count( $data ) > 0 )
	@foreach ( $data as $dt )
	@php
		$dt = (array) $dt;
		
	@endphp
	<tr>
		<td style="text-align: left;">{{ $dt['val_ebcc_code'] }}</td>
		<td style="text-align: left;">{{ $dt['val_werks'] }}</td>
		<td style="text-align: left;">{{ $dt['val_nik_validator'] }}</td>
		<td style="text-align: left;">{{ $dt['val_nama_validator'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jabatan_validator'] }}</td>
		<td style="text-align: left;">{{ $dt['val_date_time'] }}</td>
		<td style="text-align: left;">{{ $dt['val_afd_code'] }}</td>
		<td style="text-align: left;">{{ $dt['val_block_code'] }}</td>
		<td style="text-align: left;">{{ $dt['val_block_name'] }}</td>
		<td style="text-align: left;">{{ $dt['val_tph_code'] }}</td>
		<td style="text-align: left;">{{ $dt['val_delivery_ticket'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_bm'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_bk'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_ms'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_or'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_bb'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_jk'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_ba'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jml_brd'] }}</td>
		<td style="text-align: left;">{{ $dt['val_jjg_panen'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_id_rencana'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_no_bcc'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_werks'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_nik_kerani_buah'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_nama_kerani_buah'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_jabatan_kerani_buah'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_date_time'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_afd_code'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_block_code'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_block_name'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_tph_code'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_jml_bm'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_jml_bk'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_jml_ms'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_jml_or'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_jml_bb'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_jml_jk'] }}</td>
		<td style="text-align: left;">{{ $dt['ebccjml_ba'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_jml_brd'] }}</td>
		<td style="text-align: left;">{{ $dt['ebcc_jjg_panen'] }}</td>

	</tr>
	@endforeach
	@endif
</table>