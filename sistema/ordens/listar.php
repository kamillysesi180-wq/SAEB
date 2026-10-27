<?php
require_once __DIR__ . '/../config/conexao.php';
verificarLogin();

// Ordenado pela data de abertura (mais recente primeiro)
$ordens = $conn->query("
    SELECT os.id, os.data_abertura, os.data_fechamento, os.status,
           os.descricao_problema, os.solucao,
           c.nome AS cliente,
           e.tipo, e.marca, e.modelo, e.numero_serie
    FROM ordens_servico os
    INNER JOIN clientes c ON c.id = os.cliente_id
    INNER JOIN equipamentos e ON e.id = os.equipamento_id
    ORDER BY os.data_abertura DESC
");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordens de Servico - Sistema SAEP</title>
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
        <h2>Gerenciamento de Ordens de Servico</h2>

        <?php if (isset($_GET['ok'])): ?>
            <div class="alert alert-sucesso">Operacao realizada com sucesso!</div>
        <?php endif; ?>
        <?php if (isset($_GET['erro'])): ?>
            <div class="alert alert-erro"><?php echo htmlspecialchars($_GET['erro']); ?></div>
        <?php endif; ?>

        <div class="acoes">
            <a href="cadastrar.php" class="btn btn-sucesso">+ Nova Ordem de Servico</a>
        </div>

        <table class="tabela">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Data Abertura</th>
                    <th>Cliente</th>
                    <th>Equipamento</th>
                    <th>Status</th>
                    <th>Problema</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ordens->num_rows > 0): ?>
                    <?php while ($os = $ordens->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $os['id']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($os['data_abertura'])); ?></td>
                            <td><?php echo htmlspecialchars($os['cliente']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($os['tipo'] . ' ' . $os['marca'] . ' ' . $os['modelo']); ?>
                                <?php if ($os['numero_serie']): ?>
                                    <br><small>S/N: <?php echo htmlspecialchars($os['numero_serie']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status status-<?php echo strtolower(str_replace(' ', '-', $os['status'])); ?>">
                                    <?php echo $os['status']; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars(mb_strimwidth($os['descricao_problema'], 0, 45, '...')); ?></td>
                            <td>
                                <a href="editar.php?id=<?php echo $os['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                                <a href="excluir.php?id=<?php echo $os['id']; ?>" class="btn btn-perigo btn-sm btn-excluir">Excluir</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="texto-centro">Nenhuma ordem de servico cadastrada.</td></tr>
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
