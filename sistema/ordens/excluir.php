<?php
require_once __DIR__ . '/../config/conexao.php';
verificarLogin();

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM ordens_servico WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: listar.php?ok=1");
exit;
?>
