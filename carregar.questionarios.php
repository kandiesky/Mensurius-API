<?php
require "db.php";
require "funcoes.php";

$id = $_GET['id'];
$intervalo = intval($_GET['offset']); //Age como um multiplicador de 6 -- também retorna como valor para paginas.atual
$numero_questionarios = 6;
if ($intervalo <= 0) {
    $intervalo = 1;
    //return print_json(array(), "OFFSET INCORRETO", false);
}

$intervalo_max = $numero_questionarios * $intervalo;
$intervalo_min = $intervalo_max - $numero_questionarios;

$sql = $pdo->prepare("SELECT `admin`, `nome`, `codigo`, `pergunta`, `respostas`, `midia`, `link`, `agradecimento`, `validade` FROM `mensurius_questionarios` WHERE `admin` = :id ORDER BY `criacao` DESC LIMIT $intervalo_min, $intervalo_max ");

if ($sql->execute(array(":id" => $id))) {
    $questionarios = $sql->fetchAll(PDO::FETCH_NAMED);
    $data = array();
    foreach ($questionarios as $index => $questionario) {
        $questionario['respostas'] = json_decode($questionario['respostas'], true);
        foreach ($questionario['respostas'] as $index_resposta => $resposta) {
            $questionario['respostas'][$index_resposta]['contagem'] = 0;
        }

        $votos_sql = $pdo->prepare("SELECT `voto` FROM `mensurius_votos` WHERE `codigo` = ?");
        $votos_sql->execute(array($questionario['codigo']));
        $votos = $votos_sql->fetchAll(PDO::FETCH_NUM);

        foreach ($votos as $index_votos => $voto) {
            $questionario['respostas'][$voto[0]]['contagem']++;
        }
        $questionario['votosTotal'] = count($votos);
        $vencimento = strtotime($questionario['validade']);
        $questionario['validade'] = date("d/m/Y", $vencimento);
        
        $data['questionarios'][] = $questionario;
    }

    $sql_contagem = $pdo->prepare("SELECT COUNT(*) FROM `mensurius_questionarios` WHERE `admin` = ?");
    if ($sql_contagem->execute(array($id))) {
        $contagem_paginas = ceil($sql_contagem->fetch()[0] / $numero_questionarios);
        if ($contagem_paginas != 0) {
            $data['paginas'] = array(
                "atual" => $intervalo,
                "total" => $contagem_paginas,
            );
        } else {
            $data['paginas'] = array(
                "atual" => 0,
                "total" => -1,
            );
        }

        return print_json($data, "");
    }

}
return print_json(array(), "NÃO FOI POSSÍVEL CARREGAR SEUS QUESTIONÁRIOS. TENTE NOVAMENTE MAIS TARDE.", false);
