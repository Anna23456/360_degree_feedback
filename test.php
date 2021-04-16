<?php
require_once 'connection.php';
$link = mysqli_connect($host, $user, $password, $database) 
    	or die("Ошибка " . mysqli_error($link));
$query ="SELECT * FROM skills";
$skills = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link)); 

$idAboutTest = $_GET['id']; 
$aboutTest = find('user.get', ['ID'=>$idAboutTest]);
foreach ($aboutTest['result'] as $user) {
			$nameAboutTest = $user['LAST_NAME'];
		}
function find($method, $params){


	$_REQUEST['DOMAIN'] = 'b24-68ns2y.bitrix24.ru';
	$_REQUEST['AUTH_ID'] = '5a5479600053d3a20053d31c00000001000003ae3051ccfa5b8c82e97e33cd185e2520';

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
		<title>Оценка</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
		<link rel="stylesheet" type="text/css" href="style.css">
		
	</head>
	<body>
		<h2>Оценка 360 </h2>
		<div class="decription">
			<p> Вы проходите тест об <?=$nameAboutTest?>.</p>
			<p> Выберете подходящуюю, по вашему мнению, оценку сотрудника по 5-ти бальной шкале.</p>
		</div>
		<form method="POST">
			<table id="test">
				<tr>
					<th> Компетенция </th>
					<th> Очень плохо </th>
					<th> Плохо </th>
					<th> Средне </th>
					<th> Хорошо </th>
					<th> Очень хорошо </th>
					<th> Не знаю </th>
				</tr>
				<?php
				  	while ($row = mysqli_fetch_array($skills)) {
				  		$id_skill[] = $row['id_skill'];
					  	echo "<tr><td>".$row['competency']."</td>";
					  	for ($i = -2; $i < 3; $i++) {
					  		echo "<td><input type='radio' name='".$row['id_skill']."' value='".$i."'></td>";
					  	}
					  	echo "<td><input type='radio' name='".$row['id_skill']."' value='100' checked></td>";//Вариант "не знаю"
					  	echo "</tr>";
					}
				?>
			</table>
			<input type='submit' class='btn btn-outline-dark' id='submit' value='Завершить'>
		</form>
	</body>
</html>

<?php
if (isset($_POST[$id_skill[0]])){
	$date = date("d.m.y");

	foreach ($id_skill as $id) {
		$answers[$id] = $_POST[$id]; //получаем ответы оценки
	}

	//получаем данные о текущем пользователе
	$method = 'user.current';
	$cur = find($method, []);
	$idCur = $cur['result']['ID'];

	//данные для записи в бд
	print_r($answers);
	echo $date;
	echo $idAboutTest;
	echo $idCur;
	
}
?>