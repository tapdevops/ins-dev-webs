<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use RdKafka;

class KafkaController extends Controller
{
    public function __construct() {
		$this->env = 'dev';
		$this->db_mobile_ins = ($this->env == 'production' ? DB::connection('mobile_ins') : DB::connection('mobile_ins_dev'));
	}
	
	public function tes()
	{
		$this->db_mobile_ins->statement(" insert into testing (ID,FULLNAME,STATUS_TPH_SCAN,ALASAN_MANUAL) values('22','LVI','','') ");
		echo 'oke tes';
	}
	
	public function cek_offset_payload($topic)
	{
		$get = $this->db_mobile_ins->select("select * from TM_KAFKA_PAYLOADS where TOPIC_NAME = '$topic'");
		// print_r($get);die;
		if(count($get)){
			return $get[0]->offset;
		}else{
			return false;
		}
	}
	
	public function RUN_INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_H()
	{
		$topic = "INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_H";
		$Kafka = new RdKafka\Consumer();
		$Kafka->setLogLevel(LOG_DEBUG);
		$Kafka->addBrokers( "149.129.252.13" );
		$Topic = $Kafka->newTopic( $topic );
		$Topic->consumeStart( 0, RD_KAFKA_OFFSET_BEGINNING );
		while ( true ) {
			$message = $Topic->consume( 0, 1000 );
			if ( null === $message ) {
				continue;
			} elseif ( $message->err ) {
				echo $message->errstr(), "\n";
				break;
			} else {
				$payload = json_decode( $message->payload, true );
				
				$last_offset = $this->cek_offset_payload($topic);
				if( $last_offset !== false ){
					if($last_offset==null){
						if( (int)$message->offset >= $last_offset ){
							$this->insert_h( $payload, (int)$message->offset );
						}	
					}else{
						if( (int)$message->offset > $last_offset ){
							$this->insert_h( $payload, (int)$message->offset );
						}	
					}
				}
			}
		}
		
		
	}
	
	public function RUN_INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_D()
	{
		$topic = "INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_D";
		$Kafka = new RdKafka\Consumer();
		$Kafka->setLogLevel(LOG_DEBUG);
		$Kafka->addBrokers( "149.129.252.13" );
		$Topic = $Kafka->newTopic( $topic );
		$Topic->consumeStart( 0, RD_KAFKA_OFFSET_BEGINNING );
		while ( true ) {
			$message = $Topic->consume( 0, 1000 );
			if ( null === $message ) {
				continue;
			} elseif ( $message->err ) {
				echo $message->errstr(), "\n";
				break;
			} else {
				$payload = json_decode( $message->payload, true );
				$last_offset = $this->cek_offset_payload($topic);
				if( $last_offset !== false ){
					if($last_offset==null){
						if( (int)$message->offset >= $last_offset ){
							$this->insert_d( $payload, (int)$message->offset );
						}	
					}else{
						if( (int)$message->offset > $last_offset ){
							$this->insert_d( $payload, (int)$message->offset );
						}	
					}
				}
			}
		}
		
		
	}
	
	public function insert_h($payload, $offset)
	{
		$prm = array('EBCC_VALIDATION_CODE', 'WERKS', 'AFD_CODE', 'BLOCK_CODE', 'NO_TPH', 'STATUS_TPH_SCAN', 'ALASAN_MANUAL', 'LAT_TPH', 'LON_TPH', 'DELIVERY_CODE', 'STATUS_DELIVERY_CODE', 'INSERT_USER', 'INSERT_TIME', 'STATUS_SYNC', 'SYNC_TIME', 'UPDATE_USER', 'UPDATE_TIME');
		
		$object = (object) $payload;
		foreach($prm as $prm){
			$val[$prm] = $object->$prm;
		}
		
		try{
			$sql = "INSERT INTO MOBILE_INSPECTION.TR_EBCC_VALIDATION_H ( 
							EBCC_VALIDATION_CODE, 
							WERKS, 
							AFD_CODE, 
							BLOCK_CODE,
							NO_TPH, 
							STATUS_TPH_SCAN, 
							ALASAN_MANUAL, 
							LAT_TPH, 
							LON_TPH, 
							DELIVERY_CODE, 
							STATUS_DELIVERY_CODE, 
							INSERT_USER, 
							INSERT_TIME, 
							STATUS_SYNC, 
							SYNC_TIME, 
							UPDATE_USER, 
							UPDATE_TIME ) 
							VALUES (
								'{$val['EBCC_VALIDATION_CODE']}', 
								'{$val['WERKS']}', 
								'{$val['AFD_CODE']}', 
								'{$val['BLOCK_CODE']}', 
								'{$val['NO_TPH']}', 
								'{$val['STATUS_TPH_SCAN']}', 
								'{$val['ALASAN_MANUAL']}', 
								'{$val['LAT_TPH']}', 
								'{$val['LON_TPH']}', 
								'{$val['DELIVERY_CODE']}', 
								'{$val['STATUS_DELIVERY_CODE']}', 
								'{$val['INSERT_USER']}', 
								to_date('{$val['INSERT_TIME']}','YYYYMMDDHH24MISS'), 
								'{$val['STATUS_SYNC']}', 
								to_date('{$val['SYNC_TIME']}','YYYYMMDDHH24MISS'), 
								'{$val['UPDATE_USER']}', 
								null )";
			$this->db_mobile_ins->statement($sql);
			$this->db_mobile_ins->commit();
			
			//update offset payloads			
			$this->db_mobile_ins->statement("UPDATE 
												MOBILE_INSPECTION.TM_KAFKA_PAYLOADS
											SET
												OFFSET = $offset,
												EXECUTE_DATE = SYSDATE
											WHERE
												TOPIC_NAME = 'INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_H'");
			$this->db_mobile_ins->commit();
			
		}catch (\Throwable $e) {
			// return response()->json( $e->getMessage() );
        }catch (\Exception $e) {
			//
		}
	}
	
	public function insert_d($payload, $offset)
	{
		$prm = array('EBCC_VALIDATION_CODE','ID_KUALITAS','JUMLAH','INSERT_USER','INSERT_TIME','STATUS_SYNC','SYNC_TIME');
		$object = (object) $payload;
		foreach($prm as $prm){
			$val[$prm] = $object->$prm;
		}
		
		$sql = " INSERT INTO MOBILE_INSPECTION.TR_EBCC_VALIDATION_D ( 
							EBCC_VALIDATION_CODE, 
							ID_KUALITAS, 
							JUMLAH, 
							INSERT_USER, 
							INSERT_TIME, 
							STATUS_SYNC, 
							SYNC_TIME ) 
							VALUES ( 
								'{$val['EBCC_VALIDATION_CODE']}', 
								'{$val['ID_KUALITAS']}', 
								'{$val['JUMLAH']}', 
								'{$val['INSERT_USER']}', 
								to_date('{$val['INSERT_TIME']}','YYYYMMDDHH24MISS'), 
								'{$val['STATUS_SYNC']}',
								to_date('{$val['SYNC_TIME']}','YYYYMMDDHH24MISS') )";
		
		try{
			$this->db_mobile_ins->statement($sql);
			$this->db_mobile_ins->commit();
			
			//update offset payloads			
			$this->db_mobile_ins->statement("UPDATE 
												MOBILE_INSPECTION.TM_KAFKA_PAYLOADS
											SET
												OFFSET = $offset,
												EXECUTE_DATE = SYSDATE
											WHERE
												TOPIC_NAME = 'INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_D'");
			$this->db_mobile_ins->commit();
		}catch (\Throwable $e) {
			// return response()->json( $e->getMessage() );
        }catch (\Exception $e) {
			//
		}
	}
	
}
