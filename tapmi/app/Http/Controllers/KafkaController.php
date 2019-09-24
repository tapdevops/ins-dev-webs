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
	
	public function RUN_INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_H()
	{
		$Kafka = new RdKafka\Consumer();
		$Kafka->setLogLevel(LOG_DEBUG);
		$Kafka->addBrokers( "149.129.252.13" );
		$Topic = $Kafka->newTopic( "INS_MSA_EBCCVAL_TR_EBCC_VALIDATION_H" );
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
				// print_r($payload);
				$this->insert( $payload );
			}
		}
		
		
	}
	
	public function insert_h($payload)
	{
		//$EBCC_VALIDATION_CODE, $WERKS, $AFD_CODE, $BLOCK_CODE, $NO_TPH, $STATUS_TPH_SCAN, $ALASAN_MANUAL, $LAT_TPH, $LON_TPH, $DELIVERY_CODE, $STATUS_DELIVERY_CODE, $INSERT_USER, $INSERT_TIME, $STATUS_SYNC, $SYNC_TIME, $UPDATE_USER, $UPDATE_TIME
		
		try{
			$this->db_mobile_ins->statement(" INSERT INTO 
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
											VALUES ('$EBCC_VALIDATION_CODE',
													 '$WERKS',
													 '$AFD_CODE',
													 '$BLOCK_CODE',
													 '$NO_TPH',
													 '$STATUS_TPH_SCAN',
													 '$ALASAN_MANUAL',
													 '$LAT_TPH',
													 '$LON_TPH',
													 '$DELIVERY_CODE',
													 '$STATUS_DELIVERY_CODE',
													 '$INSERT_USER',
													 '$INSERT_TIME',
													 '$STATUS_SYNC',
													 '$SYNC_TIME',
													 '$UPDATE_USER',
													 '$UPDATE_TIME'); ");
			$this->db_mobile_ins->commit();
		}catch (\Throwable $e) {
			// return response()->json( $e->getMessage() );
        }catch (\Exception $e) {
			//
		}
	}
	
	public function insert_d()
	{
		try{
			// $this->db_mobile_ins->statement(" insert into testing (ID,FULLNAME,STATUS_TPH_SCAN,ALASAN_MANUAL) values('22','LVI','','') ");
			$this->db_mobile_ins->statement(" INSERT INTO 
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
												'A001',
												'2',
												'2',
												'0101',
												TO_DATE( '2019-01-01 10:02:03', 'RRRR-MM-DD HH24:MI:SS' ),
												'SYNC',
												TO_DATE( '2019-01-01 10:02:03', 'RRRR-MM-DD HH24:MI:SS' )
											) ");
			$this->db_mobile_ins->commit();
		}catch (\Throwable $e) {
			// return response()->json( $e->getMessage() );
        }catch (\Exception $e) {
			//
		}
	}
	
}
