<?php

# Namespace
namespace App\Http\Controllers;


# Default Laravel Vendor Setup
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use View;
use Validator;
use Redirect;
use Session;
use Config;
use URL;
use DateTime;
use Maatwebsite\Excel\Facades\Excel;

# API
use App\APISetup;
use App\APIData as Data;
// use App\Kafka;

class KafkaController extends Controller {
	public function index() {
		$produce = \Kafka\Produce::getInstance('149.129.252.13:2181', 3000);
		$produce->setRequireAck(-1);
		$produce->setMessages('test', 0, array('test1111111'));
		$produce->setMessages('test6', 0, array('test1111111'));
		$produce->setMessages('test6', 2, array('test1111111'));
		$produce->setMessages('test6', 1, array('test111111111133'));
		$result = $produce->send();
		var_dump($result);
	}
}