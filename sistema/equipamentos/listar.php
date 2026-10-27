<?php
require_once __DIR__ . '/../config/conexao.php';
verificarLogin();

$equipamentos = $conn->query("
    SELECT e.id, e.tipo, e.marca, e.modelo, e.numero_serie, e.criado_em,
           c.nome AS cliente
    FROM equipamentos e
    INNER JOIN clientes c ON c.id = e.cliente_id
    ORDER BY e.id DESC
");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipamentos - Sistema SAEP</title>
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
        <h2>Gerenciamento de Equipamentos</h2>

        <?php if (isset($_GET['ok'])): ?>
            <div class="alert alert-sucesso">Operacao realizada com sucesso!</div>
        <?php endif; ?>
        <?php if (isset($_GET['erro'])): ?>
            <div class="alert alert-erro"><?php echo htmlspecialchars($_GET['erro']); ?></div>
        <?php endif; ?>

        <div class="acoes">
            <a href="cadastrar.php" class="btn btn-sucesso">+ Novo Equipamento</a>
        </div>

        <table class="tabela">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Marca / Modelo</th>
                    <th>N. Serie</th>
                    <th>Cadastro</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($equipamentos->num_rows > 0): ?>
                    <?php while ($e = $equipamentos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $e['id']; ?></td>
                            <td><?php echo htmlspecialchars($e['cliente']); ?></td>
                            <td><?php echo htmlspecialchars($e['tipo']); ?></td>
                            <td><?php echo htmlspecialchars($e['marca'] . ' ' . $e['modelo']); ?></td>
                            <td><?php echo htmlspecialchars($e['numero_serie'] ?? '-'); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($e['criado_em'])); ?></td>
                            <td>
                                <a href="editar.php?id=<?php echo $e['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                                <a href="excluir.php?id=<?php echo $e['id']; ?>" class="btn btn-perigo btn-sm btn-excluir">Excluir</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="texto-centro">Nenhum equipamento cadastrado.</td></tr>
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
