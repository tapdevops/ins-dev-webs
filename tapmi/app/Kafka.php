<?php

/**
 * Kafka Class
 *
 * @package  Laravel
 * @author   Ferdinand
 */

namespace App;
// require '../vendor/autoload.php';
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
// use \Kafka;

class Kafka extends Model {
	public function consumer() {
		$config = \Kafka\ConsumerConfig::getInstance();
		$config->setMetadataRefreshIntervalMs(10000);
		$config->setMetadataBrokerList('127.0.0.1:9092');
		$config->setGroupId('test');
		$config->setBrokerVersion('1.0.0');
		$config->setTopics(['test']);
		$consumer = new \Kafka\Consumer();

		$consumer->start(function($topic, $part, $message) {
			var_dump($message);
		});
	}
}