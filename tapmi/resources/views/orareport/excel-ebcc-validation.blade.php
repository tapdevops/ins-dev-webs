<table>
	<tr>
		<th style="text-align: left;back">Kode EBCC Validation</th>
		
	</tr>
	@if ( count( $data ) > 0 )
	@foreach ( $data as $dt )
	<tr>
		<td style="text-align: left;">{{ $dt->ebcc_validation_code }}</td>

	</tr>
	@endforeach
	@endif
</table>