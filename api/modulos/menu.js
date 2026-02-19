import { router } from "../router/router.js";
export const mainMenu = ()=> {
    const menuTemplate = `
    <!-- Header alinhado com topbar -->
    <div class="d-flex align-items-center px-3 topbar bg-dark border-0">
      <h5 class="mb-0 text-white">Admin</h5>
    </div>

    <!-- Menu -->
    <nav class="p-3">
      <ul class="nav nav-pills flex-column gap-1">
        <li class="nav-item"><a class="nav-link active" href="#">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Usuários</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Produtos</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Pedidos</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Configurações</a></li>
      </ul>
    </nav>
    `;
    //adicionando menu no template
    const barMenu = document.querySelector("#mySideMenu");
    barMenu.innerHTML = menuTemplate;
    //criando o sistema de navegação por rota
    const navLinks = document.querySelectorAll(".nav-link");
    navLinks.forEach( link => {
        link.addEventListener("click",(e)=> {
            console.log(e.currentTarget.innerText)
            let rota = e.currentTarget.innerText;
            router(rota)
        })
    });
    
}
export const adminMenu = ()=> {
    const admMenuTemplate = `
        <li><a class="dropdown-item" href="#">Perfil</a></li>
        <li><a class="dropdown-item" href="#">Sair</a></li>
    `;
    //adicionando menu no template
    const admiMenuArea = document.querySelector("#adm-Menu");
    admiMenuArea.innerHTML = admMenuTemplate;
    //criando o sistema de navegação por rota
    const dropItens = document.querySelectorAll(".dropdown-item");
    dropItens.forEach( item => {
        item.addEventListener("click",(e)=> {
            console.log(e.currentTarget.innerText)
            let rota = e.currentTarget.innerText;
            router(rota)
        })
    });
}