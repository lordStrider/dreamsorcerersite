<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
ini_set('display_errors', 1);
error_reporting(E_ALL);
// 1. Carrega as dependências do Composer (JWT e Dotenv)
require 'vendor/autoload.php';

// 2. Carrega as variáveis do seu arquivo .env
// O Dotenv procura o arquivo na raiz do projeto
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

header("Content-Type: application/json");

try {
    // 3. Conexão PDO usando as variáveis do .env
    $conn = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 4. Recebe os dados do JSON enviado pelo Frontend
    $dados = json_decode(file_get_contents("php://input"));

    if (!empty($dados->usuario) && !empty($dados->senha)) {
        
        // Verifica se o usuário já existe para evitar duplicidade
        $check = $conn->prepare("SELECT id FROM usuarios WHERE usuario = :u");
        $check->execute([':u' => $dados->usuario]);
        
        if ($check->rowCount() > 0) {
            http_response_code(400);
            echo json_encode(["erro" => "Este nome de usuário já está em uso."]);
            exit;
        }

        // 5. CRIPTOGRAFIA: Gera o hash seguro da senha
        $senhaHash = password_hash($dados->senha, PASSWORD_DEFAULT);

        // 6. Insere no banco
        $stmt = $conn->prepare("INSERT INTO usuarios (usuario, senha) VALUES (:u, :s)");
        $stmt->execute([
            ':u' => $dados->usuario,
            ':s' => $senhaHash
        ]);

        echo json_encode(["status" => "sucesso", "mensagem" => "Usuário registrado com sucesso!"]);
        
    } else {
        http_response_code(400);
        echo json_encode(["erro" => "Dados incompletos."]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro de banco de dados: " . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["erro" => "Erro interno: " . $e->getMessage()]);
}
?>