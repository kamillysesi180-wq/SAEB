<?php
require_once __DIR__ . '/../config/conexao.php';
verificarLogin();

$id = intval($_GET['id'] ?? 0);
$erro = '';

$stmt = $conn->prepare("SELECT * FROM equipamentos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$equip = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$equip) {
    header("Location: listar.php?erro=Equipamento+nao+encontrado");
    exit;
}

$clientes = $conn->query("SELECT id, nome FROM clientes ORDER BY nome");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id   = intval($_POST['cliente_id'] ?? 0);
    $tipo         = trim($_POST['tipo'] ?? '');
    $marca        = trim($_POST['marca'] ?? '');
    $modelo       = trim($_POST['modelo'] ?? '');
    $numero_serie = trim($_POST['numero_serie'] ?? '');
    $descricao    = trim($_POST['descricao'] ?? '');

    if ($cliente_id <= 0 || empty($tipo) || empty($marca) || empty($modelo)) {
        $erro = 'Cliente, Tipo, Marca e Modelo sao obrigatorios.';
    } else {
        $stmt = $conn->prepare("UPDATE equipamentos SET cliente_id=?, tipo=?, marca=?, modelo=?, numero_serie=?, descricao=? WHERE id=?");
        $stmt->bind_param("isssssi", $cliente_id, $tipo, $marca, $modelo, $numero_serie, $descricao, $id);

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
    <title>Editar Equipamento - Sistema SAEP</title>
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
        <a href="listar.php" class="ativo">Equipamentos</a>
        <a href="../ordens/listar.php">Ordens de Servico</a>
    </nav>

    <main class="conteudo">
        <h2>Editar Equipamento #<?php echo $equip['id']; ?></h2>

        <?php if ($erro): ?>
            <div class="alert alert-erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <div class="form-box">
            <form method="POST" action="editar.php?id=<?php echo $id; ?>">
                <div class="form-group">
                    <label for="cliente_id">Cliente *</label>
                    <select id="cliente_id" name="cliente_id" required>
                        <?php while ($c = $clientes->fetch_assoc()): ?>
                            <option value="<?php echo $c['id']; ?>"
                                <?php echo ($equip['cliente_id'] == $c['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['nome']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="tipo">Tipo *</label>
                        <input type="text" id="tipo" name="tipo" required
                               value="<?php echo htmlspecialchars($equip['tipo']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="marca">Marca *</label>
                        <input type="text" id="marca" name="marca" required
                               value="<?php echo htmlspecialchars($equip['marca']); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="modelo">Modelo *</label>
                        <input type="text" id="modelo" name="modelo" required
                               value="<?php echo htmlspecialchars($equip['modelo']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="numero_serie">Numero de Serie</label>
                        <input type="text" id="numero_serie" name="numero_serie"
                               value="<?php echo htmlspecialchars($equip['numero_serie'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="descricao">Descricao</label>
                    <textarea id="descricao" name="descricao" rows="3"><?php echo htmlspecialchars($equip['descricao'] ?? ''); ?></textarea>
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
