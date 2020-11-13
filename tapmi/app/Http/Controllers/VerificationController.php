<?php

	namespace App\Http\Controllers;

	use Illuminate\Http\Response;
	use Illuminate\Routing\Controller;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Collection;
	use Illuminate\Support\Facades\File;
   use Illuminate\Support\Facades\Storage;
   use GuzzleHttp\Exception\GuzzleException;
   use GuzzleHttp\Client;
	use View;
	use Validator;
	use Redirect;
	use Session;
	use Config;
	use URL;
	use DateTime;
	use Maatwebsite\Excel\Facades\Excel;
	use App\Validation;
	use App\Employee;
	use App\TMParameter;
  use App\TRValidasiHeader;
  use App\TRValidasiDetail;
	use App\TRBunchCounting;
   use DataTables;
   use Ramsey\Uuid\Uuid;
   use App\ValidasiHeader;


   # API Setup
   use App\APISetup;
   use App\APIData as Data;


class VerificationController extends Controller {

	protected $active_menu;

	public function __construct() {
      $this->active_menu = '_'.str_replace( '.', '', '02.02.00.00.00' ).'_';
		  $this->db_mobile_ins = DB::connection('mobile_ins');
      $this->db_ebcc = DB::connection('ebcc');
	}

	#   		 									  		            ▁ ▂ ▄ ▅ ▆ ▇ █ Index
    # -------------------------------------------------------------------------------------
   
   public function index(Request $request,$tgl = null){
      
      $data['nodata'] = $request->nodata?1:0;
      if(empty($tgl)){
         $day =  date("Y-m-d", strtotime("yesterday"));
      }else{
         $day =  date("Y-m-d", strtotime($tgl));
      }
      $day = '2020-10-31';
      $data['active_menu'] = $this->active_menu;
      $data['tgl_validasi'] = $day;
      $client = new \GuzzleHttp\Client();
      $res = $client->request( 'GET', APISetup::url()['msa']['ins']['bunchcounting'].'/v1.0/web/bunch-counting', [
        'json' => [
          // 'KETERANGAN' => 'BELUM DIVERIFIKASI',
          'DATE_FROM' => str_replace('-', '', $day).'000000',
          'DATE_TO' => str_replace('-', '', $day).'999999'
        ]
      ]);
      $bunch_counting = json_decode( $res->getBody() );
      $data['data_header'] = array();
      if($bunch_counting->status==true)
      {
        $data['data_header'] = $bunch_counting->data;
      }
      $last_work_daily = $this->db_mobile_ins->select("SELECT trunc(TANGGAL) - trunc(sysdate) AS DIFF,MIN(FLAG_HK),MIN(NAMA_HARI) 
                                                               FROM TM_TIME_DAILY@DWH_LINK 
                                                               WHERE TANGGAL < trunc(sysdate)
                                                               AND FLAG_HK = 'Y'
                                                               GROUP BY TANGGAL
                                                               ORDER BY TANGGAL DESC FETCH NEXT 1 ROWS ONLY");
      $data['last_work_daily'] = isset($last_work_daily[0])?$last_work_daily[0]->diff:'-1';
      return view( 'verifikasi.listheader', $data );
   }

   public function cek_aslap(Request $request){
      date_default_timezone_set('Asia/Jakarta');
      set_time_limit(0);

      $result = ( new ValidasiHeader() )->validasi_cek_aslap($request->tanggal);
      $res = json_encode($result);
      $data = json_decode($res,true);
      // dd($data);
      $day =  date("Y-m-d", strtotime($request->tanggal));
      foreach ($data as $key => $value) 
      {
         //  IF DATA NOT EXPORTED TO SAP
          if($value['export_status']!='X')
          {
            $id_validasi = $value['ebcc_nik_kerani_buah'].'-'.$value['ebcc_nik_mandor'].'-'.str_replace('-','',$day);
            // $check = TRValidasiDetail::where(['id_validasi'=>$id_validasi,'no_bcc'=>$value['ebcc_no_bcc']])->first();
            // if(!$check)
            // {
              $emp = Employee::where('EMPLOYEE_NIK',session('NIK'))->first();
              $fullname = $emp['employee_fullname'];
              TRValidasiDetail::insert([
                'uuid' => Uuid::uuid1()->toString(),
                'id_validasi' => $id_validasi,
                'data_source' => $value['val_sumber'],
                'val_ebcc_code' => $value['val_ebcc_code'],
                'tanggal_ebcc' => $value['val_date_time'],
                'nik_krani_buah' => $value['ebcc_nik_kerani_buah'],
                'nama_krani_buah' => $value['ebcc_nama_kerani_buah'],
                'nik_mandor' => $value['ebcc_nik_mandor'],
                'nama_mandor' => $value['ebcc_nama_mandor'],
                'ba_code' => $value['val_werks'],
                'ba_name' => $value['val_est_name'],
                'afd_code' => $value['val_afd_code'],
                'block_code' => $value['val_block_code'],
                'block_name' => $value['val_block_name'],
                'no_tph' => $value['val_tph_code'],
                'no_bcc' => $value['ebcc_no_bcc'],
                'jjg_ebcc_bm' => $value['ebcc_jml_bm'],
                'jjg_ebcc_bk' => $value['ebcc_jml_bk'],
                'jjg_ebcc_ms' => $value['ebcc_jml_ms'],
                'jjg_ebcc_or' => $value['ebcc_jml_or'],
                'jjg_ebcc_bb' => $value['ebcc_jml_bb'],
                'jjg_ebcc_jk' => $value['ebcc_jml_jk'],
                'jjg_ebcc_ba' => $value['ebcc_jml_ba'],
                'jjg_ebcc_total' => $value['ebcc_jjg_panen'],
                'jjg_ebcc_1' => NULL,
                'jjg_ebcc_2' => NULL,
                'jjg_validate_bm' => $value['val_jml_1'],
                'jjg_validate_bk' => $value['val_jml_2'],
                'jjg_validate_ms' => $value['val_jml_3'],
                'jjg_validate_or' => $value['val_jml_4'],
                'jjg_validate_bb' => $value['val_jml_6'],
                'jjg_validate_jk' => $value['val_jml_15'],
                'jjg_validate_ba' => $value['val_jml_16'],
                'jjg_validate_total' => $value['val_total_jjg'],
                'jjg_validate_1' => NULL,
                'jjg_validate_2' => NULL,
                'kondisi_foto' => NULL,
                'insert_time' => date('Y-M-d H.i.s'),
                'insert_user' => $value['val_nik_validator'],
                'insert_user_fullname' => $value['val_nama_validator'],
                'insert_user_userrole' => $value['val_jabatan_validator']
              ]);
  
              // INSERT LOG TO EBCC
              if(substr($value['val_jabatan_validator'],0,7)=='ASISTEN')
              {
                 $this->db_ebcc->table('T_VALIDASI')->insert([
                    'TANGGAL_EBCC'=>$value['val_date_time'],
                    'NO_BCC'=>$value['ebcc_no_bcc'],
                    'TANGGAL_VALIDASI' => date('Y-m-d H:i:s'),
                    'ROLES' => $value['val_jabatan_validator'],
                    'NIK' => $value['val_nik_validator'],
                    'NAMA' => $value['val_nama_validator'],
                    'NIK_KRANI_BUAH' => $value['ebcc_nik_kerani_buah'],
                    'NIK_MANDOR' => $value['ebcc_nik_mandor']
                 ]);
              }
              $check_kabun_validation = $this->db_ebcc->table('T_VALIDASI')->
                                                        where(['NO_BCC'=>$value['ebcc_no_bcc']])->
                                                        whereIn('ROLES',[ 'KEPALA KEBUN',
                                                                          'KEPALA_KEBUN',
                                                                          'ASISTEN KEPALA',
                                                                          'ASISTEN_KEPALA',
                                                                          'EM',
                                                                          'SEM GM',
                                                                          'SENIOR ESTATE MANAGER'])->first();
            // NOTE : DISABLE REPLACE DATA PANEN EBCC 2020-10-12
              // UPDATE BCC HASIL PANEN KUALITAS IF KABUN NEVER VALIDATE
            //   if(!$check_kabun_validation)
            //   {                                        
            //     // UPDATE QUANTITY MENTAH
            //      $this->db_ebcc->table('T_HASILPANEN_KUALTAS')->where([
            //         'ID_BCC'=>$value['ebcc_no_bcc'],
            //         'ID_KUALITAS' => 1
            //      ])->update(['QTY'=>$value['val_jml_1']]);
            //     // UPDATE QUANTITY BUSUK
            //      $this->db_ebcc->table('T_HASILPANEN_KUALTAS')->where([
            //         'ID_BCC'=>$value['ebcc_no_bcc'],
            //         'ID_KUALITAS' => 6
            //      ])->update(['QTY'=>$value['val_jml_6']]);
            //     // UPDATE QUANTITY JAJANG KOSONG
            //      $this->db_ebcc->table('T_HASILPANEN_KUALTAS')->where([
            //         'ID_BCC'=>$value['ebcc_no_bcc'],
            //         'ID_KUALITAS' => 15
            //      ])->update(['QTY'=>$value['val_jml_15']]);
            //     // UPDATE QUANTITY OVERRIPE
            //      $this->db_ebcc->table('T_HASILPANEN_KUALTAS')->where([
            //         'ID_BCC'=>$value['ebcc_no_bcc'],
            //         'ID_KUALITAS' => 4
            //      ])->update(['QTY'=>$value['val_jml_4']]);
            //     // UPDATE QUANTITY MASAK
            //      $this->db_ebcc->table('T_HASILPANEN_KUALTAS')->where([
            //         'ID_BCC'=>$value['ebcc_no_bcc'],
            //         'ID_KUALITAS' => 3
            //      ])->update(['QTY'=>$value['val_jml_3']]);
            //   }   
          }
      }
   }


   public function getEbccValHeader(request $request){
      $data['active_menu'] = $this->active_menu;
      $day = $request->tanggal;
      $res = json_encode(( new ValidasiHeader() )->validasi_header($day));
      $data['data_header'] = json_decode($res,true);
      $data['session'] = session();
      return view( 'verifikasi.listheader', $data );
   }

   public function getValHeader(request $request){
      $data['active_menu'] = $this->active_menu;
      $day = date("Y-m-d", strtotime($request->tanggal));
      $data['tgl_validasi'] = $day;
      $client = new \GuzzleHttp\Client();
      $res = $client->request( 'GET', APISetup::url()['msa']['ins']['bunchcounting'].'/v1.0/web/bunch-counting', [
        'json' => [
          // 'KETERANGAN' => 'BELUM DIVERIFIKASI',
          'DATE_FROM' => str_replace('-', '', $day).'000000',
          'DATE_TO' => str_replace('-', '', $day).'999999'
        ]
      ]);
      $bunch_counting = json_decode( $res->getBody() );
      $data['data_header'] = array();
      if($bunch_counting->status==true)
      {
        $data['data_header'] = $bunch_counting->data;
      }
      $last_work_daily = $this->db_mobile_ins->select("SELECT trunc(TANGGAL) - trunc(sysdate) AS DIFF,MIN(FLAG_HK),MIN(NAMA_HARI) 
                                                               FROM TM_TIME_DAILY@DWH_LINK 
                                                               WHERE TANGGAL < trunc(sysdate)
                                                               AND FLAG_HK = 'Y'
                                                               GROUP BY TANGGAL
                                                               ORDER BY TANGGAL DESC FETCH NEXT 1 ROWS ONLY");
      $data['last_work_daily'] = isset($last_work_daily[0])?$last_work_daily[0]->diff:'-1';
      return view( 'verifikasi.filtertable', $data );
   }

   
   public function getAllfilter($date){
      $day =  date("Y-m-d", strtotime($date));
      $res = ( new ValidasiHeader() )->validasi_header($day);
      return response()->json($res);
   }

   public function getAll(){
      $ba_afd_code =explode(",",session('LOCATION_CODE'));
      $code = implode("','", $ba_afd_code);
      $res = ( new ValidasiHeader() )->data();
      return response()->json($res);
      // return response()->json([
      //       "data" => $res
      // ], 201);
   }
	    
    public function xx_create($id) 
    {   
       
      $data['active_menu'] = $this->active_menu;

       //jika arr_id != null, maka explode utk daptkan id
            //for check if id di tr_validasi_header ada dan validate < target? got next id, jika tidak ada / kurang dari target maka buka form validasi,
            // get query berdasarkan kombinasi.
            // break
            //else kembali ke halaman list


        $string = str_replace(".","/",$id);
        $arr = explode("-", $string, 5);
        $nik_kerani = $arr[0];
        $nik_mandor = $arr[1];
        $tanggal = date("Y-m-d",strtotime($arr[2]));
        $tgl = $arr[2];
        $ba_code = $arr[3];
        $afd = $arr[4];
        $id_validasi = $nik_kerani."-".$nik_mandor."-".$tgl;

        $valid_data = json_encode(( new ValidasiHeader() )->validasi_askep($id));
        $result = json_decode( $valid_data,true);
                   
        $i = 1; //start jumlah validasi
        $no_val = TRValidasiHeader::select('JUMLAH_EBCC_VALIDATED')->where('ID_VALIDASI',$id_validasi)->first();
        
        // dd($no_val,$no_val['jumlah_ebcc_validated']);
        if($no_val == null){
            $val = 1;
        }else{
            $val = $i + $no_val['jumlah_ebcc_validated'];
        }
        $target = TMParameter::select('PARAMETER_DESC')->where('PARAMETER_NAME','TARGET_VALIDASI')->get();

        $data['data_validasi'] = $result;
        $data['no_validasi'] = $val;
        $data['target'] = $target[0]->parameter_desc;

        return view('verifikasi.image_preview',$data);
    }

    public function create($tgl) 
    {   
      $data['active_menu'] = $this->active_menu;
      $day = date("Y-m-d", strtotime($tgl));
      $data['tgl_validasi'] = $day;
      $client = new \GuzzleHttp\Client();
      $data['last_work_daily'] = isset($last_work_daily[0])?$last_work_daily[0]->diff:'-1';
      $res = $client->request( 'GET', APISetup::url()['msa']['ins']['bunchcounting'].'/v1.0/web/bunch-counting', [
        'json' => [
          'KETERANGAN' => 'BELUM DIVERIFIKASI',
          'DATE_FROM' => str_replace('-', '', $day).'000000',
          'DATE_TO' => str_replace('-', '', $day).'999999'
        ]
      ]);
      $bunch_counting = json_decode( $res->getBody() );
      $data_header = array();
      if($bunch_counting->status==true)
      {
        $data['data'] = $bunch_counting->data;
        $comp = substr($data['data'][0]->WERKS, 0,2);
        $comp = $this->db_mobile_ins->select("SELECT * FROM tap_dw.tm_comp@proddw_link WHERE comp_code = $comp FETCH NEXT 1 ROWS ONLY");
        $data['pt'] = $comp[0]->comp_name;
        return view('verifikasi.image_preview',$data);
      }
      else 
      {
        return Redirect::to('listverifikasi/'.$tgl.'?nodata=1');
      }

       
    }

    public function export($tgl)
    {
      $day = date("Y-m-d", strtotime($tgl));
      $data['tgl_validasi'] = $day;
      $client = new \GuzzleHttp\Client();
      $res = $client->request( 'GET', APISetup::url()['msa']['ins']['bunchcounting'].'/v1.0/web/bunch-counting', [
        'json' => [
          // 'KETERANGAN' => 'BELUM DIVERIFIKASI',
          'DATE_FROM' => str_replace('-', '', $day).'000000',
          'DATE_TO' => str_replace('-', '', $day).'999999'
        ]
      ]);
      $bunch_counting = json_decode( $res->getBody() );
      $data_header = array();
      if($bunch_counting->status==true)
      {
        $data_header = $bunch_counting->data;
      }
      // Set Empty Array (Biar gak error)
      $results['head'] = array();
      $results['summary'] = array();
      $results['periode'] = $day;
      $results['data_header'] = $data_header;
      $results['sheet_name'] = 'Verifiation AI';
      $results['view'] = 'report.excel-verification-ai';
      $file_name = 'List-Verifiation-AI';
      // $results['data'] = $data_header;

      // print '<pre>';
      // print_r( $results );
      // print '</pre>';
      // dd();
      Excel::create( $file_name, function( $excel ) use ( $results ) {
        $excel->sheet( $results['sheet_name'], function( $sheet ) use ( $results ) {
          $sheet->loadView( $results['view'], $results );
        } );
      } )->export( 'xlsx' );
    }

    public function create_action(Request $request)
    { 
        date_default_timezone_set('Asia/Jakarta');
        if($request->KONDISI_FOTO=='Foto bagus & Inputan PIC Sesuai' || 
           $request->KONDISI_FOTO=='Foto bagus & tapi Inputan PIC Tidak Sesuai' || 
           $request->KONDISI_FOTO=='Foto Bagus tapi Jumlah Janjang lebih dari 30' || 
           $request->KONDISI_FOTO=='Gambar Terpotong')
        {
          $type = 'BISA DIHITUNG';
        }
        else 
        {
          $type = 'TIDAK BISA DIHITUNG';
        }
        // dd(session()->all());
      // "_token" => "7YmdmvnWadt6eMnmfaXq68ZocBKVNfvit0NvLRSB"
      // "TANGGAL_TRANSAKSI" => "2020-10-31"
      // "EBCC_CODE" => "V025920201031123623056044"
      // "KONDISI_FOTO" => "Foto Bagus tapi Jumlah Janjang > 30"
        $client = new \GuzzleHttp\Client();
        $res = $client->request( 'GET', APISetup::url()['msa']['ins']['bunchcounting'].'/v1.0/web/bunch-counting', [
          'json' => [
            'KETERANGAN' => 'BELUM DIVERIFIKASI',
            'EBCC_CODE' => $request->NO_BCC
          ]
        ]);
        $bunch_counting = json_decode( $res->getBody() );


        $res = $client->request( 'POST', APISetup::url()['msa']['ins']['image'].'/v2.2/copy-image', [
          'json' => [
            'TYPE' => $type,
            'CATEGORY' => $request->KONDISI_FOTO,
            'EBCC_CODE' => $request->NO_BCC
          ]
        ]);
        $copy_image = json_decode( $res->getBody() );

        if($bunch_counting->status==true && $copy_image->status==true)
        {
          $bunch_counting_data = $bunch_counting->data;
          $code_block = $bunch_counting_data[0]->BLOCK_CODE;
          $code_ba = $bunch_counting_data[0]->WERKS;
          $code_company = substr($bunch_counting_data[0]->WERKS, 0,2);
          $username_PIC = $bunch_counting_data[0]->INSERT_USER;
          $username_Create = session('USERNAME');
          $getBlock = $this->db_mobile_ins->select("SELECT * FROM tap_dw.tm_block@proddw_link WHERE block_code = '$code_block' FETCH NEXT 1 ROWS ONLY");
          $getBA = $this->db_mobile_ins->select("SELECT * FROM tap_dw.tm_est@proddw_link WHERE werks = '$code_ba' FETCH NEXT 1 ROWS ONLY");
          $getCompany = $this->db_mobile_ins->select("SELECT * FROM tap_dw.tm_comp@proddw_link WHERE comp_code = '$code_company' FETCH NEXT 1 ROWS ONLY");
          $getUserPIC = $this->db_mobile_ins->select("SELECT * FROM tap_dw.tm_employee_hris@proddw_link WHERE employee_username = '$username_PIC' FETCH NEXT 1 ROWS ONLY");
          $getUserCreate = $this->db_mobile_ins->select("SELECT * FROM tap_dw.tm_employee_hris@proddw_link WHERE employee_username = '$username_Create' FETCH NEXT 1 ROWS ONLY");
            // dd($getBlock,$getBA,$getCompany,$getUserPIC,$getUserCreate);
          if(isset($getBlock[0]) && isset($getBA[0]) && isset($getCompany[0]) && isset($getUserPIC[0]) && isset($getUserCreate[0]))
          {
              $score_ai = json_encode($bunch_counting_data[0]->SCORES);
              $average = 0;
              $i = 0;
              foreach ($bunch_counting_data[0]->SCORES as $key => $value) {
               $i++;
               $average+=$value;
              }
              $average = $i==0?0:($average/$i);
              $create_data = array( 'TANGGAL_TRANSAKSI' => $request->TANGGAL_TRANSAKSI.' '.date('H.i.s'),
                                    'NIK_PIC' => $getUserPIC[0]->employee_nik,
                                    'NAMA_PIC' => $getUserPIC[0]->employee_fullname,
                                    'ROLE_PIC' => $bunch_counting_data[0]->ROLE,
                                    'BA_CODE' => $bunch_counting_data[0]->WERKS,
                                    'BA_NAME' => $getBA[0]->est_name,
                                    'AFD_CODE' => $bunch_counting_data[0]->AFD_CODE,
                                    'BLOCK_CODE' => $code_block,
                                    'BLOCK_NAME' => $getBlock[0]->block_name,
                                    'NO_TPH' => $bunch_counting_data[0]->NO_TPH,
                                    'NO_BCC' => $request->NO_BCC,
                                    'TOTAL_JJG_PIC' => $bunch_counting_data[0]->COUNT_VALIDATION,
                                    'TOTAL_JJG_AI' => $bunch_counting_data[0]->COUNT_AI,
                                    'SELISIH_JJG' => $bunch_counting_data[0]->COUNT_DIFF,
                                    'PERCENT_VARIANCE' => ($bunch_counting_data[0]->COUNT_DIFF/$bunch_counting_data[0]->COUNT_VALIDATION),
                                    'SCORES_AI' => $score_ai,
                                    'START_PROCESS_AI' => $bunch_counting_data[0]->START_PROCESS,
                                    'END_PROCESS_AI' => $bunch_counting_data[0]->END_PROCESS,
                                    'DATA_SOURCE' => $bunch_counting_data[0]->SOURCE,
                                    'IMG_URL' => $bunch_counting_data[0]->IMAGE_URL,
                                    'IMG_NAME' => $bunch_counting_data[0]->IMAGE_NAME,
                                    'KONDISI_FOTO' => $request->KONDISI_FOTO,
                                    'INSERT_TIME' => date('Y-M-d H.i.s'),
                                    'INSERT_USER' => $getUserCreate[0]->employee_username,
                                    'INSERT_USER_FULLNAME' => $getUserCreate[0]->employee_fullname,
                                    'INSERT_USER_USERROLE' => session('JOB_CODE'),
                                    'AVG_SCORES_AI' => $average);
              // dd($request);
              TRBunchCounting::updateOrCreate($create_data);
              return Redirect::to('verifikasi/create/'.substr($request->TANGGAL_TRANSAKSI,0,10));
          }
          else 
          {
            return Redirect::to('verifikasi/create/'.substr($request->TANGGAL_TRANSAKSI,0,10).'?error=2');
          }
        }
        else 
        {
          return Redirect::to('verifikasi/create/'.substr($request->TANGGAL_TRANSAKSI,0,10).'?error=1');
        }
         
         return Redirect::to('verifikasi/create/'.substr($request->TANGGAL_TRANSAKSI,0,10));

    }


    public function compare_ebcc($id) {
        $sql = " SELECT tanggal_rencana AS tanggal_ebcc,
        nik_kerani_buah,
        nama_kerani_buah,
        id_ba AS kode_ba,
        est.comp_name AS nama_pt,
        est.est_name AS bisnis_area,
        id_afd AS afd,
        id_blok AS blok,
        est.block_name AS nama_blok,
        no_tph AS tph,
        no_bcc AS no_bcc,
        picture_name,
        ebcc_jml_bm,
        ebcc_jml_bk,
        ebcc_jml_ms,
        ebcc_jml_or,
        ebcc_jml_bb,
        ebcc_jml_jk,
        ebcc_jml_ba,
        ebcc_total,
        tanggal_validasi,
        kondisi_foto,
        nik_pembuat,
        nama_pembuat,
        user_role,
        jjg_validate_bm,
        jjg_validate_bk,
        jjg_validate_ms,
        jjg_validate_or,
        jjg_validate_bb,
        jjg_validate_jk,
        jjg_validate_ba,
        nvl(jjg_validate_total,0) as jjg_validate_total
          FROM (SELECT ebcc.tanggal_rencana,
                ebcc.nik_kerani_buah,
                ebcc.nama_kerani_buah,
                ebcc.id_ba,
                ebcc.id_afd,
                ebcc.id_blok,
                ebcc.no_tph,
                ebcc.no_bcc,
                ebcc.picture_name,
                ebcc.ebcc_jml_bm,
                ebcc.ebcc_jml_bk,
                ebcc.ebcc_jml_ms,
                ebcc.ebcc_jml_or,
                ebcc.ebcc_jml_bb,
                ebcc.ebcc_jml_jk,
                ebcc.ebcc_jml_ba,
                  ebcc.ebcc_jml_bm
                + ebcc.ebcc_jml_bk
                + ebcc.ebcc_jml_ms
                + ebcc.ebcc_jml_or
                + ebcc.ebcc_jml_bb
                + ebcc.ebcc_jml_jk
                + ebcc.ebcc_jml_ba
                   AS ebcc_total,
                validasi.jjg_validate_bm,
                validasi.jjg_validate_bk,
                validasi.jjg_validate_ms,
                validasi.jjg_validate_or,
                validasi.jjg_validate_bb,
                validasi.jjg_validate_jk,
                validasi.jjg_validate_ba,
                validasi.jjg_validate_total,
                validasi.kondisi_foto,
                TO_CHAR(validasi.insert_time, 'YYYY-MM-DD HH24:MI') AS tanggal_validasi,
                validasi.insert_user AS nik_pembuat,
                validasi.insert_user_fullname AS nama_pembuat,
                validasi.insert_user_userrole AS user_role
           FROM (SELECT tanggal_rencana,
                        nik_kerani_buah,
                        nama_kerani_buah,
                        id_ba,
                        id_afd,
                        id_blok,
                        no_tph,
                        no_bcc,
                        ebcc_jml_bm,
                        ebcc_jml_bk,
                        ebcc_jml_ms,
                        ebcc_jml_or,
                        ebcc_jml_bb,
                        ebcc_jml_jk,
                        ebcc_jml_ba,
                        picture_name
                   FROM (  SELECT hrp.tanggal_rencana,
                                  SUBSTR (id_ba_afd_blok, 1, 4) id_ba,
                                  SUBSTR (id_ba_afd_blok, 5, 1) id_afd,
                                  SUBSTR (id_ba_afd_blok, 6, 3) id_blok,
                                  hp.no_tph no_tph,
                                  hp.picture_name,
                                  COUNT (DISTINCT hp.no_bcc) jlh_ebcc,
                                  MAX (hrp.nik_kerani_buah) nik_kerani_buah,
                                  MAX (emp_ebcc.emp_name) nama_kerani_buah,
                                  MAX (no_bcc) no_bcc,
                                  MAX (hp.no_rekap_bcc) no_rekap_bcc,
                                  SUM (
                                     CASE
                                        WHEN thk.id_kualitas = 1 THEN thk.qty
                                     END)
                                     ebcc_jml_bm,
                                  SUM (
                                     CASE
                                        WHEN thk.id_kualitas = 2 THEN thk.qty
                                     END)
                                     ebcc_jml_bk,
                                  SUM (
                                     CASE
                                        WHEN thk.id_kualitas = 3 THEN thk.qty
                                     END)
                                     ebcc_jml_ms,
                                  SUM (
                                     CASE
                                        WHEN thk.id_kualitas = 4 THEN thk.qty
                                     END)
                                     ebcc_jml_or,
                                  SUM (
                                     CASE
                                        WHEN thk.id_kualitas = 6 THEN thk.qty
                                     END)
                                     ebcc_jml_bb,
                                  SUM (
                                     CASE
                                        WHEN thk.id_kualitas = 15 THEN thk.qty
                                     END)
                                     ebcc_jml_jk,
                                  SUM (
                                     CASE
                                        WHEN thk.id_kualitas = 16 THEN thk.qty
                                     END)
                                     ebcc_jml_ba
                             FROM ebcc.t_header_rencana_panen hrp
                                  LEFT JOIN ebcc.t_detail_rencana_panen drp
                                     ON hrp.id_rencana = drp.id_rencana
                                  LEFT JOIN ebcc.t_hasil_panen hp
                                     ON     hp.id_rencana = drp.id_rencana
                                        AND hp.no_rekap_bcc = drp.no_rekap_bcc
                                  LEFT JOIN ebcc.t_employee emp_ebcc
                                     ON emp_ebcc.nik = hrp.nik_kerani_buah
                                  LEFT JOIN ebcc.t_hasilpanen_kualtas thk
                                     ON     hp.no_bcc = thk.id_bcc
                                        AND hp.id_rencana = thk.id_rencana
                            WHERE     SUBSTR (id_ba_afd_blok, 1, 2) IN (SELECT comp_code
                                                                          FROM tap_dw.tm_comp@proddw_link)
                                  AND hp.no_bcc = '$id' --tinggal ganti nomor bcc nya
                         GROUP BY hrp.tanggal_rencana,
                                  SUBSTR (id_ba_afd_blok, 1, 4),
                                  SUBSTR (id_ba_afd_blok, 5, 1),
                                  SUBSTR (id_ba_afd_blok, 6, 3),
                                  hp.no_tph,
                                  hp.picture_name)) ebcc
                LEFT JOIN mobile_inspection.tr_validasi_detail validasi
                   ON     ebcc.no_bcc = REPLACE (validasi.no_bcc, '.')
                      AND ebcc.nik_kerani_buah = validasi.nik_krani_buah
                      AND ebcc.tanggal_rencana = validasi.tanggal_ebcc
                      AND ebcc.id_ba = validasi.ba_code
                      AND ebcc.id_afd = validasi.afd_code
                      AND ebcc.id_blok = validasi.block_code
                      AND ebcc.no_tph = validasi.no_tph) mst
        LEFT JOIN
        (SELECT DISTINCT tc.comp_name,
                         est.werks,
                         est.est_name,
                         afd.afd_code,
                         afd.afd_name,
                         blok.block_code,
                         blok.block_name
           FROM tap_dw.tm_comp@proddw_link tc
                LEFT JOIN tap_dw.tm_est@proddw_link est
                   ON tc.comp_code = est.comp_code
                LEFT JOIN tap_dw.tm_afd@proddw_link afd
                   ON est.werks = afd.werks
                LEFT JOIN tap_dw.tm_block@proddw_link blok
                   ON afd.werks = blok.werks AND afd.afd_code = blok.afd_code
          WHERE TRUNC (SYSDATE) BETWEEN est.start_valid AND est.end_valid) est
           ON     mst.id_ba = est.werks
              AND mst.id_afd = est.afd_code
              AND mst.id_blok = est.block_code
              WHERE ROWNUM = '1'
        ";
        $valid_data = json_encode($this->db_mobile_ins->select($sql));
        $results['data'] =  json_decode($valid_data,true);
        // dd($result['data']== null);
  		if ( !empty( $results['data']) ) {
  			return view( 'validasi/ebcc-compare', $results );
  		}
  		else {
  			return 'Data not found.';
  		}
	 }


}