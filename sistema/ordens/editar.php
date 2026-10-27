<?php
require_once __DIR__ . '/../config/conexao.php';
verificarLogin();

$id = intval($_GET['id'] ?? 0);
$erro = '';

$stmt = $conn->prepare("SELECT * FROM ordens_servico WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$os = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$os) {
    header("Location: listar.php?erro=Ordem+nao+encontrada");
    exit;
}

$clientes = $conn->query("SELECT id, nome FROM clientes ORDER BY nome");
$equipamentos = $conn->query("
    SELECT e.id, e.tipo, e.marca, e.modelo, c.nome AS cliente
    FROM equipamentos e
    INNER JOIN clientes c ON c.id = e.cliente_id
    ORDER BY c.nome, e.tipo
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id         = intval($_POST['cliente_id'] ?? 0);
    $equipamento_id     = intval($_POST['equipamento_id'] ?? 0);
    $status             = $_POST['status'] ?? 'Aberta';
    $descricao_problema = trim($_POST['descricao_problema'] ?? '');
    $solucao            = trim($_POST['solucao'] ?? '');

    $status_validos = ['Aberta', 'Em andamento', 'Concluida', 'Cancelada'];
    if (!in_array($status, $status_validos)) {
        $status = 'Aberta';
    }

    if ($cliente_id <= 0 || $equipamento_id <= 0 || empty($descricao_problema)) {
        $erro = 'Cliente, Equipamento e Descricao do problema sao obrigatorios.';
    } else {
        $data_fechamento = $os['data_fechamento'];
        if (($status === 'Concluida' || $status === 'Cancelada') && empty($data_fechamento)) {
            $data_fechamento = date('Y-m-d H:i:s');
        }
        if ($status === 'Aberta' || $status === 'Em andamento') {
            $data_fechamento = null;
        }

        $stmt = $conn->prepare("
            UPDATE ordens_servico
            SET cliente_id=?, equipamento_id=?, status=?, descricao_problema=?, solucao=?, data_fechamento=?
            WHERE id=?
        ");
        $stmt->bind_param("iissssi", $cliente_id, $equipamento_id, $status, $descricao_problema, $solucao, $data_fechamento, $id);

        if ($stmt->execute()) {
            header("Location: listar.php?ok=1");
            exit;
        } else {
            $erro = 'Erro ao atualizar: ' . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Ordem de Servico - Sistema SAEP</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="topo">
        <div class="topo-esquerda">
            <h1>SAEP - Assistencia Tecnica</h1>
        </div>
        <div class="topo-direita">
            <span class="usuario-logado">Ola, <strong><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></strong></span>
            <a href="../logout.php" class="btn btn-sair">Sair</a>
        </div>
    </header>

    <nav class="menu">
        <a href="../dashboard.php">Dashboard</a>
        <a href="../clientes/listar.php">Clientes</a>
        <a href="../equipamentos/listar.php">Equipamentos</a>
        <a href="listar.php" class="ativo">Ordens de Servico</a>
    </nav>

    <main class="conteudo">
        <h2>Editar Ordem de Servico #<?php echo $os['id']; ?></h2>

        <?php if ($erro): ?>
            <div class="alert alert-erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <div class="form-box">
            <form method="POST" action="editar.php?id=<?php echo $id; ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label for="cliente_id">Cliente *</label>
                        <select id="cliente_id" name="cliente_id" required>
                            <?php while ($c = $clientes->fetch_assoc()): ?>
                                <option value="<?php echo $c['id']; ?>"
                                    <?php echo ($os['cliente_id'] == $c['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['nome']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="equipamento_id">Equipamento *</label>
                        <select id="equipamento_id" name="equipamento_id" required>
                            <?php while ($e = $equipamentos->fetch_assoc()): ?>
                                <option value="<?php echo $e['id']; ?>"
                                    <?php echo ($os['equipamento_id'] == $e['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($e['cliente'] . ' - ' . $e['tipo'] . ' ' . $e['marca'] . ' ' . $e['modelo']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <?php foreach (['Aberta', 'Em andamento', 'Concluida', 'Cancelada'] as $st): ?>
                            <option value="<?php echo $st; ?>" <?php echo ($os['status'] === $st) ? 'selected' : ''; ?>>
                                <?php echo $st; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="descricao_problema">Descricao do Problema *</label>
                    <textarea id="descricao_problema" name="descricao_problema" rows="3" required><?php echo htmlspecialchars($os['descricao_problema']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="solucao">Solucao / Observacoes</label>
                    <textarea id="solucao" name="solucao" rows="3"><?php echo htmlspecialchars($os['solucao'] ?? ''); ?></textarea>
                </div>
                <div class="form-acoes">
                    <button type="submit" class="btn btn-sucesso">Salvar</button>
                    <a href="listar.php" class="btn btn-aviso">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="rodape">
        Sistema de Avaliacao da Educacao Profissional &copy; <?php echo date('Y'); ?>
    </footer>
</body>
</html>
