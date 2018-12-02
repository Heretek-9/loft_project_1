<?php
require 'src'.DIRECTORY_SEPARATOR.'db.php';
require_once __DIR__.'/vendor/autoload.php';

$res = DB::exec("SELECT * FROM `users`");
$users = DB::fetchAll($res);

$res = DB::exec("SELECT * FROM `orders`");
$orders = DB::fetchAll($res);

$loader = new Twig_Loader_Filesystem(__DIR__.'/twig');
$twig = new Twig_Environment($loader);

echo $twig->render('admin.twig', array(
    'users' => $users,
    'orders' => $orders
));
