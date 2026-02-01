<?php
// Define o cabeçalho para JSON e permissões de CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Recebe os dados (suporta tanto formulário padrão quanto JSON enviado via Fetch)
    $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;

    $nome    = filter_var($input['name'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $email   = filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $assunto = filter_var($input['subject'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $mensagem = filter_var($input['message'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

    // Validação
    if (empty($nome) || empty($email) || empty($mensagem)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "Dados incompletos."]);
        exit;
    }

    $seu_email = "contato@dreamsorcererstudios.com.br.com.br";

    // 1. E-mail para a empresa
    $corpo_adm = "Nova mensagem de: $nome\nEmail: $email\nAssunto: $assunto\nMensagem: $mensagem";
    $envio_adm = mail($seu_email, "API Contato: $assunto", $corpo_adm, "From: sistema@seusite.com");

    // 2. E-mail de confirmação para o cliente
    $corpo_cliente = "Olá $nome, recebemos seu contato sobre $assunto. Responderemos em breve!";
    mail($email, "Recebemos seu e-mail!", $corpo_cliente, "From: $seu_email");

    if ($envio_adm) {
        http_response_code(200); // OK
        echo json_encode([
            "status" => "success",
            "message" => "Mensagem enviada com sucesso!"
        ]);
    } else {
        http_response_code(500); // Server Error
        echo json_encode(["status" => "error", "message" => "Falha ao enviar e-mail."]);
    }

} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["status" => "error", "message" => "Método não permitido."]);
}