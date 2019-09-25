<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ReportOracle extends Model
{
	
	protected $env;
	protected $db_mobile_ins;
	
	public function __construct() {
		$this->env = 'dev';
		$this->db_mobile_ins = ($this->env == 'production' ? DB::connection('mobile_ins') : DB::connection('mobile_ins_dev'));
	}
	
    public function EBCC_VALIDATION_ESTATE()
	{
		$get = $this->db_mobile_ins->select("
				SELECT
					EBCC_HEADER.EBCC_VALIDATION_CODE,
					EST.WERKS,
					EST.EST_NAME,
					EBCC_HEADER.AFD_CODE,
					EBCC_HEADER.BLOCK_CODE,
					EBCC_HEADER.NO_TPH,
					EBCC_HEADER.STATUS_TPH_SCAN,
					EBCC_HEADER.ALASAN_MANUAL,
					EBCC_HEADER.LAT_TPH,
					EBCC_HEADER.LON_TPH,
					EBCC_HEADER.DELIVERY_CODE,
					EBCC_HEADER.STATUS_DELIVERY_CODE,
					USER_AUTH.EMPLOYEE_NIK AS NIK_VALIDATOR,
					HRIS.EMPLOYEE_FULLNAME AS NAMA_VALIDATOR,
					HRIS.EMPLOYEE_POSITION AS JABATAN_VALIDATOR,
					EBCC_DETAIL.*
				FROM
					MOBILE_INSPECTION.TR_EBCC_VALIDATION_H EBCC_HEADER
					LEFT JOIN MOBILE_INSPECTION.TM_USER_AUTH USER_AUTH ON 
						USER_AUTH.USER_AUTH_CODE = EBCC_HEADER.INSERT_USER
					LEFT JOIN TAP_DW.TM_EMPLOYEE_HRIS@PRODDW_LINK HRIS ON
						HRIS.EMPLOYEE_NIK = USER_AUTH.EMPLOYEE_NIK
					LEFT JOIN TAP_DW.TM_EST@PRODDW_LINK EST ON 
						EST.WERKS = EBCC_HEADER.WERKS 
						AND SYSDATE BETWEEN EST.START_VALID AND EST.END_VALID
					INNER JOIN (
						SELECT * FROM (
							SELECT
								KUALITAS.ID_KUALITAS,
								EBCC_DETAIL.EBCC_VALIDATION_CODE,
								EBCC_DETAIL.JUMLAH
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
							FOR ID_KUALITAS IN ( 
								1, 
								2, 
								3, 
								4, 
								5, 
								6, 
								7, 
								8, 
								9, 
								10, 
								11, 
								12, 
								13, 
								14, 
								15, 
								16 
							)
						)
						WHERE EBCC_VALIDATION_CODE IS NOT NULL
					) EBCC_DETAIL ON
						EBCC_DETAIL.EBCC_VALIDATION_CODE = EBCC_HEADER.EBCC_VALIDATION_CODE
				WHERE
					SUBSTR( EBCC_HEADER.EBCC_VALIDATION_CODE, 0, 1 ) = 'V'
		");
		return $get;
	}
}
