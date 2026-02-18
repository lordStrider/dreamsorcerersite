import { createPush } from "./createPush.js";
export const temaplatePush = ()=> {
    const meuPush = `
    <div class="container-fluid p-4">
  <div class="row">

    <!-- FORMULÁRIO -->
    <div class="col-lg-6 col-xs-12">
      <div class="card p-4 shadow-sm">

        <div class="mb-3">
          <label class="form-label">
            Título da notificação
            <i class="bi bi-question-circle text-muted"></i>
          </label>
          <input class="form-control" placeholder="Insira um título opcional" id="titulo">
        </div>

        <div class="mb-3">
          <label class="form-label">
            Texto da notificação
          </label>
          <textarea class="form-control" rows="3" placeholder="Insira o texto de notificação" id="corpo"></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">
            Imagem de notificação (opcional)
            <i class="bi bi-question-circle text-muted"></i>
          </label>
          <div class="input-group">
            <input class="form-control" placeholder="https://" id="imagem">
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label">
            Token do usuário
            <i class="bi bi-question-circle text-muted"></i>
          </label>
          <input class="form-control" placeholder="Informe nome opcional" id="token">
        </div>
        <div class="row">
          <div class="col-6">
            <div class="btn btn-primary" id="btnEnviar">Enviar</div>
          </div>
          <div class="col-6">
            <div class="btn btn-secondary" id="btnEnviarAll">Enviar todos</div>
          </div>
        </div>
      </div>
    </div>

    <!-- PREVIEW -->
    <div class="col-lg-6 col-xs-12">
      <div class="card-preview shadow-sm">

        <h5>Visualização do dispositivo</h5>
        <p class="form-hint">
        Aqui você pode ver visualmente como será a mensagem.
        </p>
        <!-- Tabs -->
        

        <!-- Android -->
        <span class="visualisacao">Estado Inicial</span>
        <div class="device">
        <div class="text-center mt-2 text-muted small"></div>
    <div class="notification d-flex align-items-center">
    
    <!-- Texto -->
    <div class="flex-grow-1">
      <strong id="previewTitle">Título da notificação</strong><br>
      <small class="text-muted" id="previewText">Texto da notificação</small>
    </div>

    <!-- Imagem à direita -->
    <img
      src="https://cdn-icons-png.flaticon.com/512/4712/4712109.png"
      class="ms-3" alt="Ícone"
    id="previewImage"/>

  </div>

</div>

        <!-- Apple -->
        <div class="device">
        <div class="text-center mt-2 text-muted small">Estado Espandido</div>
          <div class="notification">
            
            <div>
              <strong>Título da notificação</strong><br>
              <small>Texto da notificação</small>
            </div>
          </div>
         
        </div>

      </div>
    </div>

  </div>
</div>
    `;
const conteudo = document.querySelector(".content");
conteudo.innerHTML = meuPush;
createPush();
}