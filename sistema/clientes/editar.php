<?php
require_once __DIR__ . '/../config/conexao.php';
verificarLogin();

$id = intval($_GET['id'] ?? 0);
$erro = '';

$stmt = $conn->prepare("SELECT id, nome, email, endereco FROM clientes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cliente) {
    header("Location: listar.php?erro=Cliente+nao+encontrado");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome'] ?? '');
    $cpf      = trim($_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');

    if (empty($nome)) {
        $erro = 'Nome e obrigatorio.';
    } else {
        // Se CPF/telefone forem informados, atualiza com hash; senao mantem os atuais
        if (!empty($cpf) && !empty($telefone)) {
            $cpf_hash      = protegerDado($cpf);
            $telefone_hash = protegerDado($telefone);
            $stmt = $conn->prepare("UPDATE clientes SET nome=?, cpf=?, telefone=?, email=?, endereco=? WHERE id=?");
            $stmt->bind_param("sssssi", $nome, $cpf_hash, $telefone_hash, $email, $endereco, $id);
        } else {
            $stmt = $conn->prepare("UPDATE clientes SET nome=?, email=?, endereco=? WHERE id=?");
            $stmt->bind_param("sssi", $nome, $email, $endereco, $id);
        }

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
    <title>Editar Cliente - Sistema SAEP</title>
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
        <a href="listar.php" class="ativo">Clientes</a>
        <a href="../equipamentos/listar.php">Equipamentos</a>
        <a href="../ordens/listar.php">Ordens de Servico</a>
    </nav>

    <main class="conteudo">
        <h2>Editar Cliente #<?php echo $cliente['id']; ?></h2>

        <?php if ($erro): ?>
            <div class="alert alert-erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <div class="form-box">
            <form method="POST" action="editar.php?id=<?php echo $id; ?>">
                <div class="form-group">
                    <label for="nome">Nome *</label>
                    <input type="text" id="nome" name="nome" required
                           value="<?php echo htmlspecialchars($cliente['nome']); ?>">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="cpf">CPF (deixe em branco para manter)</label>
                        <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00">
                    </div>
                    <div class="form-group">
                        <label for="telefone">Telefone (deixe em branco para manter)</label>
                        <input type="text" id="telefone" name="telefone" placeholder="(00) 00000-0000">
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email"
                           value="<?php echo htmlspecialchars($cliente['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="endereco">Endereco</label>
                    <input type="text" id="endereco" name="endereco"
                           value="<?php echo htmlspecialchars($cliente['endereco'] ?? ''); ?>">
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
