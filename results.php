<?php
require_once (__DIR__.'/settings.php');
require_once 'connection.php';

$link = mysqli_connect($host, $user, $password, $database) 
    	or die('Ошибка ' . mysqli_error($link));
$query = 'SELECT DISTINCT idAboutTest FROM results';
$idsAboutTest = mysqli_query($link, $query) or die ('Ошибка '.mysqli_error($link)); //получаем всех о ком проходили тесты

$query = 'SELECT * FROM skills';
$skills = mysqli_query($link, $query) or die ('Ошибка '.mysqli_error($link)); //получаем все компетенции из бд
while ($row = mysqli_fetch_array($skills)) {
	$idsSkill[] = $row['idSkill'];
	$Skills[] = $row['competency'];
}
/*while ($row = mysqli_fetch_array($answers)) {
	$user = find('user.search', ['ID'=>$row['idAboutTest']]);
	$fio = $user['result']['0']['LAST_NAME'].' '.substr($user['result']['0']['NAME'],0,1).'.'.substr($user['result']['0']['SECOND_NAME'],0,1)'.';
	$date = $row['date'];
	$i=0;
	$answers[$i]['fio'] = $fio; 
	$answers[$i]['date'] = $date;
	$answers[$i]['self'] = [];  
	$answers[$i]['boss'] = [];  
	$answers[$i]['under'] = []; 
	$answers[$i]['collegues'] = []; 
	$answers[$i]['mean'] = [];  
}*/ 
while ($row = mysqli_fetch_array($idsAboutTest)) {
 	$id = $row['idAboutTest'];
 	$user = find('user.search', ['ID'=>$id]);
	$fio = $user['result']['0']['LAST_NAME'].' '.mb_substr($user['result']['0']['NAME'],0,1).'.';
	//if (isset($user['result']['0']['SECOND_NAME'])) $fio .= mb_substr($user['result']['0']['SECOND_NAME'],0,1).'.';
 	$data[$id]['fio'] = $fio;
 	$query = 'SELECT * FROM results WHERE idAboutTest='.$id.' AND typeWhoTookTest="self"';
	$answers = mysqli_query($link, $query) or die ('Ошибка '.mysqli_error($link)); //получаем самооценку об одном id
	if (mysqli_num_rows($answers) > 0) 
		while ($row = mysqli_fetch_array($answers)) {
			$data[$id]['self'][$row['idSkill']] = $row['valueAnswer'];
		};
	$query = 'SELECT DISTINCT date FROM results WHERE idAboutTest='.$id;
	$answers = mysqli_query($link, $query) or die ('Ошибка '.mysqli_error($link)); 
	$data[$id]['date'] = mysqli_fetch_array($answers)['date'];//дата теста одна для всех проходящих


	$query = 'SELECT * FROM results WHERE idAboutTest='.$id.' AND typeWhoTookTest="boss"';
	$answers = mysqli_query($link, $query) or die ('Ошибка '.mysqli_error($link)); //получаем оценку руководства об одном id
	if (mysqli_num_rows($answers) > 0) 
		while ($row = mysqli_fetch_array($answers)) {
			$data[$id]['boss'][$row['idSkill']] = $row['valueAnswer'];
		};

	$query = 'SELECT * FROM results WHERE idAboutTest='.$id.' AND typeWhoTookTest="under"';
	$answers = mysqli_query($link, $query) or die ('Ошибка '.mysqli_error($link)); //получаем оценку подчиненных об одном id
	$count = mysqli_num_rows($answers);
	if ($count > 0) {
		while ($row = mysqli_fetch_array($answers)) {
			$under[$row['idSkill']] = ($under[$row['idSkill']]) ? $under[$row['idSkill']]+$row['valueAnswer'] : $row['valueAnswer'];
		};
		foreach ($under as $idSkill => $valueSkill) {
			$data[$id]['under'][$idSkill] = round($valueSkill/$count);
		}
	}

	$query = 'SELECT * FROM results WHERE idAboutTest='.$id.' AND typeWhoTookTest="collegue"';
	$answers = mysqli_query($link, $query) or die ('Ошибка '.mysqli_error($link)); //получаем оценку коллег об одном id
	$count = mysqli_num_rows($answers);
	if ($count > 0){ 
		while ($row = mysqli_fetch_array($answers)) {
			$collegue[$row['idSkill']] = ($collegue[$row['idSkill']]) ? $collegue[$row['idSkill']]+$row['valueAnswer'] : $row['valueAnswer'];
		};
		foreach ($collegue as $idSkill => $valueSkill) {
			$data[$id]['under'][$idSkill] = round($valueSkill/$count);
		}
	}

	foreach ($idsSkill as $idSkill) {
		$count = 0;
		$data[$id]['mean'][$idSkill] = 0;
		if (isset($data[$id]['self'][$idSkill])){ $data[$id]['mean'][$idSkill] += $data[$id]['self'][$idSkill]; $count++;} else $data[$id]['self'][$idSkill] = '-';

		if (isset($data[$id]['boss'][$idSkill])){ $data[$id]['mean'][$idSkill] += $data[$id]['boss'][$idSkill]; $count++;} else $data[$id]['boss'][$idSkill] = '-';

		if (isset($data[$id]['under'][$idSkill])){$data[$id]['mean'][$idSkill] += $data[$id]['under'][$idSkill]; $count++;} else $data[$id]['under'][$idSkill] = '-';

		if (isset($data[$id]['collegue'][$idSkill])){$data[$id]['mean'][$idSkill] += $data[$id]['collegue'][$idSkill]; $count++;} else $data[$id]['collegue'][$idSkill] = '-';

		if ($count!=0)
			$data[$id]['mean'][$idSkill] = round($data[$id]['mean'][$idSkill]/$count);
		else $data[$id]['mean'][$idSkill] = '-';
	}
	
}
//var_dump($data);
$typesWhoTook = ['self', 'boss', 'collegue', 'under', 'mean'];
require_once 'content/results.html';
?>
