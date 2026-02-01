export const enviarEmail = ()=> {
    $('.contact-form').on('submit', function(e) {
        // 1. Impede o recarregamento da página
        e.preventDefault();

        // Referência do formulário e do botão para feedback visual
        const $form = $(this);
        const $btn = $form.find('.btn-submit');
        const originalBtnText = $btn.text();

        // 2. Coleta os dados do formulário
        // Usamos FormData para facilitar, mas transformamos em objeto para o padrão REST JSON
        const formData = {};
        $form.serializeArray().forEach(item => {
            formData[item.name] = item.value;
        });

        // 3. Feedback visual: desabilita o botão enquanto envia
        $btn.prop('disabled', true).text('Enviando...');

        // 4. Chamada AJAX
        $.ajax({
            url: 'contato.php', // Verifique se o caminho está correto
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData), // Enviando como string JSON
            dataType: 'json',
            success: function(response) {
                // Executado se o status code for 200
                alert('Sucesso: ' + response.message);
                $form[0].reset(); // Limpa o formulário
            },
            error: function(xhr) {
                // Executado para erros 400, 405, 500, etc.
                const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Erro desconhecido ao enviar.';
                alert('Ops! ' + errorMsg);
            },
            complete: function() {
                // Reabilita o botão após terminar (sucesso ou erro)
                $btn.prop('disabled', false).text(originalBtnText);
            }
        });
    });
}