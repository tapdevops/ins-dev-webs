<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\APISetup;
use App\ReportOracle;
use DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportOracleController extends Controller
{
	protected $url_api_ins_msa_hectarestatement;
	protected $active_menu;
	
    public function __construct() {
		$this->env = 'dev';
		$this->active_menu = '_' . str_replace('.', '', '02.03.00.00.00') . '_';
		$this->url_api_ins_msa_hectarestatement = APISetup::url()['msa']['ins']['hectarestatement'];
		$this->db_mobile_ins = ($this->env == 'production' ? DB::connection('mobile_ins') : DB::connection('mobile_ins_dev'));
	}
	
	public function download()
	{
		$url_region_data = $this->url_api_ins_msa_hectarestatement . '/region/all';
		$data['region_data'] = APISetup::ins_rest_client('GET', $url_region_data);
		$data['active_menu'] = $this->active_menu;
		return view('orareport.download', $data);
	}
	
	public function download_proses(Request $request)
	{
		$RO = new ReportOracle;
		$type = $request->REPORT_TYPE;
		
		// dd($type);
		if( $type == 'EBCC_VALIDATION_ESTATE' ){
			$data = $RO->EBCC_VALIDATION_ESTATE();
		}
		
		$results['data'] = $data;
		
		Excel::create('Report-Sampling-EBCC', function ($excel) use ($results) {
			$excel->sheet('Sampling EBCC', function ($sheet) use ($results) {
				$sheet->loadView('orareport.excel-ebcc-validation', $results);
			});
		})->export('xls');
	}
}
