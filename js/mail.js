export const enviarEmail = () => {
    $('.contact-form').on('submit', function(e) {
        // 1. Impede o recarregamento da página
        e.preventDefault();

        // Referência do formulário e do botão para feedback visual
        const $form = $(this);
        const $btn = $form.find('.btn-submit');
        const originalBtnText = $btn.text();

        // 2. Feedback visual imediato
        $btn.prop('disabled', true).text('Verificando...');

        // 3. Execução do reCAPTCHA v3
        grecaptcha.ready(function() {
            grecaptcha.execute('6LcSpIQaAAAAANSYx9WGDqySFpncIFCbBNXCyU3r', { action: 'submit' }).then(function(token) {
                
                // 4. Coleta os dados do formulário
                const formData = {};
                $form.serializeArray().forEach(item => {
                    formData[item.name] = item.value;
                });

                // Adiciona o token do reCAPTCHA aos dados que serão enviados para a API
                formData['recaptcha_token'] = token;

                // 5. Atualiza feedback visual para o envio real
                $btn.text('Enviando...');

                // 6. Chamada AJAX
                $.ajax({
                    url: 'contato.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData), // Agora inclui o token
                    dataType: 'json',
                    success: function(response) {
                        alert('Sucesso: ' + response.message);
                        $form[0].reset(); 
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Erro desconhecido ao enviar.';
                        alert('Ops! ' + errorMsg);
                    },
                    complete: function() {
                        // Reabilita o botão após terminar
                        $btn.prop('disabled', false).text(originalBtnText);
                    }
                });
            });
        });
    });
}