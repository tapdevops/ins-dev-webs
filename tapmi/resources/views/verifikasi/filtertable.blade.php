<style type="text/css">
	.btn-white{
		-webkit-box-shadow: 0 5px 10px 2px rgba(152,22,244,.19)!important;
	    -moz-box-shadow: 0 5px 10px 2px rgba(152,22,244,.19)!important;
	    box-shadow: 0 5px 10px 2px rgba(152,22,244,.19)!important;
	}
	.select2-container,.input-group,.m-input{
		margin-bottom: 10px;
	}
	.input-group-text {
    	padding: .65rem .2rem !important;
    }
</style>
<div class="row">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-4">
				<div class="input-daterange input-group">
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
<table>
	<thead>
		<tr>
			<td style="vertical-align: top;min-width:130px;width:10%;padding-right: 10px;">
				<select width="100%" style="width:100%" class="select2" id='ba' name='ba'>
					<option value="ALL" {{$request->ba=='ALL'?'selected':''}}>ALL</option>
					@foreach($ba as $ba)
					<option value="{{$ba}}" {{$request->ba==$ba?'selected':''}}>{{$ba}}</option>
					@endforeach
				</select>
			</td>
			<td style="vertical-align: top;min-width:130px;width:10%;padding-right: 10px;">
				<select width="100%" style="width:100%" class="select2" id='afd'>
					<option value="ALL" {{$request->afd=='ALL'?'selected':''}}>ALL</option>
					@foreach($afd as $afd)
					<option value="{{$afd}}" {{$request->afd==$afd?'selected':''}}>{{$afd}}</option>
					@endforeach
				</select>
			</td>
			<td style="vertical-align: top;min-width:130px;width:12.5%;padding-right: 10px;">
				<select style="width:100%" class="select2" id='pic'>
					<option value="ALL" {{$request->pic=='ALL'?'selected':''}}>ALL</option>
					@foreach($pic as $pic)
					<option value="{{$pic}}" {{$request->pic==$pic?'selected':''}}>{{$pic}}</option>
					@endforeach
				</select>
			</td>
			<td style="vertical-align: top;min-width:130px;width:12.5%;padding-right: 10px;">
				<select style="width:100%" class="select2" id='role'>
					<option value="ALL" {{$request->role=='ALL'?'selected':''}}>ALL</option>
					@foreach($role as $role)
					<option value="{{$role}}" {{$request->role==$role?'selected':''}}>{{$role}}</option>
					@endforeach
				</select>
			</td>
			<td style="vertical-align: top;min-width:130px;width:12.5%;padding-right: 10px;">
				<div class="input-group">
					<input id="jjg_aslap_min" name="jjg_aslap_min" value="{{$request->jjg_aslap_min}}" type="number" class="form-control" style="padding: 2px !important;">
					<div class="input-group-append">
						<span class="input-group-text">
							-
						</span>
					</div>
					<input id="jjg_aslap_max" name="jjg_aslap_max" value="{{$request->jjg_aslap_max}}" type="number" class="form-control" style="padding: 2px !important;">
				</div>
			</td>
			<td style="vertical-align: top;min-width:130px;width:12.5%;padding-right: 10px;">
				<div class="input-group">
					<input id="jjg_ai_min" name="jjg_ai_min" value="{{$request->jjg_ai_min}}" type="number" class="form-control" style="padding: 2px !important;">
					<div class="input-group-append">
						<span class="input-group-text">
							-
						</span>
					</div>
					<input id="jjg_ai_max" name="jjg_ai_max" value="{{$request->jjg_ai_max}}" type="number" class="form-control" style="padding: 2px !important;">
				</div>
			</td>
			<td style="vertical-align: top;min-width:130px;width:12.5%;padding-right: 10px;"><input id="jjg_selisih" name="jjg_selisih" value="{{$request->jjg_selisih}}" type="number" class="form-control m-input"></td>
			<td style="vertical-align: top;min-width:130px;width:12.5%;"><button type="button" name="btsearch" class="btn btn-primary btsearch tampilkan">Filter</button></td>
		</tr>
	</thead>
</table>
<table class="m-datatable" id="html_table" width="100%">
	<thead>
		<tr>
			<th style="vertical-align: top;"></select>Bisnis Area</th>
			<th style="vertical-align: top;">Afdeling</th>
			<th style="vertical-align: top;"></select>PIC Sampling</th>
			<th style="vertical-align: top;">Role</th>
			<th style="vertical-align: top;"></div><div style="font-size: 11px;">Janjang Panen Versi Validasi Sampling</div></th>
			<th style="vertical-align: top;"><div style="font-size: 11px;">Janjang Panen Versi Sistem - Validasi Otomatis</div></th>
			<th style="vertical-align: top;">Selisih Janjang</th>
			<th style="">Keterangan</th>
		</tr>
	</thead>
	<tbody>
		@foreach($data_header as $data)
		@if($data->COUNT_DIFF>$request->jjg_selisih || $data->COUNT_DIFF<(0-$request->jjg_selisih))
			@if($request->ba=='ALL' || $request->ba==(ISSET($data->WERKS)?$data->WERKS:''))
				@if($request->afd=='ALL' || $request->afd==(ISSET($data->AFD_CODE)?$data->AFD_CODE:''))
					@if($request->pic=='ALL' || $request->pic==$data->INSERT_USER)
						@if($request->role=='ALL' || $request->role==$data->ROLE)
							@if($request->jjg_aslap_min<=(ISSET($data->COUNT_VALIDATION)?$data->COUNT_VALIDATION:0) && $request->jjg_aslap_max>=(ISSET($data->COUNT_VALIDATION)?$data->COUNT_VALIDATION:0))
								@if($request->jjg_ai_min<=$data->COUNT_AI && $request->jjg_ai_max>=$data->COUNT_AI)
									<tr>
										<td>{{ISSET($data->WERKS)?$data->WERKS:''}}</td>
										<td>{{ISSET($data->AFD_CODE)?$data->AFD_CODE:''}}</td>
										<td>{{$data->INSERT_USER}}</td>
										<td>{{$data->ROLE}}</td>
										<td style="text-align: center !important;">{{ISSET($data->COUNT_VALIDATION)?$data->COUNT_VALIDATION:''}}</td>
										<td style="text-align: center !important;">{{$data->COUNT_AI}}</td>
										<td style="text-align: center !important;">{{$data->COUNT_DIFF}}</td>
										<td><span class="text-{{$data->KETERANGAN=='BELUM DIVERIFIKASI'?'danger':'success'}}"><b>{{$data->KETERANGAN}}</b></span></td>
									</tr>
								@endif
							@endif
						@endif
					@endif
				@endif
			@endif
		@endif
		@endforeach
	</tbody>
</table>
<script type="text/javascript">
	$('.select2').select2();
	$("#generalSearch").datepicker({
		todayHighlight: !0,
		templates: {
			leftArrow: '<i class="la la-angle-left"></i>',
			rightArrow: '<i class="la la-angle-right"></i>'
		},
		// startDate: "<?php echo $last_work_daily ?>d",
		endDate: "1",
		format: 'dd-M-yyyy',
        orientation: 'bottom'
	});
	$(".tampilkan").click(function(){
		var search = document.getElementById('generalSearch').value;
		refreshData();
	});


	function refreshData(){
		var search = document.getElementById('generalSearch').value;
		var ba = document.getElementById('ba').value;
		var afd = document.getElementById('afd').value;
		var pic = document.getElementById('pic').value;
		var role = document.getElementById('role').value;
		var jjg_aslap_min = document.getElementById('jjg_aslap_min').value;
		var jjg_aslap_max = document.getElementById('jjg_aslap_max').value;
		var jjg_ai_min = document.getElementById('jjg_ai_min').value;
		var jjg_ai_max = document.getElementById('jjg_ai_max').value;
		var jjg_selisih = document.getElementById('jjg_selisih').value;
		const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
		$('.m-datatable').waitMe( {
					effect: 'win8',
					text: 'Update data...',
					bg: '#ffffff',
					color: '#000'
				} );
		$.ajax({
			url:'/getNewdataverification2',
			type:'get',
			data:{
				CSRF_TOKEN,
				'tanggal' : search,
				'ba' : ba,
				'afd' : afd,
				'pic' : pic,
				'role' : role,
				'jjg_aslap_min' : jjg_aslap_min,
				'jjg_aslap_max' : jjg_aslap_max,
				'jjg_ai_min' : jjg_ai_min,
				'jjg_ai_max' : jjg_ai_max,
				'jjg_selisih' : jjg_selisih
			},
			success:function(data){
				$("#table").html(data);
				$(".m-datatable").mDatatable({});
			}
		})
	}
</script>