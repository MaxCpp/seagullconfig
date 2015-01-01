<?php
/*
	Class SeagullConfig 0.0.1
	Date start: 2014-01-01
*/
require_once($_SERVER['DOCUMENT_ROOT'].'/assets/modules/seagulllibrary/class.seagullmodule.php');
//require_once(SITE_ROOT.'/assets/modules/trainings/classes/class.trainings.php');

class CSeagullConfig extends CSeagullModule {
	var $nameModule = 'seagullconfig';

	function __construct() { //------------------------------------------------------
		$args = func_get_args();

		if (isset($args[0])) {
			$this->msg = $args[0];
		}

		if (isset($args[1])) {
			$this->modx = $args[1];
		}

		$this->config = new CConfig($this->msg);
		$this->config->getVariables($this->nameModule);
		$this->ph['title'] = 'Глобальные настройки';
		$this->ph['nameModule'] = $this->nameModule;

//-------------------------------------------------
		$columns = array();
		$columns['id'] = array(
					'title'=>'ID',
					'form_hidden'=>true,
					'form_dontEdit'=>true,
					'table_theadParam'=>'style="width:30px"'
					);

		$columns['state'] =	array(
					'title'=>'Состояние',
					'form_fieldType'=>'select',
					'values'=>array('0'=>'Ожидаем','1'=>'Не приехали','2'=>'Проживают','3'=>'Выехали'),
					'table_value2key'=>true,
					'table_theadParam'=>'style="width:80px"'
					);

/*		$columns['guaranty'] =	array(
					'title'=>'Гарантия',
					'form_fieldType'=>'checkbox',
					'values'=>array('0'=>'Ожидаем','1'=>'Не приехали','2'=>'Проживают','3'=>'Выехали'),
					'table_value2key'=>true,
					'table_theadParam'=>'style="width:80px"'
					);
*/
/*		$columns['group_review'] = array(
					'title'=>'Группы мероприятий',
					'values'=>$this->trainings->getArrayTrainings(),
					'form_fieldType'=>'select',
					'table_value2key'=>true
					);
*/
		$columns['fio'] =	array(
					'title'=>'ФИО',
					'form_fieldType'=>'input',
					'form_fieldParam'=>'style="width:300px"'
					);

		$columns['email'] =	array(
					'title'=>'E-mail',
					'form_fieldType'=>'input',
					'form_fieldParam'=>'style="width:300px"'
					);

		$columns['phone'] =	array(
					'title'=>'Телефон',
					'form_fieldType'=>'input',
					'form_fieldParam'=>'style="width:120px"',
					'table_theadParam'=>'style="width:120px"'
					);

		$columns['arrival_date'] = array(
					'title'=>'Дата заезда',
					'form_fieldType'=>'datetime',
					'form_fieldParam'=>'class="datetimepicker"',
					'form_mysql_mask'=>'FROM_UNIXTIME(`arrival_date`, "%d.%m.%Y %H:%i") `arrival_date`',
					'table_mysql_mask'=>'FROM_UNIXTIME(`arrival_date`, "%d.%m.%Y %H:%i") `arrival_date`',
					'table_theadParam'=>'style="width:100px"'
					);

		$columns['nights'] = array(
					'title'=>'Ночей',
					'form_fieldType'=>'input',
					'form_fieldParam'=>'style="width:40px"'
					);

		$columns['male'] =	array(
					'title'=>'Мужчин',
					'form_fieldType'=>'input',
					'form_fieldParam'=>'style="width:40px"'
					);

		$columns['female'] =	array(
					'title'=>'Женщин',
					'form_fieldType'=>'input',
					'form_fieldParam'=>'style="width:40px"'
					);

		$columns['rooms'] = array(
					'title'=>'Комнаты',
					'form_fieldType'=>'arr_checkbox',
					'values'=>$this->rooms
//					'table_hidden'=>true
					);

		$columns['comment'] = array(
					'title'=>'Комментарий',
					'form_fieldType'=>'textarea',
					'form_fieldParam'=>'style="width:80%"',
					'table_hidden'=>true
					);

		$columns['date_insert'] = array(
					'title'=>'Дата добавления',
					'form_dontEdit'=>true,
					'form_mysql_mask'=>'FROM_UNIXTIME(`date_insert`, "%d.%m.%Y") `date_insert`',
					'table_mysql_mask'=>'FROM_UNIXTIME(`date_insert`, "%d.%m.%Y") `date_insert`',
					'table_theadParam'=>'style="width:110px"'
					);

		$columns['date_update'] = array(
					'title'=>'Дата обновления',
					'values'=>'unix_timestamp(now())',
					'form_hidden'=>true,
					'form_fieldType'=>'date',
					'form_mysql_mask'=>'FROM_UNIXTIME(`date_update`, "%d.%m.%Y %H:%i") `date_update`',
					'table_mysql_mask'=>'FROM_UNIXTIME(`date_update`, "%d.%m.%Y %H:%i") `date_update`',
					'table_theadParam'=>'style="width:102px"',
					'table_hidden'=>true
					);

		$this->tables['booking'] = new CEditTable('seagull_booking', $columns);
		$this->tables['booking']->config = &$this->config;
		$this->tables['booking']->setConfig('table_param', 'id="t-bookings" class="b-table tpaginator" cellpadding="0" cellspacing="0"');
		$this->tables['booking']->setConfig('table_mysql_select', '`id`, `state`, `fio`, `email`, `phone`, FROM_UNIXTIME(`arrival_date`, "%d.%m.%Y %H:%i") `arrival_date`, `nights`, `male`, `female`, `rooms`, `comment`, `how_you_know`, `viewed`, FROM_UNIXTIME(`date_insert`, "%d.%m.%Y %H:%i") `date_insert`');
		$this->tables['booking']->setConfig('tr_param', array('id'=>' id="row%id%" class="row-edit"', 'viewed'=>array(0=>'b-row_unviewed')));
		$this->tables['booking']->setConfig('sort_direct', 'DESC');
		$this->tables['booking']->setConfig('label_begin', '<label style="width:150px; display:block; float:left">');
		$this->tables['booking']->setConfig('label_end', '</label>');
		$this->tables['booking']->setConfig('paginatorRowsByPage', $this->config->paginatorBackend->rowsByPage);
		$this->tables['booking']->setConfig('paginatorAdvLinks', $this->config->paginatorBackend->advLinks);
	}

	function handlePost() { //------------------------------------------------------
//	add_log(ea($_POST,1), 'booking.log');
		switch($_POST['cmd']) {
			case 'install':
				$this->install();
				$this->ph['title'] = 'Установка модуля';
				$this->file_tpl = 'main';
			break;

			case 'addBooking':
				$this->ph['render_form'] = $this->tables['booking']->renderForm();
				$this->ph['title'] = 'Добавление брони';
				$this->ph['bookingID'] = $_POST['item_id'];
				$this->file_tpl = 'form';
			break;

			case 'editForm':
				$this->ph['render_form'] = $this->tables['booking']->renderForm($_POST['itemID']);
				$this->ph['title'] = 'Редактирование брони №'.$_POST['itemID'];
				$this->ph['bookingID'] = $_POST['itemID'];
				$this->file_tpl = 'form';
			break;

			case 'saveBooking':
//	TODO: тут должна быть проверка данных
			add_log(ea($_POST, 1), 'booking.log');
//				$_POST['published'] = isset($_POST['published']) ? '1' : '0';
				$_POST['date_update'] = mktime();
				$this->ph['render_form'] = $this->tables['booking']->saveForm($_POST['itemID']);
//				$this->file_tpl = 'form';
				$this->ph['booking_list'] = $this->tables['booking']->renderTable(1);
				$this->ph['paginator_links'] = $this->tables['booking']->renderPaginator();
				$this->file_tpl = 'main';
			break;

			case 'delBooking':
				if ($this->tables['booking']->del($_POST['itemID']))
					$this->msg->setOk('Бронь №'.$_POST['itemID'].' удалена');
				else
					$this->msg->setError('Бронь не удалена');

				$this->ph['booking_list'] = $this->tables['booking']->renderTable();
				$this->file_tpl = 'main';
			break;

			case 'config':
				$this->ph['config'] = $this->config->renderForm($this->nameModule);
				$this->file_tpl = 'config';
			break;

			default:
				$this->ph['config'] = $this->config->renderForm($this->nameModule);
				$this->file_tpl = 'config';
			break;
		}
		$this->ph['msgType'] = ' b-msg_'.$this->msg->getType();
		$this->ph['msg'] = $this->msg->render();
	}

	function saveBooking($aData) { //------------------------------------------------------
//		ФИО
		$r = check_text($_POST['fio']);
		switch ($r) {
			case 1: $this->msg->setError('Укажите имя и фамилию'); $this->msg->setHighlight('fio'); break;
			case 2: $this->msg->setError('Поле "Имя и фамилия" не соответствует формату'); $this->msg->setHighlight('fio'); break;
		}

//		E-mail
		$r = check_email($_POST['email']);
		switch ($r) {
			case 2: $this->msg->setError('E-mail не соответствует формату'); break;
		}

//		Телефон
		$r = check_phone($_POST['phone']);
		switch ($r) {
			case 2: $this->msg->setError('Телефон не соответсвует формату'); break;
		}

//		Дата прибытия
		$r = check_date($_POST['arrival_date']);
		switch ($r) {
			case 0: {
				$date = explode('.', $_POST['arrival_date']);
				$arrival_date = mktime(0, 0, 0, $date[1], $date[0], $date[2]);
			} break;
			case 1: $this->msg->setError('Введите дату приезда'); $this->msg->setHighlight('arrival_date'); break;
			case 2: $this->msg->setError('Дата не соответствует формату'); $this->msg->setHighlight('arrival_date'); break;
		}

//		Время прибытия
		$r = check_time($_POST['arrival_time']);
		switch ($r) {
			case 0: {
				$time = explode(':', $_POST['arrival_time']);
				if (!empty($date) and !empty($time))
					$arrival_date = mktime($time[0], $time[1], 0, $date[1], $date[0], $date[2]);
			} break;
			case 1: $this->msg->setError('Введите время приезда'); $this->msg->setHighlight('arrival_time'); break;
			case 2: $this->msg->setError('Время не соответствует формату'); $this->msg->setHighlight('arrival_time'); break;
		}

//		Количество ночей
		$r = check_number($_POST['nights']);
		switch ($r) {
			case 1: $this->msg->setError('Введите количество ночей'); $this->msg->setHighlight('nights'); break;
			case 2: $this->msg->setError('Количество ночей не соответствует формату'); $this->msg->setHighlight('nights'); break;
		}

//		Количество мужчин
		$r = check_number($_POST['male']);
		switch ($r) {
			case 2: $this->msg->setError('Количество мужчин не соответствует формату'); break;
		}

//		Количество женщин
		$r = check_number($_POST['female']);
		switch ($r) {
			case 2: $this->msg->setError('Количество женщин не соответствует формату'); break;
		}

//		Общее количество человек
		if (empty($_POST['male']) and empty($_POST['female'])) {
			$this->msg->setError('Введите количество человек');
			$this->msg->setHighlight('male');
			$this->msg->setHighlight('female');
		}

//		Комнаты
		if (is_array($_POST['rooms'])) {
			$rooms = '';
			if (isset($_POST['rooms'][0])) {
				$rooms = '0';
				$room_names = $this->rooms[0];
			}
			else {
				foreach ($_POST['rooms'] as $key=>$value) {
					$rooms[] = $key;
					$room_names[] = $this->rooms[$key];
				}
				$room_names = implode(', ', $room_names);
				$_POST['rooms'] = implode(',', $rooms);
			}
		}
		else {
			$this->msg->setError('Выберите комнату');
			$this->msg->setHighlight('rooms');
		}

//		SPAM
		$spam = check_spam($_POST['comment']);
		$spam = $spam && check_spam($_POST['how_you_know']);

		$arr['Имя и фамилия:'] = $_POST['fio'];
		$arr['E-mail:'] = $_POST['email'];
		$arr['Телефон:'] = $_POST['phone'];
		$arr['Дата приезда:'] = $_POST['arrival_date'].' '.$_POST['arrival_time'];
		$arr['Количество ночей:'] = $_POST['nights'];
		$male = $_POST['male'] ? $_POST['male'].' мужчин'.compliteWord($_POST['male'], 'а', 'ы', '') : '';
		$female = ($_POST['female'] ? $_POST['female'].' женщин'.compliteWord($_POST['female'], 'а', 'ы', '') :'');
		$arr['Количество человек:'] = ($male and $female) ? $male.' + '.$female : $male.$female;
		$arr['Тип комнаты:'] = $room_names;
		$arr['Комментарии:'] = $_POST['comment'];
		$arr['Как Вы услышали о нас?:'] = $_POST['how_you_know'];
		$body = arr2tableHTML($arr);

		if ($spam==0) {
			sendEmail($this->config->admin_emails, 'СПАМ Бронирование 7skyhostel.ru ('.$_POST['fio'].')', $body);
			$this->msg->setError('Ай, ай, ай не хорошо! Сообщение содержит элементы СПАМа.');
		}

		if (!$this->msg->keep) {
			$_POST['male'] = empty($_POST['male']) ? '0' : $_POST['male'];
			$_POST['female'] = empty($_POST['female']) ? '0' : $_POST['female'];
			$date_insert = mktime();

			$same_request = sql2table("SELECT `id` FROM ".$this->tables['booking']->table." WHERE `fio`='".$_POST['fio']."' AND `email`='".$_POST['email']."' AND `phone`='".$_POST['phone']."' AND `arrival_date`='".$arrival_date."' AND `nights`=".$_POST['nights']." AND `male`=".$_POST['male']." AND `female`=".$_POST['female']." AND `rooms`='".$_POST['rooms']."'");

			if (count($same_request)) {
				sendEmail($this->config->admin_emails, 'Повторное бронирование 7skyhostel.ru ('.$_POST['fio'].')', $body);
				$this->msg->setInfo('Ваша заявка уже принята, нет нужды отправлять повторно');
			}
			else {
				$r = run_sql('INSERT INTO '.$this->tables['booking']->table." (`fio`, `email`, `phone`, `arrival_date`, `nights`, `male`, `female`, `rooms`, `comment`, `how_you_know`, `date_insert`)
				VALUES ('".$_POST['fio']."', '".$_POST['email']."', '".$_POST['phone']."', ".$arrival_date.', '.$_POST['nights'].', '.$_POST['male'].', '.$_POST['female'].", '".$_POST['rooms']."', '".$_POST['comment']."', '".$_POST['how_you_know']."', ".$date_insert.")");

				if ($r) {
					$this->msg->setOk('Благодарим, заявка принята');
					sendEmail($this->config->notice_emails.','.$this->config->admin_emails, 'Бронирование с сайта 7skyhostel.ru ('.$_POST['fio'].')', $body, 'html', $_POST['email']);

					if ($this->config->responseEmail->active === '1') {
						$body = array('username'=>$_POST['fio']);
						$from_email = empty($this->config->responseEmail->fromEmail) ? NULL : $this->config->responseEmail->fromEmail;
						sendEmail($_POST['email'], $this->config->responseEmail->subject, $this->parseContent($this->config->responseEmail->body, $body, '[*', '*]'), 'html', $from_email);
					}
				}
				else {
					$this->msg->setError('Ошибка при отправке заявки');
				}
			}
		}
	}

	function delBooking($bookingID) {
		if (isset($bookingID)) {
			$r = run_sql('DELETE FROM '.$this->tables['booking']->table.' WHERE `id`='.$bookingID);
			if ($r)
				return 1;
		}
		return 0;
	}

	function renderSnippetForm() { //------------------------------------------------------

		foreach ($this->rooms as $key=>$item) {
			$ph['rooms'] .= '<label style="width:100px; display:block"><input type="checkbox" name="rooms['.$key.']" value="1" /> '.$item.'</label>';
		}

		$output = $this->getTpl('frontend/'.$tpl);

		return $this->parseTemplate('frontend/booking_form', $ph);
	}

	function install() { //------------------------------------------------------
		global $dbase;

		$r = true;
		$this->config->install();
		$groupID = $this->config->addGroup($this->nameModule);

		$r &= (boolean)$this->config->setVariable('notice_emails', 'richard_kb@mail.ru', $this->nameModule, NULL, 'T', 'Присылать уведомления о новом отзыве на e-mail\'ы', '300px', 'Чтобы указать несколько e-mail\'ов введите их через запятую.');
//		$r ? $this->msg->setOk('Добавлена переменная "notice_emails"') : $this->msg->setError('Ошибка при добавлени переменной "notice_emails"');
		$r &= (boolean)$this->config->setVariable('admin_emails', 'maxcpp@gmail.com', $this->nameModule, NULL, 'T', 'Администраторский e-mail', '300px', 'Чтобы указать несколько e-mail\'ов введите их через запятую.');

		$r &= (boolean)$this->config->setVariable('paginatorBackend', NULL, $this->nameModule, NULL, 'FIELDSET', 'Постраничная навигация в админке');
//		$r &= (boolean)$this->config->setVariable('active', 1, $this->nameModule, 'paginatorBackend', 'C', 'Включить');
		$r &= (boolean)$this->config->setVariable('rowsByPage', '15', $this->nameModule, 'paginatorBackend', 'N', 'Кол-во записей на странице', '50px');
		$r &= (boolean)$this->config->setVariable('advLinks', '2', $this->nameModule, 'paginatorBackend', 'N', 'Общее кол-во выводимых ссылок', '50px');

		$r &= (boolean)$this->config->setVariable('responseEmail', NULL, $this->nameModule, NULL, 'FIELDSET', 'Ответное письмо');
//		$r &= (boolean)$this->config->setVariable('active', 1, $this->nameModule, 'paginatorBackend', 'C', 'Включить');
		$r &= (boolean)$this->config->setVariable('active', '0', $this->nameModule, 'responseEmail', 'C', 'Отправлять письмо');
		$r &= (boolean)$this->config->setVariable('subject', 'Заявка на сайте', $this->nameModule, 'responseEmail', 'T', 'Тема письма', '300px');
		$r &= (boolean)$this->config->setVariable('body', '<p>Добрый день, [*username*]!</p><p>&nbsp;</p><p>Благодарим за Ваш запрос.</p>', $this->nameModule, 'responseEmail', 'TA', 'Общее кол-во выводимых ссылок', '50px');
		$r &= (boolean)$this->config->setVariable('fromEmail', 'info@', $this->nameModule, 'responseEmail', 'T', 'Значение поля "От кого"', '150px');

		$r ? $this->msg->setOk('Переменные установлены') : $this->msg->setError('Ошибка при установки переменных');

		$r = retr_sql('SHOW TABLE STATUS FROM '.$dbase.' LIKE \''.$this->tables['booking']->tablename.'\'');
		if (!$r) {
			$r = run_sql('CREATE TABLE '.$this->tables['booking']->table." (
					`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`fio` varchar(255) NOT NULL,
					`email` varchar(255) NOT NULL,
					`phone` varchar(32) NOT NULL,
					`arrival_date` int(10) unsigned DEFAULT NULL,
					`nights` tinyint(3) unsigned DEFAULT NULL,
					`male` tinyint(3) unsigned DEFAULT NULL,
					`female` tinyint(3) unsigned DEFAULT NULL,
					`rooms` varchar(255) NOT NULL,
					`comment` text,
					`how_you_know` text,
					`date_insert` int(10) unsigned NOT NULL,
					`state` enum('0','1','2','3') NOT NULL,
					`viewed` enum('0','1') NOT NULL DEFAULT '0',
					`date_update` int(10) unsigned NOT NULL,
					PRIMARY KEY (`id`)
				) ENGINE=MYISAM DEFAULT CHARSET=utf8");
			if (!$r)
				$this->msg->setError('Таблица "'.$this->tables['booking']->tablename.'" не создана <span class="comment">('.mysql_error().')</span>');
		} else
			$this->msg->setWarning('Таблица "'.$this->tables['booking']->tablename.'" уже создана');

		if (!$this->msg->keep_error) {
			return 1;
		}
		return 0;
	}
}
?>
