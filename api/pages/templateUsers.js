import { temaplatePush } from "../modulos/templatePush.js";
import { mainMenu,adminMenu } from "../modulos/menu.js";
import { consultaUsers } from "../modulos/consultaUsers.js";
export const templateUsers = (_usuario)=> {
    _usuario = "Admin"
    const nomeProjects = "BibliaQuiz"
    const myTemplate = `
<div class="admin-grid">

  <!-- SIDEBAR (DESKTOP) -->
  <aside class="sidebar d-none d-lg-flex flex-column" id="mySideMenu">
    
  </aside>

  <!-- TOPBAR -->
  <header class="topbar d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
      <!-- Hamburger (mobile) -->
      <button class="btn btn-outline-secondary d-lg-none"
              data-bs-toggle="offcanvas"
              data-bs-target="#mobileSidebar">
        <i class="bi bi-list"></i>
      </button>
      <h5 class="mb-0 subTitleMenu">Dashboard - ${nomeProjects}</h5>
    </div>

    <div class="dropdown">
      <button class="btn btn-dark dropdown-toggle" data-bs-toggle="dropdown">
        <i class="bi bi-person-circle"></i> ${_usuario}
      </button>
      <ul class="dropdown-menu dropdown-menu-end" id="adm-Menu">
        <li><a class="dropdown-item" href="#">Perfil</a></li>
        <li><a class="dropdown-item" href="#">Sair</a></li>
      </ul>
    </div>
  </header>

  <!-- CONTEÚDO -->
  <main class="content">

    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <small class="text-muted">Usuários</small>
            <h3 id="total-geral">1.245</h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <small class="text-muted">Pedidos</small>
            <h3>312</h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <small class="text-muted">Faturamento</small>
            <h3>R$ 18.430</h3>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <h6 class="card-title mb-3">Últimos Usuários</h6>
        <table class="table align-middle">
          <thead class="table-light">
            <tr>
              <th>Nome</th>
              <th>Email</th>
              <th>Status</th>
              <th>Perfil</th>
            </tr>
          </thead>
          <tbody class="user-list">
            <tr>
              <td>Ana Silva</td>
              <td>ana@email.com</td>
              <td><span class="badge bg-success">Ativo</span></td>
            </tr>
            <tr>
              <td>João Lima</td>
              <td>joao@email.com</td>
              <td><span class="badge bg-danger">Inativo</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<!-- SIDEBAR MOBILE -->
<div class="offcanvas offcanvas-start bg-dark text-white" id="mobileSidebar">
  <div class="offcanvas-header">
    <h5 class="mb-0">Admin</h5>
    <button class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="nav nav-pills flex-column gap-1">
      <li class="nav-item"><a class="nav-link active" href="#">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="#">Usuários</a></li>
      <li class="nav-item"><a class="nav-link" href="#">Produtos</a></li>
      <li class="nav-item"><a class="nav-link" href="#">Pedidos</a></li>
      <li class="nav-item"><a class="nav-link" href="#">Configurações</a></li>
    </ul>
  </div>
</div>
    `;
    const myApp = document.querySelector("#app");
    myApp.innerHTML = myTemplate;
    //temaplatePush()
    mainMenu()
    adminMenu()
    consultaUsers()
}