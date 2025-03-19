$(document).ready(function () {
    let espacoCount = 1;
    let base_url = $("[name=baseUrl]").val();

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
                            <select name="espaco[]" class="form-control espaco-select" required>
                                ${espacoOptionsHtml} <!-- Inserindo as opções dinâmicas -->
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
        espacoCount--;
        atualizarRecursos();
    });

    // Monitorar seleção de espaços e carregar recursos
    $(document).on("change", ".espaco-select", function () {
        atualizarRecursos();
    });

    function atualizarRecursos() {
        let espacosSelecionados = [];
        $(".espaco-select").each(function () {
            let val = $(this).val();
            if (val) espacosSelecionados.push(val);
        });

        if (espacosSelecionados.length > 0) {
            carregarRecursos(espacosSelecionados);
        } else {
            $("#recursos-lista").hide();
            $("#mensagem-recursos").show();
        }
    }

    function carregarRecursos(espacosSelecionados) {
        $.ajax({
            url: base_url + "recursos/getByEspacos",
            type: "POST",
            data: { espacos: espacosSelecionados },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    let recursosHtml = "<fieldset class='fieldset-child'><legend class='fieldset-child'>Recursos Disponíveis</legend>";

                    // **📌 Adiciona Recursos de Espaços**
                    for (let espaco in response.recursos_por_espaco) {
                        let nomeEspaco = response.recursos_por_espaco[espaco][0]?.nome_espaco || `Espaço ${espaco}`;
                        recursosHtml += `<strong>${nomeEspaco}:</strong><br>`;
                        response.recursos_por_espaco[espaco].forEach((recurso) => {
                            recursosHtml += criarRecursoHTML(recurso);
                        });
                    }

                    // **📌 Adiciona Recursos de Prédios**
                    for (let predio in response.recursos_por_predio) {
                        let nomePredio = response.recursos_por_predio[predio][0]?.nome_predio || `Prédio ${predio}`;
                        recursosHtml += `<strong>${nomePredio}:</strong><br>`;
                        response.recursos_por_predio[predio].forEach((recurso) => {
                            recursosHtml += criarRecursoHTML(recurso);
                        });
                    }

                    // **📌 Adiciona Recursos Gerais**
                    if (response.recursos_gerais.length > 0) {
                        recursosHtml += `<strong>Recursos Gerais:</strong><br>`;
                        response.recursos_gerais.forEach((recurso) => {
                            recursosHtml += criarRecursoHTML(recurso);
                        });
                    }

                    recursosHtml += "</fieldset>";

                    // **📌 Atualiza a View**
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

    function criarRecursoHTML(recurso) {
        return `
            <div class="recurso-item d-flex align-items-center justify-content-between p-2 border rounded">
                <!-- Checkbox -->
                <div class="d-flex align-items-center">
                    <input class="form-check-input recurso-checkbox me-2" type="checkbox" id="recurso_${recurso.id}" name="recursos[]" value="${recurso.id}">
                    <label class="form-check-label fw-bold" for="recurso_${recurso.id}">
                        ${recurso.nome} <span class="text-muted">(${recurso.tipo})</span>
                    </label>
                </div>
                
                <!-- Disponibilidade e Input de Quantidade -->
                <div class="d-flex align-items-center">
                    <span class="text-success me-2">Disponível: ${recurso.quantidade}</span>
                    <input type="number" class="form-control quantidade-input text-center" 
                        id="quantidade_recurso_${recurso.id}" 
                        name="quantidade_recurso[${recurso.id}]" 
                        min="1" max="${recurso.quantidade}" 
                        disabled 
                        placeholder="Qtd"
                        style="width: 70px;">
                </div>
            </div>
        `;
    }
    
    // Ativar/desativar o input de quantidade ao marcar/desmarcar o checkbox
    $(document).on("change", ".recurso-checkbox", function () {
        let inputQtd = $(this).closest(".recurso-item").find(".quantidade-input");
        inputQtd.prop("disabled", !$(this).is(":checked"));
    });

    $('.nav.nav-tabs .nav-item a').click(function (e) {
        e.preventDefault();
        $('.nav.nav-tabs .nav-item a').removeClass('active').removeAttr('aria-current');
        $(this).addClass('active').attr('aria-current', 'page');
        let tabId = $(this).attr('href');
        $(tabId).addClass('show active').siblings().removeClass('show active');
    });
});
