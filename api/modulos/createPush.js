export const createPush = () => {

    const titulo = document.querySelector("#titulo");
    const corpo = document.querySelector("#corpo");
    const imagem = document.querySelector("#imagem");
    const token = document.querySelector("#token");

    const previewTitle = document.getElementById('previewTitle');
    const previewText = document.getElementById('previewText');
    const previewImage = document.getElementById('previewImage');
    const btnEnviar = document.querySelector("#btnEnviar");
    const btnEnviarAll = document.querySelector("#btnEnviarAll");
    btnEnviarAll.addEventListener("click", () => {
        const dados = {
            titulo: titulo.value,
            corpo: corpo.value,
            imagem: imagem.value
        }
    $.ajax({
            url: 'enviar_massa.php', // Nome do arquivo PHP que criamos
            type: 'POST',
            data: dados,
            dataType: 'json', // Garante que o jQuery trate a resposta como JSON
            success: function(data) {
                if(data.status === 'processado') {
                   console.log(`
                        <div class="alert alert-success">
                            <b>Sucesso!</b><br>
                            ✅ Enviados: ${data.enviados}<br>
                            ❌ Falhas: ${data.erros}<br>
                            🧹 Tokens Inválidos Removidos: ${data.removidos_por_invalidez}
                        </div>
                    `);
                } else {
                    $('#resultado').html('<div class="alert alert-danger">Erro: ' + data.error + '</div>');
                }
            },
            error: function() {
                alert("Erro crítico ao processar a requisição no servidor");
            },
            complete: function() {
                
            }
        });
    });
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
            data: dados,

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
// export const pushMassive = ()=> {
//     const btnEnviarAll = document.querySelector("#btnEnviarAll");
//     btnEnviarAll.addEventListener("click", () => {
//     $.ajax({
//             url: 'enviar_massa.php', // Nome do arquivo PHP que criamos
//             type: 'POST',
//             data: $(this).serialize(),
//             dataType: 'json',
//             success: function(data) {
//                 if(data.status === 'processado') {
//                     $('#resultado').html(`
//                         <div class="alert alert-success">
//                             <b>Sucesso!</b><br>
//                             ✅ Enviados: ${data.enviados}<br>
//                             ❌ Falhas: ${data.erros}<br>
//                             🧹 Tokens Inválidos Removidos: ${data.removidos_por_invalidez}
//                         </div>
//                     `);
//                 } else {
//                     $('#resultado').html('<div class="alert alert-danger">Erro: ' + data.error + '</div>');
//                 }
//             },
//             error: function() {
//                 $('#resultado').html('<div class="alert alert-danger">Erro crítico ao processar a requisição no servidor.</div>');
//             },
//             complete: function() {
//                 $('#loader').hide();
//                 $('#btnEnviar').prop('disabled', false);
//             }
//         });
//     });
// }