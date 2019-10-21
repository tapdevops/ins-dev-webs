<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use RdKafka;

class KafkaController extends Controller
{
    public function __construct() {
		$this->db_mobile_ins = DB::connection( 'mobile_ins' );
	}
	
	public function tes() {
		$this->db_mobile_ins->statement(" insert into testing (ID,FULLNAME,STATUS_TPH_SCAN,ALASAN_MANUAL) values('22','LVI','','') ");
		echo 'oke tes';
	}
	
	public function cek_offset_payload( $topic ) {
		$get = $this->db_mobile_ins->select( "SELECT * FROM TM_KAFKA_PAYLOADS WHERE TOPIC_NAME = '$topic'" );

		if ( count( $get ) ) {
			return $get[0]->offset;
		} 
		else {
			return false;
		}
	}
	
	public function RUN_INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_H() {

		// Kafka Config
		$topic = "INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_H";
		$Kafka = new RdKafka\Consumer();
		# $Kafka->setLogLevel(LOG_DEBUG);
		$Kafka->addBrokers( config('app.kafkahost') );
		$Topic = $Kafka->newTopic( $topic );
		$Topic->consumeStart( 0, RD_KAFKA_OFFSET_BEGINNING );


		while ( true ) {
			$message = $Topic->consume( 0, 1000 );
			if ( null === $message ) {
				continue;
			} 
			else if ( $message->err ) {
				echo $message->errstr(), "\n";
				break;
			} 
			else {
				$payload = json_decode( $message->payload, true );
				
				$last_offset = $this->cek_offset_payload( $topic );
				if ( $last_offset !== false ){
					if ( $last_offset ==null){
						if( (int)$message->offset >= $last_offset ){
							echo $this->insert_h( $payload, (int)$message->offset );
						}	
					} else {
						if ( (int)$message->offset > $last_offset ){
							echo $this->insert_h( $payload, (int)$message->offset );
						}	
					}
				}
			}
		}
		
	}
	
	public function RUN_INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_D() {

		// Kafka Config
		$topic = "INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_D";
		$Kafka = new RdKafka\Consumer();
		# $Kafka->setLogLevel(LOG_DEBUG);
		$Kafka->addBrokers( config('app.kafkahost') );
		$Topic = $Kafka->newTopic( $topic );
		$Topic->consumeStart( 0, RD_KAFKA_OFFSET_BEGINNING );

		while ( true ) {
			$message = $Topic->consume( 0, 1000 );
			if ( null === $message ) {
				continue;
			} 
			else if ( $message->err ) {
				echo $message->errstr(), "\n";
				break;
			} 
			else {
				$payload = json_decode( $message->payload, true );
				$last_offset = $this->cek_offset_payload( $topic );
				if ( $last_offset !== false ){
					if ( $last_offset == null ) {
						if ( (int)$message->offset >= $last_offset ) {
							echo $this->insert_d( $payload, (int)$message->offset );
						}	
					}
					else {
						if ( (int)$message->offset > $last_offset ) {
							echo $this->insert_d( $payload, (int)$message->offset );
						}	
					}
				}
			}
		}
		
	}


	public function RUN_INS_MSA_AUTH_TM_USER_AUTH() {

		// Kafka Config
		$topic = "INS_MSA_AUTH_TM_USER_AUTH";
		$Kafka = new RdKafka\Consumer();
		# $Kafka->setLogLevel(LOG_DEBUG);
		$Kafka->addBrokers( config('app.kafkahost') );
		$Topic = $Kafka->newTopic( $topic );
		$Topic->consumeStart( 0, RD_KAFKA_OFFSET_BEGINNING );

		while ( true ) {
			$message = $Topic->consume( 0, 1000 );
			if ( null === $message ) {
				continue;
			} 
			else if ( $message->err ) {
				echo $message->errstr(), "\n";
				break;
			} 
			else {
				$payload = json_decode( $message->payload, true );
				$last_offset = $this->cek_offset_payload( $topic );
				if ( $last_offset !== false ){
					if ( $last_offset == null ) {
						if ( (int)$message->offset >= $last_offset ) {
							echo $this->insert_tm_user_auth( $payload, (int)$message->offset );
						}	
					}
					else {
						if ( (int)$message->offset > $last_offset ) {
							echo $this->insert_tm_user_auth( $payload, (int)$message->offset );
						}	
					}
				}
			}
		}
		
	}

	# PHP Kafka MOBILE_INSPECTION.TR_FINDING
	public function RUN_INS_MSA_FINDING_TR_FINDING() {
		// Kafka Config
		$topic = "INS_MSA_FINDING_TR_FINDING";
		$Kafka = new RdKafka\Consumer();
		# $Kafka->setLogLevel(LOG_DEBUG);
		$Kafka->addBrokers( config('app.kafkahost') );
		$Topic = $Kafka->newTopic( $topic );
		$Topic->consumeStart( 0, RD_KAFKA_OFFSET_BEGINNING );

		while ( true ) {
			$message = $Topic->consume( 0, 1000 );
			if ( null === $message ) {
				continue;
			} 
			else if ( $message->err ) {
				echo $message->errstr(), "\n";
				break;
			} 
			else {
				$payload = json_decode( $message->payload, true );
				$last_offset = $this->cek_offset_payload( $topic );
				if ( $last_offset !== false ){
					if ( $last_offset == null ) {
						if ( (int)$message->offset >= $last_offset ) {
							echo $this->insert_tr_finding( $payload, (int)$message->offset );
						}	
					}
					else {
						if ( (int)$message->offset > $last_offset ) {
							echo $this->insert_tr_finding( $payload, (int)$message->offset );
						}	
					}
				}
			}
		}
	}
	
	public function insert_h( $payload, $offset ) {
		
		try {
			$INSTM = date( 'YmdHis', strtotime( $payload['INSTM'] ) );
			$STIME = date( 'YmdHis', strtotime( $payload['STIME'] ) );



			$sql = "INSERT INTO 
					MOBILE_INSPECTION.TR_EBCC_VALIDATION_H ( 
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
						UPDATE_TIME 
				) 
				VALUES (
					'{$payload['EBVTC']}', 
					'{$payload['WERKS']}', 
					'{$payload['AFD_CODE']}', 
					'{$payload['BLOCK_CODE']}', 
					'{$payload['NO_TPH']}', 
					'{$payload['STPHS']}', 
					'{$payload['ALSNM']}', 
					'{$payload['LAT_TPH']}', 
					'{$payload['LON_TPH']}', 
					'{$payload['DLVCD']}', 
					'{$payload['SDLVC']}', 
					'{$payload['INSUR']}', 
					to_date('$INSTM','YYYYMMDDHH24MISS'), 
					'{$payload['SSYNC']}', 
					to_date('$STIME','YYYYMMDDHH24MISS'), 
					'{$payload['UPTUR']}', 
					null 
				)";
			$this->db_mobile_ins->statement($sql);
			$this->db_mobile_ins->commit();
			
			//update offset payloads			
			$this->db_mobile_ins->statement( "
				UPDATE 
					MOBILE_INSPECTION.TM_KAFKA_PAYLOADS
				SET
					OFFSET = $offset,
					EXECUTE_DATE = SYSDATE
				WHERE
					TOPIC_NAME = 'INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_H'
			" );
			$this->db_mobile_ins->commit();
			return 'Insert Success'.PHP_EOL;
		}catch (\Throwable $e) {
			return 'Insert Failde: '.$e->getMessage().PHP_EOL;
			// return response()->json( $e->getMessage() );
        }catch (\Exception $e) {
			return 'Insert Failde: '.$e->getMessage().PHP_EOL;
		}
	}
	
	public function insert_d($payload, $offset)
	{
		$INSTM = ( (bool) strtotime( $payload['INSTM'] ) == true ? "to_date('".date( 'YmdHis', strtotime( $payload['INSTM'] ) )."','YYYYMMDDHH24MISS')" : "NULL" );
		$STIME = ( (bool) strtotime( $payload['STIME'] ) == true ? "to_date('".date( 'YmdHis', strtotime( $payload['STIME'] ) )."','YYYYMMDDHH24MISS')" : "NULL" );
		$check = collect( $this->db_mobile_ins->select( "
			SELECT 
				COUNT( * ) AS COUNT 
			FROM 
				TR_EBCC_VALIDATION_D
			WHERE
				EBCC_VALIDATION_CODE = '{$payload['EBVTC']}'
				AND ID_KUALITAS = '{$payload['IDKLT']}'
		" ) )->first();

		if ( $check->count == 0 ) {
			$sql = "
				INSERT INTO 
					MOBILE_INSPECTION.TR_EBCC_VALIDATION_D ( 
						EBCC_VALIDATION_CODE, 
						ID_KUALITAS, 
						JUMLAH, 
						INSERT_USER, 
						INSERT_TIME, 
						STATUS_SYNC, 
						SYNC_TIME 
					) 
				VALUES ( 
					'{$payload['EBVTC']}', 
					'{$payload['IDKLT']}', 
					'{$payload['JML']}', 
					'{$payload['INSUR']}', 
					$INSTM, 
					'{$payload['SSYNC']}',
					$STIME 
				)
			";
			
			try {
				$this->db_mobile_ins->statement( $sql );
				$this->db_mobile_ins->commit();
				
				// Update offset payloads
				$this->db_mobile_ins->statement( "
					UPDATE 
						MOBILE_INSPECTION.TM_KAFKA_PAYLOADS
					SET
						OFFSET = $offset,
						EXECUTE_DATE = SYSDATE
					WHERE
						TOPIC_NAME = 'INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_D'
				" );
				$this->db_mobile_ins->commit();
				return date( 'Y-m-d H:i:s' ).' - INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_D - INSERT '.$payload['EBVTC'].' - SUCCESS '.PHP_EOL;
			}
			catch ( \Throwable $e ) {
				return date( 'Y-m-d H:i:s' ).' - INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_D - INSERT '.$payload['EBVTC'].' - FAILED '.$e->getMessage().PHP_EOL;
	        }
	        catch ( \Exception $e ) {
				return date( 'Y-m-d H:i:s' ).' - INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_D - INSERT '.$payload['EBVTC'].' - FAILED '.$e->getMessage().PHP_EOL;
			}
		}
		else {
			// Update offset payloads
			$this->db_mobile_ins->statement( "
				UPDATE 
					MOBILE_INSPECTION.TM_KAFKA_PAYLOADS
				SET
					OFFSET = $offset,
					EXECUTE_DATE = SYSDATE
				WHERE
					TOPIC_NAME = 'INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_D'
			" );
			$this->db_mobile_ins->commit();
			return date( 'Y-m-d H:i:s' ).' - INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_D - INSERT '.$payload['EBVTC'].' - DUPLICATE '.PHP_EOL;
		}
	}

	public function insert_tm_user_auth( $payload, $offset ) {
		$sql = "INSERT INTO 
				MOBILE_INSPECTION.TM_USER_AUTH (
					USER_AUTH_CODE,
					EMPLOYEE_NIK,
					USER_ROLE,
					LOCATION_CODE,
					REF_ROLE,
					INSERT_USER,
					INSERT_TIME,
					UPDATE_USER,
					UPDATE_TIME,
					DELETE_USER,
					DELETE_TIME
				) 
			VALUES (
				'{$payload['URACD']}',
				'{$payload['EMNIK']}',
				'{$payload['URROL']}',
				'{$payload['LOCCD']}',
				'{$payload['RROLE']}',
				null,
				null,
				null,
				null,
				null,
				null
		)";

		try {
			$this->db_mobile_ins->statement($sql);
			$this->db_mobile_ins->commit();
			
			//update offset payloads			
			$this->db_mobile_ins->statement( "
				UPDATE 
					MOBILE_INSPECTION.TM_KAFKA_PAYLOADS
				SET
					OFFSET = $offset,
					EXECUTE_DATE = SYSDATE
				WHERE
					TOPIC_NAME = 'INS_MSA_AUTH_TM_USER_AUTH'
			" );
			$this->db_mobile_ins->commit();
			return 'Insert Success -> '.$payload["URACD"].PHP_EOL;
		} 
		catch ( \Throwable $e ) {
			return 'Insert Failde: '.$e->getMessage().PHP_EOL;
        }
        catch ( \Exception $e ) {
			return 'Insert Failde: '.$e->getMessage().PHP_EOL;
		}
	}


	public function insert_tr_finding( $payload, $offset ) {
		$INSTM = ( (bool) strtotime( $payload['INSTM'] ) == true ? "to_date('".date( 'YmdHis', strtotime( $payload['INSTM'] ) )."','YYYYMMDDHH24MISS')" : "NULL" );
		$DUE_DATE = ( (bool) strtotime( $payload['DUE_DATE'] ) == true ? "to_date('".date( 'YmdHis', strtotime( $payload['DUE_DATE'] ) )."','YYYYMMDDHH24MISS')" : "NULL" );
		$check = collect( $this->db_mobile_ins->select( "
			SELECT 
				COUNT( * ) AS COUNT 
			FROM 
				TR_FINDING
			WHERE
				FINDING_CODE = '{$payload['FNDCD']}'
		" ) )->first();

		if ( $check->count == 0 ) {

			$sql = "INSERT INTO 
					MOBILE_INSPECTION.TR_FINDING (
						FINDING_CODE,
						WERKS,
						AFD_CODE,
						BLOCK_CODE,
						FINDING_CATEGORY,
						FINDING_DESC,
						FINDING_PRIORITY,
						DUE_DATE,
						ASSIGN_TO,
						PROGRESS,
						LAT_FINDING,
						LONG_FINDING,
						REFFERENCE_INS_CODE,
						INSERT_USER,
						INSERT_TIME,
						UPDATE_USER,
						UPDATE_TIME,
						DELETE_USER,
						DELETE_TIME
					) 
				VALUES (
					'{$payload['FNDCD']}',
					'{$payload['WERKS']}',
					'{$payload['AFD_CODE']}',
					'{$payload['BLOCK_CODE']}',
					'{$payload['FNDCT']}',
					'{$payload['FNDDS']}',
					'{$payload['FNDPR']}',
					$DUE_DATE,
					'{$payload['ASSTO']}',
					{$payload['PRGRS']},
					'{$payload['LATFN']}',
					'{$payload['LONFN']}',
					'{$payload['RFINC']}',
					'',
					null,
					'',
					null,
					'',
					null
			)";

			try {
				$this->db_mobile_ins->statement( $sql );
				$this->db_mobile_ins->commit();
				
				// Update offset payloads
				$this->db_mobile_ins->statement( "
					UPDATE 
						MOBILE_INSPECTION.TM_KAFKA_PAYLOADS
					SET
						OFFSET = $offset,
						EXECUTE_DATE = SYSDATE
					WHERE
						TOPIC_NAME = 'INS_MSA_FINDING_TR_FINDING'
				" );
				$this->db_mobile_ins->commit();
				return date( 'Y-m-d H:i:s' ).' - INS_MSA_FINDING_TR_FINDING - INSERT '.$payload['FNDCD'].' - SUCCESS '.PHP_EOL;
			}
			catch ( \Throwable $e ) {
				return date( 'Y-m-d H:i:s' ).' - INS_MSA_FINDING_TR_FINDING - INSERT '.$payload['FNDCD'].' - FAILED '.$e->getMessage().PHP_EOL;
	        }
	        catch ( \Exception $e ) {
				return date( 'Y-m-d H:i:s' ).' - INS_MSA_FINDING_TR_FINDING - INSERT '.$payload['FNDCD'].' - FAILED '.$e->getMessage().PHP_EOL;
			}
		}
		else {
			// Update offset payloads
			$this->db_mobile_ins->statement( "
				UPDATE 
					MOBILE_INSPECTION.TM_KAFKA_PAYLOADS
				SET
					OFFSET = $offset,
					EXECUTE_DATE = SYSDATE
				WHERE
					TOPIC_NAME = 'INS_MSA_FINDING_TR_FINDING'
			" );
			$this->db_mobile_ins->commit();
			return date( 'Y-m-d H:i:s' ).' - INS_MSA_FINDING_TR_FINDING - INSERT '.$payload['FNDCD'].' - DUPLICATE '.PHP_EOL;
		}

	}
	
}
