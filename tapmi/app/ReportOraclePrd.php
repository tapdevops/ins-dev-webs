<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ReportOracle extends Model
{
	
	protected $env;
	protected $db_mobile_ins;
	
	public function __construct() {
		$this->db_mobile_ins = DB::connection( 'mobile_ins' );
	}
	
	public function EBCC_VALIDATION_ESTATE_HEAD()
	{
		$get = $this->db_mobile_ins->select("
				SELECT
					KUALITAS.ID_KUALITAS,
					KUALITAS.NAMA_KUALITAS
				FROM
					TAP_DW.T_KUALITAS_PANEN@DWH_LINK KUALITAS
				WHERE
					KUALITAS.ACTIVE_STATUS = 'YES' and KUALITAS.ID_KUALITAS not in ( '14','11','12','13' )
				ORDER BY 
					KUALITAS.GROUP_KUALITAS ASC,
					KUALITAS.UOM ASC,
					KUALITAS.NAMA_KUALITAS ASC
		");
		return $get;
	}
	
	public function EBCC_VALIDATION( $REPORT_TYPE , $START_DATE , $END_DATE , $REGION_CODE , $COMP_CODE , $BA_CODE , $AFD_CODE , $BLOCK_CODE ) {

		if ( $REPORT_TYPE ) {
			if ( $REPORT_TYPE == 'EBCC_VALIDATION_ESTATE' ) {
				$REPORT_TYPE = 'V';
			}
			else {
				$REPORT_TYPE = 'M';
			}
		}	

		$where = "";
		$where .= ( $REGION_CODE != "" && $COMP_CODE == "" ) ? " and EST.REGION_CODE = '$REGION_CODE'  ": "";
		$where .= ( $COMP_CODE != "" && $BA_CODE == "" ) ? " and EST.COMP_CODE = '$COMP_CODE'  ": "";
		$where .= ( $BA_CODE != "" && $AFD_CODE == "" ) ? " and EBCC_HEADER.WERKS = '$BA_CODE'  ": "";
		$where .= ( $AFD_CODE != "" && $BLOCK_CODE == "" ) ? " and EBCC_HEADER.WERKS||EBCC_HEADER.AFD_CODE = '$AFD_CODE'  ": "";
		$where .= ( $AFD_CODE != "" && $BLOCK_CODE != "" ) ? " and EBCC_HEADER.WERKS||EBCC_HEADER.AFD_CODE||EBCC_HEADER.BLOCK_CODE = '$BLOCK_CODE'  ": "";
		
		$START_DATE = date( 'Y-m-d', strtotime( $START_DATE ) );
		$END_DATE = date( 'Y-m-d', strtotime( $END_DATE ) );

		# Note:
		# Untuk mengambil SPMON dari TAP_DW.TR_HS_LAND_USE, berdasarkan End Date minus 1 (satu) bulan, 
		# kecuali jika bulan Januari, maka akan diambil bulan Januarinya, bukan Desember.
		$sql = "
			SELECT
				HEADER.*,
				NVL( DETAIL.JML_1, 0 ) AS VAL_JML_1,
				NVL( DETAIL.JML_2, 0 ) AS VAL_JML_2,
				NVL( DETAIL.JML_3, 0 ) AS VAL_JML_3,
				NVL( DETAIL.JML_4, 0 ) AS VAL_JML_4,
				NVL( DETAIL.JML_5, 0 ) AS VAL_JML_5,
				NVL( DETAIL.JML_6, 0 ) AS VAL_JML_6,
				NVL( DETAIL.JML_7, 0 ) AS VAL_JML_7,
				NVL( DETAIL.JML_8, 0 ) AS VAL_JML_8,
				NVL( DETAIL.JML_9, 0 ) AS VAL_JML_9,
				NVL( DETAIL.JML_10, 0 ) AS VAL_JML_10,
				NVL( DETAIL.JML_11, 0 ) AS VAL_JML_11,
				NVL( DETAIL.JML_12, 0 ) AS VAL_JML_12,
				NVL( DETAIL.JML_13, 0 ) AS VAL_JML_13,
				NVL( DETAIL.JML_14, 0 ) AS VAL_JML_14,
				NVL( DETAIL.JML_15, 0 ) AS VAL_JML_15,
				NVL( DETAIL.JML_16, 0 ) AS VAL_JML_16,
				(
					NVL( DETAIL.JML_1, 0 ) + 
					NVL( DETAIL.JML_2, 0 ) + 
					NVL( DETAIL.JML_3, 0 ) + 
					NVL( DETAIL.JML_4, 0 ) + 
					NVL( DETAIL.JML_6, 0 ) +
					NVL( DETAIL.JML_15, 0 ) +
					NVL( DETAIL.JML_16, 0 )
				) AS VAL_TOTAL_JJG
			FROM
				(
					SELECT 
						EBCC_VAL.VAL_EBCC_CODE,
						EBCC_VAL.VAL_WERKS,
						EBCC_VAL.VAL_EST_NAME,
						EBCC_VAL.VAL_NIK_VALIDATOR,
						EBCC_VAL.VAL_NAMA_VALIDATOR,
						EBCC_VAL.VAL_JABATAN_VALIDATOR,
						EBCC_VAL.VAL_STATUS_TPH_SCAN,
						EBCC_VAL.VAL_LAT_TPH,
						EBCC_VAL.VAL_LON_TPH,
						EBCC_VAL.VAL_MATURITY_STATUS,
						CASE
							WHEN EBCC_VAL.VAL_ALASAN_MANUAL IS NULL THEN ''
							ELSE
								CASE
									WHEN EBCC_VAL.VAL_ALASAN_MANUAL = '1' THEN 'QR Codenya Hilang'
									WHEN EBCC_VAL.VAL_ALASAN_MANUAL = '2' THEN 'QR Codenya Rusak'
							END
						END AS VAL_ALASAN_MANUAL,
						EBCC_VAL.VAL_AFD_CODE,
						EBCC_VAL.VAL_BLOCK_CODE,
						EBCC_VAL.VAL_DATE_TIME AS VAL_DATE_TIME,
						EBCC_VAL.VAL_BLOCK_NAME,
						EBCC_VAL.VAL_TPH_CODE,
						EBCC_VAL.VAL_DELIVERY_TICKET
					FROM
						(
							SELECT
								EBCC_HEADER.EBCC_VALIDATION_CODE AS VAL_EBCC_CODE,
								EST.WERKS AS VAL_WERKS,
								EST.EST_NAME AS VAL_EST_NAME,
								EBCC_HEADER.AFD_CODE AS VAL_AFD_CODE,
								CAST( EBCC_HEADER.SYNC_TIME AS DATE ) AS VAL_DATE_TIME,
								EBCC_HEADER.BLOCK_CODE AS VAL_BLOCK_CODE,
								EBCC_HEADER.STATUS_TPH_SCAN AS VAL_STATUS_TPH_SCAN,
								EBCC_HEADER.ALASAN_MANUAL AS VAL_ALASAN_MANUAL,
								EBCC_HEADER.NO_TPH AS VAL_TPH_CODE,
								EBCC_HEADER.DELIVERY_CODE AS VAL_DELIVERY_TICKET,
								USER_AUTH.EMPLOYEE_NIK AS VAL_NIK_VALIDATOR,
								EMP.EMPLOYEE_FULLNAME AS VAL_NAMA_VALIDATOR,
								EMP.EMPLOYEE_POSITION AS VAL_JABATAN_VALIDATOR,
								LAND_USE.MATURITY_STATUS AS VAL_MATURITY_STATUS,
								LAND_USE.SPMON AS VAL_SPMON,
								SUBBLOCK.BLOCK_NAME AS VAL_BLOCK_NAME,
								EBCC_HEADER.LAT_TPH AS VAL_LAT_TPH,
								EBCC_HEADER.LAT_TPH AS VAL_LON_TPH
							FROM
								MOBILE_INSPECTION.TR_EBCC_VALIDATION_H EBCC_HEADER
								LEFT JOIN MOBILE_INSPECTION.TM_USER_AUTH USER_AUTH ON 
									USER_AUTH.USER_AUTH_CODE = (
										CASE
											WHEN LENGTH( EBCC_HEADER.INSERT_USER ) = 3 THEN '0' || EBCC_HEADER.INSERT_USER
											ELSE EBCC_HEADER.INSERT_USER
										END
									)
								LEFT JOIN (
									SELECT 
										EMPLOYEE_NIK,
										EMPLOYEE_FULLNAME,
										EMPLOYEE_POSITION,
										EMPLOYEE_JOINDATE as START_DATE,
										CASE 
											WHEN EMPLOYEE_RESIGNDATE IS NULL
											THEN TO_DATE( '99991231', 'RRRRMMDD' )
											ELSE EMPLOYEE_RESIGNDATE
										END as END_DATE
									FROM 
										TAP_DW.TM_EMPLOYEE_HRIS@DWH_LINK 
									UNION ALL
									SELECT 
										NIK, 
										EMPLOYEE_NAME,
										JOB_CODE,
										START_VALID,
										CASE
											WHEN RES_DATE IS NOT NULL 
											THEN RES_DATE
											ELSE END_VALID
										END END_VALID
									FROM 
										TAP_DW.TM_EMPLOYEE_SAP@DWH_LINK
								) EMP ON
									EMP.EMPLOYEE_NIK = USER_AUTH.EMPLOYEE_NIK
									AND TRUNC( EBCC_HEADER.INSERT_TIME ) BETWEEN TRUNC( EMP.START_DATE ) AND TRUNC( EMP.END_DATE )
								LEFT JOIN TAP_DW.TM_EST@DWH_LINK EST ON 
									EST.WERKS = EBCC_HEADER.WERKS 
									AND SYSDATE BETWEEN EST.START_VALID AND EST.END_VALID
								LEFT JOIN (
									SELECT
										WERKS,
										AFD_CODE,
										BLOCK_CODE,
										BLOCK_NAME,
										MATURITY_STATUS,
										SPMON
									FROM
										TAP_DW.TR_HS_LAND_USE@DWH_LINK
									WHERE
										1 = 1
										AND ROWNUM < 2
										AND MATURITY_STATUS IS NOT NULL
										AND SPMON
											BETWEEN 
												(
													SELECT
														CASE
															WHEN TO_CHAR( TO_DATE( '$END_DATE', 'RRRR-MM-DD' ), 'MM' ) = '01'
															THEN TRUNC( TO_DATE( '$END_DATE', 'RRRR-MM-DD' ), 'MM' )
															ELSE TRUNC( ADD_MONTHS( TO_DATE( '$END_DATE', 'RRRR-MM-DD' ), -1 ), 'MM' )
														END
													FROM
														DUAL
												)
											AND
												(
													(
														SELECT
															CASE
																WHEN TO_CHAR( TO_DATE( '$END_DATE', 'RRRR-MM-DD' ), 'MM' ) = '01'
																THEN LAST_DAY( TO_DATE( '$END_DATE', 'RRRR-MM-DD' ) )
																ELSE LAST_DAY( ADD_MONTHS( TO_DATE( '$END_DATE', 'RRRR-MM-DD' ), -1 ) )
															END
														FROM
															DUAL
													)
												)
								) LAND_USE
									ON LAND_USE.WERKS = EBCC_HEADER.WERKS
									AND LAND_USE.AFD_CODE = EBCC_HEADER.AFD_CODE
									AND LAND_USE.BLOCK_CODE = EBCC_HEADER.BLOCK_CODE
								LEFT JOIN TAP_DW.TM_SUB_BLOCK@DWH_LINK SUBBLOCK
									ON SUBBLOCK.WERKS = EBCC_HEADER.WERKS
									AND SUBBLOCK.SUB_BLOCK_CODE = EBCC_HEADER.BLOCK_CODE
							WHERE
								1 = 1
								AND SUBSTR( EBCC_HEADER.EBCC_VALIDATION_CODE, 0, 1 ) = '$REPORT_TYPE'
								AND TRUNC( EBCC_HEADER.SYNC_TIME ) BETWEEN TRUNC( TO_DATE( '$START_DATE', 'RRRR-MM-DD' ) ) AND TRUNC( TO_DATE( '$END_DATE', 'RRRR-MM-DD' ) )
								$where
						) EBCC_VAL
					GROUP BY
						EBCC_VAL.VAL_EBCC_CODE,
						EBCC_VAL.VAL_WERKS,
						EBCC_VAL.VAL_EST_NAME,
						EBCC_VAL.VAL_NIK_VALIDATOR,
						EBCC_VAL.VAL_NAMA_VALIDATOR,
						EBCC_VAL.VAL_JABATAN_VALIDATOR,
						EBCC_VAL.VAL_STATUS_TPH_SCAN,
						EBCC_VAL.VAL_ALASAN_MANUAL,
						EBCC_VAL.VAL_DATE_TIME,
						EBCC_VAL.VAL_AFD_CODE,
						EBCC_VAL.VAL_BLOCK_CODE,
						EBCC_VAL.VAL_BLOCK_NAME,
						EBCC_VAL.VAL_TPH_CODE,
						EBCC_VAL.VAL_DELIVERY_TICKET,
						EBCC_VAL.VAL_LAT_TPH,
						EBCC_VAL.VAL_LON_TPH,
						EBCC_VAL.VAL_MATURITY_STATUS,
						EBCC_VAL.VAL_SPMON
				) HEADER
				LEFT JOIN (
					SELECT 
						* 
					FROM (
						SELECT
							KUALITAS.ID_KUALITAS AS IDK,
							EBCC_DETAIL.EBCC_VALIDATION_CODE,
							EBCC_DETAIL.JUMLAH
						FROM
							TAP_DW.T_KUALITAS_PANEN@DWH_LINK KUALITAS
							LEFT JOIN MOBILE_INSPECTION.TR_EBCC_VALIDATION_D EBCC_DETAIL ON EBCC_DETAIL.ID_KUALITAS = KUALITAS.ID_KUALITAS
						WHERE
							KUALITAS.ACTIVE_STATUS = 'YES'
						ORDER BY 
							KUALITAS.GROUP_KUALITAS ASC,
							KUALITAS.UOM ASC,
							KUALITAS.NAMA_KUALITAS ASC
					)
					PIVOT (
						SUM( JUMLAH )
						FOR IDK IN ( 
							'1' AS JML_1,
							'2' AS JML_2,
							'3' AS JML_3,
							'4' AS JML_4,
							'5' AS JML_5,
							'6' AS JML_6,
							'7' AS JML_7,
							'8' AS JML_8,
							'9' AS JML_9,
							'10' AS JML_10,
							'11' AS JML_11,
							'12' AS JML_12,
							'13' AS JML_13,
							'14' AS JML_14,
							'15' AS JML_15,
							'16' AS JML_16
						)
					)
					WHERE EBCC_VALIDATION_CODE IS NOT NULL
				) DETAIL
					ON HEADER.VAL_EBCC_CODE = DETAIL.EBCC_VALIDATION_CODE
			ORDER BY
				HEADER.VAL_DATE_TIME DESC
		";
		
		// print '<pre>';
		// print_r( $sql );
		// print '</pre>';
		// dd();

		$get = $this->db_mobile_ins->select( $sql );
		return $get;
	}

	public function EBCC_COMPARE_PREVIEW( $id ) {
		$sql = "
			SELECT
			HEADER.*,
			NVL( DETAIL.JML_1, 0 ) AS VAL_JML_1,
			NVL( DETAIL.JML_2, 0 ) AS VAL_JML_2,
			NVL( DETAIL.JML_3, 0 ) AS VAL_JML_3,
			NVL( DETAIL.JML_4, 0 ) AS VAL_JML_4,
			NVL( DETAIL.JML_5, 0 ) AS VAL_JML_5,
			NVL( DETAIL.JML_6, 0 ) AS VAL_JML_6,
			NVL( DETAIL.JML_7, 0 ) AS VAL_JML_7,
			NVL( DETAIL.JML_8, 0 ) AS VAL_JML_8,
			NVL( DETAIL.JML_9, 0 ) AS VAL_JML_9,
			NVL( DETAIL.JML_10, 0 ) AS VAL_JML_10,
			NVL( DETAIL.JML_11, 0 ) AS VAL_JML_11,
			NVL( DETAIL.JML_12, 0 ) AS VAL_JML_12,
			NVL( DETAIL.JML_13, 0 ) AS VAL_JML_13,
			NVL( DETAIL.JML_14, 0 ) AS VAL_JML_14,
			NVL( DETAIL.JML_15, 0 ) AS VAL_JML_15,
			NVL( DETAIL.JML_16, 0 ) AS VAL_JML_16,
			(
				NVL( DETAIL.JML_1, 0 ) + 
				NVL( DETAIL.JML_2, 0 ) + 
				NVL( DETAIL.JML_3, 0 ) + 
				NVL( DETAIL.JML_4, 0 ) + 
				NVL( DETAIL.JML_6, 0 ) +
				NVL( DETAIL.JML_15, 0 ) +
				NVL( DETAIL.JML_16, 0 )
			) AS VAL_TOTAL_JJG,
			F_GET_EBCC_COMPARE ( 
				HEADER.VAL_WERKS, 
				HEADER.VAL_AFD_CODE, 
				HEADER.VAL_BLOCK_CODE,
				TO_CHAR( HEADER.VAL_DATE_TIME, 'DD-MM-YYYY' ), 
				HEADER.VAL_TPH_CODE, 
				(
					NVL( DETAIL.JML_1, 0 ) + 
					NVL( DETAIL.JML_2, 0 ) + 
					NVL( DETAIL.JML_3, 0 ) + 
					NVL( DETAIL.JML_4, 0 ) + 
					NVL( DETAIL.JML_6, 0 ) +
					NVL( DETAIL.JML_15, 0 ) +
					NVL( DETAIL.JML_16, 0 )
				)
			) AS EBCC_NO_BCC
		FROM
			(
				SELECT 
					EBCC_VAL.VAL_EBCC_CODE,
					EBCC_VAL.VAL_WERKS,
					EBCC_VAL.VAL_EST_NAME,
					EBCC_VAL.VAL_NIK_VALIDATOR,
					EBCC_VAL.VAL_NAMA_VALIDATOR,
					EBCC_VAL.VAL_JABATAN_VALIDATOR,
					EBCC_VAL.VAL_STATUS_TPH_SCAN,
					EBCC_VAL.VAL_LAT_TPH,
					EBCC_VAL.VAL_LON_TPH,
					CASE
						WHEN EBCC_VAL.VAL_ALASAN_MANUAL IS NULL THEN ''
						ELSE
							CASE
								WHEN EBCC_VAL.VAL_ALASAN_MANUAL = '1' THEN 'QR Codenya Hilang'
								WHEN EBCC_VAL.VAL_ALASAN_MANUAL = '2' THEN 'QR Codenya Rusak'
						END
					END AS VAL_ALASAN_MANUAL,
					EBCC_VAL.VAL_AFD_CODE,
					EBCC_VAL.VAL_BLOCK_CODE,
					EBCC_VAL.VAL_DATE_TIME AS VAL_DATE_TIME,
					EBCC_VAL.VAL_BLOCK_NAME,
					EBCC_VAL.VAL_TPH_CODE,
					EBCC_VAL.VAL_DELIVERY_TICKET
				FROM
					(
						SELECT
							EBCC_HEADER.EBCC_VALIDATION_CODE AS VAL_EBCC_CODE,
							EST.WERKS AS VAL_WERKS,
							EST.EST_NAME AS VAL_EST_NAME,
							EBCC_HEADER.AFD_CODE AS VAL_AFD_CODE,
							CAST( EBCC_HEADER.SYNC_TIME AS DATE ) AS VAL_DATE_TIME,
							EBCC_HEADER.BLOCK_CODE AS VAL_BLOCK_CODE,
							EBCC_HEADER.STATUS_TPH_SCAN AS VAL_STATUS_TPH_SCAN,
							EBCC_HEADER.ALASAN_MANUAL AS VAL_ALASAN_MANUAL,
							EBCC_HEADER.NO_TPH AS VAL_TPH_CODE,
							EBCC_HEADER.DELIVERY_CODE AS VAL_DELIVERY_TICKET,
							USER_AUTH.EMPLOYEE_NIK AS VAL_NIK_VALIDATOR,
							EMP.EMPLOYEE_FULLNAME AS VAL_NAMA_VALIDATOR,
							EMP.EMPLOYEE_POSITION AS VAL_JABATAN_VALIDATOR,
							SUBBLOCK.BLOCK_NAME AS VAL_BLOCK_NAME,
							EBCC_HEADER.LAT_TPH AS VAL_LAT_TPH,
							EBCC_HEADER.LAT_TPH AS VAL_LON_TPH
						FROM
							MOBILE_INSPECTION.TR_EBCC_VALIDATION_H EBCC_HEADER
							LEFT JOIN MOBILE_INSPECTION.TM_USER_AUTH USER_AUTH ON 
								USER_AUTH.USER_AUTH_CODE = (
									CASE
										WHEN LENGTH( EBCC_HEADER.INSERT_USER ) = 3 THEN '0' || EBCC_HEADER.INSERT_USER
										ELSE EBCC_HEADER.INSERT_USER
									END
								)
							LEFT JOIN (
								SELECT 
									EMPLOYEE_NIK,
									EMPLOYEE_FULLNAME,
									EMPLOYEE_POSITION,
									EMPLOYEE_JOINDATE as START_DATE,
									CASE 
										WHEN EMPLOYEE_RESIGNDATE IS NULL
										THEN TO_DATE( '99991231', 'RRRRMMDD' )
										ELSE EMPLOYEE_RESIGNDATE
									END as END_DATE
								FROM 
									TAP_DW.TM_EMPLOYEE_HRIS@DWH_LINK 
								UNION ALL
								SELECT 
									NIK, 
									EMPLOYEE_NAME,
									JOB_CODE,
									START_VALID,
									CASE
										WHEN RES_DATE IS NOT NULL 
										THEN RES_DATE
										ELSE END_VALID
									END END_VALID
								FROM 
									TAP_DW.TM_EMPLOYEE_SAP@DWH_LINK
							) EMP ON
								EMP.EMPLOYEE_NIK = USER_AUTH.EMPLOYEE_NIK
								AND TRUNC( EBCC_HEADER.INSERT_TIME ) BETWEEN TRUNC( EMP.START_DATE ) AND TRUNC( EMP.END_DATE )
							LEFT JOIN TAP_DW.TM_EST@DWH_LINK EST ON 
								EST.WERKS = EBCC_HEADER.WERKS 
								AND SYSDATE BETWEEN EST.START_VALID AND EST.END_VALID
							LEFT JOIN TAP_DW.TM_SUB_BLOCK@DWH_LINK SUBBLOCK
								ON SUBBLOCK.WERKS = EBCC_HEADER.WERKS
								AND SUBBLOCK.SUB_BLOCK_CODE = EBCC_HEADER.BLOCK_CODE
						WHERE
							1 = 1
							AND EBCC_HEADER.EBCC_VALIDATION_CODE = '{$id}'
							AND ROWNUM < 2
					) EBCC_VAL
				GROUP BY
					EBCC_VAL.VAL_EBCC_CODE,
					EBCC_VAL.VAL_WERKS,
					EBCC_VAL.VAL_EST_NAME,
					EBCC_VAL.VAL_NIK_VALIDATOR,
					EBCC_VAL.VAL_NAMA_VALIDATOR,
					EBCC_VAL.VAL_JABATAN_VALIDATOR,
					EBCC_VAL.VAL_STATUS_TPH_SCAN,
					EBCC_VAL.VAL_ALASAN_MANUAL,
					EBCC_VAL.VAL_DATE_TIME,
					EBCC_VAL.VAL_AFD_CODE,
					EBCC_VAL.VAL_BLOCK_CODE,
					EBCC_VAL.VAL_BLOCK_NAME,
					EBCC_VAL.VAL_TPH_CODE,
					EBCC_VAL.VAL_DELIVERY_TICKET,
					EBCC_VAL.VAL_LAT_TPH,
					EBCC_VAL.VAL_LON_TPH
			) HEADER
			LEFT JOIN (
				SELECT 
					* 
				FROM (
					SELECT
						KUALITAS.ID_KUALITAS AS IDK,
						EBCC_DETAIL.EBCC_VALIDATION_CODE,
						EBCC_DETAIL.JUMLAH
					FROM
						TAP_DW.T_KUALITAS_PANEN@DWH_LINK KUALITAS
						LEFT JOIN MOBILE_INSPECTION.TR_EBCC_VALIDATION_D EBCC_DETAIL ON EBCC_DETAIL.ID_KUALITAS = KUALITAS.ID_KUALITAS
					WHERE
						KUALITAS.ACTIVE_STATUS = 'YES'
					ORDER BY 
						KUALITAS.GROUP_KUALITAS ASC,
						KUALITAS.UOM ASC,
						KUALITAS.NAMA_KUALITAS ASC
				)
				PIVOT (
					SUM( JUMLAH )
					FOR IDK IN ( 
						'1' AS JML_1,
						'2' AS JML_2,
						'3' AS JML_3,
						'4' AS JML_4,
						'5' AS JML_5,
						'6' AS JML_6,
						'7' AS JML_7,
						'8' AS JML_8,
						'9' AS JML_9,
						'10' AS JML_10,
						'11' AS JML_11,
						'12' AS JML_12,
						'13' AS JML_13,
						'14' AS JML_14,
						'15' AS JML_15,
						'16' AS JML_16
					)
				)
				WHERE EBCC_VALIDATION_CODE IS NOT NULL
			) DETAIL
				ON HEADER.VAL_EBCC_CODE = DETAIL.EBCC_VALIDATION_CODE
		";
		
		// print '<pre>';
		// print_r( $sql );
		// print '</pre>';
		// dd();

		$get = collect( $this->db_mobile_ins->select( $sql ) )->first();

		// print '<pre>';
		// print_r( $get );
		// print '</pre>';
		// dd();
		$joindata = array();
	
		if ( !empty( $get ) ) {
			$client = new \GuzzleHttp\Client();
			$image_selfie = $client->request( 'GET', 'http://149.129.245.230:3012/api/v1.1/foto-transaksi/'.$get->val_ebcc_code.'?status_image=SELFIE_V' );
			$image_selfie = json_decode( $image_selfie->getBody(), true );
			$image_janjang = $client->request( 'GET', 'http://149.129.245.230:3012/api/v1.1/foto-transaksi/'.$get->val_ebcc_code.'?status_image=JANJANG' );
			$image_janjang = json_decode( $image_janjang->getBody(), true );

			// print '<pre>';
			// print_r( $image_janjang );
			// print '</pre>';

			$joindata['val_image_selfie'] = ( isset( $image_selfie['data']['http'][0] ) ? $image_selfie['data']['http'][0] : url( 'assets/user.jpg' ) );
			$joindata['val_image_janjang'] = ( isset( $image_janjang['data']['http'][0] ) ? $image_janjang['data']['http'][0] : url( 'assets/dummy-janjang.jpg' ) );
			 
			$joindata['ebcc_image_selfie'] = url( 'assets/user.jpg' );
			$joindata['ebcc_image_janjang'] = url( 'assets/dummy-janjang.jpg' );

			$joindata['val_ebcc_code'] = $get->val_ebcc_code;
			$joindata['val_werks'] = $get->val_werks;
			$joindata['val_est_name'] = $get->val_est_name;
			$joindata['val_nik_validator'] = $get->val_nik_validator;
			$joindata['val_nama_validator'] = $get->val_nama_validator;
			$joindata['val_jabatan_validator'] = $get->val_jabatan_validator;
			$joindata['val_status_tph_scan'] = $get->val_status_tph_scan;
			$joindata['val_alasan_manual'] = $get->val_alasan_manual;
			$joindata['val_afd_code'] = $get->val_afd_code;
			$joindata['val_block_code'] = $get->val_block_code;
			$joindata['val_date_time'] = $get->val_date_time;
			$joindata['val_block_name'] = $get->val_block_name;
			$joindata['val_tph_code'] = $get->val_tph_code;
			$joindata['val_delivery_ticket'] = $get->val_delivery_ticket;
			$joindata['val_jml_bm'] = $get->val_jml_1;
			$joindata['val_jml_bk'] = $get->val_jml_2;
			$joindata['val_jml_ms'] = $get->val_jml_3;
			$joindata['val_jml_or'] = $get->val_jml_4;
			$joindata['val_jml_bb'] = $get->val_jml_6;
			$joindata['val_jml_jk'] = $get->val_jml_15;
			$joindata['val_jml_ba'] = $get->val_jml_16;
			$joindata['val_jml_brd'] = $get->val_jml_5;
			$joindata['val_jjg_panen'] = $get->val_total_jjg;
			$joindata['ebcc_jml_bm'] = '';
			$joindata['ebcc_jml_bk'] = '';
			$joindata['ebcc_jml_ms'] = '';
			$joindata['ebcc_jml_or'] = '';
			$joindata['ebcc_jml_bb'] = '';
			$joindata['ebcc_jml_jk'] = '';
			$joindata['ebcc_jml_ba'] = '';
			$joindata['ebcc_jml_brd'] = '';
			$joindata['ebcc_jjg_panen'] = '';
			$joindata['ebcc_nik_kerani_buah'] = '';
			$joindata['ebcc_nama_kerani_buah'] = '';
			$joindata['ebcc_status_tph'] = '';
			$joindata['ebcc_keterangan_qrcode'] = '';
			$joindata['ebcc_no_bcc'] = $get->ebcc_no_bcc;
			$joindata['akurasi_kualitas_ms'] = '';
			$joindata['match_status'] = 'NOT MATCH';
			$date = date( 'd-m-Y', strtotime( $get->val_date_time ) );
			
			if ( $get->ebcc_no_bcc != null ) {
				$sql_ebcc = "SELECT
						HDP.ID_RENCANA,
						HDP.TANGGAL_RENCANA,
						HDP.NIK_KERANI_BUAH,
						EMP_EBCC.EMP_NAME,
						HDP.ID_BA_AFD_BLOK,
						HDP.NO_REKAP_BCC,
						HP.NO_TPH,
						HP.NO_BCC,
						HP.STATUS_TPH,
						CASE
							WHEN HP.KETERANGAN_QRCODE IS NULL THEN ''
							ELSE
								CASE
									WHEN HP.KETERANGAN_QRCODE = '1' THEN ' - QR Codenya Hilang'
									WHEN HP.KETERANGAN_QRCODE = '2' THEN ' - QR Codenya Rusak'
									ELSE ''
								END
						END AS KETERANGAN_QRCODE,
						NVL( EBCC.F_GET_HASIL_PANEN_BUNCH ( TBA.ID_BA, HP.NO_REKAP_BCC, HP.NO_BCC, 'BUNCH_HARVEST' ), 0 ) as JJG_PANEN,
						NVL( EBCC.F_GET_HASIL_PANEN_NUMBERX( HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 1 ), 0 ) AS EBCC_JML_BM,
						NVL( EBCC.F_GET_HASIL_PANEN_NUMBERX( HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 2 ), 0 ) AS EBCC_JML_BK,
						NVL( EBCC.F_GET_HASIL_PANEN_NUMBERX( HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 3 ), 0 ) AS EBCC_JML_MS,
						NVL( EBCC.F_GET_HASIL_PANEN_NUMBERX( HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 4 ), 0 ) AS EBCC_JML_OR,
						NVL( EBCC.F_GET_HASIL_PANEN_NUMBERX( HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 6 ), 0 ) AS EBCC_JML_BB,
						NVL( EBCC.F_GET_HASIL_PANEN_NUMBERX( HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 15 ), 0 ) AS EBCC_JML_JK,
						NVL( EBCC.F_GET_HASIL_PANEN_NUMBERX( HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 16 ), 0 ) AS EBCC_JML_BA,   
						NVL( EBCC.F_GET_HASIL_PANEN_BRDX ( HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC ), 0 ) AS EBCC_JML_BRD
					FROM (
							SELECT
								HRP.ID_RENCANA AS ID_RENCANA,
								HRP.TANGGAL_RENCANA AS TANGGAL_RENCANA,
								HRP.NIK_KERANI_BUAH AS NIK_KERANI_BUAH,
								DRP.ID_BA_AFD_BLOK AS ID_BA_AFD_BLOK,
								DRP.NO_REKAP_BCC AS NO_REKAP_BCC
							FROM
								EBCC.T_HEADER_RENCANA_PANEN HRP 
								LEFT JOIN EBCC.T_DETAIL_RENCANA_PANEN DRP ON HRP.ID_RENCANA = DRP.ID_RENCANA
						) HDP
						LEFT JOIN EBCC.T_HASIL_PANEN HP ON HP.ID_RENCANA = HDP.ID_RENCANA AND HP.NO_REKAP_BCC = HDP.NO_REKAP_BCC
						LEFT JOIN EBCC.T_BLOK TB ON TB.ID_BA_AFD_BLOK = HDP.ID_BA_AFD_BLOK
						LEFT JOIN EBCC.T_AFDELING TA ON TA.ID_BA_AFD = TB.ID_BA_AFD
						LEFT JOIN EBCC.T_BUSSINESSAREA TBA ON TBA.ID_BA = TA.ID_BA
						LEFT JOIN EBCC.T_EMPLOYEE EMP_EBCC ON EMP_EBCC.NIK = HDP.NIK_KERANI_BUAH 
					WHERE
						HP.NO_BCC = '{$get->ebcc_no_bcc}'";
						
				$query_ebcc = collect( $this->db_mobile_ins->select( $sql_ebcc ) )->first();

				// print '<pre>';
				// print_r( $query_ebcc );
				// print '</pre>';

				$joindata['ebcc_nik_kerani_buah'] = $query_ebcc->nik_kerani_buah;
				$joindata['ebcc_nama_kerani_buah'] = $query_ebcc->emp_name;
				$joindata['ebcc_no_bcc'] = $query_ebcc->no_bcc;
				$joindata['ebcc_jml_bm'] = $query_ebcc->ebcc_jml_bm;
				$joindata['ebcc_jml_bk'] = $query_ebcc->ebcc_jml_bk;
				$joindata['ebcc_jml_ms'] = $query_ebcc->ebcc_jml_ms;
				$joindata['ebcc_jml_or'] = $query_ebcc->ebcc_jml_or;
				$joindata['ebcc_jml_bb'] = $query_ebcc->ebcc_jml_bb;
				$joindata['ebcc_jml_jk'] = $query_ebcc->ebcc_jml_jk;
				$joindata['ebcc_jml_ba'] = $query_ebcc->ebcc_jml_ba;
				$joindata['ebcc_jml_brd'] = $query_ebcc->ebcc_jml_brd;
				$joindata['ebcc_jjg_panen'] = $query_ebcc->jjg_panen;
				$joindata['ebcc_status_tph'] = $query_ebcc->status_tph;
				$joindata['ebcc_keterangan_qrcode'] = $query_ebcc->keterangan_qrcode;
				
				$joindata['match_status'] = ( intval( $query_ebcc->jjg_panen ) == intval( $get->val_total_jjg ) ? 'MATCH' : 'NOT MATCH' );
				$akurasi_kualitas_ms = intval( $query_ebcc->ebcc_jml_ms ) - intval( $get->val_jml_3 );
				$joindata['akurasi_kualitas_ms'] = ( $akurasi_kualitas_ms > 0 ? $akurasi_kualitas_ms : 0 );
				

			}
		}

		

		// print '<pre>';
		// print_r( $joindata );
		// print '<pre>';
		// dd();
		return $joindata;
	}
	
	public function EBCC_COMPARE_OLD( $REPORT_TYPE , $START_DATE , $END_DATE , $REGION_CODE , $COMP_CODE , $BA_CODE , $AFD_CODE , $BLOCK_CODE ) {
		if ( $REPORT_TYPE == 'EBCC_COMPARE_ESTATE' ) {
			$REPORT_TYPE = 'V';
		}
		else if ( $REPORT_TYPE == 'EBCC_COMPARE_MILL' ) {
			$REPORT_TYPE = 'M';
		}
		$where = "";
		$where_ebcc = "";

		$START_DATE = date( 'Y-m-d', strtotime( $START_DATE ) );
		$END_DATE = date( 'Y-m-d', strtotime( $END_DATE ) );
		$where = "";
		$where .= ( $REGION_CODE != "" && $COMP_CODE == "" ) ? " AND EST.REGION_CODE = '$REGION_CODE'  ": "";
		$where .= ( $COMP_CODE != "" && $BA_CODE == "" ) ? " AND EST.COMP_CODE = '$COMP_CODE'  ": "";
		$where .= ( $BA_CODE != "" && $AFD_CODE == "" ) ? " AND EST.WERKS = '$BA_CODE'  ": "";
		$where .= ( $AFD_CODE != "" && $BLOCK_CODE == "" ) ? " AND EBCC_HEADER.WERKS||EBCC_HEADER.AFD_CODE = '$AFD_CODE'  ": "";
		$where .= ( $AFD_CODE != "" && $BLOCK_CODE != "" ) ? " AND EBCC_HEADER.WERKS||EBCC_HEADER.AFD_CODE||EBCC_HEADER.BLOCK_CODE = '$BLOCK_CODE'  ": "";
		
		$sql = "
			SELECT
				HEADER.*,
				NVL( DETAIL.JML_1, 0 ) AS VAL_JML_1,
				NVL( DETAIL.JML_2, 0 ) AS VAL_JML_2,
				NVL( DETAIL.JML_3, 0 ) AS VAL_JML_3,
				NVL( DETAIL.JML_4, 0 ) AS VAL_JML_4,
				NVL( DETAIL.JML_5, 0 ) AS VAL_JML_5,
				NVL( DETAIL.JML_6, 0 ) AS VAL_JML_6,
				NVL( DETAIL.JML_7, 0 ) AS VAL_JML_7,
				NVL( DETAIL.JML_8, 0 ) AS VAL_JML_8,
				NVL( DETAIL.JML_9, 0 ) AS VAL_JML_9,
				NVL( DETAIL.JML_10, 0 ) AS VAL_JML_10,
				NVL( DETAIL.JML_11, 0 ) AS VAL_JML_11,
				NVL( DETAIL.JML_12, 0 ) AS VAL_JML_12,
				NVL( DETAIL.JML_13, 0 ) AS VAL_JML_13,
				NVL( DETAIL.JML_14, 0 ) AS VAL_JML_14,
				NVL( DETAIL.JML_15, 0 ) AS VAL_JML_15,
				NVL( DETAIL.JML_16, 0 ) AS VAL_JML_16,
				(
					NVL( DETAIL.JML_1, 0 ) + 
					NVL( DETAIL.JML_2, 0 ) + 
					NVL( DETAIL.JML_3, 0 ) + 
					NVL( DETAIL.JML_4, 0 ) + 
					NVL( DETAIL.JML_6, 0 ) +
					NVL( DETAIL.JML_15, 0 ) +
					NVL( DETAIL.JML_16, 0 )
				) AS VAL_TOTAL_JJG,
				F_GET_EBCC_COMPARE ( 
					HEADER.VAL_WERKS, 
					HEADER.VAL_AFD_CODE, 
					HEADER.VAL_BLOCK_CODE,
					TO_CHAR( HEADER.VAL_DATE_TIME, 'DD-MM-YYYY' ), 
					HEADER.VAL_TPH_CODE, 
					(
						NVL( DETAIL.JML_1, 0 ) + 
						NVL( DETAIL.JML_2, 0 ) + 
						NVL( DETAIL.JML_3, 0 ) + 
						NVL( DETAIL.JML_4, 0 ) + 
						NVL( DETAIL.JML_6, 0 ) +
						NVL( DETAIL.JML_15, 0 ) +
						NVL( DETAIL.JML_16, 0 )
					)
				) AS EBCC_NO_BCC
			FROM
				(
					SELECT 
						EBCC_VAL.VAL_EBCC_CODE,
						EBCC_VAL.VAL_WERKS,
						EBCC_VAL.VAL_EST_NAME,
						EBCC_VAL.VAL_NIK_VALIDATOR,
						EBCC_VAL.VAL_NAMA_VALIDATOR,
						EBCC_VAL.VAL_JABATAN_VALIDATOR,
						EBCC_VAL.VAL_STATUS_TPH_SCAN,
						EBCC_VAL.VAL_LAT_TPH,
						EBCC_VAL.VAL_LON_TPH,
						EBCC_VAL.VAL_MATURITY_STATUS,
						CASE
							WHEN EBCC_VAL.VAL_ALASAN_MANUAL IS NULL THEN ''
							ELSE
								CASE
									WHEN EBCC_VAL.VAL_ALASAN_MANUAL = '1' THEN 'QR Codenya Hilang'
									WHEN EBCC_VAL.VAL_ALASAN_MANUAL = '2' THEN 'QR Codenya Rusak'
								END
						END AS VAL_ALASAN_MANUAL,
						EBCC_VAL.VAL_AFD_CODE,
						EBCC_VAL.VAL_BLOCK_CODE,
						EBCC_VAL.VAL_DATE_TIME AS VAL_DATE_TIME,
						EBCC_VAL.VAL_BLOCK_NAME,
						EBCC_VAL.VAL_TPH_CODE,
						EBCC_VAL.VAL_DELIVERY_TICKET
					FROM
						(
							SELECT
								EBCC_HEADER.EBCC_VALIDATION_CODE AS VAL_EBCC_CODE,
								EST.WERKS AS VAL_WERKS,
								EST.EST_NAME AS VAL_EST_NAME,
								EBCC_HEADER.AFD_CODE AS VAL_AFD_CODE,
								CAST( EBCC_HEADER.SYNC_TIME AS DATE ) AS VAL_DATE_TIME,
								EBCC_HEADER.BLOCK_CODE AS VAL_BLOCK_CODE,
								EBCC_HEADER.STATUS_TPH_SCAN AS VAL_STATUS_TPH_SCAN,
								EBCC_HEADER.ALASAN_MANUAL AS VAL_ALASAN_MANUAL,
								EBCC_HEADER.NO_TPH AS VAL_TPH_CODE,
								EBCC_HEADER.DELIVERY_CODE AS VAL_DELIVERY_TICKET,
								USER_AUTH.EMPLOYEE_NIK AS VAL_NIK_VALIDATOR,
								EMP.EMPLOYEE_FULLNAME AS VAL_NAMA_VALIDATOR,
								EMP.EMPLOYEE_POSITION AS VAL_JABATAN_VALIDATOR,
								LAND_USE.MATURITY_STATUS AS VAL_MATURITY_STATUS,
								LAND_USE.SPMON AS VAL_SPMON,
								SUBBLOCK.BLOCK_NAME AS VAL_BLOCK_NAME,
								EBCC_HEADER.LAT_TPH AS VAL_LAT_TPH,
								EBCC_HEADER.LAT_TPH AS VAL_LON_TPH
							FROM
								MOBILE_INSPECTION.TR_EBCC_VALIDATION_H EBCC_HEADER
								LEFT JOIN MOBILE_INSPECTION.TM_USER_AUTH USER_AUTH ON 
									USER_AUTH.USER_AUTH_CODE = (
										CASE
											WHEN LENGTH( EBCC_HEADER.INSERT_USER ) = 3 THEN '0' || EBCC_HEADER.INSERT_USER
											ELSE EBCC_HEADER.INSERT_USER
										END
									)
								LEFT JOIN (
									SELECT 
										EMPLOYEE_NIK,
										EMPLOYEE_FULLNAME,
										EMPLOYEE_POSITION,
										EMPLOYEE_JOINDATE as START_DATE,
										CASE 
											WHEN EMPLOYEE_RESIGNDATE IS NULL
											THEN TO_DATE( '99991231', 'RRRRMMDD' )
											ELSE EMPLOYEE_RESIGNDATE
										END as END_DATE
									FROM 
										TAP_DW.TM_EMPLOYEE_HRIS@DWH_LINK 
									UNION ALL
									SELECT 
										NIK, 
										EMPLOYEE_NAME,
										JOB_CODE,
										START_VALID,
										CASE
											WHEN RES_DATE IS NOT NULL 
											THEN RES_DATE
											ELSE END_VALID
										END END_VALID
									FROM 
										TAP_DW.TM_EMPLOYEE_SAP@DWH_LINK
								) EMP ON
									EMP.EMPLOYEE_NIK = USER_AUTH.EMPLOYEE_NIK
									AND TRUNC( EBCC_HEADER.INSERT_TIME ) BETWEEN TRUNC( EMP.START_DATE ) AND TRUNC( EMP.END_DATE )
								LEFT JOIN TAP_DW.TM_EST@DWH_LINK EST ON 
									EST.WERKS = EBCC_HEADER.WERKS 
									AND SYSDATE BETWEEN EST.START_VALID AND EST.END_VALID
								LEFT JOIN (
									SELECT
										WERKS,
										AFD_CODE,
										BLOCK_CODE,
										BLOCK_NAME,
										MATURITY_STATUS,
										SPMON
									FROM
										TAP_DW.TR_HS_LAND_USE@DWH_LINK
									WHERE
										1 = 1
										AND ROWNUM < 2
										AND MATURITY_STATUS IS NOT NULL
										AND SPMON
											BETWEEN 
												(
													SELECT
														CASE
															WHEN TO_CHAR( TO_DATE( '$END_DATE', 'RRRR-MM-DD' ), 'MM' ) = '01'
															THEN TRUNC( TO_DATE( '$END_DATE', 'RRRR-MM-DD' ), 'MM' )
															ELSE TRUNC( ADD_MONTHS( TO_DATE( '$END_DATE', 'RRRR-MM-DD' ), -1 ), 'MM' )
														END
													FROM
														DUAL
												)
											AND
												(
													(
														SELECT
															CASE
																WHEN TO_CHAR( TO_DATE( '$END_DATE', 'RRRR-MM-DD' ), 'MM' ) = '01'
																THEN LAST_DAY( TO_DATE( '$END_DATE', 'RRRR-MM-DD' ) )
																ELSE LAST_DAY( ADD_MONTHS( TO_DATE( '$END_DATE', 'RRRR-MM-DD' ), -1 ) )
															END
														FROM
															DUAL
													)
												)
								) LAND_USE
									ON LAND_USE.WERKS = EBCC_HEADER.WERKS
									AND LAND_USE.AFD_CODE = EBCC_HEADER.AFD_CODE
									AND LAND_USE.BLOCK_CODE = EBCC_HEADER.BLOCK_CODE
								LEFT JOIN TAP_DW.TM_SUB_BLOCK@DWH_LINK SUBBLOCK
									ON SUBBLOCK.WERKS = EBCC_HEADER.WERKS
									AND SUBBLOCK.SUB_BLOCK_CODE = EBCC_HEADER.BLOCK_CODE
							WHERE
								1 = 1
								AND SUBSTR( EBCC_HEADER.EBCC_VALIDATION_CODE, 0, 1 ) = '$REPORT_TYPE'
								AND TRUNC( EBCC_HEADER.SYNC_TIME ) BETWEEN TRUNC( TO_DATE( '$START_DATE', 'RRRR-MM-DD' ) ) AND TRUNC( TO_DATE( '$END_DATE', 'RRRR-MM-DD' ) )
								$where
						) EBCC_VAL
					GROUP BY
						EBCC_VAL.VAL_EBCC_CODE,
						EBCC_VAL.VAL_WERKS,
						EBCC_VAL.VAL_EST_NAME,
						EBCC_VAL.VAL_NIK_VALIDATOR,
						EBCC_VAL.VAL_NAMA_VALIDATOR,
						EBCC_VAL.VAL_JABATAN_VALIDATOR,
						EBCC_VAL.VAL_STATUS_TPH_SCAN,
						EBCC_VAL.VAL_ALASAN_MANUAL,
						EBCC_VAL.VAL_DATE_TIME,
						EBCC_VAL.VAL_AFD_CODE,
						EBCC_VAL.VAL_BLOCK_CODE,
						EBCC_VAL.VAL_BLOCK_NAME,
						EBCC_VAL.VAL_TPH_CODE,
						EBCC_VAL.VAL_DELIVERY_TICKET,
						EBCC_VAL.VAL_LAT_TPH,
						EBCC_VAL.VAL_LON_TPH,
						EBCC_VAL.VAL_MATURITY_STATUS,
						EBCC_VAL.VAL_SPMON
				) HEADER
				LEFT JOIN (
					SELECT 
						* 
					FROM (
						SELECT
							KUALITAS.ID_KUALITAS AS IDK,
							EBCC_DETAIL.EBCC_VALIDATION_CODE,
							EBCC_DETAIL.JUMLAH
						FROM
							TAP_DW.T_KUALITAS_PANEN@DWH_LINK KUALITAS
							LEFT JOIN MOBILE_INSPECTION.TR_EBCC_VALIDATION_D EBCC_DETAIL ON EBCC_DETAIL.ID_KUALITAS = KUALITAS.ID_KUALITAS
						WHERE
							KUALITAS.ACTIVE_STATUS = 'YES'
						ORDER BY 
							KUALITAS.GROUP_KUALITAS ASC,
							KUALITAS.UOM ASC,
							KUALITAS.NAMA_KUALITAS ASC
					)
					PIVOT (
						SUM( JUMLAH )
						FOR IDK IN ( 
							'1' AS JML_1,
							'2' AS JML_2,
							'3' AS JML_3,
							'4' AS JML_4,
							'5' AS JML_5,
							'6' AS JML_6,
							'7' AS JML_7,
							'8' AS JML_8,
							'9' AS JML_9,
							'10' AS JML_10,
							'11' AS JML_11,
							'12' AS JML_12,
							'13' AS JML_13,
							'14' AS JML_14,
							'15' AS JML_15,
							'16' AS JML_16
						)
					)
					WHERE EBCC_VALIDATION_CODE IS NOT NULL
				) DETAIL
					ON HEADER.VAL_EBCC_CODE = DETAIL.EBCC_VALIDATION_CODE
			ORDER BY
				HEADER.VAL_NAMA_VALIDATOR ASC,
				HEADER.VAL_AFD_CODE ASC,
				HEADER.VAL_DATE_TIME DESC
		";

		// print '<pre>';
		// print_r( $sql );
		// print '</pre>';
		// dd();
		$get = $this->db_mobile_ins->select( $sql );
		$joindata = array();
		$summary_data = array();

		if ( !empty( $get ) ) {
			$i = 0;
			foreach ( $get as $ec ) {
				$summary_code = date( 'Ymd', strtotime( $ec->val_date_time ) ).$ec->val_nik_validator.'_'.$ec->val_werks.$ec->val_afd_code;
				$joindata[$i]['summary_code'] = $summary_code;
				$joindata[$i]['val_ebcc_code'] = $ec->val_ebcc_code;
				$joindata[$i]['val_werks'] = $ec->val_werks;
				$joindata[$i]['val_est_name'] = $ec->val_est_name;
				$joindata[$i]['val_nik_validator'] = $ec->val_nik_validator;
				$joindata[$i]['val_nama_validator'] = $ec->val_nama_validator;
				$joindata[$i]['val_jabatan_validator'] = $ec->val_jabatan_validator;
				$joindata[$i]['val_status_tph_scan'] = $ec->val_status_tph_scan;
				$joindata[$i]['val_alasan_manual'] = $ec->val_alasan_manual;
				$joindata[$i]['val_afd_code'] = $ec->val_afd_code;
				$joindata[$i]['val_block_code'] = $ec->val_block_code;
				$joindata[$i]['val_date_time'] = date( 'Y-m-d', strtotime( $ec->val_date_time ) );
				$joindata[$i]['val_block_name'] = $ec->val_block_name;
				$joindata[$i]['val_tph_code'] = $ec->val_tph_code;
				$joindata[$i]['val_delivery_ticket'] = $ec->val_delivery_ticket;
				$joindata[$i]['val_jml_bm'] = $ec->val_jml_1;
				$joindata[$i]['val_jml_bk'] = $ec->val_jml_2;
				$joindata[$i]['val_jml_ms'] = $ec->val_jml_3;
				$joindata[$i]['val_jml_or'] = $ec->val_jml_4;
				$joindata[$i]['val_jml_bb'] = $ec->val_jml_6;
				$joindata[$i]['val_jml_jk'] = $ec->val_jml_15;
				$joindata[$i]['val_jml_ba'] = $ec->val_jml_16;
				$joindata[$i]['val_jml_brd'] = $ec->val_jml_5;
				$joindata[$i]['val_jjg_panen'] = $ec->val_total_jjg;
				$joindata[$i]['ebcc_jml_bm'] = '';
				$joindata[$i]['ebcc_jml_bk'] = '';
				$joindata[$i]['ebcc_jml_ms'] = '';
				$joindata[$i]['ebcc_jml_or'] = '';
				$joindata[$i]['ebcc_jml_bb'] = '';
				$joindata[$i]['ebcc_jml_jk'] = '';
				$joindata[$i]['ebcc_jml_ba'] = '';
				$joindata[$i]['ebcc_jml_brd'] = '';
				$joindata[$i]['ebcc_jjg_panen'] = '';
				$joindata[$i]['ebcc_nik_kerani_buah'] = '';
				$joindata[$i]['ebcc_nama_kerani_buah'] = '';
				$joindata[$i]['ebcc_status_tph'] = '';
				$joindata[$i]['ebcc_keterangan_qrcode'] = '';
				$joindata[$i]['ebcc_no_bcc'] = $ec->ebcc_no_bcc;
				$joindata[$i]['akurasi_kualitas_ms'] = '';
				$joindata[$i]['match_status'] = 'NOT MATCH';
				$joindata[$i]['link_foto'] = url( 'preview/compare-ebcc/'.$ec->val_ebcc_code );

				// Data Summary
				if ( !isset( $summary_data[$summary_code] ) ) {
					$summary_data[$summary_code] = array();
					$summary_data[$summary_code]['nama'] = $ec->val_nama_validator;
					$summary_data[$summary_code]['match'] = 0;
					$summary_data[$summary_code]['akurasi'] = 0;
					$summary_data[$summary_code]['tanggal'] = date( 'd M Y', strtotime( $ec->val_date_time ) );
					$summary_data[$summary_code]['jumlah_data'] = 0;
					$summary_data[$summary_code]['val_jml_bm'] = 0;
					$summary_data[$summary_code]['val_jml_bk'] = 0;
					$summary_data[$summary_code]['val_jml_ms'] = 0;
					$summary_data[$summary_code]['val_jml_or'] = 0;
					$summary_data[$summary_code]['val_jml_bb'] = 0;
					$summary_data[$summary_code]['val_jml_jk'] = 0;
					$summary_data[$summary_code]['val_jml_ba'] = 0;
					$summary_data[$summary_code]['val_jml_brd'] = 0;
					$summary_data[$summary_code]['val_jjg_panen'] = 0;
					$summary_data[$summary_code]['ebcc_jml_bm'] = 0;
					$summary_data[$summary_code]['ebcc_jml_bk'] = 0;
					$summary_data[$summary_code]['ebcc_jml_ms'] = 0;
					$summary_data[$summary_code]['ebcc_jml_or'] = 0;
					$summary_data[$summary_code]['ebcc_jml_bb'] = 0;
					$summary_data[$summary_code]['ebcc_jml_jk'] = 0;
					$summary_data[$summary_code]['ebcc_jml_ba'] = 0;
					$summary_data[$summary_code]['ebcc_jml_brd'] = 0;
					$summary_data[$summary_code]['ebcc_jjg_panen'] = 0;
				}

				$summary_data[$summary_code]['jumlah_data'] += 1;
				$summary_data[$summary_code]['val_jml_bm'] = $summary_data[$summary_code]['val_jml_bm'] + intval( $ec->val_jml_1 );
				$summary_data[$summary_code]['val_jml_bk'] = $summary_data[$summary_code]['val_jml_bk'] + intval( $ec->val_jml_2 );
				$summary_data[$summary_code]['val_jml_ms'] = $summary_data[$summary_code]['val_jml_ms'] + intval( $ec->val_jml_3 );
				$summary_data[$summary_code]['val_jml_or'] = $summary_data[$summary_code]['val_jml_or'] + intval( $ec->val_jml_4 );
				$summary_data[$summary_code]['val_jml_bb'] = $summary_data[$summary_code]['val_jml_bb'] + intval( $ec->val_jml_6 );
				$summary_data[$summary_code]['val_jml_jk'] = $summary_data[$summary_code]['val_jml_jk'] + intval( $ec->val_jml_15 );
				$summary_data[$summary_code]['val_jml_ba'] = $summary_data[$summary_code]['val_jml_ba'] + intval( $ec->val_jml_16 );
				$summary_data[$summary_code]['val_jml_brd'] = $summary_data[$summary_code]['val_jml_brd'] + intval( $ec->val_jml_5 );
				$summary_data[$summary_code]['val_jjg_panen'] = $summary_data[$summary_code]['val_jjg_panen'] + intval( $ec->val_total_jjg );

				$date = date( 'd-m-Y', strtotime( $ec->val_date_time ) );
				
				if ( $ec->ebcc_no_bcc != null ) {
					$sql_ebcc = "SELECT
							HDP.ID_RENCANA,
							HDP.TANGGAL_RENCANA,
							HDP.NIK_KERANI_BUAH,
							EMP_EBCC.EMP_NAME,
							HDP.ID_BA_AFD_BLOK,
							HDP.NO_REKAP_BCC,
							HP.NO_TPH,
							HP.NO_BCC,
							HP.STATUS_TPH,
							CASE
								WHEN HP.KETERANGAN_QRCODE IS NULL THEN ''
								ELSE
									CASE
										WHEN HP.KETERANGAN_QRCODE = '1' THEN ' - QR Codenya Hilang'
										WHEN HP.KETERANGAN_QRCODE = '2' THEN ' - QR Codenya Rusak'
										ELSE ''
									END
							END AS KETERANGAN_QRCODE,
							NVL( EBCC.F_GET_HASIL_PANEN_BUNCH ( TBA.ID_BA, HP.NO_REKAP_BCC, HP.NO_BCC, 'BUNCH_HARVEST' ), 0 ) as JJG_PANEN,
							NVL( EBCC.F_GET_HASIL_PANEN_NUMBERX( HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 1 ), 0 ) AS EBCC_JML_BM,
							NVL( EBCC.F_GET_HASIL_PANEN_NUMBERX( HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 2 ), 0 ) AS EBCC_JML_BK,
							NVL( EBCC.F_GET_HASIL_PANEN_NUMBERX( HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 3 ), 0 ) AS EBCC_JML_MS,
							NVL( EBCC.F_GET_HASIL_PANEN_NUMBERX( HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 4 ), 0 ) AS EBCC_JML_OR,
							NVL( EBCC.F_GET_HASIL_PANEN_NUMBERX( HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 6 ), 0 ) AS EBCC_JML_BB,
							NVL( EBCC.F_GET_HASIL_PANEN_NUMBERX( HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 15 ), 0 ) AS EBCC_JML_JK,
							NVL( EBCC.F_GET_HASIL_PANEN_NUMBERX( HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 16 ), 0 ) AS EBCC_JML_BA,   
							NVL( EBCC.F_GET_HASIL_PANEN_BRDX ( HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC ), 0 ) AS EBCC_JML_BRD
						FROM (
								SELECT
									HRP.ID_RENCANA AS ID_RENCANA,
									HRP.TANGGAL_RENCANA AS TANGGAL_RENCANA,
									HRP.NIK_KERANI_BUAH AS NIK_KERANI_BUAH,
									DRP.ID_BA_AFD_BLOK AS ID_BA_AFD_BLOK,
									DRP.NO_REKAP_BCC AS NO_REKAP_BCC
								FROM
									EBCC.T_HEADER_RENCANA_PANEN HRP 
									LEFT JOIN EBCC.T_DETAIL_RENCANA_PANEN DRP ON HRP.ID_RENCANA = DRP.ID_RENCANA
							) HDP
							LEFT JOIN EBCC.T_HASIL_PANEN HP ON HP.ID_RENCANA = HDP.ID_RENCANA AND HP.NO_REKAP_BCC = HDP.NO_REKAP_BCC
							LEFT JOIN EBCC.T_BLOK TB ON TB.ID_BA_AFD_BLOK = HDP.ID_BA_AFD_BLOK
							LEFT JOIN EBCC.T_AFDELING TA ON TA.ID_BA_AFD = TB.ID_BA_AFD
							LEFT JOIN EBCC.T_BUSSINESSAREA TBA ON TBA.ID_BA = TA.ID_BA
							LEFT JOIN EBCC.T_EMPLOYEE EMP_EBCC ON EMP_EBCC.NIK = HDP.NIK_KERANI_BUAH 
						WHERE
							HP.NO_BCC = '{$ec->ebcc_no_bcc}'";
							
					$query_ebcc = collect( $this->db_mobile_ins->select( $sql_ebcc ) )->first();

					$joindata[$i]['ebcc_nik_kerani_buah'] = $query_ebcc->nik_kerani_buah;
					$joindata[$i]['ebcc_nama_kerani_buah'] = $query_ebcc->emp_name;
					$joindata[$i]['ebcc_no_bcc'] = $query_ebcc->no_bcc;
					$joindata[$i]['ebcc_jml_bm'] = $query_ebcc->ebcc_jml_bm;
					$joindata[$i]['ebcc_jml_bk'] = $query_ebcc->ebcc_jml_bk;
					$joindata[$i]['ebcc_jml_ms'] = $query_ebcc->ebcc_jml_ms;
					$joindata[$i]['ebcc_jml_or'] = $query_ebcc->ebcc_jml_or;
					$joindata[$i]['ebcc_jml_bb'] = $query_ebcc->ebcc_jml_bb;
					$joindata[$i]['ebcc_jml_jk'] = $query_ebcc->ebcc_jml_jk;
					$joindata[$i]['ebcc_jml_ba'] = $query_ebcc->ebcc_jml_ba;
					$joindata[$i]['ebcc_jml_brd'] = $query_ebcc->ebcc_jml_brd;
					$joindata[$i]['ebcc_jjg_panen'] = $query_ebcc->jjg_panen;
					$joindata[$i]['ebcc_status_tph'] = $query_ebcc->status_tph;
					$joindata[$i]['ebcc_keterangan_qrcode'] = $query_ebcc->keterangan_qrcode;

					$summary_data[$summary_code]['ebcc_jml_bm'] = $summary_data[$summary_code]['ebcc_jml_bm'] + $query_ebcc->ebcc_jml_bm;
					$summary_data[$summary_code]['ebcc_jml_bk'] = $summary_data[$summary_code]['ebcc_jml_bk'] + $query_ebcc->ebcc_jml_bk;
					$summary_data[$summary_code]['ebcc_jml_ms'] = $summary_data[$summary_code]['ebcc_jml_ms'] + $query_ebcc->ebcc_jml_ms;
					$summary_data[$summary_code]['ebcc_jml_or'] = $summary_data[$summary_code]['ebcc_jml_or'] + $query_ebcc->ebcc_jml_or;
					$summary_data[$summary_code]['ebcc_jml_bb'] = $summary_data[$summary_code]['ebcc_jml_bb'] + $query_ebcc->ebcc_jml_bb;
					$summary_data[$summary_code]['ebcc_jml_jk'] = $summary_data[$summary_code]['ebcc_jml_jk'] + $query_ebcc->ebcc_jml_jk;
					$summary_data[$summary_code]['ebcc_jml_ba'] = $summary_data[$summary_code]['ebcc_jml_ba'] + $query_ebcc->ebcc_jml_ba;
					$summary_data[$summary_code]['ebcc_jml_brd'] = $summary_data[$summary_code]['ebcc_jml_brd'] + $query_ebcc->ebcc_jml_brd;
					$summary_data[$summary_code]['ebcc_jjg_panen'] = $summary_data[$summary_code]['ebcc_jjg_panen'] + $query_ebcc->jjg_panen;
					$summary_data[$summary_code]['match'] = $summary_data[$summary_code]['match'] + ( intval( $query_ebcc->jjg_panen ) == intval( $ec->val_total_jjg ) ? 1 : 0 );

					if ( intval( $query_ebcc->jjg_panen ) == intval( $ec->val_total_jjg ) ) {
						$summary_data[$summary_code]['akurasi'] = $summary_data[$summary_code]['akurasi'] + abs( intval( $query_ebcc->ebcc_jml_ms ) - intval( $ec->val_jml_3 ) );
					}

					$joindata[$i]['match_status'] = ( intval( $query_ebcc->jjg_panen ) == intval( $ec->val_total_jjg ) ? 'MATCH' : 'NOT MATCH' );
					$akurasi_kualitas_ms =  abs( $ec->val_jml_3 - $query_ebcc->ebcc_jml_ms );
					$joindata[$i]['akurasi_kualitas_ms'] = ( $akurasi_kualitas_ms > 0 ? $akurasi_kualitas_ms : 0 );
				}
					
				$i++;
			}
		}

		// print '<pre>';
		// print_r( $summary_data );
		// print '</pre>';
		// dd();

		return array(
			"data" => $joindata,
			"summary" => $summary_data
		);
	}
	
	public function EBCC_COMPARE($REPORT_TYPE , $START_DATE , $END_DATE , $REGION_CODE , $COMP_CODE , $BA_CODE , $AFD_CODE , $BLOCK_CODE)
	{
		if ( $REPORT_TYPE == 'EBCC_COMPARE_ESTATE' ) {
			$REPORT_TYPE = 'V';
		}
		else if ( $REPORT_TYPE == 'EBCC_COMPARE_MILL' ) {
			$REPORT_TYPE = 'M';
		}
		$where = "";
		$where_ebcc = "";
		$where_tgl_ebcc = "";

		$START_DATE = date( 'Ymd', strtotime( $START_DATE ) );
		$END_DATE = $END_DATE ? date( 'Ymd', strtotime( $END_DATE ) ) : date( 'Ymd', strtotime( $START_DATE ) );

		//print $START_DATE.'/'.$END_DATE;
		//dd();
		
		$where .= $START_DATE ? " AND TRUNC(INSERT_TIME) BETWEEN TO_DATE('$START_DATE','RRRRMMDD') AND TO_DATE('$END_DATE','RRRRMMDD') ": "";
		$where .= $REGION_CODE ? " AND WERKS IN (SELECT WERKS FROM TAP_DW.TM_EST@DWH_LINK WHERE REGION_CODE = '$REGION_CODE') ": "";
		$where .= $COMP_CODE ? " AND SUBSTR(WERKS,1,2) = '$COMP_CODE' ": "";
		$where .= $BA_CODE ? " AND WERKS = '$BA_CODE'  ": "";
		$where_tgl_ebcc .= $START_DATE ? " AND TRUNC(TANGGAL_RENCANA) BETWEEN TO_DATE('$START_DATE','RRRRMMDD') AND TO_DATE('$END_DATE','RRRRMMDD') ": "";
		$where_ebcc .= $REGION_CODE ? " AND TBA.ID_BA IN (SELECT WERKS FROM TAP_DW.TM_EST@DWH_LINK WHERE REGION_CODE = '$REGION_CODE') ": "";
		$where_ebcc .= $COMP_CODE ? " AND SUBSTR(TBA.ID_BA,1,2) = '$COMP_CODE' ": "";
		$where_ebcc .= $BA_CODE ? " AND TBA.ID_BA = '$BA_CODE'  ": "";
		
		$sql = "SELECT 	*
FROM (
	SELECT 	TRUNC(SAMPLING_EBCC.INSERT_TIME) as SAMPLING_TGL,
			USER_AUTH.EMPLOYEE_NIK as SAMPLING_NIK_PELAKU,
			EMP_NAME.EMPLOYEE_FULLNAME as SAMPLING_NAMA_PELAKU,
			USER_AUTH.USER_ROLE as SAMPLING_ROLE_PELAKU,
			SAMPLING_EBCC.WERKS as SAMPLING_BA_CODE, 
			EST.EST_NAME as SAMPLING_BA_NAME,
			SAMPLING_EBCC.AFD_CODE as SAMPLING_AFD_CODE, 
			TO_CHAR(SAMPLING_EBCC.BLOCK_CODE, '000') as SAMPLING_BLOCK_CODE, 
			MS_BLOCK.BLOCK_NAME as SAMPLING_BLOCK_NAME,
			TO_CHAR(SAMPLING_EBCC.NO_TPH, '000') as SAMPLING_NO_TPH, 
			SAMPLING_EBCC.EBCC_VALIDATION_CODE as SAMPLING_CODE,
			CASE
				WHEN SAMPLING_EBCC.STATUS_TPH_SCAN = 'AUTOMATIC'
				THEN 'SCAN'
				WHEN SAMPLING_EBCC.STATUS_TPH_SCAN = 'MANUAL' AND SAMPLING_EBCC.ALASAN_MANUAL = '1'
				THEN 'MANUAL - QR Code TPH Hilang'
				WHEN SAMPLING_EBCC.STATUS_TPH_SCAN = 'MANUAL' AND SAMPLING_EBCC.ALASAN_MANUAL = '2'
				THEN 'MANUAL - QR Code TPH Rusak'
			END as SAMPLING_STATUS_QRCODE_TPH,
			SAMPLING_EBCC.JML_BM as SAMPLING_JML_BM,
			SAMPLING_EBCC.JML_BK as SAMPLING_JML_BK,
			SAMPLING_EBCC.JML_MS as SAMPLING_JML_MS,
			SAMPLING_EBCC.JML_OR as SAMPLING_JML_OR,
			SAMPLING_EBCC.JML_BB as SAMPLING_JML_BB,
			SAMPLING_EBCC.JML_JK as SAMPLING_JML_JK,
			SAMPLING_EBCC.JML_BA as SAMPLING_JML_BA,
			SAMPLING_EBCC.JML_BRD as SAMPLING_JML_BRD,
			SAMPLING_EBCC.SAMPLING_TOTAL_JJG,
			EBCC.EBCC_NIK_PELAKU,
			EBCC.EBCC_NAMA_PELAKU,
			EBCC.EBCC_CODE,
			EBCC.EBCC_STATUS_QRCODE_TPH,
			EBCC.EBCC_JML_BM,
			EBCC.EBCC_JML_BK,
			EBCC.EBCC_JML_MS,
			EBCC.EBCC_JML_OR,
			EBCC.EBCC_JML_BB,
			EBCC.EBCC_JML_JK,
			EBCC.EBCC_JML_BA,   
			EBCC.EBCC_TOTAL_JJG_PANEN,
			'' as LINK_FOTO,
			CASE
				WHEN NVL(SAMPLING_EBCC.SAMPLING_TOTAL_JJG,0) = NVL(EBCC.EBCC_TOTAL_JJG_PANEN,0)
				THEN 'MATCH'
				ELSE 'NOT MATCH'
			END as AKURASI_SAMPLING,
			CASE
				WHEN NVL(SAMPLING_EBCC.SAMPLING_TOTAL_JJG,0) = NVL(EBCC.EBCC_TOTAL_JJG_PANEN,0)
				THEN ABS(SAMPLING_EBCC.JML_MS - EBCC.EBCC_JML_MS)
				ELSE NULL
			END as AKURASI_MS
	FROM (
		SELECT 	SAMPLING_EBCC.*,
				( JML_BM + JML_BK + JML_MS + JML_OR + JML_BB + JML_JK + JML_BA) as SAMPLING_TOTAL_JJG
		FROM (
			SELECT 	*
			FROM (		
				SELECT 	EBCC_HEADER.INSERT_TIME,
						EBCC_HEADER.INSERT_USER,
						EBCC_HEADER.WERKS, 
						EBCC_HEADER.AFD_CODE, 
						EBCC_HEADER.BLOCK_CODE, 
						EBCC_HEADER.NO_TPH, 
						EBCC_HEADER.EBCC_VALIDATION_CODE,
						EBCC_HEADER.STATUS_TPH_SCAN,
						EBCC_HEADER.ALASAN_MANUAL,
						EBCC_DETAIL.ID_KUALITAS, 
						EBCC_DETAIL.JUMLAH
				FROM (
					SELECT 	INSERT_TIME,
							INSERT_USER,
							WERKS, 
							AFD_CODE, 
							BLOCK_CODE, 
							NO_TPH, 
							EBCC_VALIDATION_CODE,
							STATUS_TPH_SCAN,
							ALASAN_MANUAL
					FROM MOBILE_INSPECTION.TR_EBCC_VALIDATION_H 
					WHERE SUBSTR(EBCC_VALIDATION_CODE,1,1) = '$REPORT_TYPE' 
						$where
				) EBCC_HEADER
				LEFT JOIN MOBILE_INSPECTION.TR_EBCC_VALIDATION_D EBCC_DETAIL
					ON EBCC_HEADER.EBCC_VALIDATION_CODE = EBCC_DETAIL.EBCC_VALIDATION_CODE
			)
			PIVOT (
				MAX( NVL(JUMLAH,0) )
				FOR ID_KUALITAS IN ( 
					'1' AS JML_BM,
					'2' AS JML_BK,
					'3' AS JML_MS,
					'4' AS JML_OR,
					'6' AS JML_BB,
					'15' AS JML_JK,
					'16' AS JML_BA,
					'5' AS JML_BRD
				)
			)
		) SAMPLING_EBCC
	) SAMPLING_EBCC
	LEFT JOIN MOBILE_INSPECTION.TM_USER_AUTH USER_AUTH 
		ON TO_CHAR(USER_AUTH.USER_AUTH_CODE, '0000') = TO_CHAR(SAMPLING_EBCC.INSERT_USER, '0000')
	LEFT JOIN (
		SELECT	EMPLOYEE_NIK,
				EMPLOYEE_FULLNAME,
				EMPLOYEE_JOINDATE as START_DATE,
				CASE 
					WHEN EMPLOYEE_RESIGNDATE IS NULL
					THEN TO_DATE('99991231', 'RRRRMMDD')
					ELSE EMPLOYEE_RESIGNDATE
				END as END_DATE
		FROM TAP_DW.TM_EMPLOYEE_HRIS@DWH_LINK 
		UNION ALL
		SELECT 	NIK, 
				EMPLOYEE_NAME,
				START_VALID,
				CASE
					WHEN RES_DATE IS NOT NULL 
					THEN RES_DATE
					ELSE END_VALID
				END END_VALID
		FROM TAP_DW.TM_EMPLOYEE_SAP@DWH_LINK
	) EMP_NAME
		ON EMP_NAME.EMPLOYEE_NIK = USER_AUTH.EMPLOYEE_NIK
		AND TRUNC(SAMPLING_EBCC.INSERT_TIME) BETWEEN TRUNC(EMP_NAME.START_DATE) AND TRUNC(EMP_NAME.END_DATE)
	LEFT JOIN TAP_DW.TM_EST@DWH_LINK EST 
		ON EST.WERKS = SAMPLING_EBCC.WERKS
		AND TRUNC(SAMPLING_EBCC.INSERT_TIME) BETWEEN EST.START_VALID AND EST.END_VALID
	LEFT JOIN TAP_DW.TM_BLOCK@DWH_LINK MS_BLOCK
		ON MS_BLOCK.WERKS = SAMPLING_EBCC.WERKS
		AND MS_BLOCK.AFD_CODE = SAMPLING_EBCC.AFD_CODE
		AND TO_CHAR(MS_BLOCK.BLOCK_CODE, '000') = TO_CHAR(SAMPLING_EBCC.BLOCK_CODE, '000')
		AND TRUNC(SAMPLING_EBCC.INSERT_TIME) BETWEEN MS_BLOCK.START_VALID AND MS_BLOCK.END_VALID
	LEFT JOIN (	
		SELECT	HDP.NIK_KERANI_BUAH as EBCC_NIK_PELAKU,
				EMP_EBCC.EMP_NAME as EBCC_NAMA_PELAKU,
				HP.NO_BCC as EBCC_CODE,
				CASE
					WHEN HP.STATUS_TPH IS NULL
					THEN 'N/A'
					WHEN HP.STATUS_TPH = 'AUTOMATIC'
					THEN 'SCAN'
					WHEN HP.STATUS_TPH = 'MANUAL' AND HP.KETERANGAN_QRCODE = '1'
					THEN 'MANUAL - QR Code TPH Hilang'
					WHEN HP.STATUS_TPH = 'MANUAL' AND HP.KETERANGAN_QRCODE = '2'
					THEN 'MANUAL - QR Code TPH Rusak'
					ELSE HP.STATUS_TPH
				END as EBCC_STATUS_QRCODE_TPH,
				NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 1), 0) AS EBCC_JML_BM,
				NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 2), 0) AS EBCC_JML_BK,
				NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 3), 0) AS EBCC_JML_MS,
				NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 4), 0) AS EBCC_JML_OR,
				NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 6), 0) AS EBCC_JML_BB,
				NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 15), 0) AS EBCC_JML_JK,
				NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 16), 0) AS EBCC_JML_BA,   
				NVL( EBCC.F_GET_HASIL_PANEN_BUNCH ( TBA.ID_BA, HP.NO_REKAP_BCC, HP.NO_BCC, 'BUNCH_HARVEST' ), 0 ) as EBCC_TOTAL_JJG_PANEN,
				TBA.ID_BA as WERKS,
				TA.ID_AFD as AFD_CODE,
				TB.ID_BLOK as BLOCK_CODE,
				HDP.TANGGAL_RENCANA as TGL_EBCC,
				HP.NO_TPH
		FROM (	
			SELECT 	HRP.ID_RENCANA AS ID_RENCANA,
					HRP.TANGGAL_RENCANA AS TANGGAL_RENCANA,
					HRP.NIK_KERANI_BUAH AS NIK_KERANI_BUAH,
					DRP.ID_BA_AFD_BLOK AS ID_BA_AFD_BLOK,
					DRP.NO_REKAP_BCC AS NO_REKAP_BCC
			FROM (
				SELECT *
				FROM EBCC.T_HEADER_RENCANA_PANEN 
				WHERE 1=1 
					$where_tgl_ebcc
			) HRP 
			LEFT JOIN EBCC.T_DETAIL_RENCANA_PANEN DRP 
				ON HRP.ID_RENCANA = DRP.ID_RENCANA
		) HDP
		LEFT JOIN EBCC.T_HASIL_PANEN HP 
			ON HP.ID_RENCANA = HDP.ID_RENCANA AND HP.NO_REKAP_BCC = HDP.NO_REKAP_BCC
		LEFT JOIN EBCC.T_BLOK TB 
			ON TB.ID_BA_AFD_BLOK = HDP.ID_BA_AFD_BLOK
		LEFT JOIN EBCC.T_AFDELING TA 
			ON TA.ID_BA_AFD = TB.ID_BA_AFD
		LEFT JOIN EBCC.T_BUSSINESSAREA TBA 
			ON TBA.ID_BA = TA.ID_BA
		LEFT JOIN EBCC.T_EMPLOYEE EMP_EBCC 
			ON EMP_EBCC.NIK = HDP.NIK_KERANI_BUAH
		WHERE 1=1
			$where_ebcc 
	) EBCC
		ON EBCC.WERKS = SAMPLING_EBCC.WERKS
		AND EBCC.AFD_CODE = SAMPLING_EBCC.AFD_CODE
		AND TO_CHAR(EBCC.BLOCK_CODE, '000') = TO_CHAR(SAMPLING_EBCC.BLOCK_CODE, '000')
		AND TO_CHAR(EBCC.NO_TPH, '000') = TO_CHAR(SAMPLING_EBCC.NO_TPH, '000')
		AND TRUNC(EBCC.TGL_EBCC) = TRUNC(SAMPLING_EBCC.INSERT_TIME)
		AND EBCC.EBCC_TOTAL_JJG_PANEN = SAMPLING_EBCC.SAMPLING_TOTAL_JJG
	UNION ALL
	SELECT 	TRUNC(SUMMARY_DATA.SAMPLING_TGL) as SAMPLING_TGL,
			SUMMARY_DATA.SAMPLING_NIK_PELAKU,
			SUMMARY_DATA.SAMPLING_NAMA_PELAKU,
			SUMMARY_DATA.SAMPLING_ROLE_PELAKU,
			'TOTAL' as SAMPLING_BA_CODE, 
			'' as SAMPLING_BA_NAME,
			'' as SAMPLING_AFD_CODE, 
			'' as SAMPLING_BLOCK_CODE, 
			'' as SAMPLING_BLOCK_NAME,
			'' as SAMPLING_NO_TPH, 
			'' as SAMPLING_CODE,
			'' as SAMPLING_STATUS_QRCODE_TPH,
			SUM(SUMMARY_DATA.SAMPLING_JML_BM) as SAMPLING_JML_BM,
			SUM(SUMMARY_DATA.SAMPLING_JML_BK) as SAMPLING_JML_BK,
			SUM(SUMMARY_DATA.SAMPLING_JML_MS) as SAMPLING_JML_MS,
			SUM(SUMMARY_DATA.SAMPLING_JML_OR) as SAMPLING_JML_OR,
			SUM(SUMMARY_DATA.SAMPLING_JML_BB) as SAMPLING_JML_BB,
			SUM(SUMMARY_DATA.SAMPLING_JML_JK) as SAMPLING_JML_JK,
			SUM(SUMMARY_DATA.SAMPLING_JML_BA) as SAMPLING_JML_BA,
			SUM(SUMMARY_DATA.SAMPLING_JML_BRD) as SAMPLING_JML_BRD,
			SUM(SUMMARY_DATA.SAMPLING_TOTAL_JJG) as SAMPLING_TOTAL_JJG,
			'' as EBCC_NIK_PELAKU,
			'' as EBCC_NAMA_PELAKU,
			'' as EBCC_CODE,
			'' as EBCC_STATUS_QRCODE_TPH,
			SUM(SUMMARY_DATA.EBCC_JML_BM) as EBCC_JML_BM,
			SUM(SUMMARY_DATA.EBCC_JML_BK) as EBCC_JML_BK,
			SUM(SUMMARY_DATA.EBCC_JML_MS) as EBCC_JML_MS,
			SUM(SUMMARY_DATA.EBCC_JML_OR) as EBCC_JML_OR,
			SUM(SUMMARY_DATA.EBCC_JML_BB) as EBCC_JML_BB,
			SUM(SUMMARY_DATA.EBCC_JML_JK) as EBCC_JML_JK,
			SUM(SUMMARY_DATA.EBCC_JML_BA) as EBCC_JML_BA,   
			SUM(SUMMARY_DATA.EBCC_TOTAL_JJG_PANEN) as EBCC_TOTAL_JJG_PANEN,
			'' as LINK_FOTO,
			TO_CHAR((SUM(AKURASI_SAMPLING_MATCH) / COUNT(1) * 100), '00.00') as AKURASI_SAMPLING,
			((SUM(SUMMARY_DATA.SAMPLING_TOTAL_JJG) - SUM(AKURASI_MS)) / SUM(SUMMARY_DATA.SAMPLING_TOTAL_JJG) * 100)  as AKURASI_MS
	FROM (
		SELECT 	SAMPLING_EBCC.INSERT_TIME as SAMPLING_TGL,
				USER_AUTH.EMPLOYEE_NIK as SAMPLING_NIK_PELAKU,
				EMP_NAME.EMPLOYEE_FULLNAME as SAMPLING_NAMA_PELAKU,
				USER_AUTH.USER_ROLE as SAMPLING_ROLE_PELAKU,
				SAMPLING_EBCC.WERKS as SAMPLING_BA_CODE, 
				EST.EST_NAME as SAMPLING_BA_NAME,
				SAMPLING_EBCC.AFD_CODE as SAMPLING_AFD_CODE, 
				TO_CHAR(SAMPLING_EBCC.BLOCK_CODE, '000') as SAMPLING_BLOCK_CODE, 
				MS_BLOCK.BLOCK_NAME as SAMPLING_BLOCK_NAME,
				TO_CHAR(SAMPLING_EBCC.NO_TPH, '000') as SAMPLING_NO_TPH, 
				SAMPLING_EBCC.EBCC_VALIDATION_CODE as SAMPLING_CODE,
				CASE
					WHEN SAMPLING_EBCC.STATUS_TPH_SCAN = 'AUTOMATIC'
					THEN 'SCAN'
					WHEN SAMPLING_EBCC.STATUS_TPH_SCAN = 'MANUAL' AND SAMPLING_EBCC.ALASAN_MANUAL = '1'
					THEN 'MANUAL - QR Code TPH Hilang'
					WHEN SAMPLING_EBCC.STATUS_TPH_SCAN = 'MANUAL' AND SAMPLING_EBCC.ALASAN_MANUAL = '2'
					THEN 'MANUAL - QR Code TPH Rusak'
				END as SAMPLING_STATUS_QRCODE_TPH,
				SAMPLING_EBCC.JML_BM as SAMPLING_JML_BM,
				SAMPLING_EBCC.JML_BK as SAMPLING_JML_BK,
				SAMPLING_EBCC.JML_MS as SAMPLING_JML_MS,
				SAMPLING_EBCC.JML_OR as SAMPLING_JML_OR,
				SAMPLING_EBCC.JML_BB as SAMPLING_JML_BB,
				SAMPLING_EBCC.JML_JK as SAMPLING_JML_JK,
				SAMPLING_EBCC.JML_BA as SAMPLING_JML_BA,
				SAMPLING_EBCC.JML_BRD as SAMPLING_JML_BRD,
				SAMPLING_EBCC.SAMPLING_TOTAL_JJG,
				EBCC.EBCC_NIK_PELAKU,
				EBCC.EBCC_NAMA_PELAKU,
				EBCC.EBCC_CODE,
				EBCC.EBCC_STATUS_QRCODE_TPH,
				EBCC.EBCC_JML_BM,
				EBCC.EBCC_JML_BK,
				EBCC.EBCC_JML_MS,
				EBCC.EBCC_JML_OR,
				EBCC.EBCC_JML_BB,
				EBCC.EBCC_JML_JK,
				EBCC.EBCC_JML_BA,   
				EBCC.EBCC_TOTAL_JJG_PANEN,
				'' as LINK_FOTO,
				CASE
					WHEN NVL(SAMPLING_EBCC.SAMPLING_TOTAL_JJG,0) = NVL(EBCC.EBCC_TOTAL_JJG_PANEN,0)
					THEN 'MATCH'
					ELSE 'NOT MATCH'
				END as AKURASI_SAMPLING,
				CASE
					WHEN NVL(SAMPLING_EBCC.SAMPLING_TOTAL_JJG,0) = NVL(EBCC.EBCC_TOTAL_JJG_PANEN,0)
					THEN 1
					ELSE 0
				END as AKURASI_SAMPLING_MATCH,
				CASE
					WHEN NVL(SAMPLING_EBCC.SAMPLING_TOTAL_JJG,0) = NVL(EBCC.EBCC_TOTAL_JJG_PANEN,0)
					THEN ABS(SAMPLING_EBCC.JML_MS - EBCC.EBCC_JML_MS)
					ELSE NULL
				END as AKURASI_MS
		FROM (
			SELECT 	SAMPLING_EBCC.*,
					( JML_BM + JML_BK + JML_MS + JML_OR + JML_BB + JML_JK + JML_BA) as SAMPLING_TOTAL_JJG
			FROM (
				SELECT 	*
				FROM (		
					SELECT 	EBCC_HEADER.INSERT_TIME,
							EBCC_HEADER.INSERT_USER,
							EBCC_HEADER.WERKS, 
							EBCC_HEADER.AFD_CODE, 
							EBCC_HEADER.BLOCK_CODE, 
							EBCC_HEADER.NO_TPH, 
							EBCC_HEADER.EBCC_VALIDATION_CODE,
							EBCC_HEADER.STATUS_TPH_SCAN,
							EBCC_HEADER.ALASAN_MANUAL,
							EBCC_DETAIL.ID_KUALITAS, 
							EBCC_DETAIL.JUMLAH
					FROM (
						SELECT 	INSERT_TIME,
								INSERT_USER,
								WERKS, 
								AFD_CODE, 
								BLOCK_CODE, 
								NO_TPH, 
								EBCC_VALIDATION_CODE,
								STATUS_TPH_SCAN,
								ALASAN_MANUAL
						FROM MOBILE_INSPECTION.TR_EBCC_VALIDATION_H 
						WHERE SUBSTR(EBCC_VALIDATION_CODE,1,1) = '$REPORT_TYPE' 
							$where
					) EBCC_HEADER
					LEFT JOIN MOBILE_INSPECTION.TR_EBCC_VALIDATION_D EBCC_DETAIL
						ON EBCC_HEADER.EBCC_VALIDATION_CODE = EBCC_DETAIL.EBCC_VALIDATION_CODE
				)
				PIVOT (
					MAX( NVL(JUMLAH,0) )
					FOR ID_KUALITAS IN ( 
						'1' AS JML_BM,
						'2' AS JML_BK,
						'3' AS JML_MS,
						'4' AS JML_OR,
						'6' AS JML_BB,
						'15' AS JML_JK,
						'16' AS JML_BA,
						'5' AS JML_BRD
					)
				)
			) SAMPLING_EBCC
		) SAMPLING_EBCC
		LEFT JOIN MOBILE_INSPECTION.TM_USER_AUTH USER_AUTH 
			ON TO_CHAR(USER_AUTH.USER_AUTH_CODE, '0000') = TO_CHAR(SAMPLING_EBCC.INSERT_USER, '0000')
		LEFT JOIN (
			SELECT	EMPLOYEE_NIK,
					EMPLOYEE_FULLNAME,
					EMPLOYEE_JOINDATE as START_DATE,
					CASE 
						WHEN EMPLOYEE_RESIGNDATE IS NULL
						THEN TO_DATE('99991231', 'RRRRMMDD')
						ELSE EMPLOYEE_RESIGNDATE
					END as END_DATE
			FROM TAP_DW.TM_EMPLOYEE_HRIS@DWH_LINK 
			UNION ALL
			SELECT 	NIK, 
					EMPLOYEE_NAME,
					START_VALID,
					CASE
						WHEN RES_DATE IS NOT NULL 
						THEN RES_DATE
						ELSE END_VALID
					END END_VALID
			FROM TAP_DW.TM_EMPLOYEE_SAP@DWH_LINK
		) EMP_NAME
			ON EMP_NAME.EMPLOYEE_NIK = USER_AUTH.EMPLOYEE_NIK
			AND TRUNC(SAMPLING_EBCC.INSERT_TIME) BETWEEN TRUNC(EMP_NAME.START_DATE) AND TRUNC(EMP_NAME.END_DATE)
		LEFT JOIN TAP_DW.TM_EST@DWH_LINK EST 
			ON EST.WERKS = SAMPLING_EBCC.WERKS
			AND TRUNC(SAMPLING_EBCC.INSERT_TIME) BETWEEN EST.START_VALID AND EST.END_VALID
		LEFT JOIN TAP_DW.TM_BLOCK@DWH_LINK MS_BLOCK
			ON MS_BLOCK.WERKS = SAMPLING_EBCC.WERKS
			AND MS_BLOCK.AFD_CODE = SAMPLING_EBCC.AFD_CODE
			AND TO_CHAR(MS_BLOCK.BLOCK_CODE, '000') = TO_CHAR(SAMPLING_EBCC.BLOCK_CODE, '000')
			AND TRUNC(SAMPLING_EBCC.INSERT_TIME) BETWEEN MS_BLOCK.START_VALID AND MS_BLOCK.END_VALID
		LEFT JOIN (	
			SELECT	HDP.NIK_KERANI_BUAH as EBCC_NIK_PELAKU,
					EMP_EBCC.EMP_NAME as EBCC_NAMA_PELAKU,
					HP.NO_BCC as EBCC_CODE,
					CASE
						WHEN HP.STATUS_TPH IS NULL
						THEN 'N/A'
						WHEN HP.STATUS_TPH = 'AUTOMATIC'
						THEN 'SCAN'
						WHEN HP.STATUS_TPH = 'MANUAL' AND HP.KETERANGAN_QRCODE = '1'
						THEN 'MANUAL - QR Code TPH Hilang'
						WHEN HP.STATUS_TPH = 'MANUAL' AND HP.KETERANGAN_QRCODE = '2'
						THEN 'MANUAL - QR Code TPH Rusak'
						ELSE HP.STATUS_TPH
					END as EBCC_STATUS_QRCODE_TPH,
					NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 1), 0) AS EBCC_JML_BM,
					NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 2), 0) AS EBCC_JML_BK,
					NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 3), 0) AS EBCC_JML_MS,
					NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 4), 0) AS EBCC_JML_OR,
					NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 6), 0) AS EBCC_JML_BB,
					NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 15), 0) AS EBCC_JML_JK,
					NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 16), 0) AS EBCC_JML_BA,   
					NVL( EBCC.F_GET_HASIL_PANEN_BUNCH ( TBA.ID_BA, HP.NO_REKAP_BCC, HP.NO_BCC, 'BUNCH_HARVEST' ), 0 ) as EBCC_TOTAL_JJG_PANEN,
					TBA.ID_BA as WERKS,
					TA.ID_AFD as AFD_CODE,
					TB.ID_BLOK as BLOCK_CODE,
					HDP.TANGGAL_RENCANA as TGL_EBCC,
					HP.NO_TPH
			FROM (	
				SELECT 	HRP.ID_RENCANA AS ID_RENCANA,
						HRP.TANGGAL_RENCANA AS TANGGAL_RENCANA,
						HRP.NIK_KERANI_BUAH AS NIK_KERANI_BUAH,
						DRP.ID_BA_AFD_BLOK AS ID_BA_AFD_BLOK,
						DRP.NO_REKAP_BCC AS NO_REKAP_BCC
				FROM (
					SELECT *
					FROM EBCC.T_HEADER_RENCANA_PANEN 
					WHERE 1=1 
						$where_tgl_ebcc
				) HRP 
				LEFT JOIN EBCC.T_DETAIL_RENCANA_PANEN DRP 
					ON HRP.ID_RENCANA = DRP.ID_RENCANA
			) HDP
			LEFT JOIN EBCC.T_HASIL_PANEN HP 
				ON HP.ID_RENCANA = HDP.ID_RENCANA AND HP.NO_REKAP_BCC = HDP.NO_REKAP_BCC
			LEFT JOIN EBCC.T_BLOK TB 
				ON TB.ID_BA_AFD_BLOK = HDP.ID_BA_AFD_BLOK
			LEFT JOIN EBCC.T_AFDELING TA 
				ON TA.ID_BA_AFD = TB.ID_BA_AFD
			LEFT JOIN EBCC.T_BUSSINESSAREA TBA 
				ON TBA.ID_BA = TA.ID_BA
			LEFT JOIN EBCC.T_EMPLOYEE EMP_EBCC 
				ON EMP_EBCC.NIK = HDP.NIK_KERANI_BUAH
			WHERE 1=1
				$where_ebcc
		) EBCC
			ON EBCC.WERKS = SAMPLING_EBCC.WERKS
			AND EBCC.AFD_CODE = SAMPLING_EBCC.AFD_CODE
			AND TO_CHAR(EBCC.BLOCK_CODE, '000') = TO_CHAR(SAMPLING_EBCC.BLOCK_CODE, '000')
			AND TO_CHAR(EBCC.NO_TPH, '000') = TO_CHAR(SAMPLING_EBCC.NO_TPH, '000')
			AND TRUNC(EBCC.TGL_EBCC) = TRUNC(SAMPLING_EBCC.INSERT_TIME)
			AND EBCC.EBCC_TOTAL_JJG_PANEN = SAMPLING_EBCC.SAMPLING_TOTAL_JJG
	) SUMMARY_DATA
	GROUP BY TRUNC(SUMMARY_DATA.SAMPLING_TGL),
			SUMMARY_DATA.SAMPLING_NIK_PELAKU,
			SUMMARY_DATA.SAMPLING_NAMA_PELAKU,
			SUMMARY_DATA.SAMPLING_ROLE_PELAKU
)
ORDER BY 	SAMPLING_TGL,
			SAMPLING_NIK_PELAKU,
			SAMPLING_BA_CODE, 
			SAMPLING_AFD_CODE, 
			SAMPLING_BLOCK_CODE, 
			SAMPLING_NO_TPH";
		// print_r($sql);die;
		$get = $this->db_mobile_ins->select( $sql );

		return $get;
	}

}