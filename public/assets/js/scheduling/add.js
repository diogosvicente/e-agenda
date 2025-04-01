$(document).ready(function () {
    let espacoCount = 1;
    let base_url = $("#baseUrl").val(); // Obtido do input hidden

    // Adicionar novo espaço
    $("#addEspaco").click(function () {
        espacoCount++;

        // Obtém as opções do primeiro select de espaços
        let espacoOptionsHtml = $(".espaco-select:first").html();

        let newEspaco = `
            <div class="espaco-entry" data-espaco="${espacoCount}">
                <fieldset class="fieldset-child">
                    <legend class="fieldset-child">Espaço ${espacoCount}</legend>

                    <div class="row">
                        <div class="col-sm-12">
                            <label for="espaco_${espacoCount}" class="form-label">Espaço Disponível: *</label>
                            <select id="espaco_${espacoCount}" name="espaco[]" class="form-control espaco-select" required>
                                ${espacoOptionsHtml}
                            </select>
                        </div>
                    </div>

                    <div class="datas_horarios">
                        <div class="row mt-3 data-hora-entry">
                            <div class="col-sm-4">
                                <label class="form-label">Data Início:</label>
                                <input type="date" name="data_inicio[]" class="form-control" required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Hora de Início:</label>
                                <input type="time" name="hora_inicio[]" class="form-control" required>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">Hora de Fim:</label>
                                <input type="time" name="hora_fim[]" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-success addDataHora">+ Adicionar Data/Hora</button>
                        </div>
                        <div class="col-sm-6 text-end">
                            <button type="button" class="btn btn-danger removeEspaco">Remover Espaço</button>
                        </div>
                    </div>
                </fieldset>
            </div>
        `;

        $("#espacos-container").append(newEspaco);

        // Atualiza os recursos quando um novo espaço é adicionado
        atualizarRecursos();
    });

    // Adicionar nova Data/Hora no respectivo espaço
    $(document).on("click", ".addDataHora", function () {
        let espaco = $(this).closest(".espaco-entry");
        let newEntry = `
            <div class="row mt-3 data-hora-entry">
                <div class="col-sm-4">
                    <input type="date" name="data_inicio[]" class="form-control" required>
                </div>
                <div class="col-sm-4">
                    <input type="time" name="hora_inicio[]" class="form-control" required>
                </div>
                <div class="col-sm-3">
                    <input type="time" name="hora_fim[]" class="form-control" required>
                </div>
                <div class="col-sm-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-entry">X</button>
                </div>
            </div>
        `;
        espaco.find(".datas_horarios").append(newEntry);
    });

    // Remover Data/Hora (exceto a primeira de cada espaço)
    $(document).on("click", ".remove-entry", function () {
        let espaco = $(this).closest(".espaco-entry");
        if (espaco.find(".data-hora-entry").length > 1) {
            $(this).closest(".data-hora-entry").remove();
        }
    });

    // Remover Espaço e atualizar contador
    $(document).on("click", ".removeEspaco", function () {
        $(this).closest(".espaco-entry").remove();

        // Renomeia os espaços corretamente após a remoção
        $(".espaco-entry").each(function (index) {
            $(this).attr("data-espaco", index + 1);
            $(this).find("legend").text("Espaço " + (index + 1));
        });

        atualizarRecursos();
    });

    // Monitorar seleção de espaços e carregar recursos
    $(document).on("change", ".espaco-select", function () {
        atualizarRecursos();
    });

    let atualizarRecursosTimeout;
    function atualizarRecursos() {
        clearTimeout(atualizarRecursosTimeout);
        atualizarRecursosTimeout = setTimeout(() => {
            let espacosSelecionados = $(".espaco-select").map(function () {
                return $(this).val();
            }).get().filter(Boolean); // Remove valores vazios

            if (espacosSelecionados.length > 0) {
                carregarRecursos(espacosSelecionados);
            } else {
                $("#recursos-lista").hide();
                $("#mensagem-recursos").show();
            }
        }, 500); // Aguarda 500ms antes de buscar os recursos
    }

    function carregarRecursos(espacosSelecionados) {
        // Salvar seleção e quantidades antes da atualização
        let recursosSelecionados = {};
        $(".recurso-checkbox").each(function () {
            let recursoId = $(this).val();
            let quantidade = $(this).closest(".recurso-item").find(".quantidade-input").val();
            let marcado = $(this).prop("checked");

            if (marcado) {
                recursosSelecionados[recursoId] = quantidade; // Armazena ID do recurso e quantidade
            }
        });

        $.ajax({
            url: base_url + "recursos/getByEspacos",
            type: "POST",
            data: { espacos: espacosSelecionados },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    let recursosHtml = "<fieldset class='fieldset-child'><legend class='fieldset-child'>Recursos Disponíveis</legend>";

                    // Recursos por Espaço
                    for (let espaco in response.recursos_por_espaco) {
                        let nomeEspaco = response.recursos_por_espaco[espaco][0]?.nome_espaco || `Espaço ${espaco}`;
                        recursosHtml += `<strong>${nomeEspaco}:</strong><br>`;
                        response.recursos_por_espaco[espaco].forEach((recurso) => {
                            recursosHtml += criarRecursoHTML(recurso, recursosSelecionados);
                        });
                    }

                    // Recursos por Prédio
                    for (let predio in response.recursos_por_predio) {
                        let nomePredio = response.recursos_por_predio[predio][0]?.nome_predio || `Prédio ${predio}`;
                        recursosHtml += `<strong>${nomePredio}:</strong><br>`;
                        response.recursos_por_predio[predio].forEach((recurso) => {
                            recursosHtml += criarRecursoHTML(recurso, recursosSelecionados);
                        });
                    }

                    // Recursos Gerais
                    if (response.recursos_gerais.length > 0) {
                        recursosHtml += `<strong>Recursos Gerais:</strong><br>`;
                        response.recursos_gerais.forEach((recurso) => {
                            recursosHtml += criarRecursoHTML(recurso, recursosSelecionados);
                        });
                    }

                    recursosHtml += "</fieldset>";

                    // Atualiza a View
                    $("#recursos-gerais").html(recursosHtml);
                    $("#recursos-lista").show();
                    $("#mensagem-recursos").hide();
                }
            },
            error: function () {
                console.error("Erro ao buscar recursos.");
            }
        });
    }

    // Função para criar os elementos dos recursos, preservando seleções
    function criarRecursoHTML(recurso, recursosSelecionados) {
        let checked = recursosSelecionados.hasOwnProperty(recurso.id) ? "checked" : "";
        let qtd = recursosSelecionados[recurso.id] !== undefined ? recursosSelecionados[recurso.id] : ""; // Mantém o valor correto

        return `
            <div class="recurso-item d-flex align-items-center justify-content-between p-2 border rounded">
                <div class="d-flex align-items-center">
                    <input class="form-check-input recurso-checkbox me-2" type="checkbox" id="recurso_${recurso.id}" name="recursos[]" value="${recurso.id}" ${checked}>
                    <label class="form-check-label fw-bold" for="recurso_${recurso.id}">
                        ${recurso.nome} <span class="text-muted">(${recurso.tipo})</span>
                    </label>
                </div>
                <span class="text-success me-2">Disponível: ${recurso.quantidade}</span>
                <input type="number" class="form-control quantidade-input text-center" 
                    name="quantidade_recurso[${recurso.id}]" 
                    min="1" max="${recurso.quantidade}" 
                    value="${qtd}" 
                    ${checked ? "" : "disabled"} 
                    style="width: 70px;">
            </div>
        `;
    }

    // Ativar/desativar o input de quantidade ao marcar/desmarcar o checkbox
    $(document).on("change", ".recurso-checkbox", function () {
        let inputQtd = $(this).closest(".recurso-item").find(".quantidade-input");
        inputQtd.prop("disabled", !$(this).is(":checked"));
    });

    $('.nav.nav-tabs .nav-item a').on("click", function (e) {
        e.preventDefault();
        $(this).tab('show'); // Usa o Bootstrap para alternar corretamente
    });

    // --- Funções para manipulação do responsável ---
    // Assumindo que as variáveis globais 'loggedUser' e 'users' já foram definidas na página
    let loggedUser = window.loggedUser || {};
    let users = window.users || [];

    // Função que copia os valores dos selects para os inputs correspondentes
    function updateResponsavelIDs() {
        $("#responsavel_nome_id").val( $("#responsavel_nome").val() );
        $("#responsavel_unidade_id").val( $("#responsavel_unidade").val() );
    }

    function toggleResponsavel(){
        let checkbox = $("#eu_sou_o_responsavel");
        let select = $("#responsavel_nome");
    
        $("#id_solicitante").val(loggedUser.id_usuario);
        $("#solicitante_nome").val(loggedUser.nome);
        $("#id_unidade_solicitante").val(loggedUser.id_unidade);
        $("#solicitante_unidade").val(loggedUser.id_unidade);
    
        if(checkbox.is(":checked")){
            if(loggedUser){
                $("#responsavel_nome").val(loggedUser.id_usuario || "");
                $("#responsavel_unidade").val(loggedUser.id_unidade || "");
                $("#responsavel_email").val(loggedUser.email || "");
                $("#responsavel_telefone1").val(loggedUser.telefone1 || "");
                $("#responsavel_telefone2").val(loggedUser.telefone2 || "");
                select.prop("disabled", true);
            }
        } else {
            select.prop("disabled", false);
            select.val("");
            $("#responsavel_unidade").val("");
            $("#responsavel_email").val("");
            $("#responsavel_telefone1").val("");
            $("#responsavel_telefone2").val("");
        }
        updateResponsavelIDs();
    }
    
    function fillResponsavelDetails(){
        if($("#eu_sou_o_responsavel").is(":checked")) return;
        let select = $("#responsavel_nome");
        let userId = select.val();
        let selectedUser = users.find(function(u){ return u.id == userId; });
        if(selectedUser){
            $("#responsavel_unidade").val(selectedUser.id_unidade || "");
            $("#responsavel_email").val(selectedUser.email || "");
            $("#responsavel_telefone1").val(selectedUser.telefone1 || "");
            $("#responsavel_telefone2").val(selectedUser.telefone2 || "");
        } else {
            $("#responsavel_unidade").val("");
            $("#responsavel_email").val("");
            $("#responsavel_telefone1").val("");
            $("#responsavel_telefone2").val("");
        }
        updateResponsavelIDs();
    }

    // Vincula os eventos aos campos do responsável
    $("#eu_sou_o_responsavel").change(toggleResponsavel);
    $("#responsavel_nome").change(fillResponsavelDetails);

    // Expondo as funções para o escopo global
    window.fillResponsavelDetails = fillResponsavelDetails;
    window.toggleResponsavel = toggleResponsavel;

    // Inicializa o estado do responsável ao carregar a página
    toggleResponsavel();
});
