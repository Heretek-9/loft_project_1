<?php

class DB
{	
	static private $host = '127.0.0.1';
	static private $user = 'root';
	static private $password = '';
	static private $database = 'loft_project_1';
	static private $connection = null;
	static private $instance = null;

	private function __construct() {

		$dsn = 'mysql:host='.self::$host.';charset=utf8';
		try {
			$pdo = new PDO($dsn, self::$user, self::$password);
		} catch(PDOException $err) {
			die('Не удалось подключиться: '.$err->getMessage());
		}

		$dbCheck = $pdo->query('USE '.self::$database);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if (!$dbCheck) {// автосоздание базы данных и таблиц
			$pdo->query('CREATE DATABASE '.self::$database);
			$dbCheck = $pdo->query('USE '.self::$database);
			if (!$dbCheck) {
				die('Не удалось создать базу '.self::$database);
			}
			$pdo->query("CREATE TABLE `users` (
				`id` INT(255) NOT NULL AUTO_INCREMENT,
				`email` TEXT NOT NULL,
				`name` TEXT NOT NULL,
				`phone` TEXT NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE = InnoDB;");
			$pdo->query("CREATE TABLE `orders` (
				`id` INT(255) NOT NULL AUTO_INCREMENT,
				`user_id` INT(255) NOT NULL,
				`street` TEXT NOT NULL,
				`house` TEXT NOT NULL,
				`building` TEXT NOT NULL,
				`appartment` TEXT NOT NULL,
				`floor` TEXT NOT NULL,
				`comment` TEXT NOT NULL,
				`need_change` INT(1) NOT NULL,
				`pay_by_card` INT(1) NOT NULL,
				`call_back` INT(1) NOT NULL,
				`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`)
			) ENGINE = InnoDB;");
		}

		self::$connection = $pdo;
	}

	public static function init() 
	{
		if (is_null(self::$instance)) {
			self::$instance = new self;
		}
	}

	private function __clone() {}

	private function __wakeup() {}

	public static function exec($query, $params = array()) 
	{
		try {
			$result = self::$connection->prepare($query);
		} catch(PDOException $err) {
			die('Не удалось подготовить запрос: '.$err->getMessage());
		}
		try {
			$result->execute($params);
		} catch(PDOException $err) {
			die('Не удалось выполнить запрос: '.$err->getMessage());
		}
		return $result;
	}

	public static function fetch($result)
	{
		return $result->fetch(PDO::FETCH_ASSOC);
	}

	public static function fetchAll($result)
	{
		return $result->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function getInsertedId()
	{
		return self::$connection->lastInsertId();
	}
}

DB::init();