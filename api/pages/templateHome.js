export const templateHome = ()=> {
    const myTemplate = `
    <div class="container-fluid vh-100 corpo-bg">
        <div class="row h-100">

            <!-- LADO ESQUERDO -->
            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center left-panel">
                <div class="text-white text-center px-5">
                    <h1 class="fw-bold">BEM VINDO DE VOLTA!</h1>
                    <p class="mt-3">
                        Acesse com sua conta para gerenciar.
                    </p>
                </div>
            </div>

            <!-- LADO DIREITO -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center login-area">
                <div class="login-box w-100">

                    <h2 class="mb-4 text-center fw-bold">Administrativo</h2>

                    <form id="loginForm">
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="" required>
                        </div>

                        <div class="mb-3">
                            <input type="password" class="form-control" placeholder="" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-login">
                            Acessar
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
    `;
    const myApp = document.querySelector("#app");
    myApp.innerHTML = myTemplate;

}