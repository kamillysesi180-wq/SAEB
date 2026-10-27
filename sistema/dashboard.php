<?php
require_once __DIR__ . '/config/conexao.php';
verificarLogin();

// Contadores para o painel
$total_clientes = $conn->query("SELECT COUNT(*) AS total FROM clientes")->fetch_assoc()['total'];
$total_equip    = $conn->query("SELECT COUNT(*) AS total FROM equipamentos")->fetch_assoc()['total'];
$total_os       = $conn->query("SELECT COUNT(*) AS total FROM ordens_servico")->fetch_assoc()['total'];
$os_abertas     = $conn->query("SELECT COUNT(*) AS total FROM ordens_servico WHERE status IN ('Aberta','Em andamento')")->fetch_assoc()['total'];

// Ultimas ordens
$ultimas = $conn->query("
    SELECT os.id, os.data_abertura, os.status, os.descricao_problema,
           c.nome AS cliente, e.tipo, e.marca, e.modelo
    FROM ordens_servico os
    INNER JOIN clientes c ON c.id = os.cliente_id
    INNER JOIN equipamentos e ON e.id = os.equipamento_id
    ORDER BY os.data_abertura DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema SAEP</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="topo">
        <div class="topo-esquerda">
            <h1>SAEP - Assistencia Tecnica</h1>
        </div>
        <div class="topo-direita">
            <span class="usuario-logado">Ola, <strong><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></strong></span>
            <a href="logout.php" class="btn btn-sair">Sair</a>
        </div>
    </header>

    <nav class="menu">
        <a href="dashboard.php" class="ativo">Dashboard</a>
        <a href="clientes/listar.php">Clientes</a>
        <a href="equipamentos/listar.php">Equipamentos</a>
        <a href="ordens/listar.php">Ordens de Servico</a>
    </nav>

    <main class="conteudo">
        <h2>Painel Principal</h2>

        <div class="cards">
            <div class="card">
                <span class="card-numero"><?php echo $total_clientes; ?></span>
                <span class="card-label">Clientes</span>
            </div>
            <div class="card">
                <span class="card-numero"><?php echo $total_equip; ?></span>
                <span class="card-label">Equipamentos</span>
            </div>
            <div class="card">
                <span class="card-numero"><?php echo $total_os; ?></span>
                <span class="card-label">Ordens de Servico</span>
            </div>
            <div class="card destaque">
                <span class="card-numero"><?php echo $os_abertas; ?></span>
                <span class="card-label">Em aberto</span>
            </div>
        </div>

        <section class="secao">
            <h3>Ultimas Ordens de Servico</h3>
            <table class="tabela">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Data Abertura</th>
                        <th>Cliente</th>
                        <th>Equipamento</th>
                        <th>Status</th>
                        <th>Problema</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($ultimas->num_rows > 0): ?>
                        <?php while ($row = $ultimas->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['data_abertura'])); ?></td>
                                <td><?php echo htmlspecialchars($row['cliente']); ?></td>
                                <td><?php echo htmlspecialchars($row['tipo'] . ' ' . $row['marca'] . ' ' . $row['modelo']); ?></td>
                                <td><span class="status status-<?php echo strtolower(str_replace(' ', '-', $row['status'])); ?>"><?php echo $row['status']; ?></span></td>
                                <td><?php echo htmlspecialchars(mb_strimwidth($row['descricao_problema'], 0, 50, '...')); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="texto-centro">Nenhuma ordem cadastrada.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer class="rodape">
        Sistema de Avaliacao da Educacao Profissional &copy; <?php echo date('Y'); ?>
    </footer>
</body>
</html>
