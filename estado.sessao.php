<?php
require "funcoes.php";

session_start();
if (isset($_SESSION['mensurius']) && $_SESSION['mensurius']['sessao']['id'] > 0) {
    print_json($_SESSION['mensurius'], "");
} else {
    print_json(array(), "", false);
}

session_write_close();
exit;
