<?php
require_once __DIR__ . '/../config/conexao.php';
verificarLogin();

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Verifica se ha ordens vinculadas
    $check = $conn->prepare("SELECT COUNT(*) AS total FROM ordens_servico WHERE equipamento_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $total = $check->get_result()->fetch_assoc()['total'];
    $check->close();

    if ($total > 0) {
        header("Location: listar.php?erro=Nao+e+possivel+excluir.+Existem+ordens+de+servico+vinculadas.");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM equipamentos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: listar.php?ok=1");
exit;
?>
