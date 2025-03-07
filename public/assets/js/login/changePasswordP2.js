$(document).ready(function () {

    // Botão "Concluir" (nova senha)
    $("#btnChangePassword").click(function () {
        changePasswordSendP2();
    });

    // Botão "Cancelar"
    $("#btnCancel").click(function () {
        window.location.href = $("[name=baseUrl]").val();
    });

    // Exibir/ocultar campo #password
    $("#passwordEye1").click(function () {
        exibeSenha($("#password"), $(this));
    });

    // Exibir/ocultar campo #password2
    $("#passwordEye2").click(function () {
        exibeSenha($("#password2"), $(this));
    });

    // Monitorar digitação no campo #password para validar requisitos em tempo real
    $("#password").on("keyup", function(){
        let pwd = $(this).val();

        // Requisitos:
        let minLength    = pwd.length >= 8;    // >= 8 chars
        let hasUpperCase = /[A-Z]/.test(pwd); // Pelo menos 1 maiúscula
        let hasLowerCase = /[a-z]/.test(pwd); // Pelo menos 1 minúscula
        let hasNumber    = /\d/.test(pwd);    // Pelo menos 1 número
        let hasSpecial   = /[\W_]/.test(pwd); // Pelo menos 1 caractere especial

        // Atualiza cada requisito (verde se ok, vermelho se não):
        if (minLength) {
            $("#reqLength").removeClass("text-danger").addClass("text-success");
        } else {
            $("#reqLength").removeClass("text-success").addClass("text-danger");
        }

        if (hasUpperCase) {
            $("#reqUpper").removeClass("text-danger").addClass("text-success");
        } else {
            $("#reqUpper").removeClass("text-success").addClass("text-danger");
        }

        if (hasLowerCase) {
            $("#reqLower").removeClass("text-danger").addClass("text-success");
        } else {
            $("#reqLower").removeClass("text-success").addClass("text-danger");
        }

        if (hasNumber) {
            $("#reqNumber").removeClass("text-danger").addClass("text-success");
        } else {
            $("#reqNumber").removeClass("text-success").addClass("text-danger");
        }

        if (hasSpecial) {
            $("#reqSpecial").removeClass("text-danger").addClass("text-success");
        } else {
            $("#reqSpecial").removeClass("text-success").addClass("text-danger");
        }
    });

});

/**
 * Envia a nova senha (Etapa 2) via AJAX para /alterar_a_senha/validar
 */
function changePasswordSendP2() {
    $("#btnChangePassword").prop("disabled", true);
    $('#divLoading').show();

    // Validação local dos campos de nova senha
    if (!dataValidation()) {
        $("#btnChangePassword").prop("disabled", false);
        $('#divLoading').hide();
        return;
    }

    // Serializa e adiciona step=2
    var params = $("#formChangePassword").serialize();
    params += '&step=2';

    // Limpa mensagens
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
            $("[name='csrf_test_name']").val(retorno.csrf_hash);

            var id, message, x;
            if (retorno.status === "SUCCESS") {
                // Remove erros anteriores
                $("[id^='divError-']").html("").hide();
                $("input").removeClass("is-invalid");

                // Exibe mensagem de sucesso
                $("#msgSucessoGeral").html(retorno.message).show();
                document.querySelector('#msgSucessoGeral').scrollIntoView({
                    behavior: 'smooth'
                });

                // Desativar TODOS os campos e botões:
                // 1) Senha atual (etapa 1)
                $("#passwordAtual").prop("readonly", true).prop("disabled", true);
                
                // 2) Nova senha e confirmação
                $("#password, #password2").prop("readonly", true).prop("disabled", true);
                
                // 3) Botões "Enviar" e "Concluir"
                $("#btnValidatePasswordSend").prop("disabled", true);
                $("#btnChangePassword").prop("disabled", true);
                $("#btnChangePassword").hide();

                if ($("#btnGoHome").length === 0) {
                    let btnHome = $('<button id="btnGoHome" type="button" class="btn btn-success mt-3">Ir para a Página Inicial</button>');
                    btnHome.on("click", function() {
                        window.location.href = $("[name=baseUrl]").val();
                    });
                    // Anexa diretamente no fieldset #passo2Fieldset
                    $("#passo2Fieldset").append(btnHome);
                }
            } else if (retorno.status.substr(0, 5) === "ERROR") {
                // Erros de campo
                $("[id^='divError-']").html("").hide();
                $("input").removeClass("is-invalid");
                for (x in retorno.errors) {
                    id = retorno.errors[x].id;
                    message = retorno.errors[x].message;
                    $('#' + id).addClass("is-invalid");
                    $("#divError-" + id).html(message).show();
                }

                // Exibe mensagem "Verifique os dados informados." ou outra
                $("#msgErroGeral").html(retorno.message).show();
                $('html, body').animate({scrollTop: $(document).height()});

            } else if (retorno.status.substr(0, 6) === "NOTICE") {
                // Avisos
                for (x in retorno.errors) {
                    id = retorno.errors[x].id;
                    message = retorno.errors[x].message;
                    $("#divNotice-" + id).html(message).show();
                }
                $("#msgAvisoGeral").html(retorno.message).show();
                $('html, body').animate({scrollTop: $(document).height()});

            } else {
                // Caso não previsto
                $("#msgErroGeral").html(debug(retorno)).show();
                $('html, body').animate({scrollTop: $(document).height()});
            }
        },
        error: function (xhr) {
            $("#divLoading").hide();
            $("[id^=msg]").html("").hide();
            $("#msgErroGeral").html("Código do erro: " + xhr.status + "<br />Erro: " + xhr.statusText).fadeIn(300);
            $('html, body').animate({scrollTop: $(document).height()});
        },
        complete: function () {
            $('#divLoading').hide();
            $("#btnChangePassword").prop("disabled", false);
        }
    });
}

/**
 * Valida localmente os campos de nova senha (#password, #password2).
 * Se a senha tiver menos de 8 caracteres, exibe "Verifique os dados informados." (status=ERROR) no back-end.
 */
function dataValidation() {
    var totalErros = 0;
    $("[id^='divError-']").html("").hide();
    $("input").removeClass("is-invalid");

    // Verifica o campo #password
    let pwd = $.trim($('#password').val());
    if (pwd === "") {
        totalErros++;
        $('#password').addClass("is-invalid");
        var labelPasswd1 = $('label[for="password"]').text().replace(/\*/g,'').toUpperCase();
        $('#divError-password').html('O campo '+ labelPasswd1 +' é obrigatório.').show();
    } else if (pwd.length < 8) {
        totalErros++;
        $('#password').addClass("is-invalid");
        $('#divError-password').html('A senha deve ter pelo menos 8 caracteres.').show();
    }

    // Verifica o campo #password2
    let pwd2 = $.trim($('#password2').val());
    if (pwd2 === "") {
        totalErros++;
        $('#password2').addClass("is-invalid");
        $('#divError-password2').html("Digite a senha novamente.").show();
    } else if (pwd2.length < 8) {
        totalErros++;
        $('#password2').addClass("is-invalid");
        $('#divError-password2').html('A senha deve ter pelo menos 8 caracteres.').show();
    }

    // Verifica se #password e #password2 são iguais
    if (pwd !== pwd2) {
        totalErros++;
        $('#password2').addClass("is-invalid");
        $('#divError-password2').html("As senhas não coincidem.").show();
    }

    // Se tiver erros, foca no primeiro
    if (totalErros > 0) {
        setFocusError();
    }
    return !(totalErros > 0);
}
