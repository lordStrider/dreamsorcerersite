<?php
require 'vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// Garante que a resposta de erro seja JSON se a validação falhar
header("Content-Type: application/json");

try {
    // 1. Carrega o .env (se já não tiver sido carregado pelo db.php ou similar)
    if (!isset($_ENV['JWT_KEY'])) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }

    $key = $_ENV['JWT_KEY'];

    // 2. Pega os headers da requisição
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

    if (!$authHeader) {
        http_response_code(401);
        echo json_encode(["erro" => "Token não fornecido. Acesso negado."]);
        exit;
    }

    // 3. Extrai o token (Bearer <token>)
    $token = str_replace("Bearer ", "", $authHeader);

    // 4. Decodifica e valida o token
    // O decode lança uma Exception se o token for falso, expirado ou alterado
    $decoded = JWT::decode($token, new Key($key, 'HS256'));

    // Se chegou aqui, o token é válido! 
    // Podemos guardar os dados do usuário para usar na página que chamou este script
    $usuarioLogado = (array) $decoded;

} catch (Exception $e) {
    // Se o token for inválido ou expirado, cai aqui
    http_response_code(401);
    echo json_encode([
        "erro" => "Token inválido ou expirado",
        "detalhes" => $e->getMessage()
    ]);
    exit;
}