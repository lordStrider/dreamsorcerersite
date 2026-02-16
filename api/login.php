<?php
// 1. Inicia o buffer de saída para evitar que espaços ou avisos quebrem o JSON
ob_start();

require 'vendor/autoload.php';
require 'db.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use \Firebase\JWT\JWT;

header("Content-Type: application/json");

// Captura qualquer erro fatal que não seria pego pelo try/catch comum
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE)) {
        ob_clean(); // Limpa qualquer saída anterior
        echo json_encode(["erro" => "Erro Fatal PHP: " . $error['message']]);
        exit;
    }
});

try {
    // Puxa a chave do .env
    $key = $_ENV['JWT_KEY'] ?? null;

    if (!$key) {
        throw new Exception("Chave secreta (JWT_KEY) não definida no .env");
    }

    // Recebe os dados do JSON
    $input = file_get_contents("php://input");
    $dados = json_decode($input);

    if (empty($dados->usuario) || empty($dados->senha)) {
        http_response_code(400);
        echo json_encode(["erro" => "Usuário e senha são obrigatórios"]);
        exit;
    }

    // Busca o usuário no banco
    $stmt = $conn->prepare("SELECT id, senha FROM usuarios WHERE usuario = :u LIMIT 1");
    $stmt->execute([':u' => $dados->usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Validação de senha
    if ($user && password_verify($dados->senha, $user['senha'])) {
        
        $payload = [
            "iss" => "seu-site.com",
            "iat" => time(),
            "exp" => time() + (60 * 60 * 24), // 24 horas
            "uid" => $user['id']
        ];

        // Tenta gerar o Token
        $jwt = JWT::encode($payload, $key, 'HS256');

        // LIMPEZA FINAL: Apaga qualquer aviso/espaço que tenha vazado
        ob_clean();
        echo json_encode(["token" => $jwt]);
        exit;

    } else {
        http_response_code(401);
        echo json_encode(["erro" => "Usuário ou senha inválidos"]);
        exit;
    }

} catch (PDOException $e) {
    // Erros específicos de Banco de Dados
    ob_clean();
    http_response_code(500);
    echo json_encode(["erro" => "Erro no Banco de Dados: " . $e->getMessage()]);
} catch (Exception $e) {
    // Qualquer outro erro (como erro na biblioteca JWT)
    ob_clean();
    http_response_code(500);
    echo json_encode(["erro" => "Erro no Servidor: " . $e->getMessage()]);
}