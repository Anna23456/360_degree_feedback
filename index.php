<?php

$_REQUEST['DOMAIN'] = 'b24-68ns2y.bitrix24.ru';
$_REQUEST['AUTH_ID'] = '5a5479600053d3a20053d31c00000001000003ae3051ccfa5b8c82e97e33cd185e2520';

$params =  ['USER_TYPE'=>"employee"];
$result = find('user.get', $params);
//out($_REQUEST);

function out($var){
	echo '<pre style="padding:5px;margin:10px;">';
	if(is_string($var)) $var = htmlspecialchars($var);
	print_r($var);
	echo '</pre>';
}

function notice($id, $message){
	$method = 'im.notify';
	$params = [
		'to' => $id,
		'message' => $message
	];

	$queryUrl = 'https://'.$_REQUEST['DOMAIN'].'/rest/'.$method.'.json';
	$queryData = http_build_query(array_merge($params, array("auth" => $_REQUEST['AUTH_ID'])));

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

function find($method, $params){

	$queryUrl = 'https://'.$_REQUEST['DOMAIN'].'/rest/'.$method.'.json';
	$queryData = http_build_query(array_merge($params, array("auth" => $_REQUEST['AUTH_ID'])));


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

?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Выбор пользователей</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
		<link rel="stylesheet" type="text/css" href="style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	</head>
	<body>

		<h2> Выберите сотрудников для оценки:</h2>
		<form method="POST">
			<ul>
				<?php 
				foreach ($result['result'] as $user) { 
					echo '<li><input type="checkbox" name="fio[]" value="'.$user['ID'].'"> '.$user['LAST_NAME'].' '.$user['NAME'].$user['SECOND_NAME'].' </li>';	
				} ?>
			</ul>
			<input type='submit' class='btn btn-outline-dark' id='start' value='Заупстить'>
		</form>

		<!-- высплывающее уведомление -->
		<div class="position-fixed top-0 end-0 p-3 text-light" style="z-index: 5">
		  <div id="liveToast" class="toast hide bg-dark" role="alert" aria-live="assertive" aria-atomic="true" style="width:400px">
		    <div class="toast-body">
		     	Уведомления о прохождении теста успешно разосланы.
		    	<!-- <button type="button" class="btn-close bg-light" data-bs-dismiss="toast" aria-label="Close"></button> -->
		    </div>
		  </div>
		</div>

		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
	</body>
</html>

<?php

if (isset($_POST['fio'])){
	$ids = $_POST['fio'];//checked пункты

	echo "<script> 
	$(document).ready(function(){
		$('.toast').toast('show');
		})
		</script>";//высплывающее уведомление

	foreach ($ids as $id) {
		$method = 'user.get';
		$params =  ['ID'=>$id];
		$res = find($method, $params);
		foreach ($res['result'] as $user) {
			$lastName = $user['LAST_NAME'];
			$departments = $user['UF_DEPARTMENT'];//получаем id отделов
		}
		$_REQUEST['id']=$id;
		
		$link = "<a href='test.php?id=".$id."'>Тест здесь.</a>";
		echo $link;
		$message = "Пожалуйста, пройдите тестирование о ".$lastName.". ".$link;

		foreach ($departments as $department) {
			$res = find('department.get', ['ID'=>$department]);

			foreach ($res['result'] as $dep) {
				$head = $dep['UF_HEAD'];
				if($head!=$id)
					notice($head, $message);//уведомление руководителю отдела
				else{
					if (isset($dep['PARENT'])){
						$parentDep = $dep['PARENT'];
						$r = find('department.get', ['ID'=>$parentDep]);
						foreach ($r['result'] as $d)
							notice($d, $message);
							//если тест по руководителю отдела, то уведомление руководителю вышестоящего
					}
				}
			}

			$res = find('user.get', ['UF_DEPARTMENT'=>$department]);
			foreach ($res['result'] as $user) {
				notice($user['ID'], $message);//уведомление всем сотрудникам отдела
			}
		}
	}
}

?>
