<?php

require "db.php";
require "funcoes.php";

$codigo = $_GET['qid'];
$admin = $_GET['id'];

$sql = $pdo->prepare("SELECT `nome`, `codigo`, `pergunta`, `respostas`, `midia`, `link`, `agradecimento`, `validade` FROM `mensurius_questionarios` WHERE `codigo` = :codigo AND `admin` = :admin;");
$sqlVotos = $pdo->prepare("SELECT `voto`, `relacionado`, `data` FROM `mensurius_votos` WHERE `codigo` = :codigo");
$sqlNomes = $pdo->prepare("SELECT `id` AS id, `nome` AS nome FROM `administrativa`");

if ($sql->execute(array(":codigo" => $codigo, ":admin" => $admin)) && $sqlVotos->execute(array(":codigo" => $codigo)) && $sqlNomes->execute()) {
    $votos = $sqlVotos->fetchAll();
    $questionario = $sql->fetch(PDO::FETCH_NAMED);
    $nomes = $sqlNomes->fetchAll(PDO::FETCH_KEY_PAIR);

    $dataSet = array();
    $labels = array();

    $questionario['votosTotal'] = count($votos);
    $questionario['respostas'] = json_decode($questionario['respostas'], true);
    $questionario['link'] = json_decode($questionario['link'], true);
    if(!isset($questionario['link']) || empty($questionario['link'])){
        $questionario['link'] = "";
    }
    
    $vencimento = strtotime($questionario['validade']);
    $questionario['validade'] = date("d/m/Y", $vencimento);

    foreach ($questionario['respostas'] as $index_resposta => $resposta) {
        $questionario['respostas'][$index_resposta]['contagem'] = 0;
    }

    foreach ($votos as $index => $voto) {

        $questionario['respostas'][$voto['voto']]['contagem']++;
        
        $idRelativo = $voto['relacionado'];
        $labels[$idRelativo] = $nomes[$idRelativo];
        if (!isset($dataSet[$idRelativo])) {
            $dataSet[$idRelativo] = 1;
        } else {
            $dataSet[$idRelativo]++;
        }
    }

    $dataSet = array_values($dataSet);
    $labels = array_values($labels);

    return print_json(array("dataset" => $dataSet, "labels" => $labels, "questionario" => $questionario), "INFORMAÇÕES CARREGADAS COM SUCESSO!");
} else {
    return print_json(array(), "NÃO FOI POSSÍVEL SE CONECTAR AO BANCO DE DADOS. TENTE NOVAMENTE.", false);
}
