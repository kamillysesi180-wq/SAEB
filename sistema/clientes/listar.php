<?php
require_once __DIR__ . '/../config/conexao.php';
verificarLogin();

$termo = trim($_GET['busca'] ?? '');
$clientes = null;

if ($termo !== '') {
    $like = '%' . $termo . '%';
    $stmt = $conn->prepare("SELECT id, nome, email, endereco, criado_em FROM clientes WHERE nome LIKE ? OR email LIKE ? ORDER BY nome");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $clientes = $stmt->get_result();
} else {
    $clientes = $conn->query("SELECT id, nome, email, endereco, criado_em FROM clientes ORDER BY nome");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Sistema SAEP</title>
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
        <h2>Gerenciamento de Clientes</h2>

        <?php if (isset($_GET['ok'])): ?>
            <div class="alert alert-sucesso">Operacao realizada com sucesso!</div>
        <?php endif; ?>
        <?php if (isset($_GET['erro'])): ?>
            <div class="alert alert-erro"><?php echo htmlspecialchars($_GET['erro']); ?></div>
        <?php endif; ?>

        <div class="acoes">
            <a href="cadastrar.php" class="btn btn-sucesso">+ Novo Cliente</a>
            <form method="GET" action="listar.php" class="busca-form">
                <input type="text" name="busca" placeholder="Buscar por nome ou e-mail..."
                       value="<?php echo htmlspecialchars($termo); ?>">
                <button type="submit" class="btn btn-primary">Buscar</button>
                <?php if ($termo !== ''): ?>
                    <a href="listar.php" class="btn btn-aviso">Limpar</a>
                <?php endif; ?>
            </form>
        </div>

        <table class="tabela">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Endereco</th>
                    <th>Cadastro</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($clientes && $clientes->num_rows > 0): ?>
                    <?php while ($c = $clientes->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $c['id']; ?></td>
                            <td><?php echo htmlspecialchars($c['nome']); ?></td>
                            <td><?php echo htmlspecialchars($c['email'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($c['endereco'] ?? '-'); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($c['criado_em'])); ?></td>
                            <td>
                                <a href="editar.php?id=<?php echo $c['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                                <a href="excluir.php?id=<?php echo $c['id']; ?>" class="btn btn-perigo btn-sm btn-excluir">Excluir</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="texto-centro">Nenhum cliente encontrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <footer class="rodape">
        Sistema de Avaliacao da Educacao Profissional &copy; <?php echo date('Y'); ?>
    </footer>
    <script src="../js/script.js"></script>
</body>
</html>
