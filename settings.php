<?php
//define('C_REST_CLIENT_ID','local.6076ef07246071.62408049');//Application ID
//define('C_REST_CLIENT_SECRET','yzXrc9oPvym3F3iRdQ8vIKivYmbd17nQH6yGUuctfTmNLWuST5');//Application key
// or
//define('C_REST_WEB_HOOK_URL','https://rest-api.bitrix24.com/rest/1/doutwqkjxgc3mgc1/');//url on creat Webhook

//define('C_REST_CURRENT_ENCODING','windows-1251');
//define('C_REST_IGNORE_SSL',true);//turn off validate ssl by curl
//define('C_REST_LOG_TYPE_DUMP',true); //logs save var_export for viewing convenience
//define('C_REST_BLOCK_LOG',true);//turn off default logs
//define('C_REST_LOGS_DIR', __DIR__ .'/logs/'); //directory path to save the log
//if (isset($_REQUEST['AUTH_ID'])){
	$_REQUEST['AUTH_ID'] = '3bc97c600053d3a20053d31c0000000100000381a17e510b1cf92f8cd6d0d17cba23fe';
	$_REQUEST['DOMAIN'] = 'b24-68ns2y.bitrix24.ru';
	//$_REQUEST['APP_SID'] = 'c5e592c6c23e43ca3c00c3fcc2a0b957';
//}


function find($method, $params){

	$queryUrl = 'https://'.$_REQUEST['DOMAIN'].'/rest/'.$method.'.json';
	$queryData = http_build_query(array_merge($params, array('auth' => $_REQUEST['AUTH_ID'])));


	$curl = curl_init();
	curl_setopt_array($curl, array(

		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_POST => 1,
		CURLOPT_HEADER => 0,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $queryUrl,
		CURLOPT_POSTFIELDS => $queryData,
	));

	$result = json_decode(curl_exec($curl), true);
	curl_close($curl);
	return $result;
}

function notice($id, $message){
	$method = 'im.notify';
	$params = [
		'to' => $id,
		'message' => $message
	];

	$queryUrl = 'https://'.$_REQUEST['DOMAIN'].'/rest/'.$method.'.json';
	$queryData = http_build_query(array_merge($params, array('auth' => $_REQUEST['AUTH_ID'])));

	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_POST => 1,
		CURLOPT_HEADER => 0,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $queryUrl,
		CURLOPT_POSTFIELDS => $queryData,
	));

	$result = json_decode(curl_exec($curl), true);
	curl_close($curl);
}
