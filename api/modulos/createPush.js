export const createPush = () => {

    const titulo = document.querySelector("#titulo");
    const corpo = document.querySelector("#corpo");
    const imagem = document.querySelector("#imagem");
    const token = document.querySelector("#token");
    const authToken = localStorage.getItem("jwt_token");
    const previewTitle = document.getElementById('previewTitle');
    const previewText = document.getElementById('previewText');
    const previewImage = document.getElementById('previewImage');
    const btnEnviar = document.querySelector("#btnEnviar");
    btnEnviar.addEventListener("click", () => {
        const dados = {
            titulo: titulo.value,
            corpo: corpo.value,
            imagem: imagem.value,
            mytoken: token.value
        }

        $.ajax({
            url: "https://api.dreamsorcererstudios.com.br/ConfigPushNotification.php",
            type: 'POST',
            contentType: 'application/json', // Importante para o php://input
            dataType: 'json',
            data: dados,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'Bearer ' + authToken);
            },
            success: function (response) {
                try {
                    let resposta = response;

                    console.log(resposta);
                } catch {
                    let resposta = response;

                    console.log(resposta);

                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });

    // aqui codigo para atualizar imagens e textos
    // Título
    titulo.addEventListener('input', () => {
        previewTitle.textContent =
            titulo.value || 'Título da notificação';
    });

    // Texto
    corpo.addEventListener('input', () => {
        previewText.textContent =
            corpo.value || 'Texto da notificação';
    });

    // Imagem
    imagem.addEventListener('input', () => {
        previewImage.src =
            imagem.value || 'https://cdn-icons-png.flaticon.com/512/4712/4712109.png';
    });

    // Fallback se a imagem quebrar
    previewImage.onerror = () => {
        previewImage.src = 'https://cdn-icons-png.flaticon.com/512/4712/4712109.png';
    };
}