import { templateHome } from "../pages/templateHome.js";
import { templateUsers } from "../pages/templateUsers.js";
import { templateAdmin } from "../pages/templateAdmin.js";
export const router = (_rota)=> {
  //const http = constantes.url;
  //const token = localStorage.getItem("access_token");
  console.log(_rota)
    switch (_rota) {
        case 'Sair':
          templateHome();
          break;
        case 'Usuários':
          templateUsers()
          break;
        case 'Dashboard':
          templateAdmin()
          break;
        case 'Comissões':
          comissoes(token,http);
          break;
        case 'QRCode':
          qrcode(token,http)
        break;
        case 'Gerenciar Escalas':
          gerenciarEscalas(token,http)
        break;
        case 'Escala Colaboradores':
          escalaColab(token,http)
        break;
        case 'Extrato':
          extrato(token,http)
        break;
        default:
          console.log('Rota não reconhecida${}');
      }
}