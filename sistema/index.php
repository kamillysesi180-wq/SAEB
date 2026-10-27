<?php
require_once __DIR__ . '/config/conexao.php';
iniciarSessao();

if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit;
?>
