<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ReportOracle extends Model
{
	
	protected $env;
	protected $db_mobile_ins;
	
	public function __construct() {
		$this->db_mobile_ins = DB::connection('mobile_ins');
	}
	
    public function EBCC_VALIDATION_ESTATE_HEAD()
	{
		$get = $this->db_mobile_ins->select("
				SELECT
					KUALITAS.ID_KUALITAS,
					KUALITAS.NAMA_KUALITAS
				FROM
					TAP_DW.T_KUALITAS_PANEN@PRODDW_LINK KUALITAS
				WHERE
					KUALITAS.ACTIVE_STATUS = 'YES' and KUALITAS.ID_KUALITAS not in ('14','11','12','13')
				ORDER BY 
					KUALITAS.GROUP_KUALITAS ASC,
					KUALITAS.UOM ASC,
					KUALITAS.NAMA_KUALITAS ASC
		");
		return $get;
	}
	
    public function EBCC_VALIDATION($REPORT_TYPE , $START_DATE , $END_DATE , $REGION_CODE , $COMP_CODE , $BA_CODE , $AFD_CODE , $BLOCK_CODE)
	{
		$where = "";
		
		if($REPORT_TYPE){
			if($REPORT_TYPE=='EBCC_VALIDATION_ESTATE'){
				$REPORT_TYPE = 'V';
			}else{
				$REPORT_TYPE = 'M';
			}
		}	


		$where .= ( $REGION_CODE != "" && $COMP_CODE == "" ) ? " and EST.REGION_CODE = '$REGION_CODE'  ": "";
		$where .= ( $COMP_CODE != "" && $BA_CODE == "" ) ? " and EST.COMP_CODE = '$COMP_CODE'  ": "";
		$where .= ( $BA_CODE != "" && $AFD_CODE == "" ) ? " and EBCC_HEADER.WERKS = '$BA_CODE'  ": "";
		$where .= ( $AFD_CODE != "" && $BLOCK_CODE == "" ) ? " and EBCC_HEADER.WERKS||EBCC_HEADER.AFD_CODE = '$AFD_CODE'  ": "";
		$where .= ( $AFD_CODE != "" && $BLOCK_CODE != "" ) ? " and EBCC_HEADER.WERKS||EBCC_HEADER.AFD_CODE||EBCC_HEADER.BLOCK_CODE = '$BLOCK_CODE'  ": "";
		
		$START_DATE = date( 'Y-m-d', strtotime( $START_DATE ) );
		$END_DATE = date( 'Y-m-d', strtotime( $END_DATE ) );
		$sql = "
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
			        WHEN EBCC_VAL.VAL_ALASAN_MANUAL IS NULL THEN 'AUTOMATIC'
			        ELSE
			            CASE
			                WHEN EBCC_VAL.VAL_ALASAN_MANUAL = '1' THEN 'MANUAL - QR Codenya Hilang'
			                WHEN EBCC_VAL.VAL_ALASAN_MANUAL = '2' THEN 'MANUAL - QR Codenya Rusak'
			        END
			    END AS VAL_ALASAN_MANUAL,
			    EBCC_VAL.VAL_AFD_CODE,
			    EBCC_VAL.VAL_BLOCK_CODE,
			    EBCC_VAL.VAL_DATE_TIME AS VAL_DATE_TIME,
			    EBCC_VAL.VAL_BLOCK_NAME,
			    EBCC_VAL.VAL_TPH_CODE,
			    EBCC_VAL.VAL_DELIVERY_TICKET,
			    NVL( SUM( EBCC_VAL.JML_1 ), 0 ) AS VAL_JML_1,
			    NVL( SUM( EBCC_VAL.JML_2), 0 ) AS VAL_JML_2,
			    NVL( SUM( EBCC_VAL.JML_3 ), 0 ) AS VAL_JML_3,
			    NVL( SUM( EBCC_VAL.JML_4 ), 0 ) AS VAL_JML_4,
			    NVL( SUM( EBCC_VAL.JML_5 ), 0 ) AS VAL_JML_5,
			    NVL( SUM( EBCC_VAL.JML_6 ), 0 ) AS VAL_JML_6,
			    NVL( SUM( EBCC_VAL.JML_7 ), 0 ) AS VAL_JML_7,
			    NVL( SUM( EBCC_VAL.JML_8 ), 0 ) AS VAL_JML_8,
			    NVL( SUM( EBCC_VAL.JML_9 ), 0 ) AS VAL_JML_9,
			    NVL( SUM( EBCC_VAL.JML_10 ), 0 ) AS VAL_JML_10,
			    NVL( SUM( EBCC_VAL.JML_11 ), 0 ) AS VAL_JML_11,
			    NVL( SUM( EBCC_VAL.JML_12 ), 0 ) AS VAL_JML_12,
			    NVL( SUM( EBCC_VAL.JML_13 ), 0 ) AS VAL_JML_13,
			    NVL( SUM( EBCC_VAL.JML_14 ), 0 ) AS VAL_JML_14,
			    NVL( SUM( EBCC_VAL.JML_15 ), 0 ) AS VAL_JML_15,
			    NVL( SUM( EBCC_VAL.JML_16 ), 0 ) AS VAL_JML_16,
			    (
			        NVL( SUM( EBCC_VAL.JML_1 ), 0 ) + 
			        NVL( SUM( EBCC_VAL.JML_2 ), 0 ) + 
			        NVL( SUM( EBCC_VAL.JML_3 ), 0 ) + 
			        NVL( SUM( EBCC_VAL.JML_4 ), 0 ) + 
			        NVL( SUM( EBCC_VAL.JML_6 ), 0 ) +
			        NVL( SUM( EBCC_VAL.JML_15 ), 0 ) +
			        NVL( SUM( EBCC_VAL.JML_16 ), 0 )
			    ) AS VAL_TOTAL_JJG
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
			            HRIS.EMPLOYEE_FULLNAME AS VAL_NAMA_VALIDATOR,
			            HRIS.EMPLOYEE_POSITION AS VAL_JABATAN_VALIDATOR,
			            LAND_USE.MATURITY_STATUS AS VAL_MATURITY_STATUS,
			            LAND_USE.SPMON AS VAL_SPMON,
			            EBCC_DETAIL.JML_1,
			            EBCC_DETAIL.JML_2,
			            EBCC_DETAIL.JML_3,
			            EBCC_DETAIL.JML_4,
			            EBCC_DETAIL.JML_5,
			            EBCC_DETAIL.JML_6,
			            EBCC_DETAIL.JML_7,
			            EBCC_DETAIL.JML_8,
			            EBCC_DETAIL.JML_9,
			            EBCC_DETAIL.JML_10,
			            EBCC_DETAIL.JML_11,
			            EBCC_DETAIL.JML_12,
			            EBCC_DETAIL.JML_13,
			            EBCC_DETAIL.JML_14,
			            EBCC_DETAIL.JML_15,
			            EBCC_DETAIL.JML_16,
			            SUBBLOCK.BLOCK_NAME AS VAL_BLOCK_NAME,
			            EBCC_HEADER.LAT_TPH AS VAL_LAT_TPH,
			            EBCC_HEADER.LAT_TPH AS VAL_LON_TPH
			        FROM
			            MOBILE_INSPECTION.TR_EBCC_VALIDATION_H EBCC_HEADER
			            LEFT JOIN MOBILE_INSPECTION.TM_USER_AUTH USER_AUTH ON 
			                USER_AUTH.USER_AUTH_CODE = EBCC_HEADER.INSERT_USER
			            LEFT JOIN TAP_DW.TM_EMPLOYEE_HRIS@PRODDW_LINK HRIS ON
			                HRIS.EMPLOYEE_NIK = USER_AUTH.EMPLOYEE_NIK
			            LEFT JOIN TAP_DW.TM_EST@PRODDW_LINK EST ON 
			                EST.WERKS = EBCC_HEADER.WERKS 
			                AND SYSDATE BETWEEN EST.START_VALID AND EST.END_VALID
			            LEFT JOIN (
			                SELECT
			                    WERKS,
			                    AFD_CODE,
			                    BLOCK_CODE,
			                    BLOCK_NAME,
			                    MATURITY_STATUS,
			                    MAX( SPMON ) AS SPMON
			                FROM
			                    TAP_DW.TR_HS_LAND_USE@PRODDW_LINK
			                WHERE
			                    1 = 1
			                    AND MATURITY_STATUS IS NOT NULL
			                GROUP BY
			                    WERKS,
			                    AFD_CODE,
			                    BLOCK_CODE,
			                    BLOCK_NAME,
			                    MATURITY_STATUS
			                ORDER BY
			                    SPMON DESC
			            ) LAND_USE
			                ON LAND_USE.WERKS = EBCC_HEADER.WERKS
			                AND LAND_USE.AFD_CODE = EBCC_HEADER.AFD_CODE
			                AND LAND_USE.BLOCK_CODE = EBCC_HEADER.BLOCK_CODE
			            LEFT JOIN (
			                SELECT * FROM (
			                    SELECT
			                        KUALITAS.ID_KUALITAS AS IDK,
			                        KUALITAS.ID_KUALITAS,
			                        EBCC_DETAIL.EBCC_VALIDATION_CODE,
			                        EBCC_DETAIL.JUMLAH,
			                        EBCC_DETAIL.JUMLAH AS QTYS
			                    FROM
			                        TAP_DW.T_KUALITAS_PANEN@PRODDW_LINK KUALITAS
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
			            ) EBCC_DETAIL ON
			                EBCC_DETAIL.EBCC_VALIDATION_CODE = EBCC_HEADER.EBCC_VALIDATION_CODE
			            LEFT JOIN EBCC.T_PARAMETER_BUNCH@PRODDB_LINK PAR 
			                ON PAR.BA_CODE = EBCC_HEADER.WERKS 
			                AND PAR.ID_KUALITAS = EBCC_DETAIL.ID_KUALITAS 
			                AND PAR.KETERANGAN = 'BUNCH_HARVEST'
			            LEFT JOIN TAP_DW.TM_SUB_BLOCK@DWH_LINK SUBBLOCK
			                ON SUBBLOCK.WERKS = EBCC_HEADER.WERKS
			                AND SUBBLOCK.SUB_BLOCK_CODE = EBCC_HEADER.BLOCK_CODE
			        WHERE
			            SUBSTR( EBCC_HEADER.EBCC_VALIDATION_CODE, 0, 1 ) = '$REPORT_TYPE'
		            	AND EBCC_HEADER.SYNC_TIME BETWEEN TO_DATE( '$START_DATE', 'RRRR-MM-DD' ) AND TO_DATE( '$END_DATE', 'RRRR-MM-DD' )
		            	$where
			    ) EBCC_VAL
			WHERE ROWNUM < 100
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
			    EBCC_VAL.VAL_SPMON";
		
		$get = $this->db_mobile_ins->select($sql);
			    // print '<pre>'.$sql;
			    // dd();
		return $get;
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

		$START_DATE = date( 'Y-m-d', strtotime( $START_DATE ) );
		$END_DATE = date( 'Y-m-d', strtotime( $END_DATE ) );

		// print $START_DATE.'/'.$END_DATE;
		// dd();
		
		// $where .= $START_DATE ? " and EBCC_DATE_TIME >= TO_TIMESTAMP('$START_DATE 00:00:00','DD-MM-YYYY HH24:MI:SS')  ": "";
		// $where .= $END_DATE ? " and EBCC_DATE_TIME <= TO_TIMESTAMP('$END_DATE 23:59:59','DD-MM-YYYY HH24:MI:SS')  ": "";		
		// $where .= $REGION_CODE ? " and EST.REGION_CODE = '$REGION_CODE'  ": "";
		// $where .= $COMP_CODE ? " and EST.COMP_CODE = '$COMP_CODE'  ": "";
		$where .= $BA_CODE ? " AND EST.WERKS = '$BA_CODE'  ": "";
		// $where .= $AFD_CODE ? " and EBCC_WERKS||EBCC_AFD_CODE = '$AFD_CODE'  ": "";
		// $where .= $BLOCK_CODE ? " and EBCC_WERKS||EBCC_AFD_CODE||EBCC_BLOCK_CODE = '$BLOCK_CODE'  ": "";
		// AND EST.WERKS = '4122'
		// $where_ebcc .= $BA_CODE ? " and EBCC_WERKS = '$BA_CODE'  ": "";
		// $where_ebcc .= $AFD_CODE ? " and EBCC_WERKS||EBCC_AFD_CODE = '$AFD_CODE'  ": "";
		// $where_ebcc .= $BLOCK_CODE ? " and EBCC_WERKS||EBCC_AFD_CODE||EBCC_BLOCK_CODE = '$BLOCK_CODE'  ": "";
		
		$sql = "SELECT 
		    EBCC_VAL.VAL_EBCC_CODE,
		    EBCC_VAL.VAL_WERKS,
		   	EBCC_VAL.VAL_EST_NAME,
		    EBCC_VAL.VAL_NIK_VALIDATOR,
		    EBCC_VAL.VAL_NAMA_VALIDATOR,
		    EBCC_VAL.VAL_JABATAN_VALIDATOR,
		    EBCC_VAL.VAL_STATUS_TPH_SCAN,
		    CASE
		    	WHEN EBCC_VAL.VAL_ALASAN_MANUAL IS NULL THEN 'AUTOMATIC'
		    	ELSE
		    		CASE
		    			WHEN EBCC_VAL.VAL_ALASAN_MANUAL = '1' THEN 'MANUAL - QR Codenya Hilang'
		    			WHEN EBCC_VAL.VAL_ALASAN_MANUAL = '2' THEN 'MANUAL - QR Codenya Rusak'
		    	END
		    END AS VAL_ALASAN_MANUAL,
		    EBCC_VAL.VAL_AFD_CODE,
		    EBCC_VAL.VAL_BLOCK_CODE,
		    EBCC_VAL.VAL_DATE_TIME AS VAL_DATE_TIME,
		    EBCC_VAL.VAL_BLOCK_NAME,
		    EBCC_VAL.VAL_TPH_CODE,
		    EBCC_VAL.VAL_DELIVERY_TICKET,
		    NVL( SUM( EBCC_VAL.JML_BM ), 0 ) AS VAL_JML_BM,
		    NVL( SUM( EBCC_VAL.JML_BK ), 0 ) AS VAL_JML_BK,
		    NVL( SUM( EBCC_VAL.JML_MS ), 0 ) AS VAL_JML_MS,
		    NVL( SUM( EBCC_VAL.JML_OR ), 0 ) AS VAL_JML_OR,
		    NVL( SUM( EBCC_VAL.JML_BB ), 0 ) AS VAL_JML_BB,
		    NVL( SUM( EBCC_VAL.JML_JK ), 0 ) AS VAL_JML_JK,
		    NVL( SUM( EBCC_VAL.JML_BA ), 0 ) AS VAL_JML_BA,
		    NVL( SUM( EBCC_VAL.JML_BRD ), 0 ) AS VAL_JML_BRD,
		    NVL( SUM( EBCC_VAL.VAL_JJG_PANEN ), 0 ) AS VAL_JJG_PANEN
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
		            HRIS.EMPLOYEE_FULLNAME AS VAL_NAMA_VALIDATOR,
		            HRIS.EMPLOYEE_POSITION AS VAL_JABATAN_VALIDATOR,
		            CASE
		                WHEN PAR.KETERANGAN = 'BUNCH_HARVEST'
		                THEN EBCC_DETAIL.QTYS
		                ELSE 0
		            END AS VAL_JJG_PANEN,
		            EBCC_DETAIL.JML_BM,
		            EBCC_DETAIL.JML_BK,
		            EBCC_DETAIL.JML_MS,
		            EBCC_DETAIL.JML_OR,
		            EBCC_DETAIL.JML_BB,
		            EBCC_DETAIL.JML_JK,
		            EBCC_DETAIL.JML_BA,
		            EBCC_DETAIL.JML_BRD,
		            SUBBLOCK.BLOCK_NAME AS VAL_BLOCK_NAME
		        FROM
		            MOBILE_INSPECTION.TR_EBCC_VALIDATION_H EBCC_HEADER
		            LEFT JOIN MOBILE_INSPECTION.TM_USER_AUTH USER_AUTH ON 
		                USER_AUTH.USER_AUTH_CODE = EBCC_HEADER.INSERT_USER
		            LEFT JOIN TAP_DW.TM_EMPLOYEE_HRIS@PRODDW_LINK HRIS ON
		                HRIS.EMPLOYEE_NIK = USER_AUTH.EMPLOYEE_NIK
		            LEFT JOIN TAP_DW.TM_EST@PRODDW_LINK EST ON 
		                EST.WERKS = EBCC_HEADER.WERKS 
		                AND SYSDATE BETWEEN EST.START_VALID AND EST.END_VALID
		            LEFT JOIN (
		                SELECT * FROM (
		                    SELECT
		                        KUALITAS.ID_KUALITAS AS IDK,
		                        KUALITAS.ID_KUALITAS,
		                        EBCC_DETAIL.EBCC_VALIDATION_CODE,
		                        EBCC_DETAIL.JUMLAH,
		                        EBCC_DETAIL.JUMLAH AS QTYS
		                    FROM
		                        TAP_DW.T_KUALITAS_PANEN@PRODDW_LINK KUALITAS
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
		                WHERE EBCC_VALIDATION_CODE IS NOT NULL
		            ) EBCC_DETAIL ON
		                EBCC_DETAIL.EBCC_VALIDATION_CODE = EBCC_HEADER.EBCC_VALIDATION_CODE
		            LEFT JOIN EBCC.T_PARAMETER_BUNCH@PRODDB_LINK PAR 
		                ON PAR.BA_CODE = EBCC_HEADER.WERKS 
		                AND PAR.ID_KUALITAS = EBCC_DETAIL.ID_KUALITAS 
		                AND PAR.KETERANGAN = 'BUNCH_HARVEST'
		            LEFT JOIN TAP_DW.TM_SUB_BLOCK@DWH_LINK SUBBLOCK
		                ON SUBBLOCK.WERKS = EBCC_HEADER.WERKS
		                AND SUBBLOCK.SUB_BLOCK_CODE = EBCC_HEADER.BLOCK_CODE
		        WHERE
		            SUBSTR( EBCC_HEADER.EBCC_VALIDATION_CODE, 0, 1 ) = 'V'
		            AND EBCC_HEADER.SYNC_TIME BETWEEN TO_DATE( '$START_DATE', 'RRRR-MM-DD' ) AND TO_DATE( '$END_DATE', 'RRRR-MM-DD' )
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
		    EBCC_VAL.VAL_DELIVERY_TICKET";

		$get = $this->db_mobile_ins->select( $sql );

		$joindata = array();

		// print '<pre>';
		// print_r( $sql );
		// print '</pre>';
		// dd();

		if ( !empty( $get ) ) {
			$i = 0;
			foreach ( $get as $ec ) {
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
				$joindata[$i]['val_date_time'] = $ec->val_date_time;
				$joindata[$i]['val_block_name'] = $ec->val_block_name;
				$joindata[$i]['val_tph_code'] = $ec->val_tph_code;
				$joindata[$i]['val_delivery_ticket'] = $ec->val_delivery_ticket;
				$joindata[$i]['val_jml_bm'] = $ec->val_jml_bm;
				$joindata[$i]['val_jml_bk'] = $ec->val_jml_bk;
				$joindata[$i]['val_jml_ms'] = $ec->val_jml_ms;
				$joindata[$i]['val_jml_or'] = $ec->val_jml_or;
				$joindata[$i]['val_jml_bb'] = $ec->val_jml_bb;
				$joindata[$i]['val_jml_jk'] = $ec->val_jml_jk;
				$joindata[$i]['val_jml_ba'] = $ec->val_jml_ba;
				$joindata[$i]['val_jml_brd'] = $ec->val_jml_brd;
				$joindata[$i]['val_jjg_panen'] = $ec->val_jjg_panen;
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
				$joindata[$i]['ebcc_no_bcc'] = '';
				$joindata[$i]['akurasi_kualitas_ms'] = '';
				$joindata[$i]['match_status'] = 'NOT MATCH';
				$date = date( 'd-m-Y', strtotime( $ec->val_date_time ) );
				# Vi, setelah data inputan ALASAN_MANUAL keluar, km ganti F_GET_TEST jadi F_GET_EBCC_COMPARE ya vi, di oracle sm di codingan ini
				$sql = "SELECT F_GET_EBCC_COMPARE( '{$ec->val_werks}', '{$ec->val_afd_code}', '{$ec->val_block_code}', '{$ec->val_tph_code}', '{$date}','{$date}' ) AS NO_BCC FROM DUAL";
				$query = collect( $this->db_mobile_ins->select( $sql ) )->first();

				if ( $query->no_bcc != null ) {
					$sql_ebcc = "SELECT
							HDP.ID_RENCANA,
							HDP.TANGGAL_RENCANA,
							HDP.NIK_KERANI_BUAH,
							EMP_EBCC.EMP_NAME,
							HDP.ID_BA_AFD_BLOK,
							HDP.NO_REKAP_BCC,
							HP.NO_TPH,
							HP.NO_BCC,
							NVL( EBCC.F_GET_HASIL_PANEN_BUNCH@PRODDB_LINK ( TBA.ID_BA, HP.NO_REKAP_BCC, HP.NO_BCC, 'BUNCH_HARVEST' ), 0 ) as JJG_PANEN,
							NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX@PRODDB_LINK (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 1), 0) AS EBCC_JML_BM,
							NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX@PRODDB_LINK (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 2), 0) AS EBCC_JML_BK,
							NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX@PRODDB_LINK (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 3), 0) AS EBCC_JML_MS,
							NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX@PRODDB_LINK (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 4), 0) AS EBCC_JML_OR,
							NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX@PRODDB_LINK (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 6), 0) AS EBCC_JML_BB,
							NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX@PRODDB_LINK (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 15), 0) AS EBCC_JML_JK,
							NVL(EBCC.F_GET_HASIL_PANEN_NUMBERX@PRODDB_LINK (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC, 16), 0) AS EBCC_JML_BA,   
							NVL(EBCC.F_GET_HASIL_PANEN_BRDX@PRODDB_LINK (HDP.ID_RENCANA, HP.NO_REKAP_BCC, HP.NO_BCC), 0) AS EBCC_JML_BRD
						FROM (
								SELECT
									HRP.ID_RENCANA AS ID_RENCANA,
									HRP.TANGGAL_RENCANA AS TANGGAL_RENCANA,
									HRP.NIK_KERANI_BUAH AS NIK_KERANI_BUAH,
									DRP.ID_BA_AFD_BLOK AS ID_BA_AFD_BLOK,
									DRP.NO_REKAP_BCC AS NO_REKAP_BCC
								FROM
									EBCC.T_HEADER_RENCANA_PANEN@PRODDB_LINK HRP 
									LEFT JOIN EBCC.T_DETAIL_RENCANA_PANEN@PRODDB_LINK DRP ON HRP.ID_RENCANA = DRP.ID_RENCANA
							) HDP
							LEFT JOIN EBCC.T_HASIL_PANEN@PRODDB_LINK HP ON HP.ID_RENCANA = HDP.ID_RENCANA AND HP.NO_REKAP_BCC = HDP.NO_REKAP_BCC
							LEFT JOIN EBCC.T_BLOK@PRODDB_LINK TB ON TB.ID_BA_AFD_BLOK = HDP.ID_BA_AFD_BLOK
							LEFT JOIN EBCC.T_AFDELING@PRODDB_LINK TA ON TA.ID_BA_AFD = TB.ID_BA_AFD
							LEFT JOIN EBCC.T_BUSSINESSAREA@PRODDB_LINK TBA ON TBA.ID_BA = TA.ID_BA
							LEFT JOIN EBCC.T_EMPLOYEE@PRODDB_LINK EMP_EBCC ON EMP_EBCC.NIK = HDP.NIK_KERANI_BUAH 
						WHERE
							HP.NO_BCC = '{$query->no_bcc}'";
							
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
					
					$joindata[$i]['match_status'] = ( intval( $query_ebcc->jjg_panen ) == intval( $ec->val_jjg_panen ) ? 'MATCH' : 'NOT MATCH' );
					$akurasi_kualitas_ms = intval( $query_ebcc->ebcc_jml_ms ) - intval( $ec->val_jml_ms );
					$joindata[$i]['akurasi_kualitas_ms'] = ( $akurasi_kualitas_ms > 0 ? $akurasi_kualitas_ms : 0 );
				}
				$i++;
			}
		}

		// print '<pre>';
		// print_r( $sql );
		// print '</pre>';
		return $joindata;
	}
}
