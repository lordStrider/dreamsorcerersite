<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Adicionei GET pois paginação costuma ser GET
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // 1. Conexão
    require_once 'dbPlayers.php'; 

    // Verificação de segurança: se no dbPlayers.php a variável for $conn, usamos ela.
    if (!isset($conn)) {
        throw new Exception("Erro: A variável de conexão \$conn não foi encontrada no dbPlayers.php.");
    }

    // 2. Parâmetros da Paginação
    $itensPorPagina = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    if ($paginaAtual < 1) $paginaAtual = 1;

    $offset = ($paginaAtual - 1) * $itensPorPagina;

    // 3. Consulta para contar o total ABSOLUTO de usuários no banco
    $totalUsuarios = $conn->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    
    // Calcula o total de páginas baseado no limite escolhido
    $totalPaginas = ceil($totalUsuarios / $itensPorPagina);

    // 4. Consulta Principal (Busca todas as colunas da página atual)
    $sql = "SELECT * FROM usuarios LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', $itensPorPagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Retorno dos Dados estruturado
    $resposta = [
        "status" => "success",
        "total_geral_usuarios" => (int)$totalUsuarios, // <--- O total que você pediu
        "paginacao" => [
            "total_paginas"   => (int)$totalPaginas,
            "pagina_atual"    => $paginaAtual,
            "itens_por_pagina" => $itensPorPagina,
            "tem_proxima"     => ($paginaAtual < $totalPaginas),
            "tem_anterior"    => ($paginaAtual > 1)
        ],
        "dados" => $usuarios
    ];

    echo json_encode($resposta, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>