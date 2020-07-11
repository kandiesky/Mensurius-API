<?php

require "db.php";
require "funcoes.php";


//Textos
session_start();
$admin = $_SESSION['mensurius']['sessao']['id'];
$nome = $_POST['nome'];
$pergunta = $_POST['pergunta'];
$links = json_decode($_POST['links'], true);
$agradecimento = $_POST['agradecimento'];
$vencimento = $_POST['vencimento'];
$respostas = json_decode($_POST['respostas'], true);
$codigo = substr(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(" ", "-", $_POST['codigo'])), 0, 50); //chamo-o de monstro
$codigo_gerado = false;
session_write_close();

/* Checa se as primeiras duas respostas estão preenchidas */
if ($respostas[0]['texto'] == "" || $respostas[1]['texto'] == "") {
    return print_json(array(), "PREENCHA TODAS AS RESPOSTAS. É NECESSÁRIO QUE AS DUAS PRIMEIRAS DUAS ESTEJAM PREENCHIDAS.", false);
}

/* Checa se a data inserida é válida, senão seta para um ano a partir de hoje */
if(!validarData($vencimento)){
    $vencimento = date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " + 365 day"));   
}

/* Checa por respostas vazias e remove elas */
foreach ($respostas as $index => $resposta) {
   if($resposta["texto"] == ""){
       /* $respostas[$index]["texto"] = "Vazio"; */
       unset($respostas[$index]);
   }
}
/* Reordena array */
$respostas = array_values($respostas);

/* Remove links vazios como failsafe */
foreach ($links as $index_links => $link) {
    if($link["link"] == "" || $link["titulo"] == ""){
        unset($link[$index_links]);
    }
}
/* Reordena a array */
$links = array_values($links);
/* Desativa se tiver vazio */
if(empty($links) || empty($links[0]["titulo"]) || count($links) === 0){
    $links = "";
}else{
    $links = json_encode($links);
}

//Geração de código caso não haja um personalizado
if (strlen($codigo) == 0) {
    $codigo_gerado = true;
    $codigo = gerar_codigo();
}

//Checagem da existência do código
if ($codigo_gerado === true) {
    while (true) {
        $codigo = gerar_codigo();
        if(checar_codigo($codigo, $pdo) === true){
            break;
        }
    }
} else {
    if (!checar_codigo($codigo, $pdo)) {
        return print_json(array(), "O CÓDIGO DIGITADO JÁ ESTÁ SENDO UTILIZADO EM OUTRO QUESTIONÁRIO. TENTE OUTRO.", false);
    }
}

//Midia
$midia = $_FILES['midia'];
$uploadMidiaFalhou = false;
//Máximo de 5mb
if (isset($midia) && file_exists($midia['tmp_name'])) {
    
    if ($midia['size'] > 5120000) {
        return print_json(array(), "A IMAGEM É MAIOR QUE O LIMITE DE 5MB", false);
    }
    
    if (getimagesize($midia["tmp_name"]) && $midia['type'] == "image/jpeg" || $midia['type'] == "image/webp" || $midia['type'] == "image/png") {

        $extensao = ".jpg";
        switch ($midia['type']) {
            case "image/jpeg":
                $extensao = ".jpg";
                break;

            case "image/png":
                $extensao = ".png";
                break;
            case "image/webp":
                $extensao = ".webp";
                break;
        }

        $nome_uniq = uniqid();
        $caminho = "../uploads/{$codigo}_{$nome_uniq}_1{$extensao}";
        
        if (move_uploaded_file($midia['tmp_name'], $caminho)) {
            $midia = "uploads/{$codigo}_{$nome_uniq}_1{$extensao}";
        } else {
            $uploadMidiaFalhou = true;
            $midia = "";
        }
    } else {
        $uploadMidiaFalhou = true;
        $midia = "";
    }
}

$sql = $pdo->prepare("INSERT INTO `mensurius_questionarios` (`admin`, `nome`, `codigo`, `pergunta`, `respostas`, `midia`, `link`, `agradecimento`, `validade`) VALUES (:admin, :nome, :codigo, :pergunta, :respostas, :midia, :link, :agradecimento, :vencimento)");
if ($sql->execute(array(
    ":admin" => $admin,
    ":nome" => $nome,
    ":codigo" => $codigo,
    ":pergunta" => $pergunta,
    ":respostas" => json_encode($respostas),
    ":midia" => $midia,
    ":link" => $links,
    ":agradecimento" => $agradecimento,
    ":vencimento" => "{$vencimento}",
))) {
    $mensagem = "QUESTIONÁRIO ADICIONADO COM SUCESSO! ";
    if ($uploadMidiaFalhou) {
        $mensagem .= "CONTUDO A IMAGEM NÃO PÔDE SER ENVIADA. CHEQUE O FORMATO DELA.";
    }
    return print_json(array("codigo" => $codigo), $mensagem);
} else {
    return print_json(array(), "NÃO FOI POSSÍVEL CRIAR O QUESTIONÁRIO. A CONEXÃO COM O BANCO DE DADOS FALHOU.", false);
}
exit;
