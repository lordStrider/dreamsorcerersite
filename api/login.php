<?php
// 1. Forçar a saída a ser sempre JSON, mesmo em caso de erro do PHP
header("Content-Type: application/json");

// 2. Capturar erros fatais e transformá-los em JSON
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE)) {
        echo json_encode([
            "erro" => "Erro Fatal no PHP: " . $error['message'],
            "arquivo" => $error['file'],
            "linha" => $error['line']
        ]);
    }
});

require 'vendor/autoload.php';
require 'db.php'; 
use \Firebase\JWT\JWT;

// Carregar chave do ENV
$key = $_ENV['JWT_KEY'] ?? null;

if (!$key) {
    http_response_code(500);
    echo json_encode(["erro" => "Chave JWT_KEY não encontrada no .env"]);
    exit;
}

$dados = json_decode(file_get_contents("php://input"));

if (!empty($dados->usuario) && !empty($dados->senha)) {
    
    try {
        $stmt = $conn->prepare("SELECT id, senha FROM usuarios WHERE usuario = :u LIMIT 1");
        $stmt->execute([':u' => $dados->usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($dados->senha, $user['senha'])) {
            $payload = [
                "iss" => "seu-app.com",
                "iat" => time(),
                "exp" => time() + (60 * 60 * 24),
                "uid" => $user['id']
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');
            echo json_encode(["token" => $jwt]);
        } else {
            http_response_code(401);
            echo json_encode(["erro" => "Login ou senha inválidos"]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["erro" => "Erro no banco: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["erro" => "Usuário e senha são obrigatórios"]);
}