<?php
require 'db.php';
require 'funcoes.php';

$login = $_POST['login'];
$senha = $_POST['senha'];

$sql = $pdo->prepare("SELECT `id`, `nome` FROM `administrativa` WHERE `login` = :login OR `email` = :login AND `senha` = :senha");
if ($sql->execute(array(":login" => $login, ":senha" => $senha))) {
    $usuario = $sql->fetch(PDO::FETCH_NAMED);
    
    if (isset($usuario['id'])) {
        $data = new DateTime('now');
        $data = $data->format('YmdHisYs');
        $chave = hash('sha256', "{$data}{$usuario['nome']}{$usuario['id']}");
        $sessao = array(
            'sessao' => array(
                'nome' => $usuario['nome'],
                'id' => $usuario['id'],
                'chave' => $chave,
            ),
        );
        session_start();
        $_SESSION['mensurius'] = $sessao;
        session_write_close();
        return print_json($sessao, "LOGIN REALIZADO COM SUCESSO!");
    } else {
        return print_json(array(), "LOGIN OU SENHA INCORRETOS. VERIFIQUE OS DADOS E TENTE NOVAMENTE!", false);
    }
} else {
    return print_json(array(), "ERRO AO SE CONECTAR AO BANCO DE DADOS. TENTE NOVAMENTE MAIS TARDE!", false);
}
