<?php
require 'db.php'; // Já carrega o autoload e o .env
use \Firebase\JWT\JWT;

header("Content-Type: application/json");

$key = $_ENV['JWT_KEY']; // Puxa do .env
$dados = json_decode(file_get_contents("php://input"));

if (!empty($dados->usuario) && !empty($dados->senha)) {
    
    $stmt = $conn->prepare("SELECT id, senha FROM usuarios WHERE usuario = :u LIMIT 1");
    $stmt->execute([':u' => $dados->usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($dados->senha, $user['senha'])) {
        $payload = [
            "iss" => "seu-app.com",
            "iat" => time(),
            "exp" => time() + (60 * 60 * 24), // Expira em 24h
            "uid" => $user['id']
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');
        echo json_encode(["token" => $jwt]);
    } else {
        http_response_code(401);
        echo json_encode(["erro" => "Login inválido"]);
    }
}