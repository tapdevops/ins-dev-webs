<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<title>Preview - LAPORAN SAMPLING EBCC vs EBCC</title>
	<style type="text/css">
		.disable-select {
			user-select: none; /* supported by Chrome and Opera */
			-webkit-user-select: none; /* Safari */
			-khtml-user-select: none; /* Konqueror HTML */
			-moz-user-select: none; /* Firefox */
			-ms-user-select: none; /* Internet Explorer/Edge */
		}
	</style>
</head>
<body>
	<div class="container disable-select" id="capture" oncontextmenu="return false;">
		<br />
		<h4 class="text-center">LAPORAN SAMPLING EBCC vs EBCC</h4>
		<p class="text-center">PT: {{ $data['val_est_name'] }}; BISNIS AREA: {{ $data['val_werks'] }}; AFD: {{ $data['val_afd_code'] }}; BLOCK: {{ $data['val_block_code'].'/'.$data['val_block_name'] }}; TPH: {{ $data['val_tph_code'] }}</p>
		<div class="row" style="margin-top: 20px;">
			<div class="col-md-6">
				<div class="card">
					<div class="card-header text-center bg-warning">
						<b>SAMPLING EBCC</b>
					</div>
					<div class="card-body">
						<table class="table table-bordered">
							<tr style="font-size:14px;">
								<td class="text-center">BM (jjg)</td>
								<td class="text-center">BK (jjg)</td>
								<td class="text-center">MS (jjg)</td>
								<td class="text-center">OR (jjg)</td>
								<td class="text-center">BB (jjg)</td>
								<td class="text-center">JK (jjg)</td>
								<td class="text-center">BA (jjg)</td>
								<td class="text-center">Total<br />Janjang<br />Panen</td>
							</tr>
							<tr>
								<td>{{ $data['val_jml_bm'] }}</td>
								<td>{{ $data['val_jml_bk'] }}</td>
								<td>{{ $data['val_jml_ms'] }}</td>
								<td>{{ $data['val_jml_or'] }}</td>
								<td>{{ $data['val_jml_bb'] }}</td>
								<td>{{ $data['val_jml_jk'] }}</td>
								<td>{{ $data['val_jml_ba'] }}</td>
								<td>{{ $data['val_jjg_panen'] }}</td>
							</tr>
						</table>
						<div class="row">
							<div class="col-md-8">
								<table cellpadding="2px;" style="font-size: 12px;">
									<tr>
										<td width="40%">NIK</td>
										<td width="5%">:</td>
										<td width="55%">{{ $data['val_nik_validator'] }}</td>
									</tr>
									<tr>
										<td>Nama Lengkap</td>
										<td>:</td>
										<td>{{ $data['val_nama_validator'] }}</td>
									</tr>
									<tr>
										<td>Jabatan</td>
										<td>:</td>
										<td>{{ $data['val_jabatan_validator'] }}</td>
									</tr>
									<tr>
										<td>Waktu Pencatatan</td>
										<td>:</td>
										<td>{{ $data['val_date_time'] }}</td>
									</tr>
									<tr>
										<td>Status Scan QR Code</td>
										<td>:</td>
										<td>{{ $data['val_status_tph_scan'].' '.$data['val_alasan_manual'] }}</td>
									</tr>
								</table>
							</div>
							<div class="col-md-4">
								<img src="http://beautycraftkitchens.com/wp-content/uploads/2017/02/dummy_user.png" width="100%;">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card">
					<div class="card-header text-center bg-success" style="color:white !important">
						<b>EBCC</b>
					</div>
					<div class="card-body">
						@if ( $data['ebcc_no_bcc'] == '' )
							<img src="{{ url( 'assets/notfound.jpg' ) }}" width="234px" class="rounded mx-auto d-block">
							<h3 class="text-center">EBCC tidak ditemukan</h3><br />
						@else
							<table class="table table-bordered">
								<tr style="font-size:14px;">
									<td class="text-center">BM (jjg)</td>
									<td class="text-center">BK (jjg)</td>
									<td class="text-center">MS (jjg)</td>
									<td class="text-center">OR (jjg)</td>
									<td class="text-center">BB (jjg)</td>
									<td class="text-center">JK (jjg)</td>
									<td class="text-center">BA (jjg)</td>
									<td class="text-center">Total<br />Janjang<br />Panen</td>
								</tr>
								<tr>
									<td>{{ $data['ebcc_jml_bm'] }}</td>
									<td>{{ $data['ebcc_jml_bk'] }}</td>
									<td>{{ $data['ebcc_jml_ms'] }}</td>
									<td>{{ $data['ebcc_jml_or'] }}</td>
									<td>{{ $data['ebcc_jml_bb'] }}</td>
									<td>{{ $data['ebcc_jml_jk'] }}</td>
									<td>{{ $data['ebcc_jml_ba'] }}</td>
									<td>{{ $data['ebcc_jjg_panen'] }}</td>
								</tr>
							</table>
							<div class="row">
								<div class="col-md-8">
									<table cellpadding="2px;" style="font-size: 12px;">
										<tr>
											<td width="40%">NIK</td>
											<td width="5%">:</td>
											<td width="55%">{{ $data['ebcc_nik_kerani_buah'] }}</td>
										</tr>
										<tr>
											<td>Nama Lengkap</td>
											<td>:</td>
											<td>{{ $data['ebcc_nama_kerani_buah'] }}</td>
										</tr>
										<tr>
											<td>Jabatan</td>
											<td>:</td>
											<td></td>
										</tr>
										<tr>
											<td>Waktu Pencatatan</td>
											<td>:</td>
											<td>{{ date( 'Y-m-d', strtotime( $data['val_date_time'] ) ) }}</td>
										</tr>
										<tr>
											<td>Status Scan QR Code</td>
											<td>:</td>
											<td>{{ $data['ebcc_status_tph'].' '.$data['ebcc_keterangan_qrcode'] }}</td>
										</tr>
									</table>
								</div>
								<div class="col-md-4">
									<img src="http://beautycraftkitchens.com/wp-content/uploads/2017/02/dummy_user.png" width="100%;">
								</div>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
	<footer>
		<br />
		<center>
			<button id="download-jpg" class="btn btn-primary"><i class="fa fa-cloud-download"></i> Download as PNG</button>
		</center>
	</footer>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
	<script type="text/javascript">
		function saveAs( uri, filename ) {
			var link = document.createElement( 'a' );
			if ( typeof link.download === 'string' ) {
				link.href = uri;
				link.download = filename;
				document.body.appendChild( link );
				link.click();
				document.body.removeChild( link );
			} 
			else {
				window.open( uri );
			}
		}
		$( document ).ready( function() {
			$( "#download-jpg" ).click( function() {
				html2canvas( document.querySelector("#capture") ).then( canvas => {
					var filename = "{{ $data['val_est_name'].' ('.$data['val_werks'].$data['val_afd_code'].$data['val_block_code'].')-'.$data['val_ebcc_code'].'-'.$data['val_tph_code'].'-'.date( 'Ymd', strtotime( $data['val_date_time'] ) ).'-'.$data['val_nik_validator'].'-'.$data['val_nama_validator'] }}";
					saveAs(canvas.toDataURL(), filename + '.png' );
				} );
				
			} );
		} );
	</script>
</body>
</html>