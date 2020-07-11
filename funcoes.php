<?php

function print_json($resposta = array(), $mensagem = "", $resultado = true)
{
    $resposta_json = json_encode(
        array_merge(
            array(
                "resultado" => $resultado,
                "resposta" => $resposta,
                "mensagem" => $mensagem,
            )
        )
    );
    print_r($resposta_json);
}

function gerar_codigo()
{
    return bin2hex(random_bytes(6));
}

function checar_codigo($codigo = "", $pdo)
{
    $sql = $pdo->prepare("SELECT COUNT(*) FROM `mensurius_questionarios` WHERE `codigo` = ?");
    $sql->execute(array($codigo));
    if($sql->fetch()[0] == 0){
        return true;
    }else{
        return false;
    }
}

function validarData($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}