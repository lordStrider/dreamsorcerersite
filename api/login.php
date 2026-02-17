<?php
require_once 'validar.php';
ob_start();

// 🔒 Desativa exibição de erros em HTML
ini_set('display_errors', 0);
ini_set('html_errors', 0);
error_reporting(0);

header("Content-Type: application/json; charset=UTF-8");

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/db.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// 🔐 Carrega variáveis do .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Captura erros fatais e força retorno JSON
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_clean();
        http_response_code(500);
        echo json_encode([
            "erro" => "Erro interno do servidor"
        ]);
        exit;
    }
});

try {

    // 🔑 Verifica chave JWT
    $key = $_ENV['JWT_KEY'] ?? null;

    if (!$key) {
        throw new Exception("JWT_KEY não definida no .env");
    }

    // 📥 Recebe JSON
    $input = file_get_contents("php://input");
    $dados = json_decode($input);

    if (!$dados) {
        http_response_code(400);
        echo json_encode(["erro" => "JSON inválido"]);
        exit;
    }

    if (empty($dados->usuario) || empty($dados->senha)) {
        http_response_code(400);
        echo json_encode(["erro" => "Usuário e senha são obrigatórios"]);
        exit;
    }

    // 🔍 Busca usuário
    $stmt = $conn->prepare("SELECT id, senha FROM usuarios WHERE usuario = :usuario LIMIT 1");
    $stmt->bindParam(":usuario", $dados->usuario);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 🔐 Validação
    if (!$user || !password_verify($dados->senha, $user['senha'])) {
        http_response_code(401);
        echo json_encode(["erro" => "Usuário ou senha inválidos"]);
        exit;
    }

    // 🎟️ Payload JWT
    $payload = [
        "iss" => "seu-dominio.com",
        "iat" => time(),
        "exp" => time() + (60 * 60 * 24), // 24h
        "uid" => $user['id']
    ];

    // 🔑 Gera token
    $jwt = JWT::encode($payload, $key, 'HS256');

    // 🔄 Limpa buffer e retorna JSON puro
    ob_clean();
    http_response_code(200);
    echo json_encode([
        "status" => "ok",
        "token" => $jwt
    ]);
    exit;

} catch (PDOException $e) {

    ob_clean();
    http_response_code(500);
    echo json_encode([
        "erro" => "Erro no banco de dados"
    ]);
    exit;

} catch (Exception $e) {

    ob_clean();
    http_response_code(500);
    echo json_encode([
        "erro" => "Erro no servidor"
    ]);
    exit;
}
?>