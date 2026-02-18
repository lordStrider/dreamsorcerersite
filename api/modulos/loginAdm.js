
import { templateAdmin } from "../pages/templateAdmin.js";
export const loginAdm = () => {
    // --- FUNÇÃO DE LOGIN ---
    const btnLogin = document.querySelector(".btn-login")
    btnLogin.addEventListener("click", async (e) => {
        const usuario = document.getElementById('loginUsuario').value;
        const senha = document.getElementById('loginSenha').value;
        try {
            // Chamada para o seu arquivo PHP
            const response = await fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    usuario: usuario,
                    senha: senha
                })
            });

            const resultado = await response.json();
            
            if (response.ok) {
                // SUCESSO: Armazena o JWT no localStorage
                localStorage.setItem('jwt_token', resultado.token);

                
                alert('Login realizado! Redirecionando...');

                // Redireciona após 1.5 segundos
                setTimeout(() => {
                    
                    templateAdmin(usuario);
                }, 1500);
            } else {
                // ERRO (401 ou outros)
                toggleOverlay()
                alert(resultado.error);
            }

        } catch (error) {
            toggleOverlay()
            alert('Erro ao conectar com o servidor.');
            console.error(error);
        }
    });
}