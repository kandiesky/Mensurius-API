<?php
require "db.php";
require "funcoes.php";

$qid = $_POST['qid'];
$voto = $_POST['voto'];
$relacionado = $_POST['relacionado'];
$votante = $_POST['votante'];

$sql = $pdo->prepare("INSERT INTO `mensurius_votos` (`codigo`, `voto`, `relacionado`, `votante`) VALUES (:qid, :voto, :relacionado, :votante)");

$dados = array(
    ":qid" => $qid,
    ":voto" => $voto,
    ":relacionado" => $relacionado,
    ":votante" => $votante,
);

if ($sql->execute($dados)) {
    return print_json(array(), "SUA RESPOSTA FOI REGISTRADA COM SUCESSO!");
} else {
    return print_json(array(), "SUA RESPOSTA NÃO FOI REGISTRADA. FALHA GERAL AO SALVAR.");
}
