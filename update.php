<?php
require("db.php");

$sql = $pdo->prepare("SELECT * FROM `mensurius_questionarios`");
$sql->execute();
$q = $sql->fetchAll();

foreach ($q as $index => $qq) {
    $link = array();
    $link[] = array("link" => $qq['link'],"titulo" => "ACESSE MAIS");

    $qs = $pdo->prepare("UPDATE `mensurius_questionarios` SET `link` = ? WHERE `id` = ?");
    $qs->execute(array(json_encode($link), $qq['id']));
}