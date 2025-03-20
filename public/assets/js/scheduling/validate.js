$(document).ready(function () {
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
    });

    // Validação em tempo real para elementos adicionados dinamicamente
    $(document).on("blur change", "input, select", function () {
        validarCampo($(this));
    });
});

/**
 * Função responsável por validar um campo específico.
 * Retorna true se houver erro, ou false se estiver ok.
 *
 * @param {jQuery} campo O campo a ser validado.
 * @returns {boolean} Indica se há erro no campo.
 */
function validarCampo(campo) {
    let idCampo = campo.attr('id');
    // Se não houver ID, não valida
    if (!idCampo) return false;

    // Corrige IDs de arrays, por exemplo: espaco[] -> espaco_
    idCampo = idCampo.replace(/\[|\]/g, "_");

    // Localiza (ou cria) a div de erro
    let errorDiv = $(`#divError-${idCampo}`);
    let hasError = false;

    // Verifica se o campo está vazio
    if ($.trim(campo.val()) === "") {
        hasError = true;
        campo.addClass("is-invalid");

        // Se a div de erro não existir, cria
        if (errorDiv.length === 0) {
            campo.after(`<div id="divError-${idCampo}" class="invalid-feedback">Este campo é obrigatório.</div>`);
        } else {
            // Se existir, apenas atualiza a mensagem
            errorDiv.html("Este campo é obrigatório.").show();
        }
    } else {
        // Se não há erro, remove classe e esconde a div
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
    $("[id^='divError-']").html("").hide();  // ou .remove() se quiser remover do DOM
    $("[id^='divNotice']").html("").hide();
    $("*").removeClass("is-invalid is-notice red");
}

/**
 * Função principal de validação antes do envio do formulário.
 */
function validateEvento() {
    $("#btnValidateEvento").prop("disabled", true);
    $('#divLoading').show();

    // Se a validação falhar, reabilita o botão e esconde o loading
    if (!dataValidation()) {
        $("#btnValidateEvento").prop("disabled", false);
        $('#divLoading').hide();
        return;
    }

    // Se passou na validação, limpa mensagens de erro e envia
    resetFormEventoMessages();

    let form = $("#formScheduling")[0];
    let formData = new FormData(form);

    $.ajax({
        type: 'POST',
        url: baseUrl + 'eventos/salvar',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $("#divLoading").show();
        },
        success: function (response) {
            if (response.success) {
                $("#msgSucessoGeral").html("Evento cadastrado com sucesso!").show();
                $("#btnLimpar").show();
                $("#formScheduling :input").prop("readonly", true);
                $("#formScheduling select").prop("disabled", true);
                $("#btnValidateEvento, #btnCancel").prop("disabled", true);
                $("#ext_buttons").html(`
                    <hr>
                    <a class="btn btn-warning" href="${baseUrl}eventos/editar/${response.id_evento}">Editar Evento</a>
                    <a class="btn btn-primary" href="${baseUrl}eventos">Voltar à Listagem</a>
                `);
            } else {
                $("#msgErroGeral").html("Erro ao cadastrar evento. Tente novamente.").show();
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
 * Faz a validação completa dos campos do formulário,
 * contabilizando erros e destacando abas, se necessário.
 * @returns {boolean} true se estiver tudo válido, false caso contrário.
 */
function dataValidation() {
    let totalErros = 0;
    let totalEventoMissing = 0;
    let totalSolicitanteMissing = 0;
    let totalEspacosMissing = 0;

    // Limpa mensagens de erro anteriores
    resetFormEventoMessages();

    // ------------------------------
    // Validações manuais de campos específicos (Ex.: Nome do Evento)
    // ------------------------------
    if ($.trim($("#titulo_evento").val()) === "") {
        totalErros++;
        totalEventoMissing++;
        $('#titulo_evento').addClass("is-invalid");
        $('#divError-titulo_evento').html("O campo NOME do evento é obrigatório.").show();
    }

    // Exemplo de validações do Solicitante
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

    // Responsável
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

    // ------------------------------
    // Validação dinâmica para campos em array (espaco[], data_inicio[], etc.)
    // ------------------------------
    $("select[name='espaco[]'], input[name='data_inicio[]'], input[name='hora_inicio[]'], input[name='hora_fim[]']").each(function (index) {
        // Ajusta ID do campo
        let newId = $(this).attr('name').replace(/\[|\]/g, "_") + "_" + (index + 1);
        $(this).attr('id', newId);

        // Se validarCampo() retornar true, houve erro
        if (validarCampo($(this))) {
            totalErros++;
            totalEspacosMissing++;
        }
    });

    // Verifica se ao menos um espaço foi selecionado
    let espacoSelecionado = false;
    $(".espaco-select").each(function () {
        if ($(this).val() !== "") {
            espacoSelecionado = true;
        }
    });
    if (!espacoSelecionado) {
        totalErros++;
        $('#espaco').addClass("is-invalid");
        $('#divError-espaco').html("Selecione pelo menos um espaço para o evento.").show();
    }

    // Validação do check de confirmação
    if (!$("#flg_confirmacao_envio").is(":checked")) {
        totalErros++;
        $('#divError-flg_confirmacao_envio').html("É necessário concordar com os termos.").show();
    }

    // Destaca abas com erro (caso queira mostrar contadores na aba)
    atualizarAbaComErros($('#evento-tab'), totalEventoMissing);
    atualizarAbaComErros($('#solicitante-tab'), totalSolicitanteMissing);
    atualizarAbaComErros($('#espacos-tab'), totalEspacosMissing);

    // Se houve algum erro, exibe mensagem geral
    if (totalErros > 0) {
        $("#msgErroGeral").html("Verifique os erros informados antes de tentar salvar novamente.").show();
    }

    // Se totalErros for 0, está tudo ok
    return totalErros === 0;
}

/**
 * Destaca uma aba com erros e mostra o número de campos pendentes.
 * @param {jQuery} aba Elemento da aba (ex.: $('#evento-tab'))
 * @param {number} totalErros Quantidade de erros na aba
 */
function atualizarAbaComErros(aba, totalErros) {
    let abaTextoOriginal = aba.clone().children().remove().end().text().trim();
    // Remove contagem anterior para evitar duplicação
    aba.find(".badge").remove();

    if (totalErros > 0) {
        aba.css({ 'border-bottom-color': 'red', 'border-width': '2px' });
        aba.html(`${abaTextoOriginal} <span class="badge bg-danger">${totalErros}</span>`);
    } else {
        aba.removeAttr('style');
        aba.html(abaTextoOriginal);
    }
}
