<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

use App\APISetup;
use App\ReportOracle;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use File;

class ReportOracleController extends Controller
{
	protected $url_api_ins_msa_hectarestatement;
	protected $active_menu;
	
	public function __construct() {
		$this->active_menu = '_' . str_replace('.', '', '02.03.00.00.00') . '_';
		$this->url_api_ins_msa_hectarestatement = APISetup::url()['msa']['ins']['hectarestatement'];
		$this->db_mobile_ins = DB::connection('mobile_ins');
	}

	public function read_nohup() {
		$content = File::get( base_path( 'nohup.out' ) );

		print '<pre>';
		print_r( $content );
		print '</pre>';
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
		$results['summary'] = array();
		$results['periode'] = date( 'Ym', strtotime( $START_DATE ) );

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
			$file_name = 'Report-Sampling-EBCC';
			$results['sheet_name'] = 'Sampling EBCC';
			$results['view'] = 'orareport.excel-ebcc-validation';
		}
		# -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		# REPORT EBCC COMPARE ESTATE/MILL
		# -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		else if ( $REPORT_TYPE == 'EBCC_COMPARE_ESTATE' || $REPORT_TYPE == 'EBCC_COMPARE_MILL' ) {	

			$query_ebcc_compare = $RO->EBCC_COMPARE_OLD(
				$REPORT_TYPE, 
				$START_DATE, 
				$END_DATE, 
				$REGION_CODE, 
				$COMP_CODE, 
				$BA_CODE, 
				$AFD_CODE, 
				$BLOCK_CODE
			);
			$results['data'] = $query_ebcc_compare['data'];
			$results['summary'] = $query_ebcc_compare['summary'];
			$file_name = 'Report-EBCC-Compare';
			$results['sheet_name'] = 'Sampling EBCC vs EBCC';
			$results['view'] = 'orareport.excel-ebcc-compare';
		}
		
		// print '<pre>';
		// print_r( $results['data'] );
		// print '<pre>';
		// dd();

		if( $file_name ) {
			Excel::create( $file_name, function( $excel ) use ( $results ) {
				$excel->sheet( $results['sheet_name'], function( $sheet ) use ( $results ) {
					$sheet->loadView( $results['view'], $results );
				});
			} )->export( 'xls' );
		}
	}

	# View Page Report EBCC Compare
	# Untuk menampilkan view
	public function view_page_report_ebcc_compare( Request $req ) {
		// $RO = new ReportOracle;
		$results = array();
		$results['data'] = ( new ReportOracle() )->EBCC_COMPARE_PREVIEW( $req->id );

		// print '<pre>';
		// print_r( $results );
		// dd();
		if ( !empty( $results['data'] ) ) {
			return view( 'orareport/preview-ebcc-compare', $results );
		}
		else {
			return 'Data not found.';
		}
	}

	public function testing() {

		# Update Data Block 4 -> 004
		$query = $this->db_mobile_ins->select( "SELECT * FROM TR_EBCC_VALIDATION_H WHERE LENGTH( BLOCK_CODE ) = 1" );
		foreach ( $query as $q ) {
			$sql = "UPDATE TR_EBCC_VALIDATION_H SET BLOCK_CODE = '00{$q->block_code}' WHERE EBCC_VALIDATION_CODE = '{$q->ebcc_validation_code}'";
			$this->db_mobile_ins->beginTransaction();
			$this->db_mobile_ins->statement( $sql );
			$this->db_mobile_ins->commit();
		}

		# Update Data Block 23 -> 023
		$query2 = $this->db_mobile_ins->select( "SELECT * FROM TR_EBCC_VALIDATION_H WHERE LENGTH( BLOCK_CODE ) = 2" );
		foreach ( $query2 as $q2 ) {
			$sql = "UPDATE TR_EBCC_VALIDATION_H SET BLOCK_CODE = '0{$q2->block_code}' WHERE EBCC_VALIDATION_CODE = '{$q2->ebcc_validation_code}'";
			$this->db_mobile_ins->beginTransaction();
			$this->db_mobile_ins->statement( $sql );
			$this->db_mobile_ins->commit();
		}

		# Update Data No TPH 4 -> 004
		$query3 = $this->db_mobile_ins->select( "SELECT * FROM TR_EBCC_VALIDATION_H WHERE LENGTH( NO_TPH ) = 1" );
		foreach ( $query3 as $q3 ) {
			$sql = "UPDATE TR_EBCC_VALIDATION_H SET NO_TPH = '00{$q3->no_tph}' WHERE EBCC_VALIDATION_CODE = '{$q3->ebcc_validation_code}'";
			$this->db_mobile_ins->beginTransaction();
			$this->db_mobile_ins->statement( $sql );
			$this->db_mobile_ins->commit();
		}

		# Update Data Block 23 -> 023
		$query4 = $this->db_mobile_ins->select( "SELECT * FROM TR_EBCC_VALIDATION_H WHERE LENGTH( NO_TPH ) = 2" );
		foreach ( $query4 as $q4 ) {
			$sql = "UPDATE TR_EBCC_VALIDATION_H SET NO_TPH = '0{$q4->no_tph}' WHERE EBCC_VALIDATION_CODE = '{$q4->ebcc_validation_code}'";
			$this->db_mobile_ins->beginTransaction();
			$this->db_mobile_ins->statement( $sql );
			$this->db_mobile_ins->commit();
		}




		// print (bool)strtotime( '201901010000' );
		// print 'ABCDZ';
		// dd();
		/*
		$query = $this->db_mobile_ins->select( "
			SELECT
				*
			FROM
				TR_EBCC_VALIDATION_H
		" );

		$i = 0;
		foreach( $query as $q ) {
			$data['EBCC_VALIDATION_CODE'] = $q->ebcc_validation_code;
			$data['WERKS'] = $q->werks;
			$data['AFD_CODE'] = $q->afd_code;
			$data['BLOCK_CODE'] = $q->block_code;
			$data['NO_TPH'] = $q->no_tph;
			$data['STATUS_TPH_SCAN'] = $q->status_tph_scan;
			$data['ALASAN_MANUAL'] = $q->alasan_manual;
			$data['LAT_TPH'] = $q->lat_tph;
			$data['LON_TPH'] = $q->lon_tph;
			$data['DELIVERY_CODE'] = $q->delivery_code;
			$data['STATUS_DELIVERY_CODE'] = $q->status_delivery_code;
			$data['INSERT_USER'] = $q->insert_user;
			$data['INSERT_TIME'] = intval( date( 'YmdHis', strtotime( $q->insert_time ) ) );
			$data['UPDATE_USER'] = $q->update_user;
			$data['UPDATE_TIME'] = 0;
			$data['STATUS_SYNC'] = $q->status_sync;
			$data['SYNC_TIME'] = ( $q->sync_time == NULL ? intval( date( 'YmdHis', strtotime( $q->sync_time ) ) ) : 0 );

			File::append( public_path( '/TR_EBCC_VALIDATION_H.csv' ), implode( ',' , $data ).PHP_EOL );
		}
		*/

		/*
		$query = $this->db_mobile_ins->select( "
			SELECT
				*
			FROM
				TR_EBCC_VALIDATION_D
			WHERE
				INSERT_TIME BETWEEN TO_DATE( '2019-07-01', 'RRRR-MM-DD' ) AND TO_DATE( '2019-10-30', 'RRRR-MM-DD' )
		" );

		$i = 0;
		foreach( $query as $q ) {
			$data['EBCC_VALIDATION_CODE'] = $q->ebcc_validation_code;
			$data['ID_KUALITAS'] = $q->id_kualitas;
			$data['JUMLAH'] = intval( $q->jumlah );
			$data['INSERT_USER'] = $q->insert_user;
			$data['INSERT_TIME'] = intval( date( 'YmdHis', strtotime( $q->insert_time ) ) );
			$data['UPDATE_USER'] = "";
			$data['UPDATE_TIME'] = 0;
			$data['STATUS_SYNC'] = $q->status_sync;
			$data['SYNC_TIME'] = intval( date( 'YmdHis', strtotime( $q->sync_time ) ) );

			File::append( public_path( '/TR_EBCC_VALIDATION_D.csv' ), implode( ',' , $data ).PHP_EOL );
		}
		*/

		// print date( 'YmdHis', strtotime( '20190724240258' ) );

	}
}