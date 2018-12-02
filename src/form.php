<?php

if (!$_POST['email']) {
	echo json_encode('Не указан email!');
	die;
}
require 'db.php';

$res = DB::exec("SELECT `id` FROM `users` WHERE `email` = :email", 
	array(':email' => $_POST['email'])
);
if ($row = DB::fetch($res)) {
	$userId = $row['id'];
} else {
	DB::exec("INSERT INTO `users` (`email`, `name`, `phone`) 
		VALUES (:email, :name, :phone)",
		array(
			':email' => $_POST['email'], 
			':name' => $_POST['name'], 
			':phone' => $_POST['phone']
		)
	);
	$userId = DB::getInsertedId();

	require_once dirname(__DIR__).'/vendor/autoload.php';

	$transport = (new Swift_SmtpTransport('ssl://smtp.yandex.ru', 465))
		->setUsername('lofttestmail@yandex.ru')
		->setPassword('qwerty7890');

	$mailer = new Swift_Mailer($transport);

	$message = (new Swift_Message('Зарегистрирован новый пользователь'))
		->setFrom(['lofttestmail@yandex.ru' => 'Burger manager'])
		->setTo([$_POST['email']])
		->setBody('Вы зарегестрированы (имя - '.$_POST['name'].', телефон - '.$_POST['phone'].')');

	$result = $mailer->send($message);
}

$need_change = 0;
$pay_by_card = 0;
$call_back = 1;

if ($_POST['payment'] == 'need_change') {
	$need_change = 1;
}
if ($_POST['payment'] == 'pay_by_card') {
	$pay_by_card = 1;
}
if ($_POST['callback']) {
	$call_back = 0;
}

DB::exec("INSERT INTO `orders` 
	(`user_id`, `street`, `house`,`building`,`appartment`,`floor`,`comment`,`need_change`,`pay_by_card`,`call_back`) 
	VALUES (:user_id, :street, :house, :building, :appartment, :floor, :comment, :need_change, :pay_by_card, :call_back)",
	array(
		':user_id' => $userId,
		':street' => $_POST['street'],
		':house' => $_POST['house'],
		':building' => $_POST['building'],
		':appartment' => $_POST['appartment'],
		':floor' => $_POST['floor'],
		':comment' => $_POST['comment'],
		':need_change' => $need_change,
		':pay_by_card' => $pay_by_card,
		':call_back' => $call_back
	)
);

$orderId = DB::getInsertedId();

$res = DB::exec("SELECT COUNT(`id`) as count FROM `orders` WHERE `user_id` = :user_id",
	array(':user_id' => $userId)
);
if ($row = DB::fetch($res)) {
	$orderCount = $row['count'];
}

$mail = 'Заказ №'.$orderId.PHP_EOL.PHP_EOL;
$mail .= 'Ваш заказ будет доставлен по адресу :'.PHP_EOL;
$mail .= 'улица '.$_POST['street'].', дом '.$_POST['house'].', корпус '.$_POST['building'].', квартира '.$_POST['appartment'].', этаж '.$_POST['floor'].PHP_EOL.PHP_EOL;
$mail .= 'Содержимое заказа: DarkBeefBurger за 500 рублей, 1 шт'.PHP_EOL.PHP_EOL;

if ($orderCount > 1) {
	$mail .= 'Спасибо! Это уже '.$orderCount.' заказ';
} else {
	$mail .= 'Спасибо - это ваш первый заказ';
}

$mailDir = dirname(__DIR__).DIRECTORY_SEPARATOR.'mail';
if (!is_dir($mailDir)) {
	mkdir($mailDir);
}
$fileName = $mailDir.DIRECTORY_SEPARATOR.'order '.$orderId.' '.date('d_m_Y H_i_s').'.txt';

file_put_contents($fileName, $mail);

echo json_encode('Заказ принят!');
