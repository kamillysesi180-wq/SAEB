<?php
require_once __DIR__ . '/config/conexao.php';
iniciarSessao();

// Se ja estiver logado, redireciona
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit;
}

$erro = '';
$msg  = '';

if (isset($_GET['msg']) && $_GET['msg'] === 'sessao_expirada') {
    $msg = 'Sua sessao expirou. Faca login novamente.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos.';
    } else {
        $stmt = $conn->prepare("SELECT id, nome, email, senha, perfil FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            if (password_verify($senha, $usuario['senha'])) {
                // Login bem-sucedido
                $_SESSION['usuario_id']     = $usuario['id'];
                $_SESSION['usuario_nome']   = $usuario['nome'];
                $_SESSION['usuario_email']  = $usuario['email'];
                $_SESSION['usuario_perfil'] = $usuario['perfil'];
                $_SESSION['ultimo_acesso']  = time();

                header("Location: dashboard.php");
                exit;
            } else {
                $erro = 'E-mail ou senha incorretos.';
            }
        } else {
            $erro = 'E-mail ou senha incorretos.';
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
    <title>Login - Sistema SAEP</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <h1>Assistencia Tecnica</h1>
            <p class="subtitle">Sistema de Gestao de Servicos</p>

            <?php if ($erro): ?>
                <div class="alert alert-erro"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <?php if ($msg): ?>
                <div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           placeholder="seu@email.com">
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required placeholder="""""""">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
            </form>

            <p class="login-info">
                Usuario de teste: <strong>admin@saep.local</strong> / <strong>123456</strong>
            </p>
        </div>
    </div>
</body>
</html>
