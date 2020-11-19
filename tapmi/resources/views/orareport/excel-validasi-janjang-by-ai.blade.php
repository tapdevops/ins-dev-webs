<!DOCTYPE html>
<html>
<table>
	<tr>
		<th colspan="11">Summary hasil Validasi Janjang By AI</th>
	</tr>
	<tr>
		<td colspan="11">REGION : {{ $region }}</td>
	</tr>
	<tr>
		<td colspan="11">PT : {{ $pt }}</td>
	</tr>
	<tr>
		<td colspan="11">PERIODE : {{ $periode }}</td>
	</tr>
	<tr>
		<td colspan="11"></td>
	</tr>
	<tr>
		<td style="text-align:center;color: #FFF; background-color: #043077;"><b>PT</b></td>
		<td style="text-align:center;color: #FFF; background-color: #043077;"><b>Jumlah Transaksi Berhasil Dihitung AI</b></td>
		<td style="text-align:center;color: #FFF; background-color: #043077;"><b>Jumlah Transaksi Tidak Berhasil Dihitung AI</b></td>
		<td style="text-align:center;color: #FFF; background-color: #043077;"><b>Janjang Panen versi PIC Sampling</b></td>
		<td style="text-align:center;color: #FFF; background-color: #043077;"><b>Janjang Panen versi Sistem - Validasi Otomatis</b></td>
		<td style="text-align:center;color: #FFF; background-color: #043077;"><b>%Akurasi Janjang Panen</b></td>
		<td style="text-align:center;color: #FFF; background-color: #043077;"><b>Foto Bagus & Inputan PIC Sesuai</b></td>
		<td style="text-align:center;color: #FFF; background-color: #043077;"><b>Foto Bagus tapi Inputan PIC Tidak Sesuai</b></td>
		<td style="text-align:center;color: #FFF; background-color: #043077;"><b>Foto Bagus tapi Jumlah Janjang > 30</b></td>
		<td style="text-align:center;color: #FFF; background-color: #043077;"><b>Foto Tidak Muncul</b></td>
		<td style="text-align:center;color: #FFF; background-color: #043077;"><b>Blur</b></td>
		<td style="text-align:center;color: #FFF; background-color: #043077;"><b>Jauh</b></td>
		<td style="text-align:center;color: #FFF; background-color: #043077;"><b>Gambar Janjang Terpotong</b></td>
		<td style="text-align:center;color: #FFF; background-color: #043077;"><b>Gelap / Tertutup Bayangan</b></td>
		<td style="text-align:center;color: #FFF; background-color: #043077;"><b>Penyusunan / Angel Pengambilan Tidak Sesuai</b></td>
	</tr>
	@if( !empty( $data ) )
		@foreach( $data as $dt )
			<tr>
				<td>
					@isset($dt->ba_code)
						{{ $dt->ba_code.' - '.$dt->ba_name }}
					@endisset
				</td>
				<td>
					@isset($dt->berhasi_dihitung)
						{{ $dt->berhasi_dihitung }}
					@endisset
				</td>
				<td>
					@isset($dt->tidak_berhasi_dihitung)
						{{ $dt->tidak_berhasi_dihitung }}
					@endisset
				</td>
				<td>
					@isset($dt->total_janjang_pic)
						{{ $dt->total_janjang_pic }}
					@endisset
				</td>
				<td>
					@isset($dt->total_janjang_ai)
						{{ $dt->total_janjang_ai }}
					@endisset
				</td>
				<td>
					@isset($dt->akurasi)
						{{ $dt->akurasi }}
					@endisset
				</td>
				<td>
					@isset($dt->kondisi_1)
						{{ $dt->kondisi_1 }}
					@endisset
				</td>
				<td>
					@isset($dt->kondisi_2)
						{{ $dt->kondisi_2 }}
					@endisset
				</td>
				<td>
					@isset($dt->kondisi_3)
						{{ $dt->kondisi_3 }}
					@endisset
				</td>
				<td>
					@isset($dt->kondisi_4)
						{{ $dt->kondisi_4 }}
					@endisset
				</td>
				<td>
					@isset($dt->kondisi_5)
						{{ $dt->kondisi_5 }}
					@endisset
				</td>
				<td>
					@isset($dt->kondisi_6)
						{{ $dt->kondisi_6 }}
					@endisset
				</td>
				<td>
					@isset($dt->kondisi_7)
						{{ $dt->kondisi_7 }}
					@endisset
				</td>
				<td>
					@isset($dt->kondisi_8)
						{{ $dt->kondisi_8 }}
					@endisset
				</td>
				<td>
					@isset($dt->kondisi_9)
						{{ $dt->kondisi_9 }}
					@endisset
				</td>
			</tr>
		@endforeach
	@else	
		<tr>
			<td colspan="15">Data Not Found</td>
		</tr>
	@endif
</table>
</html>