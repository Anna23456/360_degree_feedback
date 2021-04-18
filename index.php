<?php
print_r($_REQUEST);

require_once (__DIR__.'/settings.php');

$params =  ['USER_TYPE'=>'employee'];
$employees = find('user.get', $params);//ищем всех сотрудников

require_once 'content/index.html';

if (isset($_POST['fio'])){

	$idsAboutTest = $_POST['fio'];//checked пункты
	print_r($idsAboutTest);
	echo '		
	<script> 
		$(document).ready(function() {
			$(".toast").toast("show");
		})
	</script>';//высплывающее уведомление

	foreach ($idsAboutTest as $idAboutTest) {
		$method = 'user.get';
		$params =  ['ID'=>$idAboutTest];
		$aboutTest = find($method, $params);//о ком проходим тест
		$lastNameAboutTest = $aboutTest['result']['0']['LAST_NAME'];
		$idsDepUserAboutTest = $aboutTest['result']['0']['UF_DEPARTMENT'];
		
		$date = date('y-m-d');
		$link = '<a href="test.php?id='.$idAboutTest.'&date='.$date.'">Тест здесь.</a>';
		echo $link;
		$message = 'Пожалуйста, пройдите тестирование о '.$lastNameAboutTest.'. '.$link;

		foreach ($idsDepUserAboutTest as $idDepUserAboutTest) {
			$depUserAboutTest = find('department.get', ['ID'=>$idDepUserAboutTest]);
			$head = $depUserAboutTest['result']['0']['UF_HEAD'];

			if ($head==$idAboutTest && isset($depUserAboutTest['result']['0']['PARENT'])) {//если проходим о начальнике и есть вышестоящий отдел
				$idParentDep = $depUserAboutTest['result']['0']['PARENT'];
				$parentDep = find('department.get', ['ID'=>$idParentDep]);

				notice($parentDep['result']['0']['UF_HEAD'], $message);
				//уведомление руководителю вышестоящего
			}
			

			$colleagues = find('user.get', ['UF_DEPARTMENT'=>$idDepUserAboutTest]);
			foreach ($colleagues['result'] as $colleague) 
				notice($colleague['ID'], $message);//уведомление всем сотрудникам отдела, включая руководителя
		}
	}
}

?>
