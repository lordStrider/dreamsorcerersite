<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // 1. Conexão
    require_once 'dbPlayers.php'; 

    if (!isset($conn)) {
        throw new Exception("Erro: A variável de conexão \$conn não foi encontrada.");
    }

    // 2. Verifica se o ID foi enviado
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("ID do usuário não fornecido.");
    }

    $id = (int)$_GET['id'];

    // 3. Consulta Individual
    $sql = "SELECT * FROM Usuarios WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // 4. Retorno dos Dados
    if ($usuario) {
        echo json_encode([
            "status" => "success",
            "dados" => $usuario
        ], JSON_PRETTY_PRINT);
    } else {
        // Se não encontrar o usuário, retornamos 404
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "Usuário com ID $id não encontrado."
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>