<?php
// =====================================================
// Conexao com o banco de dados - Sistema SAEP
// =====================================================

$servidor = "localhost";
$usuario  = "root";
$senha    = "";
$banco    = "saep_db";
$porta    = 3306;

$conn = new mysqli($servidor, $usuario, $senha, $banco, $porta);

if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Tempo de expiracao da sessao (em segundos) - 30 minutos
define('SESSION_TIMEOUT', 1800);

// Funcao auxiliar para proteger dados sensiveis (hash)
function protegerDado($valor) {
    return hash('sha256', $valor);
}

// Inicia sessao com configuracoes de seguranca
function iniciarSessao() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verifica expiracao da sessao
    if (isset($_SESSION['ultimo_acesso'])) {
        $tempo_inativo = time() - $_SESSION['ultimo_acesso'];
        if ($tempo_inativo > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            header("Location: login.php?msg=sessao_expirada");
            exit;
        }
    }
    $_SESSION['ultimo_acesso'] = time();
}

// Verifica se o usuario esta autenticado
function verificarLogin() {
    iniciarSessao();
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit;
    }
}
?>
