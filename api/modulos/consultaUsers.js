

// Função para carregar os dados via AJAX
const url = "https://api.dreamsorcererstudios.com.br/"
export const consultaUsers = (pagina = 1, limite = 10) => {
    let paginaAtual = 1;
    // Altere 'seu_arquivo.php' para o nome real do seu arquivo PHP
    const userRow = (_dados) => {
        const listagem = document.querySelector(".user-list");
        let myRow = "";
        _dados.forEach(data => {
            myRow += `
                <tr>
                    <td>${data.usuario}</td>
                    <td>${data.email}</td>
                    <td><span class="badge bg-success">Ativo</span></td>
                    <td><div class="btn btn-success" data-id="${data.id}">Acessar</span></td>
                </tr>`
        });
        listagem.innerHTML = myRow;
        perfilData();
    }
    $.ajax({
        url: `${url}consultaUsers.php`,
        type: 'GET',
        data: { pagina: pagina, limit: limite },
        dataType: 'json',
        success: function (response) {
            if (response.status === "success") {
                const dados = response.dados;
                const pag = response.paginacao;

                // 1. Atualizar contadores
                $('#total-geral').text(response.total_geral_usuarios);
                console.log(dados)
                userRow(dados)
                // $('#pagina-atual').text(pag.pagina_atual);
                // $('#info-paginas').text(`Exibindo página ${pag.pagina_atual} de ${pag.total_paginas}`);
                // paginaAtual = pag.pagina_atual;

                // 2. Criar cabeçalho dinâmico (apenas na primeira carga ou se mudar)
                //     if (dados.length > 0) {
                //         let colunas = Object.keys(dados[0]);
                //         let headHtml = '<tr>';
                //         colunas.forEach(col => {
                //             headHtml += `<th class="text-capitalize">${col.replace('_', ' ')}</th>`;
                //         });
                //         headHtml += '</tr>';
                //         $('#tabela-head').html(headHtml);

                //         // 3. Preencher corpo da tabela
                //         let corpoHtml = '';
                //         dados.forEach(user => {
                //             corpoHtml += '<tr>';
                //             colunas.forEach(col => {
                //                 corpoHtml += `<td>${user[col] !== null ? user[col] : ''}</td>`;
                //             });
                //             corpoHtml += '</tr>';
                //         });
                //         $('#tabela-corpo').html(corpoHtml);
                //     } else {
                //         $('#tabela-corpo').html('<tr><td colspan="100%" class="text-center">Nenhum registro encontrado.</td></tr>');
                //     }

                //     // 4. Lógica dos botões Próximo/Anterior
                //     $('#btn-prev').toggleClass('disabled', !pag.tem_anterior).find('a').data('page', pag.pagina_atual - 1);
                //     $('#btn-next').toggleClass('disabled', !pag.tem_proxima).find('a').data('page', pag.pagina_atual + 1);

                // } else {
                //     alert("Erro do servidor: " + response.message);
                // }
            }
        },
        error: function () {
            $('#tabela-corpo').html('<tr><td colspan="100%" class="text-center text-danger">Erro ao conectar com o backend.</td></tr>');
        }
    });
    // Evento de clique nos botões de paginação
    $('.page-link').on('click', function (e) {
        e.preventDefault();
        let p = $(this).data('page');
        if (p) carregarDados(p, $('#select-limit').val());
    });

    // Evento de mudança no limite de registros
    $('#select-limit').on('change', function () {
        carregarDados(1, $(this).val());
    });
}
const perfilData = ()=> {
    const btnPerfis = document.querySelectorAll(".btn-success");
    btnPerfis.forEach( perfil => {
        perfil.addEventListener("click", (e)=> {
            const idUsuario = e.currentTarget.dataset.id;
            $.ajax({
        url: `${url}buscar_usuario.php`,
        type: 'GET',
        data: { id: idUsuario },
        success: function(response) {
            if (response.status === "success") {
                // Aqui você pode abrir um Modal ou dar um alert com os dados
                console.log(response.dados);
                alert("Usuário: " + response.dados.nome + "\nEmail: " + response.dados.email);
            }
        },
        error: function(xhr) {
            alert("Erro: " + xhr.responseJSON.message);
        }
    });
        })
    });
}

