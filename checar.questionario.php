<?php
require 'db.php';
require 'funcoes.php';

$qid = $_GET['qid'];

$sql = $pdo->prepare("SELECT COUNT(*) FROM `mensurius_questionarios` WHERE `codigo` = :qid");
if($sql->execute(array(":qid" => $qid))){
    $contagem = $sql->fetch(PDO::FETCH_NUM);
    if($contagem[0] != 0){
        return print_json(array(), "");
    }else{
        return print_json(array(), "QUESTIONÁRIO NÃO ENCONTRADO", false);
    }
    
}else{
    return print_json(array(), "", false);
}