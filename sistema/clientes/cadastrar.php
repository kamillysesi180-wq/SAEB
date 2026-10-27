<?php
require_once __DIR__ . '/../config/conexao.php';
verificarLogin();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome'] ?? '');
    $cpf      = trim($_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');

    if (empty($nome) || empty($cpf) || empty($telefone)) {
        $erro = 'Nome, CPF e Telefone sao obrigatorios.';
    } else {
        // Protege dados sensiveis com hash
        $cpf_hash      = protegerDado($cpf);
        $telefone_hash = protegerDado($telefone);

        $stmt = $conn->prepare("INSERT INTO clientes (nome, cpf, telefone, email, endereco) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nome, $cpf_hash, $telefone_hash, $email, $endereco);

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
    <title>Cadastrar Cliente - Sistema SAEP</title>
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
        <h2>Cadastrar Cliente</h2>

        <?php if ($erro): ?>
            <div class="alert alert-erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <div class="form-box">
            <form method="POST" action="cadastrar.php">
                <div class="form-group">
                    <label for="nome">Nome *</label>
                    <input type="text" id="nome" name="nome" required
                           value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="cpf">CPF *</label>
                        <input type="text" id="cpf" name="cpf" required placeholder="000.000.000-00"
                               value="<?php echo htmlspecialchars($_POST['cpf'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="telefone">Telefone *</label>
                        <input type="text" id="telefone" name="telefone" required placeholder="(00) 00000-0000"
                               value="<?php echo htmlspecialchars($_POST['telefone'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="endereco">Endereco</label>
                    <input type="text" id="endereco" name="endereco"
                           value="<?php echo htmlspecialchars($_POST['endereco'] ?? ''); ?>">
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
