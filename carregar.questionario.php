<?php

require "db.php";
require "funcoes.php";

$qid = $_GET['qid'];

$sql = $pdo->prepare("SELECT `nome`, `codigo`, `pergunta`, `respostas`, `midia`, `link`, `agradecimento`, `validade` FROM `mensurius_questionarios` WHERE `codigo` = :qid");

if ($sql->execute(array(":qid" => $qid))) {
    $questionario = $sql->fetch(PDO::FETCH_NAMED);

    if(!isset($questionario['nome'])){
        return print_json(array(), "ESTE QUESTIONÁRIO NÃO EXISTE MAIS!", false);
    }
    
    //Precisa transformar em array antes de voltar pra string, senão fica sendo tratada como string pelo javascript
    $questionario['respostas'] = json_decode($questionario['respostas'], true);
    $questionario['link'] = json_decode($questionario['link'], true);
    
    $data_hoje = new DateTime();
    $data_validade = new DateTime($questionario['validade']);

    if ($data_hoje >= $data_validade) {
        return print_json(array(), "A VALIDADE DESTE QUESTIONÁRIO VENCEU!", false);
    }

    return print_json($questionario, "CARREGADO COM SUCESSO!");
} else {
    return print_json(array(), "NÃO FOI POSSÍVEL SE CONECTAR AO BANCO DE DADOS. TENTE NOVAMENTE.", false);
}
