<?php
require 'vendor/autoload.php';
require 'db.php'; // Inclui a conexão
use \Firebase\JWT\JWT;

header("Content-Type: application/json");

$key = "sua_chave_secreta_super_segura";
$dados = json_decode(file_get_contents("php://input"));

if (!empty($dados->usuario) && !empty($dados->senha)) {
    
    // Prepara a consulta para evitar SQL Injection
    $query = "SELECT id, senha FROM usuarios WHERE usuario = :usuario LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':usuario', $dados->usuario);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se usuário existe e a senha (criptografada) bate
    if ($user && password_verify($dados->senha, $user['senha'])) {
        
        $payload = [
            "iss" => "seu-site.com",
            "iat" => time(),
            "exp" => time() + (60 * 60), // 1 hora
            "uid" => $user['id']
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');

        echo json_encode([
            "status" => "sucesso",
            "token" => $jwt
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["status" => "erro", "mensagem" => "Usuário ou senha incorretos"]);
    }
}