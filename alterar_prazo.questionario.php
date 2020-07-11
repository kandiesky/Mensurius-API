<?php

require "db.php";
require "funcoes.php";

$admin = $_POST['id'];
$chave = $_POST['chave'];
$codigo = $_POST['codigo'];
$data = $_POST['data'];

if($codigo == "" || $admin == "" || $chave == "" || $data == ""){
    return print_json(array(), "ERRO.", false);
}

if(date("d/m/Y",strtotime($data)) == "31/12/1969"){
    return print_json(array(), "DATA INVÁLIDA!", false);
}

$sql = $pdo->prepare("UPDATE `mensurius_questionarios` SET `validade` = :validade WHERE `mensurius_questionarios`.`codigo` = :codigo AND `mensurius_questionarios`.`admin` = :admin");
if($sql->execute(array(":validade" => $data, ":codigo" => $codigo, ":admin" => $admin))){
    return print_json(array(), "ATUALIZADO COM SUCESSO!");
}else{
    return print_json(array(), "HOUVE UMA FALHA AO ATUALIZAR. TENTE NOVAMENTE.");
}