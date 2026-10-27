<?php
require_once __DIR__ . '/../config/conexao.php';
verificarLogin();

$erro = '';
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
        $data_fechamento = null;
        if ($status === 'Concluida' || $status === 'Cancelada') {
            $data_fechamento = date('Y-m-d H:i:s');
        }

        $stmt = $conn->prepare("
            INSERT INTO ordens_servico (cliente_id, equipamento_id, status, descricao_problema, solucao, data_fechamento)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iissss", $cliente_id, $equipamento_id, $status, $descricao_problema, $solucao, $data_fechamento);

        if ($stmt->execute()) {
            header("Location: listar.php?ok=1");
            exit;
        } else {
            $erro = 'Erro ao cadastrar: ' . $conn->error;
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
    <title>Nova Ordem de Servico - Sistema SAEP</title>
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
        <h2>Nova Ordem de Servico</h2>

        <?php if ($erro): ?>
            <div class="alert alert-erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <div class="form-box">
            <form method="POST" action="cadastrar.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="cliente_id">Cliente *</label>
                        <select id="cliente_id" name="cliente_id" required>
                            <option value="">Selecione...</option>
                            <?php while ($c = $clientes->fetch_assoc()): ?>
                                <option value="<?php echo $c['id']; ?>"
                                    <?php echo (isset($_POST['cliente_id']) && $_POST['cliente_id'] == $c['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['nome']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="equipamento_id">Equipamento *</label>
                        <select id="equipamento_id" name="equipamento_id" required>
                            <option value="">Selecione...</option>
                            <?php while ($e = $equipamentos->fetch_assoc()): ?>
                                <option value="<?php echo $e['id']; ?>"
                                    <?php echo (isset($_POST['equipamento_id']) && $_POST['equipamento_id'] == $e['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($e['cliente'] . ' - ' . $e['tipo'] . ' ' . $e['marca'] . ' ' . $e['modelo']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="Aberta">Aberta</option>
                        <option value="Em andamento">Em andamento</option>
                        <option value="Concluida">Concluida</option>
                        <option value="Cancelada">Cancelada</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="descricao_problema">Descricao do Problema *</label>
                    <textarea id="descricao_problema" name="descricao_problema" rows="3" required><?php echo htmlspecialchars($_POST['descricao_problema'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="solucao">Solucao / Observacoes</label>
                    <textarea id="solucao" name="solucao" rows="3"><?php echo htmlspecialchars($_POST['solucao'] ?? ''); ?></textarea>
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
