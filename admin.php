<?php
require 'src'.DIRECTORY_SEPARATOR.'db.php';

$res = DB::exec("SELECT * FROM `users`");
$users = DB::fetchAll($res);

$res = DB::exec("SELECT * FROM `orders`");
$orders = DB::fetchAll($res);
?>

<h2>Пользователи</h2>
<table>
	<thead>
		<tr>
			<th>id</th>
			<th>email</th>
			<th>Имя</th>
			<th>Телефон</th>
		</tr>
	</thead>
	<tbody>
		<?php if (empty($users)) { ?>
			<tr>
				<td colspan="4" style="text-align: center;">Пока нет пользователей</td>
			</tr>
		<?php } else { 
		foreach ($users as $user) { ?>
			<tr>
				<td><?php echo $user['id'] ?></td>
				<td><?php echo $user['email'] ?></td>
				<td><?php echo $user['name'] ?></td>
				<td><?php echo $user['phone'] ?></td>
			</tr>
		<?php }} ?>
	</tbody>
</table>

<br><hr><br>

<h2>Заказы</h2>
<table>
	<thead>
		<tr>
			<th>id</th>
			<th>id пользователя</th>
			<th>Улица</th>
			<th>Дом</th>
			<th>Корпус</th>
			<th>Квартира</th>
			<th>Этаж</th>
			<th>Комментарий</th>
			<th>Потребуется сдача</th>
			<th>Оплата по карте</th>
			<th>Перезвонить</th>
			<th>Дата</th>
		</tr>
	</thead>
	<tbody>
		<?php if (empty($orders)) { ?>
			<tr>
				<td colspan="12" style="text-align: center;">Пока нет заказов</td>
			</tr>
		<?php } else { 
		foreach ($orders as $order) { ?>
			<tr>
				<td><?php echo $order['id'] ?></td>
				<td><?php echo $order['user_id'] ?></td>
				<td><?php echo $order['street'] ?></td>
				<td><?php echo $order['house'] ?></td>
				<td><?php echo $order['building'] ?></td>
				<td><?php echo $order['appartment'] ?></td>
				<td><?php echo $order['floor'] ?></td>
				<td><?php echo $order['comment'] ?></td>
				<td><?php echo $order['need_change'] ? 'Да' : 'Нет' ?></td>
				<td><?php echo $order['pay_by_card'] ? 'Да' : 'Нет' ?></td>
				<td><?php echo $order['call_back'] ? 'Да' : 'Нет' ?></td>
				<td><?php echo date('d.m.Y H:i:s', strtotime($order['date'])) ?></td>
			</tr>
		<?php }} ?>
	</tbody>
</table>
