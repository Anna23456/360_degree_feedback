<?php
require_once (__DIR__.'/settings.php');
require_once 'connection.php';

$link = mysqli_connect($host, $user, $password, $database) 
    	or die('Ошибка ' . mysqli_error($link));
$query = 'SELECT * FROM skills';
$skills = mysqli_query($link, $query) or die ('Ошибка '.mysqli_error($link)); //получаем все компетенции из бд

$date = $_GET['date']; 
$idAboutTest = $_GET['id']; 
$aboutTest = find('user.get', ['ID' => $idAboutTest]);
$nameAboutTest = $aboutTest['result']['0']['LAST_NAME'];

require_once 'content/test.html';

if (isset($_POST[$idSkill[0]])) {
	

	foreach ($idSkill as $id) {
		$answers[$id] = $_POST[$id]; //получаем ответы оценки
	}

	//получаем данные о текущем пользователе
	$curUser = find('user.current', []);
	$idCurUser = $curUser['result']['ID'];

	//выясняем кем приходится проголосовавший
	if ($idCurUser == $idAboutTest) $TypeCurUser = 'self'; else {
	$idsDepCurUser = $curUser['result']['UF_DEPARTMENT'];
	$idsDepAboutTest = $aboutTest['result']['0']['UF_DEPARTMENT'];
	foreach ($idsDepAboutTest  as $idDepAbouTest) {
		foreach ($idsDepCurUser as $idDepCurUser) {
			if ($idDepAbouTest == $idDepCurUser)  
				$idDep = $idDepCurUser;//значит работают в одном отделе
		}	
	}
	if (isset($idDep)) { 
		$dep = find('department.get', ['ID'=>$idDep]);
		switch ($dep['result']['0']['UF_HEAD']) {
			case $idCurUser:
				$TypeCurUser = 'boss';//начальник
				break;
			case $idAboutTest:
				$TypeCurUser = 'under';//подчиненный
				break;
			default:
				$TypeCurUser = 'collegue';//коллега
				break;
		}
	}//если else значит из другого отдела, а уведомление в другой отдел могло уйти только если проходим тест о начальнике
	else $TypeCurUser = 'boss';//начальник из вышестоящего отдела
	}

	//данные для записи в бд
	print_r($answers);
	echo $date;
	echo $idAboutTest;
	echo $idCurUser;
	echo $TypeCurUser;

	foreach ($answers as $idSkill => $value) {
		echo $idSkill.' '.$value;
		$query = 'INSERT INTO results (idAboutTest, idWhoTookTest, typeWhoTookTest, date, idSkill, valueAnswer) VALUES ('.$idAboutTest.', '.$idCurUser.', "'.$TypeCurUser.'", "'.$date.'", '.$idSkill.', '.$value.') ON DUPLICATE KEY UPDATE typeWhoTookTest="'.$TypeCurUser.'", date="'.$date.'", valueAnswer='.$value;
		$insert = mysqli_query($link, $query) or die('Ошибка ' . mysqli_error($link));
		echo $insert;
	}
	header("Location: results.php");

}
?>