<style type="text/css">
	.btn-white{
		-webkit-box-shadow: 0 5px 10px 2px rgba(152,22,244,.19)!important;
	    -moz-box-shadow: 0 5px 10px 2px rgba(152,22,244,.19)!important;
	    box-shadow: 0 5px 10px 2px rgba(152,22,244,.19)!important;
	}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-4">
				<div class="input-daterange input-group">
					<input type="hidden" name="werks" id="werks">
					<input type="hidden" name="afd" id="afd">
					<label style="padding-top: 6px" for="tanggal_rencana">Tanggal &nbsp; &nbsp; </label>
					<?php $tgl = date("d-M-Y", strtotime($tgl_validasi));
					?>
					<input type="text" class="form-control m-input" id="generalSearch" name="tanggal_rencana" value="{{$tgl}}" autocomplete="off" readonly="readonly" />
					
					<div class="input-group-append">
						<span class="input-group-text">
							<i class="la la-calendar"></i>
						</span>
					</div>&nbsp;&nbsp;&nbsp;
					<button type="button" name="btsearch" class="btn btn-primary btn-sm btsearch tampilkan">Tampilkan</button>
				</div>

			</div>
			<div class="col-md-4"></div>
			@if(count($data_header)>0)
			<div class="col-md-4 m--align-right" style="white-space:nowrap;margin-bottom:20px;">
				<a href="{{ URL::to('/verifikasi/export/'.$tgl_validasi) }}" class="btn btn-white m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
					<span>
						<i class="fa fa-clipboard"></i>
						<span>Export XLS</span>
					</span>
				</a>
				<a href="{{ URL::to('/verifikasi/create/'.$tgl_validasi) }}" class="btn btn-focus m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
					<span>
						<i class="fa fa-clipboard"></i>
						<span>Verifikasi</span>
					</span>
				</a>
				<div class="m-separator m-separator--dashed d-xl-none"></div>
			</div>
			@else
			<div class="col-md-4 m--align-right" style="white-space:nowrap;margin-bottom:20px;">
				<a href="#" class="btn btn-white m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill disabled">
					<span>
						<i class="fa fa-clipboard"></i>
						<span>Export XLS</span>
					</span>
				</a>
				<a href="#" class="btn btn-focus m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill disabled">
					<span>
						<i class="fa fa-clipboard"></i>
						<span>Verifikasi</span>
					</span>
				</a>
				<div class="m-separator m-separator--dashed d-xl-none"></div>
			</div>
			@endif
		</div>
	</div>
	
</div>
<table class="m-datatable" id="html_table" width="100%">
	<thead>
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
	</thead>
	<tbody>
		@foreach($data_header as $data)
		@if($data->COUNT_DIFF>6 || $data->COUNT_DIFF<-6)
		<tr>
			<td>{{$data->WERKS}}</td>
			<td>{{$data->AFD_CODE}}</td>
			<td>{{$data->INSERT_USER}}</td>
			<td>{{$data->ROLE}}</td>
			<td>{{$data->COUNT_VALIDATION}}</td>
			<td>{{$data->COUNT_AI}}</td>
			<td>{{$data->COUNT_DIFF}}</td>
			<td><span class="text-{{$data->KETERANGAN=='BELUM DIVERIFIKASI'?'danger':'success'}}"><b>{{$data->KETERANGAN}}</b></span></td>
		</tr>
		@endif
		@endforeach
	</tbody>
</table>
<script type="text/javascript">
	
	$("#generalSearch").datepicker({
		todayHighlight: !0,
		templates: {
			leftArrow: '<i class="la la-angle-left"></i>',
			rightArrow: '<i class="la la-angle-right"></i>'
		},
		// startDate: "<?php echo $last_work_daily ?>d",
		endDate: "-1d",
		format: 'dd-M-yyyy',
        orientation: 'bottom'
	});
	$(".tampilkan").click(function(){
		var search = document.getElementById('generalSearch').value;
		refreshData();
	});

	function refreshData(){
		var search = document.getElementById('generalSearch').value;
		const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
		$.ajax({
			url:'/getNewdataverification2',
			type:'get',
			data:{
				CSRF_TOKEN,
				'tanggal' : search
			},
			success:function(data){
				$("#table").html(data);
				$(".m-datatable").mDatatable({});
			}
		})
	}
</script>