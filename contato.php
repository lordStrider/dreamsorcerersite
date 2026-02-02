<?php
// Configurações de cabeçalho para API REST
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require 'vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Recebe o input
    $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;

    // --- BLOCO RECAPTCHA V3 ---
    $recaptcha_secret = '6LcSpIQaAAAAACyUYumtkwHQBPgNDuwXLZoKhXHu'; // <--- INSIRA SUA SECRET KEY AQUI
    $token = $input['recaptcha_token'] ?? '';

    if (!$token) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Token de segurança ausente."]);
        exit;
    }

    // Validação via cURL contra os servidores do Google
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['secret' => $recaptcha_secret, 'response' => $token]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $captcha_data = json_decode($response);

    // Se a validação falhar ou o score for muito baixo (abaixo de 0.5)
    if (!$captcha_data->success || $captcha_data->score < 0.5) {
        http_response_code(403);
        echo json_encode(["status" => "error", "message" => "Atividade suspeita detectada (Bot)."]);
        exit;
    }
    // --- FIM DO BLOCO RECAPTCHA ---

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
        $mail->Username   = 'contato@dreamsorcererstudios.com.br';
        $mail->Password   = '32Master#78'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8';

        // --- E-MAIL 1: PARA VOCÊ ---
        $mail->setFrom('contato@dreamsorcererstudios.com.br', 'Dream Sorcerer Studios - Contato');
        $mail->addAddress('contato@dreamsorcererstudios.com.br'); 
        $mail->addReplyTo($email, $nome);

        $mail->isHTML(true);
        $mail->Subject = "Novo Contato: " . ucfirst($assunto_form);
        $mail->Body    = "<h3>Nova mensagem recebida:</h3>
                          <p><b>Nome:</b> $nome</p>
                          <p><b>E-mail:</b> $email</p>
                          <p><b>Assunto:</b> $assunto_form</p>
                          <p><b>Mensagem:</b><br>$mensagem</p>";
        $mail->send();

        // --- E-MAIL 2: PARA O USUÁRIO (Confirmação) ---
        $mail->clearAddresses();
        $mail->addAddress($email);
        $mail->Subject = "Recebemos sua mensagem - Dream Sorcerer Studios";
        $mail->Body    = "<div style='background-color: #050111; padding: 40px 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #0d0428; border-radius: 10px; overflow: hidden; border: 1px solid #1f144d;'>
                <div style='padding: 20px 25px; background-color: #1a0b41;'>
                    <img src='https://dreamsorcererstudios.com.br/images/logo.png' style='height: 50px; vertical-align: middle; margin-right: 10px;'>
                    <span style='font-size: 20px; font-weight: bold; color: #ffffff; vertical-align: middle;'>Dream Sorcerer <span style='color: #00f2ff;'>Studios</span></span>
                </div>
                <div style='padding: 30px; line-height: 1.6;'>
                    <h2 style='color: #00ccff; margin-top: 0; text-transform: capitalize;'>Olá, $nome!</h2>
                    <p style='font-size: 16px;'>Recebemos sua mensagem sobre <strong>$assunto_form</strong>.</p>
                    <p style='font-size: 16px;'>Nossos magos e desenvolvedores já estão analisando seu contato e responderemos o mais breve possível.</p>
                    
                    <div style='margin-top: 30px; padding: 20px; background-color: #1f144d; border-radius: 8px; border-left: 4px solid #00ccff;'>
                        <p style='margin: 0; font-style: italic; font-size: 14px; color: #b3b3b3;'>'Transformando sonhos em pixels e magia em código.'</p>
                    </div>
                </div>

                <div style='padding: 30px; text-align: center; background-color: #050111; font-size: 12px; color: #666666;'>
                    <p style='margin-bottom: 10px;'>
                        <strong style='color: #ffffff;'>Dream Sorcerer <span style='color: #00ccff;'>Studios</span></strong>
                    </p>
                    <p>Este é um e-mail automático. Por favor, não responda diretamente a esta mensagem.</p>
                    <div style='margin-top: 20px;'>
                        <a href='https://dreamsorcererstudios.com.br/' style='color: #00ccff; text-decoration: none; margin: 0 10px;'>Website</a> | 
                        <a href='https://dreamsorcererstudios.com.br/#jogos' style='color: #00ccff; text-decoration: none; margin: 0 10px;'>Nossos Jogos</a>
                    </div>
                </div>
            </div>
        </div>";

        $mail->send();

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