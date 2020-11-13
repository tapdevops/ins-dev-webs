@extends( 'layouts.default.page-normal-main' )
@section( 'title', 'Validasi BCC oleh Kepala Kebun' )
@section('style')
<style>
input[type="radio"]{
    visibility:hidden;
	display: none;
}
.w-50{
	width: 50%;
}
.fields{
	border-color: #000000;
}
.fields:focus{
	border-color: #000000;
}
.btn-radio {
  	white-space: normal !important;
    height: 54px;
    width: 165px;
    border-radius: 40px;
    background-color: white;
    font-size: 12px;
    font-weight: 500;
    padding-top: 5px;
}
.btn-next {
	white-space: normal !important;
}
.btnselect {
  border-color: #000000;
  color: black;
}

.btnselect:focus {
  background-color:#4d9925;
  color: white;
}
.btnselect:focus:active{
  background-color:#4d9925;
  color: white;
}

.borderless td, .borderless th {
    border: none;
}
tr
{
    line-height:30px;
}

#container {
  width: 620px;
  height: 500px;
  overflow: hidden;
}
#container.rotate90,
#container.rotate270 {
  width: 500px;
  height: 620px
}
#image {
  transform-origin: top left;
  /* IE 10+, Firefox, etc. */
  -webkit-transform-origin: top left;
  /* Chrome */
  -ms-transform-origin: top left;
  /* IE 9 */
}
#container.rotate90 #image {
  transform: rotate(90deg) translateY(-100%);
  -webkit-transform: rotate(90deg) translateY(-100%);
  -ms-transform: rotate(90deg) translateY(-100%);
}
#container.rotate180 #image {
  transform: rotate(180deg) translate(-100%, -100%);
  -webkit-transform: rotate(180deg) translate(-100%, -100%);
  -ms-transform: rotate(180deg) translateX(-100%, -100%);
}
#container.rotate270 #image {
  transform: rotate(270deg) translateX(-100%);
  -webkit-transform: rotate(270deg) translateX(-100%);
  -ms-transform: rotate(270deg) translateX(-100%);
}
</style>
@endsection
@foreach ( $data as $key )
	
	@if($loop->first)
		@section( 'content' )
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-12">
						<h3 class="text-danger text-center">
							@if(isset($_GET['error']))
								@if($_GET['error']==1)
									Terjadi kesalahan, API Bunch Counting Error.
								@endif
								@if($_GET['error']==2)
									Terjadi kesalahan pada data Bunch Counting.
								@endif
							@endif
						</h3><br>
					</div>
				</div>
			</div>
		</div>
		<table class="borderles">
		<form action="{{ route( 'create_verification' ) }}" method="post">
		{{ csrf_field() }}
		<input type="hidden" name="TANGGAL_TRANSAKSI" value="{{$tgl_validasi}}">
		<input type="hidden" name="NO_BCC" value="{{$key->EBCC_CODE}}">
			<tr>
				<td  rowspan="7" width="45%"  style="vertical-align: top;">
					<div style="position:absolute;z-index: 1000">
					<?php	
						$img = $key->IMAGE_URL;
						if(isset($_GET['image']))
						{
							dd($img);
						}
					?>
						<div style="position:absolute;z-index: 1000">
						<input id="button" type="image" src="http://inspectiondev.tap-agri.com/storage/rotate_45.png" >
						</div>
						<!-- <img onerror="this.onerror=null;this.src='https://webhostingmedia.net/wp-content/uploads/2018/01/http-error-404-not-found.png'"  src="http://10.20.1.59/ebcc/array/uploads/{{$img}}" style="display:block;" width="80%" height="80%" > -->
						<div id="container"  style="background-position: center center; background-repeat: no-repeat;overflow: hidden;">
						<img onerror="this.onerror=null;this.src='http://inspectiondev.tap-agri.com/storage/notfound.jpg'"  src="{{$img}}" style="display:block;" width="80%" height="80%" id="image" >
						</div> 
						
					</div>
				</td>
				<td>
					<table width="100%">
						<tr>
							<td style="text-align: center;background: #f4f3f8;font-weight: 600;border: 1px solid;padding: 5px;height: 45px;line-height: 18px;">Janjang Panen<br>versi PIC Sampling</td>
							<td style="text-align: center;background: #f4f3f8;font-weight: 600;border: 1px solid;padding: 5px;line-height: 18px;">Janjang Panen<br>versi Sistem - Validasi Otomatis</td>
							<td style="text-align: center;background: #f4f3f8;font-weight: 600;border: 1px solid;padding: 5px;width: 33%;line-height: 18px;">Selisih Janjang<br>Panen</td>
						</tr>
						<tr>
							<td style="text-align: center;font-weight: 600;border: 1px solid;font-size: 25px;padding: 10px;">{{$key->COUNT_VALIDATION}}</td>
							<td style="text-align: center;font-weight: 600;border: 1px solid;font-size: 25px;padding: 10px;">{{$key->COUNT_AI}}</td>
							<td style="text-align: center;font-weight: 600;border: 1px solid;font-size: 25px;padding: 10px;">{{$key->COUNT_DIFF}}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
			<!-- Kriteria Buah Berdasarkan Foto eBCC -->
				<td colspan="8"></td>
			</tr>
			<tr>
				<td>
					<table class="borderless" width = "100%" style="margin-top: 12px;">
						<tbody>
							<tr><td style="font-weight: 600;width: 100px">PT</td><td style="font-weight: 600;width: 10px">: </td><td colspan="2"><b>{{$pt}}</b></tr>
							<tr><td style="font-weight: 600;">PIC Sampling </td><td style="font-weight: 600;"">: </td><td colspan="2"><b>{{$key->INSERT_USER}}</b></tr>
						</tbody>
					</table>
				</tr>
			<tr>
				<td style="padding-top: 15px;"><h3>Bisa Dihitung : </h3></td>
			</tr>
			<tr>
				<td>
					<div data-toggle="buttons">
						<div class="row">
							<div class="col-md-3">
								<button class="btn btn-radio btnselect">
									<input  type="radio"  name="KONDISI_FOTO" value="Foto bagus & Inputan PIC Sesuai">Foto bagus & Inputan PIC Sesuai</button>
							</div>
							<div class="col-md-3">
								<button class="btn btn-radio btnselect">
									<input  type="radio"  name="KONDISI_FOTO" value="Foto bagus & tapi Inputan PIC Tidak Sesuai">Foto bagus & tapi Inputan PIC Tidak Sesuai</button>
							</div>
							<div class="col-md-3">
								<button class="btn btn-radio btnselect">
									<input  type="radio"  name="KONDISI_FOTO" value="Foto Bagus tapi Jumlah Janjang lebih dari 30">Foto Bagus tapi Jumlah Janjang lebih dari 30</button>
							</div>
							<div class="col-md-3">
								<button class="btn btn-radio btnselect">
									<input  type="radio"  name="KONDISI_FOTO" value="Gambar Terpotong">Gambar Terpotong</button>

							</div>
							<div class="col-md-12">
								<h3><br>Tidak Bisa Dihitung Karena : </h3>
							</div>
							<div class="col-md-3">
								<button class="btn btn-radio btnselect">
									<input  type="radio"  name="KONDISI_FOTO" value="Foto Tidak Muncul">Foto Tidak Muncul</button>
							</div>
							<div class="col-md-3">
								<button class="btn btn-radio btnselect">
									<input  type="radio"  name="KONDISI_FOTO" value="Blur">Blur</button>
							</div>
							<div class="col-md-3">
								<button class="btn btn-radio btnselect">
									<input  type="radio"  name="KONDISI_FOTO" value="Jauh">Jauh</button>
							</div>
							<div class="col-md-3">
								<button class="btn btn-radio btnselect">
									<input  type="radio"  name="KONDISI_FOTO" value="Gambar Janjang Terpotong">Gambar Janjang Terpotong</button>
							</div><br><br>
							<div class="col-md-3">
								<button class="btn btn-radio btnselect">
									<input  type="radio"  name="KONDISI_FOTO" value="Gelap atau Tertutup Bayangan">Gelap atau Tertutup Bayangan</button>
							</div>
							<div class="col-md-3">
								<button class="btn btn-radio btnselect">
									<input  type="radio"  name="KONDISI_FOTO" value="Penyusunan atau Angle Pengambilan Tidak Sesuai SOP">Penyusunan atau Angle Pengambilan Tidak Sesuai SOP</button>
							</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="row">
					<div class="col-md-12" style="text-align: right;">
							<input style="border-radius: 20px;" type="submit" class="btn btn-success" name="submit" value="SIMPAN & LANJUTKAN">
					</div>
				</div>
				</td>
			</tr>
			</form>
		<table>
		@endsection
	@endif

@endforeach
@section( 'scripts' )
<script type="text/javascript">
	
	$(document).ready(function() {
		MobileInspection.set_active_menu( '{{ $active_menu }}' );
	});
	$(document).on("submit", "form", function(e){
	   	if($('input[name=KONDISI_FOTO]:checked').val())
	   	{
	   		console.log('true');
	   	}
	   	else
	   	{
		    e.preventDefault();
	   	}
	});

	var angle = 0,
  	img = document.getElementById('container');
		document.getElementById('button').onclick = function() {
		angle = (angle + 90) % 360;
		img.className = "rotate" + angle;
	}

</script>

@endsection