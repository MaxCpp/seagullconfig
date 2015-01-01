<?php

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

	require_once('./classes/class.seagullconfig.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/manager/includes/config.inc.php');

	$connect = db_connect($database_server, $database_user, $database_password);
	$db = str_replace('`', '', $dbase);
	$db = db_select($db, $connect);

	if (!$db) {
		echo 'Невозможно установить соединение c базой данных "'.$dbase.'" на "'.$database_server.'"';
		exit();
	}

	$sc = new CSeagullConfig($msg);
	$response = array();

	switch ($_REQUEST['cmd']) {
		case 'saveBooking':
			if (isset($_POST)) {
				$r = $sc->saveBooking($_POST);

				if ($r) {
					$msg->setInfo('Благодарю. Ваш отзыв принят.');
				}
				elseif (!$msg->keep)
					$msg->setError('Отзыв не сохранен. Попробуйте еще раз.');
			}
		break;

		case 'delBooking':
			if (isset($_POST)) {
				$r = $sc->delBooking($_POST['itemID']);

				if ($r) {
					$msg->setInfo('Заявка удалена');
				}
				elseif (!$msg->keep)
					$msg->setError('Заявка не удалена. Попробуйте еще раз.');
			}
		break;

		case 'saveConfig':
			if ($sc->config->saveForm($_POST['config'], $sc->nameModule))
				$msg->setOk('Настройки сохранены');
			else
				$msg->setError('Ошибка при сохранении');
		break;

		case 'getPaginatorPage':
			$response['tbody'] = $sc->tables['booking']->renderTableBody($_REQUEST['pageID']);
			$response['links'] = $sc->tables['booking']->renderPaginatorLinks($_REQUEST['pageID']);
		break;

		case 'addvariable':
			$sc->config->addVariable($_POST);
		break;
	}
	$response = array_merge($response, $msg->get());
	echo json_encode($response);
}
?>
