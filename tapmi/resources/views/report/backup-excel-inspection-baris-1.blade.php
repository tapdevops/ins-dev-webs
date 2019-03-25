<table>
	<tr>
		<th>Periode : {{ $periode }}</th>
	</tr>
	<tr>
		<th style="text-align: center;">Kode Inspeksi</th>
		<th style="text-align: center;">Kode BA</th>
		<th style="text-align: center;">Business Area</th>
		<th style="text-align: center;">AFD</th>
		<th style="text-align: center;">Kode Block</th>
		<th style="text-align: center;">Block Deskripsi</th>
		<th style="text-align: center;">Tanggal Inspeksi</th>
		<th style="text-align: center;">Areal No</th>
		<th style="text-align: center;">NIK Reporter</th>
		<th style="text-align: center;">Nama Reporter</th>
		<th style="text-align: center;">Jabatan Reporter</th>
		<th style="text-align: center;">Periode</th>
		<th style="text-align: center;">Maturity Status</th>
		<th style="text-align: center;">Lat Start</th>
		<th style="text-align: center;">Long Start</th>

		@foreach ( $ct_panen as $content )
			<th style="text-align: center;">{{ $content['CONTENT_NAME'] }}</th>
		@endforeach

		@foreach ( $ct_pemupukan as $content )
			<th style="text-align: center;">{{ $content['CONTENT_NAME'] }}</th>
		@endforeach

		@foreach ( $ct_perawatan_bobot as $content )
			<th style="text-align: center;">{{ $content['CONTENT_NAME'] }}</th>
		@endforeach

		@foreach ( $ct_perawatan as $content )
			<th style="text-align: center;">{{ $content['CONTENT_NAME'] }}</th>
		@endforeach

		@foreach ( $ct_perawatan_bobot as $content )
			<th style="text-align: center;">Nilai {{ $content['CONTENT_NAME'] }}</th>
		@endforeach
	</tr>
	@if ( count( $inspection_data ) > 0 )
		@foreach ( $inspection_data as $inspection )
			@if ( isset( $inspection['BLOCK_INSPECTION_CODE'] ) )
				<tr>
					<td style="text-align: center;">{{ $inspection['BLOCK_INSPECTION_CODE'] }}</td>
					<td style="text-align: center;">{{ $inspection['WERKS'] }}</td>
					<td>{{ $inspection['EST_NAME'] }}</td>
					<td style="text-align: center;">{{ $inspection['AFD_CODE'] }}</td>
					<td style="text-align: center;">{{ $inspection['BLOCK_CODE'] }}</td>
					<td>{{ $inspection['BLOCK_NAME'] }}</td>
					<td style="text-align: center;">{{ date( 'Y-m-d', strtotime( $inspection['INSPECTION_DATE'] ) ) }}</td>
					<td style="text-align: center;">{{ $inspection['AREAL'] }}</td>
					<td style="text-align: center;">{{ $inspection['INSPEKTOR']['EMPLOYEE_NIK'] }}</td>
					<td>{{ $inspection['INSPEKTOR']['FULLNAME'] }}</td>
					<td>{{ $inspection['INSPEKTOR']['JOB'] }}</td>
					<td style="text-align: center;">{{ date( 'Y.m', strtotime( $inspection['INSPECTION_DATE'] ) ) }}</td>
					<td style="text-align: center;">{{ $inspection['MATURITY_STATUS'] }}</td>
					<td>{{ $inspection['LAT_START_INSPECTION'] }}</td>
					<td>{{ $inspection['LONG_START_INSPECTION'] }}</td>
					
					@foreach ( $ct_panen as $kcp => $cp )
						@if ( isset( $inspection['DETAIL'][$kcp] ) )
							<td style="text-align: center;">{{ $inspection['DETAIL'][$kcp] }}</td>
						@else
							<td style="text-align: center;">0</td>
						@endif
					@endforeach

					@foreach ( $ct_pemupukan as $kcp => $cp )
						@if ( isset( $inspection['DETAIL_REAL'][$kcp] ) )
							<td style="text-align: center;">{{ $inspection['DETAIL_REAL'][$kcp] }}</td>
						@else
							<td style="text-align: center;">0</td>
						@endif
					@endforeach

					@foreach ( $ct_perawatan_bobot as $kcp => $cp )
						@if ( isset( $inspection['DETAIL_REAL'][$kcp] ) )
							<td style="text-align: center;">{{ $inspection['DETAIL_REAL'][$kcp] }}</td>
						@else
							<td></td>
						@endif
					@endforeach

					@foreach ( $ct_perawatan as $kcp => $cp )
						@if ( isset( $inspection['DETAIL_REAL'][$kcp] ) )
							<td style="text-align: center;">{{ $inspection['DETAIL_REAL'][$kcp] }}</td>
						@else
							<td></td>
						@endif
					@endforeach

					@foreach ( $ct_perawatan_bobot as $kcp => $cp )
						@if ( isset( $inspection['DETAIL'][$kcp] ) )
							<td style="text-align: center;">{{ $inspection['DETAIL'][$kcp] }}</td>
						@else
							<td style="text-align: center;">0</td>
						@endif
					@endforeach
					
				</tr>
			@endif
		@endforeach
	@endif
</table>