
import { templateAdmin } from "../pages/templateAdmin.js";
export const loginAdm = ()=> {
    // --- FUNÇÃO DE LOGIN ---
        document.getElementById('btnLogin').onsubmit = async (e) => {
            e.preventDefault();
            const dados = {
                usuario: document.getElementById('loginUsuario').value,
                senha: document.getElementById('loginSenha').value
            };

            const resp = await fetch('login.php', {
                method: 'POST',
                body: JSON.stringify(dados)
            });

            const res = await resp.json();

            if (res.token) {
                // SALVA O JWT NO NAVEGADOR
                localStorage.setItem('meu_jwt', res.token);
                templateAdmin();
            } else {
                document.getElementById('msgErro').innerText = res.erro || "Falha no login";
            }
        };
}