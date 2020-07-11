<?php
header("content-type: application/json");

date_default_timezone_set("America/Sao_Paulo");

$host = 'localhost';
$user = 'root';
$pass = '';
$bank = 'u303824046_banco';

$pdo = new pdo("mysql:host={$host};dbname={$bank};charset=utf8", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);