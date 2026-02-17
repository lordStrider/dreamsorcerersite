<?php
require_once 'validar.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Recebe os dados do POST
$titulo  = $_POST['titulo']  ?? '';
$body    = $_POST['corpo']   ?? '';
$image   = $_POST['imagem']  ?? '';
$myToken = $_POST['mytoken'] ?? '';

// ID do seu projeto (encontrado no Firebase Console ou no credentials.json)
$projectId = 'bibliaquiz-527bd';

/**
 * Função para obter o Access Token via OAuth2 (Simulada/Simplificada)
 * Nota: Em produção, use 'google/apiclient' via Composer.
 */
function getAccessToken($pathToServiceAccountJson) {
    $json = json_decode(file_get_contents($pathToServiceAccountJson), true);
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

function meuPush($titulo, $body, $image, $myToken, $projectId) {
    // 1. Obtém o token dinâmico (Substitua pelo caminho do seu arquivo JSON)
    $accessToken = getAccessToken('credentials.json');

    if (!$accessToken) {
        die("Erro ao gerar Token de Acesso.");
    }

    // 2. Endpoint da API v1
    $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

    // 3. Nova estrutura da mensagem
    $message = [
    'message' => [
        'token' => $myToken,
        'notification' => [
            'title' => $titulo,
            'body' => $body,
            'image' => $image // URL da imagem (deve ser https)
        ],
        'android' => [
            'priority' => 'high',
            'notification' => [
                'image' => $image,
                'notification_priority' => 'PRIORITY_MAX',
                'sound' => 'default'
            ]
        ],
        'apns' => [
            'payload' => [
                'aps' => [
                    'mutable-content' => 1, // Obrigatório para imagens no iOS
                    'sound' => 'default'
                ]
            ],
            'fcm_options' => [
                'image' => $image
            ]
        ]
    ]
];

    $json = json_encode($message);

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => $json
    ];

    $curl = curl_init();
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if (curl_errno($curl)) {
        echo 'Erro cURL: ' . curl_error($curl);
    } else {
        echo "Resposta ($http_code): " . $response;
    }

    curl_close($curl);
}

// Executa se houver um token de destino
if (!empty($myToken)) {
    meuPush($titulo, $body, $image, $myToken, $projectId);
} else {
    echo "Erro: Token do dispositivo não fornecido.";
}
?>