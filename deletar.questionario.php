<?php
require "db.php";
require "funcoes.php";

$admin = $_POST['id'];
$chave = $_POST['chave'];
$codigo = $_POST['codigo'];

if($codigo == "" || $admin == "" || $chave == ""){
    print_json(array(), "ERRO.", false);
}
session_start();
if ($admin != $_SESSION['mensurius']['sessao']['id'] && $chave != $_SESSION['mensurius']['sessao']['id']) {
    print_json(array(), "FALHA DE SEGURANÇA. VOCÊ NÃO PODE FAZER ISSO!", false);
}
session_write_close();

$sql = $pdo->prepare("SELECT `midia` FROM `mensurius_questionarios` WHERE `codigo` = ?");
$sql->execute(array($codigo));
$questionario = $sql->fetch();

if(!is_dir("../{$questionario['midia']}") && file_exists("../{$questionario['midia']}")){
    unlink("../{$questionario['midia']}");
}


//Deletar questionário e votos
$sql = $pdo->prepare("DELETE FROM `mensurius_questionarios` WHERE `mensurius_questionarios`.`codigo` = ? AND `admin` = ?; DELETE FROM `mensurius_votos` WHERE `mensurius_votos`.`codigo` = ?");
if ($sql->execute(array($codigo, $admin, $codigo))) {
    return print_json(array(), "DELETADO COM SUCESSO.");
}
