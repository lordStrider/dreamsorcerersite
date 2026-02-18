<?php
header("Content-Type: application/json");

// Liga o log de erros para não dar 500 "seco"
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // 1. Conexão (Cuidado com o nome da variável: é $conn)
    require_once 'dbPlayers.php'; 

    if (!isset($conn)) {
        throw new Exception("Erro: A variável de conexão \$conn não existe. Verifique o db.php.");
    }

    // 2. Configurações Firebase
    $projectId = 'bibliaquiz-527bd'; 
    $jsonPath = __DIR__ . '/credentials.json';

    // 3. Captura os dados do AJAX
    $titulo = $_POST['titulo'] ?? '';
    $body   = $_POST['corpo']  ?? '';
    $image  = $_POST['imagem'] ?? '';

    if (empty($titulo) || empty($body)) {
        throw new Exception("Preencha título e corpo da mensagem.");
    }

    // 4. Gera o Access Token (Função simplificada dentro do script)
    $accessToken = obterTokenGoogle($jsonPath);

    // 5. Busca os tokens no banco usando $conn
    $stmt = $conn->prepare("SELECT notify_token FROM usuario WHERE notify_token IS NOT NULL AND notify_token != ''");
    $stmt->execute();
    $tokens = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $stats = ['sucessos' => 0, 'falhas' => 0, 'invalidos' => []];
    $url = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";

    // 6. Loop de Envio
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]
    ]);

    foreach ($tokens as $token) {
        $payload = [
            'message' => [
                'token' => trim($token),
                'notification' => ['title' => $titulo, 'body' => $body, 'image' => $image],
                'android' => ['priority' => 'high']
            ]
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $res = json_decode(curl_exec($ch), true);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode === 200) {
            $stats['sucessos']++;
        } else {
            $stats['falhas']++;
            // Se o token for inválido, guarda para deletar
            if (isset($res['error']['details'][0]['errorCode']) && $res['error']['details'][0]['errorCode'] === 'UNREGISTERED') {
                $stats['invalidos'][] = $token;
            }
        }
    }
    curl_close($ch);

    // 7. Limpa tokens mortos
    if (!empty($stats['invalidos'])) {
        $placeholders = implode(',', array_fill(0, count($stats['invalidos']), '?'));
        $del = $conn->prepare("DELETE FROM usuario WHERE notify_token IN ($placeholders)");
        $del->execute($stats['invalidos']);
    }

    echo json_encode([
        "status" => "processado",
        "enviados" => $stats['sucessos'],
        "erros" => $stats['falhas'],
        "removidos_por_invalidez" => count($stats['invalidos'])
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}

// Função auxiliar
function obterTokenGoogle($path) {
    if (!file_exists($path)) throw new Exception("Arquivo credentials.json não encontrado.");
    $json = json_decode(file_get_contents($path), true);
    $now = time();
    $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
    $payload = base64_encode(json_encode([
        'iss' => $json['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => $now + 3600, 'iat' => $now
    ]));
    $sig = '';
    openssl_sign("$header.$payload", $sig, $json['private_key'], 'SHA256');
    $jwt = "$header.$payload." . base64_encode($sig);

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer', 'assertion' => $jwt]));
    $res = json_decode(curl_exec($ch), true);
    return $res['access_token'] ?? null;
}
?>