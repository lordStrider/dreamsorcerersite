<?php
/**
 * enviar_massa.php - Disparo de notificações para toda a base
 */
//require_once 'validar.php';
require_once 'db.php'; // Carrega sua conexão ($conn ou $pdo)

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
set_time_limit(0); // Evita que o script pare em bases de dados grandes

// 1. Configurações do Firebase
$projectId = 'bibliaquiz-527bd'; 
$serviceAccountPath = __DIR__ . '/credentials.json'; 

// 2. Recebe os dados do formulário/post
$titulo  = $_POST['titulo']  ?? '';
$body    = $_POST['corpo']   ?? '';
$image   = $_POST['imagem']  ?? '';

/**
 * Função para obter o Access Token (OAuth2)
 */
function getAccessToken($jsonPath) {
    if (!file_exists($jsonPath)) return null;
    $json = json_decode(file_get_contents($jsonPath), true);
    $now = time();
    $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
    $payload = base64_encode(json_encode([
        'iss' => $json['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => $now + 3600,
        'iat' => $now
    ]));
    $signature = '';
    openssl_sign("$header.$payload", $signature, $json['private_key'], 'SHA256');
    $jwt = "$header.$payload." . base64_encode($signature);
    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]));
    $result = json_decode(curl_exec($ch), true);
    return $result['access_token'] ?? null;
}

/**
 * Função Principal de Envio em Massa
 */
function dispararBroadcast($titulo, $body, $image, $projectId, $jsonPath, $db) {
    $accessToken = getAccessToken($jsonPath);
    if (!$accessToken) {
        die(json_encode(["error" => "Erro na autenticação Google."]));
    }

    // --- BUSCA NO BANCO ---
    // Ajuste o nome da tabela e da coluna conforme seu banco
    $sql = "SELECT notify_token FROM usuario WHERE notify_token IS NOT NULL";
    $stmt = $db->query($sql);
    $tokens = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($tokens)) {
        die(json_encode(["error" => "Nenhum token encontrado no banco."]));
    }

    $url = "https://fcm.googleapis.com/v1/projects/" . trim($projectId) . "/messages:send";
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]
    ]);

    $stats = ['sucessos' => 0, 'falhas' => 0, 'invalidos' => []];

    // --- LOOP DE ENVIO ---
    foreach ($tokens as $token) {
        $payload = [
            'message' => [
                'token' => trim($token),
                'notification' => ['title' => $titulo, 'body' => $body, 'image' => $image],
                'android' => [
                    'priority' => 'high',
                    'notification' => ['image' => $image, 'sound' => 'default']
                ],
                'apns' => [
                    'payload' => ['aps' => ['mutable-content' => 1, 'sound' => 'default']],
                    'fcm_options' => ['image' => $image]
                ]
            ]
        ];

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($httpCode === 200) {
            $stats['sucessos']++;
        } else {
            $stats['falhas']++;
            $res = json_decode($response, true);
            // Se o erro for 'UNREGISTERED' ou 'NOT_FOUND', o token é inválido
            if (isset($res['error']['details'][0]['errorCode']) && 
               ($res['error']['details'][0]['errorCode'] === 'UNREGISTERED')) {
                $stats['invalidos'][] = $token;
            }
        }
    }
    curl_close($curl);

    // --- LIMPEZA DE TOKENS INVÁLIDOS ---
    if (!empty($stats['invalidos'])) {
        $placeholders = implode(',', array_fill(0, count($stats['invalidos']), '?'));
        $sqlDelete = "DELETE FROM usuario WHERE notify_token IN ($placeholders)";
        $stmtDel = $db->prepare($sqlDelete);
        $stmtDel->execute($stats['invalidos']);
    }

    echo json_encode([
        "status" => "processado",
        "enviados" => $stats['sucessos'],
        "erros" => $stats['falhas'],
        "removidos_por_invalidez" => count($stats['invalidos'])
    ]);
}

// Início da execução
if (!empty($titulo) && !empty($body)) {
    // Note: certifique-se que o objeto de conexão em db.php se chama $pdo ou $conn
    dispararBroadcast($titulo, $body, $image, $projectId, $serviceAccountPath, $pdo);
} else {
    echo json_encode(["error" => "Título e corpo são obrigatórios."]);
}
?>