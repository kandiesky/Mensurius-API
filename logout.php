<?php
require "funcoes.php";

session_start();
unset($_SESSION['mensurius']);
session_write_close();

if (!isset($_SESSION['mensurius'])) {
    print_json(array(), "VOCÊ SAIU COM SUCESSO!");
} else {
    print_json(array(), "NÃO FOI POSSÍVEL SAIR. TENTE NOVAMENTE.", false);
}
