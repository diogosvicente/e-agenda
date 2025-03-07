$(document).ready(function () {

    // Botão "Enviar" da primeira etapa (senha atual)
    $("#btnValidatePasswordSend").click(function () {
        changePasswordSend();
    });

    // Botão "Cancelar"
    $("#btnCancel").click(function () {
        window.location.href = $("[name=baseUrl]").val();
    });

    // Exibir/ocultar senha atual
    $("#passwordEye").click(function () {
        exibeSenha($("#passwordAtual"), $(this));
    });

});

/**
 * Envia a senha atual (Etapa 1) via AJAX para /alterar_a_senha/validar
 */
function changePasswordSend() {
    $("#btnValidatePasswordSend").prop("disabled", true);
    $('#divLoading').show();

    // Validação local
    if (!dataValidation()) {
        $("#btnValidatePasswordSend").prop("disabled", false);
        $('#divLoading').hide();
        return;
    }

    // Serializa os campos do form e adiciona step=1
    var params = $("#formChangePassword").serialize();
    params += '&step=1';

    // Limpa as mensagens e envia via AJAX
    $("[id^=msg]").html("").hide();
    $.ajax({
        type: 'post',
        url: $("[name=baseUrl]").val() + 'alterar_a_senha/validar',
        data: params,
        dataType: 'json',
        beforeSend: function () {
            $("#divLoading").show();
        },
        success: function (retorno) {
            // Atualiza o token CSRF
            $("[name='csrf_test_name']").val(retorno.csrf_hash);
        
            if (retorno.status == "SUCCESS") {
                // Remove erros anteriores
                $("[id^='divError-']").html("").hide();
                $("input").removeClass("is-invalid");
        
                // Exibe a etapa 2
                $("#passo2Fieldset").show();
        
                // Opcional: desabilitar a etapa 1
                $("#passo1Fieldset :input").prop("disabled", true);
                $("#changePasswordButtons").hide();
        
                // Faz scroll até a etapa 2
                document.querySelector('#passo2Fieldset').scrollIntoView({
                    behavior: 'smooth'
                });
            } else if (retorno.status.substr(0, 5) == "ERROR") {
                $("[id^='divError-']").html("").hide();
                $("input").removeClass("is-invalid");

                // Exibe erros de campo
                for (x in retorno.errors) {
                    id = retorno.errors[x].id;
                    message = retorno.errors[x].message;
                    $('#' + id).addClass("is-invalid");
                    $("#divError-" + id).html(message).show();
                }

                $("[id^=msg]").html("").hide();
                $("#msgErroGeral").html(retorno.message).show();
                $('html, body').animate({scrollTop: $(document).height()});

            // Se houver avisos
            } else if (retorno.status.substr(0, 6) == "NOTICE") {
                for (x in retorno.errors) {
                    id = retorno.errors[x].id;
                    message = retorno.errors[x].message;
                    $("#divNotice-" + id).html(message).show();
                }
                $("[id^=msg]").html("").hide();
                $("#msgAvisoGeral").html(retorno.message).show();
                $('html, body').animate({scrollTop: $(document).height()});

            // Caso não previsto
            } else {
                $("[id^=msg]").html("").hide();
                $("#msgErroGeral").html(debug(retorno)).show();
                $('html, body').animate({scrollTop: $(document).height()});
            }
        },
        error: function (xhr) {
            // Erro de conexão ou servidor
            $("#divLoading").hide();
            $("[id^=msg]").html("").hide();
            $("#msgErroGeral").html("Código do erro: " + xhr.status + "<br />Erro: " + xhr.statusText).fadeIn(300);
            $('html, body').animate({scrollTop: $(document).height()});
        },
        complete: function () {
            // Reabilita o botão e esconde o loading
            $('#divLoading').hide();
            $("#btnValidatePasswordSend").prop("disabled", false);
        }
    });
}

/**
 * Validação local do campo "Senha atual"
 */
function dataValidation() {
    var totalErros = 0;
    $("[id^='divError-']").html("").hide();
    $("input").removeClass("is-invalid");

    // Verifica se o campo passwordAtual está vazio
    if ($.trim($('#passwordAtual').val()) === "") {
        totalErros++;
        $('#passwordAtual').addClass("is-invalid");
        var labelPasswd = $('label[for="passwordAtual"]').text().replace(/\*/g,'').toUpperCase();
        $('#divError-passwordAtual').html('O campo '+ labelPasswd +' é obrigatório.').show();
    }

    if (totalErros > 0) {
        setFocusError();
    }
    return (totalErros === 0);
}
