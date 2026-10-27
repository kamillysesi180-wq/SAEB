<?php
require_once __DIR__ . '/config/conexao.php';
iniciarSessao();

session_unset();
session_destroy();

header("Location: login.php");
exit;
?>
