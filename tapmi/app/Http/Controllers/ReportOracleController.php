<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

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
	
	public function download() {
		$url_region_data = $this->url_api_ins_msa_hectarestatement . '/region/all';
		$data['region_data'] = APISetup::ins_rest_client('GET', $url_region_data);
		$data['active_menu'] = $this->active_menu;
		return view('orareport.download', $data);
	}
	
	public function download_proses( Request $request ) {
		$RO = new ReportOracle;
		$REPORT_TYPE = $request->REPORT_TYPE != '' ? $request->REPORT_TYPE :  null;
		$START_DATE = $request->START_DATE != '' ? $request->START_DATE : null;
		$END_DATE = $request->END_DATE != '' ? $request->END_DATE : null;
		$REGION_CODE = $request->REGION_CODE != '' ? $request->REGION_CODE : null;
		$COMP_CODE = $request->COMP_CODE != '' ? $request->COMP_CODE : null;
		$BA_CODE = $request->BA_CODE != '' ? $request->BA_CODE : null;
		$AFD_CODE = $request->AFD_CODE != '' ? $request->AFD_CODE : null;
		$BLOCK_CODE = $request->BLOCK_CODE != '' ? $request->BLOCK_CODE : null;
		$file_name = null;

		// Set Empty Array (Biar gak error)
		$results['head'] = array();
		$results['data'] = array();

		# -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		# REPORT EBCC VALIDATION ESTATE/MILL
		# -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		if ( $REPORT_TYPE == 'EBCC_VALIDATION_ESTATE' || $REPORT_TYPE == 'EBCC_VALIDATION_MILL' ) {
			$results['head'] = $RO->EBCC_VALIDATION_ESTATE_HEAD();
			$results['data'] = $RO->EBCC_VALIDATION(
									$REPORT_TYPE, 
									$START_DATE, 
									$END_DATE, 
									$REGION_CODE, 
									$COMP_CODE, 
									$BA_CODE, 
									$AFD_CODE, 
									$BLOCK_CODE
								);
			$file_name 		 = 'Report-Sampling-EBCC';
			$results['view'] = 'orareport.excel-ebcc-validation';
		}
		# -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		# REPORT EBCC COMPARE ESTATE/MILL
		# -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		else if ( $REPORT_TYPE == 'EBCC_COMPARE_ESTATE' || $REPORT_TYPE == 'EBCC_COMPARE_MILL' ) {	

			$results['data'] = $RO->EBCC_COMPARE(
				$REPORT_TYPE, 
				$START_DATE, 
				$END_DATE, 
				$REGION_CODE, 
				$COMP_CODE, 
				$BA_CODE, 
				$AFD_CODE, 
				$BLOCK_CODE
			);
			$file_name 		 = 'Report-EBCC-Compare';
			$results['view'] = 'orareport.excel-ebcc-compare';
		}
		
		if($file_name){
			Excel::create($file_name, function ($excel) use ($results) {
				$excel->sheet('Sampling EBCC', function ($sheet) use ($results) {
					$sheet->loadView($results['view'], $results);
				});
			})->export('xls');
		}
	}

	public function view_page_report_ebcc_compare( Request $req ) {
		return view( 'orareport/preview-ebcc-compare' );
	}
}
