<?php
// Configurações de cabeçalho para API REST
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Se você usou Composer, este é o caminho padrão:
require 'vendor/autoload.php'; 

// Caso o autoload não exista, use os caminhos manuais abaixo (comente o de cima):
/*
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Recebe o input (seja JSON via fetch/ajax ou FormData)
    $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;

    // Sanitização básica
    $nome    = filter_var($input['name'], FILTER_SANITIZE_SPECIAL_CHARS);
    $email   = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
    $assunto_form = filter_var($input['subject'], FILTER_SANITIZE_SPECIAL_CHARS);
    $mensagem = filter_var($input['message'], FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($nome) || empty($email) || empty($mensagem)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Preencha todos os campos."]);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // Configurações SMTP da Hostinger
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'contato@dreamsorcererstudios.com.br'; // O e-mail que você criou na Hostinger
        $mail->Password   = '32Master#78';             // A senha do e-mail acima
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8';

        // --- E-MAIL 1: PARA VOCÊ (Notificação de novo contato) ---
        $mail->setFrom('contato@dreamsorcererstudios.com.br', 'Site de Jogos - Contato');
        $mail->addAddress('squallion26@gmail.com'); // Seu e-mail pessoal que receberá o aviso
        $mail->addReplyTo($email, $nome);           // Ao clicar em responder, vai para o cliente

        $mail->isHTML(true);
        $mail->Subject = "Novo Contato: " . ucfirst($assunto_form);
        $mail->Body    = "<h3>Nova mensagem recebida:</h3>
                          <p><b>Nome:</b> $nome</p>
                          <p><b>E-mail:</b> $email</p>
                          <p><b>Assunto:</b> $assunto_form</p>
                          <p><b>Mensagem:</b><br>$mensagem</p>";

        $mail->send();

        // --- E-MAIL 2: PARA O USUÁRIO (Confirmação) ---
        $mail->clearAddresses(); // Limpa o destinatário anterior
        $mail->addAddress($email); // Envia para o e-mail que o usuário digitou no form
        $mail->Subject = "Recebemos sua mensagem - Nome do Seu Site";
        $mail->Body    = "<h2>Olá, $nome!</h2>
                          <p>Recebemos seu contato sobre <b>$assunto_form</b>.</p>
                          <p>Nossa equipe já está analisando e responderemos em breve.</p>
                          <p>Atenciosamente,<br>Equipe do Site</p>";

        $mail->send();

        // Retorno de Sucesso para o AJAX
        echo json_encode(["status" => "success", "message" => "Mensagem enviada com sucesso!"]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Erro no envio: {$mail->ErrorInfo}"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Método não permitido."]);
}
?>