<?php
// 1. Ligar reporte de erros para ver o culpado real
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

try {
    // 2. Tente carregar o banco
    if (!file_exists('db.php')) throw new Exception("Arquivo db.php nao encontrado");
    require_once 'db.php'; 

    if (!isset($pdo)) throw new Exception("Variavel \$pdo nao definida no db.php");

    // 3. Dados do Firebase
    $projectId = 'bibliaquiz-527bd'; 
    $jsonPath = __DIR__ . '/credentials.json';
    
    if (!file_exists($jsonPath)) throw new Exception("Arquivo credentials.json nao encontrado em: " . $jsonPath);

    // 4. Pegar Token de Acesso (Processo embutido para evitar erro 500 de funcao)
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

    $chAuth = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt($chAuth, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chAuth, CURLOPT_POST, true);
    curl_setopt($chAuth, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]));
    $resAuth = json_decode(curl_exec($chAuth), true);
    $accessToken = $resAuth['access_token'] ?? null;
    curl_close($chAuth);

    if (!$accessToken) throw new Exception("Nao foi possivel gerar o Access Token do Google");

    // 5. Buscar os 2 tokens
    $stmt = $pdo->query("SELECT notify_token FROM usuario WHERE notify_token IS NOT NULL AND notify_token != '' LIMIT 10");
    $tokens = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $url = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";
    $resultados = [];

    foreach ($tokens as $token) {
        $msg = [
            'message' => [
                'token' => trim($token),
                'notification' => [
                    'title' => $_POST['titulo'] ?? 'Teste',
                    'body' => $_POST['corpo'] ?? 'Conteudo',
                    'image' => $_POST['imagem'] ?? ''
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken, 'Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($msg));
        $res = curl_exec($ch);
        $resultados[] = json_decode($res, true);
        curl_close($ch);
    }

    echo json_encode([
        "status" => "processado",
        "detalhes" => $resultados
    ]);

} catch (Exception $e) {
    // Se der erro, ele vai imprimir aqui em vez de dar Erro 500 genérico
    echo json_encode(["error" => $e->getMessage()]);
}