<?php 
require "db.php";
require "funcoes.php";

$admin = $_POST['id'];
$chave = $_POST['chave'];
$codigo = $_POST['codigo'];
$data = date("Y-m-d");
if($codigo == "" || $admin == "" || $chave == ""){
    return print_json(array(), "ERRO.", false);
}
session_start();
if ($admin != $_SESSION['mensurius']['sessao']['id'] && $chave != $_SESSION['mensurius']['sessao']['id']) {
    return print_json(array(), "FALHA DE SEGURANÇA. VOCÊ NÃO PODE FAZER ISSO!", false);
}
session_write_close();

$sql = $pdo->prepare("UPDATE `mensurius_questionarios` SET `validade` = :vencimento WHERE `codigo` = :codigo AND `admin` = :admin");
if($sql->execute(array(":vencimento" => $data, ":codigo" => $codigo, ":admin" => $admin))){
    return print_json(array(), "O FECHAMENTO DE PRAZO FOI FEITO COM SUCESSO!");
}else{
    return print_json(array(), "NÃO FOI POSSÍVEL FECHAR O PRAZO. HOUVE UM ERRO DE CONEXÃO COM O BANCO DE DADOS.");
}