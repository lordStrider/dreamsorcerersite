
import { templateAdmin } from "../pages/templateAdmin.js";
export const loginAdm = ()=> {
    // --- FUNÇÃO DE LOGIN ---
    const btnLogin = document.querySelector(".btn-login")
       btnLogin.addEventListener("click", async (e) => {
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
                
                mensagemDiv.style.color = 'green';
                mensagemDiv.innerText = 'Login realizado! Redirecionando...';
                
                // Redireciona após 1.5 segundos
                setTimeout(() => {
                    window.location.href = 'dashboard.php'; 
                }, 1500);
            } else {
                // ERRO (401 ou outros)
                mensagemDiv.style.color = 'red';
                mensagemDiv.innerText = resultado.erro || 'Erro ao entrar';
            }

        } catch (error) {
            mensagemDiv.style.color = 'red';
            mensagemDiv.innerText = 'Erro ao conectar com o servidor.';
            console.error(error);
        }
        });
}