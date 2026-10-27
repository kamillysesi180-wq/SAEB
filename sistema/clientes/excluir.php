<?php
require_once __DIR__ . '/../config/conexao.php';
verificarLogin();

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Verifica se ha equipamentos vinculados
    $check = $conn->prepare("SELECT COUNT(*) AS total FROM equipamentos WHERE cliente_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $total = $check->get_result()->fetch_assoc()['total'];
    $check->close();

    if ($total > 0) {
        header("Location: listar.php?erro=Nao+e+possivel+excluir.+Existem+equipamentos+vinculados.");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM clientes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: listar.php?ok=1");
exit;
?>
