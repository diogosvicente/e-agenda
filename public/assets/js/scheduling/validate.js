let baseUrl = $("#baseUrl").val();

// Função que atualiza os inputs dos IDs do aprovador
function updateAprovadorIDs() {
    $("#aprovador_nome_id").val( $("#aprovador_nome").val() );
    $("#aprovador_unidade_id").val( $("#aprovador_unidade").val() );
}

/**
 * Preenche os dados do aprovador (unidade, e-mail, telefones)
 * baseado no userId fornecido.
 */
function fillAprovadorDetails(userId) {
    // Se userId estiver vazio, limpa os campos
    if (!userId) {
        $("#aprovador_unidade").val("");
        $("#aprovador_email").val("");
        $("#aprovador_telefone1").val("");
        $("#aprovador_telefone2").val("");
        updateAprovadorIDs();
        return;
    }
    // Busca o usuário na lista global
    let user = window.users.find(u => String(u.id) === String(userId));
    if (user) {
        $("#aprovador_unidade").val(user.id_unidade || "");
        $("#aprovador_email").val(user.email || "");
        $("#aprovador_telefone1").val(user.telefone1 || "");
        $("#aprovador_telefone2").val(user.telefone2 || "");
    } else {
        $("#aprovador_unidade").val("");
        $("#aprovador_email").val("");
        $("#aprovador_telefone1").val("");
        $("#aprovador_telefone2").val("");
    }
    updateAprovadorIDs();
}

/**
 * Exemplo “dummy” para preencher dados do responsável interno
 * quando selecionado em um <select>.
 */
function fillResponsavelDetails() {
    let selectedId = $("#responsavel_nome").val();
    if (selectedId) {
        let user = window.users.find(u => String(u.id) === String(selectedId));
        if (user) {
            $("#responsavel_email").val(user.email || "");
            $("#responsavel_telefone1").val(user.telefone1 || "");
            $("#responsavel_telefone2").val(user.telefone2 || "");
        }
    } else {
        $("#responsavel_email").val("");
        $("#responsavel_telefone1").val("");
        $("#responsavel_telefone2").val("");
    }
}

/**
 * Exibe os campos de responsável interno e esconde os de responsável externo.
 */
function toggleResponsavelInterno() {
    $("#grupo-responsavel-nome-interno, #grupo-responsavel-unidade-interno").show();
    $("#grupo-responsavel-nome-externo, #grupo-responsavel-unidade-externo").hide();
}

/**
 * Esconde os campos de responsável interno e exibe os de responsável externo.
 */
function toggleResponsavelExterno() {
    $("#grupo-responsavel-nome-interno, #grupo-responsavel-unidade-interno").hide();
    $("#grupo-responsavel-nome-externo, #grupo-responsavel-unidade-externo").show();
    $("#responsavel_nome, #responsavel_unidade, #responsavel_email, #responsavel_telefone1, #responsavel_telefone2").val("");
    // Não forçamos o readonly aqui; isso será definido no handler abaixo.
}

/**
 * Se nenhum checkbox de responsável estiver marcado,
 * mostra somente o modo "interno" (select com usuários).
 */
function verificarResponsaveis() {
    let euSou = $("#eu_sou_o_responsavel").is(":checked");
    let externo = $("#responsavel_externo").is(":checked");
    if (!euSou && !externo) {
        toggleResponsavelInterno();
    }
}

/**
 * Atualiza os campos do aprovador conforme o estado do checkbox:
 * Se marcado, preenche com os dados do usuário logado (window.loggedUser)
 * e desabilita o select; caso contrário, habilita o select e limpa os campos.
 */
function toggleAprovador() {
    if ($("#eu_sou_o_aprovador").is(":checked")) {
        if (window.loggedUser && window.loggedUser.id_usuario) {
            // Usa os dados do usuário logado e desabilita o select
            $("#aprovador_nome").val(window.loggedUser.id_usuario).prop("disabled", true);
            $("#aprovador_unidade").val(window.loggedUser.id_unidade || "");
            $("#aprovador_email").val(window.loggedUser.email || "");
            $("#aprovador_telefone1").val(window.loggedUser.telefone1 || "");
            $("#aprovador_telefone2").val(window.loggedUser.telefone2 || "");
        } else {
            console.error("window.loggedUser não está definido ou não possui a propriedade 'id_usuario'.");
        }
    } else {
        // Habilita o select para escolha manual e limpa os campos
        $("#aprovador_nome").prop("disabled", false).val("");
        $("#aprovador_unidade").val("");
        $("#aprovador_email").val("");
        $("#aprovador_telefone1").val("");
        $("#aprovador_telefone2").val("");
    }
    updateAprovadorIDs();
}

$(document).ready(function () {
    // Popula os selects com os usuários já cadastrados
    if (Array.isArray(window.users)) {
        let options = '<option value="">Selecione...</option>';
        window.users.forEach(user => {
            options += `<option value="${user.id}">${user.nome}</option>`;
        });
        $("#aprovador_nome").html(options);
        $("#responsavel_nome").html(options);
    }
    
    // Inicializa os checkboxes de responsável como desmarcados e define o modo interno
    $("#eu_sou_o_responsavel, #responsavel_externo").prop("checked", false);
    toggleResponsavelInterno();
    verificarResponsaveis();

    // Lógica para os checkboxes de responsável
    $("#eu_sou_o_responsavel").on("change", function () {
        if ($(this).is(":checked")) {
            $("#responsavel_externo").prop("checked", false);
        }
        toggleResponsavelInterno();
        verificarResponsaveis();
    });
    
    $("#responsavel_externo").on("change", function () {
        if ($(this).is(":checked")) {
            $("#eu_sou_o_responsavel").prop("checked", false);
            // Se marcado, deixa os campos editáveis:
            $("#responsavel_unidade, #responsavel_email, #responsavel_telefone1, #responsavel_telefone2").prop("readonly", false);
        } else {
            // Se desmarcado, torna-os somente leitura:
            $("#responsavel_unidade, #responsavel_email, #responsavel_telefone1, #responsavel_telefone2").prop("readonly", true);
        }
        toggleResponsavelExterno();
        verificarResponsaveis();
    });

    // Lógica para o checkbox de aprovador
    $("#eu_sou_o_aprovador").on("change", function () {
        toggleAprovador();
    });
    
    // Evento para o select de aprovador: se o checkbox não estiver marcado,
    // ao selecionar um aprovador os demais campos serão preenchidos
    $("#aprovador_nome").on("change", function () {
        if (!$("#eu_sou_o_aprovador").is(":checked")) {
            let selectedId = $(this).val();
            fillAprovadorDetails(selectedId);
        }
    });
    
    // Botão para validar o formulário
    $("#btnValidateEvento").click(function () {
        validateEvento();
    });
    
    // Botão para cancelar
    $("#btnCancel").click(function () {
        let confirmacao = confirm("Tem certeza que deseja cancelar?");
        if (confirmacao) {
            window.location.href = baseUrl + 'agendamento/novo';
        }
    });
    
    // Botão para limpar o formulário
    $("#btnLimpar").click(function () {
        $("#formScheduling")[0].reset();
        resetFormEventoMessages();
        toggleResponsavelInterno();
        toggleAprovador();
    });
    
    // Validação em tempo real para elementos adicionados dinamicamente
    $(document).on("blur change", "input, select", function () {
        validarCampo($(this));
    });
    
    // Verifica o estado inicial do checkbox de aprovador ao carregar a página
    if ($("#eu_sou_o_aprovador").is(":checked")) {
        toggleAprovador();
    }
});

/**
 * Função responsável por validar um campo específico.
 */
function validarCampo(campo) {
    let idCampo = campo.attr('id');
    if (!idCampo) return false;
    idCampo = idCampo.replace(/\[|\]/g, "_");
    let errorDiv = $(`#divError-${idCampo}`);
    let hasError = false;
    if ($.trim(campo.val()) === "") {
        hasError = true;
        campo.addClass("is-invalid");
        if (errorDiv.length === 0) {
            campo.after(`<div id="divError-${idCampo}" class="invalid-feedback">Este campo é obrigatório.</div>`);
        } else {
            errorDiv.html("Este campo é obrigatório.").show();
        }
    } else {
        campo.removeClass("is-invalid");
        if (errorDiv.length > 0) {
            errorDiv.html("").hide();
        }
    }
    return hasError;
}

/**
 * Reseta as mensagens de erro e aviso do formulário.
 */
function resetFormEventoMessages() {
    $("[id^='msg']").html("").hide();
    $("[id^='divError-']").html("").hide();
    $("[id^='divNotice']").html("").hide();
    $("*").removeClass("is-invalid is-notice red");
}

/**
 * Função principal de validação antes do envio do formulário.
 */
function validateEvento() {
    $("#btnValidateEvento").prop("disabled", true);
    $('#divLoading').show();
    if (!dataValidation()) {
        $("#btnValidateEvento").prop("disabled", false);
        $('#divLoading').hide();
        return;
    }
    resetFormEventoMessages();
    let form = $("#formScheduling")[0];
    let formData = new FormData(form);
    $.ajax({
        type: 'POST',
        url: baseUrl + 'agendamento/salvar',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $("#divLoading").show();
            $("#msgSucessoGeral").hide();
            $("#msgErroGeral").hide();
            $("#msgAvisoGeral").hide();
        },
        success: function (response) {
            if (response.success) {
                $("#msgSucessoGeral").html(response.message).show();
                $("#btnLimpar").show();
                
                $("#btnValidateEvento, #btnCancel").prop("disabled", true);
                $("#ext_buttons").html(`
                    <hr>
                    <a class="btn btn-warning" href="${baseUrl}eventos/editar/${response.id_evento}">Editar Evento</a>
                    <a class="btn btn-primary" href="${baseUrl}eventos">Voltar à Listagem</a>
                `);
            } else {
                $("#msgErroGeral").html(response.message).show();
            }
        },
        error: function () {
            $("#msgErroGeral").html("Erro ao processar requisição.").show();
        },
        complete: function () {
            $('#divLoading').hide();
            $("#btnValidateEvento").prop("disabled", false);
        }
    });
}

/**
 * Faz a validação completa dos campos do formulário.
 */
function dataValidation() {
    let totalErros = 0;
    let totalEventoMissing = 0;
    let totalSolicitanteMissing = 0;
    let totalEspacosMissing = 0;
    resetFormEventoMessages();
    
    // Validação do Nome do Evento
    if ($.trim($("#titulo_evento").val()) === "") {
        totalErros++;
        totalEventoMissing++;
        $('#titulo_evento').addClass("is-invalid");
        $('#divError-titulo_evento').html("O campo NOME do evento é obrigatório.").show();
    }
    
    // Validação do Solicitante
    if ($.trim($("#solicitante_nome").val()) === "") {
        totalErros++;
        totalSolicitanteMissing++;
        $('#solicitante_nome').addClass("is-invalid");
        $('#divError-solicitante_nome').html("O campo NOME do solicitante é obrigatório.").show();
    }
    if ($.trim($("#solicitante_unidade").val()) === "") {
        totalErros++;
        totalSolicitanteMissing++;
        $('#solicitante_unidade').addClass("is-invalid");
        $('#divError-solicitante_unidade').html("O campo UNIDADE do solicitante é obrigatório.").show();
    }
    
    // Validação do Responsável – verifica qual checkbox está marcado
    if ($("#responsavel_externo").is(":checked")) {
        if ($.trim($("#responsavel_nome_externo").val()) === "") {
            totalErros++;
            totalSolicitanteMissing++;
            $('#responsavel_nome_externo').addClass("is-invalid");
            $('#divError-responsavel_nome_externo').html("O campo NOME do responsável é obrigatório.").show();
        }
        if ($.trim($("#responsavel_unidade_externo").val()) === "") {
            totalErros++;
            totalSolicitanteMissing++;
            $('#responsavel_unidade_externo').addClass("is-invalid");
            $('#divError-responsavel_unidade_externo').html("O campo UNIDADE do responsável é obrigatório.").show();
        }
        if ($.trim($("#responsavel_email").val()) === "") {
            totalErros++;
            totalSolicitanteMissing++;
            $('#responsavel_email').addClass("is-invalid");
            $('#divError-responsavel_email').html("O campo E-MAIL do responsável é obrigatório.").show();
        }
        if ($.trim($("#responsavel_telefone1").val()) === "") {
            totalErros++;
            totalSolicitanteMissing++;
            $('#responsavel_telefone1').addClass("is-invalid");
            $('#divError-responsavel_telefone1').html("O campo TELEFONE 1 do responsável é obrigatório.").show();
        }
    } else if ($("#eu_sou_o_responsavel").is(":checked")) {
        if ($.trim($("#responsavel_nome").val()) === "") {
            totalErros++;
            totalSolicitanteMissing++;
            $('#responsavel_nome').addClass("is-invalid");
            $('#divError-responsavel_nome').html("O campo NOME do responsável é obrigatório.").show();
        }
        if ($.trim($("#responsavel_unidade").val()) === "") {
            totalErros++;
            totalSolicitanteMissing++;
            $('#responsavel_unidade').addClass("is-invalid");
            $('#divError-responsavel_unidade').html("O campo UNIDADE do responsável é obrigatório.").show();
        }
        if ($.trim($("#responsavel_email").val()) === "") {
            totalErros++;
            totalSolicitanteMissing++;
            $('#responsavel_email').addClass("is-invalid");
            $('#divError-responsavel_email').html("O campo E-MAIL do responsável é obrigatório.").show();
        }
        if ($.trim($("#responsavel_telefone1").val()) === "") {
            totalErros++;
            totalSolicitanteMissing++;
            $('#responsavel_telefone1').addClass("is-invalid");
            $('#divError-responsavel_telefone1').html("O campo TELEFONE 1 do responsável é obrigatório.").show();
        }
    } else {
        if ($.trim($("#responsavel_nome").val()) === "") {
            totalErros += 4;
            totalSolicitanteMissing += 4;
            $('#responsavel_nome').addClass("is-invalid");
            $('#divError-responsavel_nome').html("O campo NOME do responsável é obrigatório.").show();
            $('#responsavel_unidade').addClass("is-invalid");
            $('#divError-responsavel_unidade').html("O campo UNIDADE do responsável é obrigatório.").show();
            $('#responsavel_email').addClass("is-invalid");
            $('#divError-responsavel_email').html("O campo E-MAIL do responsável é obrigatório.").show();
            $('#responsavel_telefone1').addClass("is-invalid");
            $('#divError-responsavel_telefone1').html("O campo TELEFONE 1 do responsável é obrigatório.").show();
        }
    }

    if ($.trim($("#aprovador_nome").val()) === "") {
        totalErros++;
        totalSolicitanteMissing++;
        $('#aprovador_nome').addClass("is-invalid");
        $('#divError-aprovador_nome').html("O campo NOME do aprovador é obrigatório.").show();
    }
    if ($.trim($("#aprovador_unidade").val()) === "") {
        totalErros++;
        totalSolicitanteMissing++;
        $('#aprovador_unidade').addClass("is-invalid");
        $('#divError-aprovador_unidade').html("O campo UNIDADE do aprovador é obrigatório.").show();
    }
    
    // Validação dinâmica para campos em array (Espaços e horários)
    $("select[name='espaco[]'], input[name='data_inicio[]'], input[name='hora_inicio[]'], input[name='hora_fim[]']").each(function (index) {
        let newId = $(this).attr('name').replace(/\[|\]/g, "_") + "_" + (index + 1);
        $(this).attr('id', newId);
        if (validarCampo($(this))) {
            totalErros++;
            totalEspacosMissing++;
        }
    });
    
    let espacoSelecionado = false;
    $(".espaco-select").each(function () {
        if ($(this).val() !== "") {
            espacoSelecionado = true;
        }
    });
    if (!espacoSelecionado) {
    totalErros++;
    // Adiciona classe de erro ao container de espaços
    $("#espacos-container").addClass("is-invalid");
    // Se não existir uma div de erro, cria uma; caso contrário, atualiza o conteúdo
    if ($("#divError-espacos-container").length === 0) {
        $("#espacos-container").append('<div id="divError-espacos-container" class="invalid-feedback">Selecione pelo menos um espaço para o evento.</div>');
    } else {
        $("#divError-espacos-container").html("Selecione pelo menos um espaço para o evento.").show();
    }
}
    
    if (!$("#flg_confirmacao_envio").is(":checked")) {
        totalErros++;
        $('#divError-flg_confirmacao_envio').html("É necessário concordar com os termos.").show();
    }
    
    atualizarAbaComErros($('#evento-tab'), totalEventoMissing);
    atualizarAbaComErros($('#solicitante-tab'), totalSolicitanteMissing);
    atualizarAbaComErros($('#espacos-tab'), totalEspacosMissing);
    if (totalErros > 0) {
        $("#msgErroGeral").html("Verifique os erros informados antes de tentar salvar novamente.").show();
    }
    return totalErros === 0;
}

/**
 * Destaca uma aba com erros e mostra o número de campos pendentes.
 */
function atualizarAbaComErros(aba, totalErros) {
    let abaTextoOriginal = aba.clone().children().remove().end().text().trim();
    aba.find(".badge").remove();
    if (totalErros > 0) {
        aba.css({ 'border-bottom-color': 'red', 'border-width': '2px' });
        aba.html(`${abaTextoOriginal} <span class="badge bg-danger">${totalErros}</span>`);
    } else {
        aba.removeAttr('style');
        aba.html(abaTextoOriginal);
    }
}
