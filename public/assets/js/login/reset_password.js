$(document).ready(function() {
    // === INÍCIO: CÓDIGO EXISTENTE (não remover) ===
    $("#toggleSenha").click(function() {
        let input = $("#senha");
        let icon  = $(this).find("i");
        if (input.attr("type") === "password") {
            input.attr("type", "text");
            icon.removeClass("fa-eye").addClass("fa-eye-slash");
        } else {
            input.attr("type", "password");
            icon.removeClass("fa-eye-slash").addClass("fa-eye");
        }
    });

    $("#toggleSenha2").click(function() {
        let input = $("#senha2");
        let icon  = $(this).find("i");
        if (input.attr("type") === "password") {
            input.attr("type", "text");
            icon.removeClass("fa-eye").addClass("fa-eye-slash");
        } else {
            input.attr("type", "password");
            icon.removeClass("fa-eye-slash").addClass("fa-eye");
        }
    });

    // Monitorar digitação no campo #senha para validar requisitos
    $("#senha").on("keyup", function(){
        let pwd = $(this).val();

        // Requisitos
        let minLength    = pwd.length >= 8;
        let hasUpperCase = /[A-Z]/.test(pwd);
        let hasLowerCase = /[a-z]/.test(pwd);
        let hasNumber    = /\d/.test(pwd);
        let hasSpecial   = /[\W_]/.test(pwd); // caractere especial

        // Atualiza cada requisito
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
    // === FIM: CÓDIGO EXISTENTE (não remover) ===

    // === INÍCIO: NOVA LÓGICA PARA RESETAR SENHA (AJAX) ===
    // Exemplo: se tiver um botão #btnResetPassword no form
    $("#btnResetPassword").click(function(e){
        e.preventDefault();
        resetarSenhaAjax();
    });
});

// Função que envia os dados via AJAX para /esqueceu_sua_senha/resetar
function resetarSenhaAjax() {
    $("#btnResetPassword").prop("disabled", true);
    $('#divLoading').show();

    // Serializa o formulário (por exemplo, #formResetPassword)
    let params = $("#formResetPassword").serialize();

    $.ajax({
        type: 'post',
        url: $("[name=baseUrl]").val() + 'esqueceu_sua_senha/resetar',
        data: params,
        dataType: 'json',
        beforeSend: function () {
            $("#divLoading").show();
            $("[id^='divError-']").html("").hide();
            $("[id^='msg']").html("").hide();
            $("input").removeClass("is-invalid");
        },
        success: function (retorno) {
            // Atualiza CSRF
            if (retorno.csrf_hash) {
                $("[name='csrf_test_name']").val(retorno.csrf_hash);
            }

            if (retorno.status === "SUCCESS") {
                // Exibe mensagem de sucesso
                $("#msgSucessoGeral").html(retorno.message).show();
                document.querySelector('#msgSucessoGeral').scrollIntoView({
                    behavior: 'smooth'
                });

                // Desabilita todos os inputs e botões do form
                $("#formResetPassword :input").prop("disabled", true);

                // Adiciona botão para ir à tela de login, se não existir
                if ($("#btnGoLogin").length === 0) {
                    $("#formResetPassword").append(
                        '<div class="row mt-3">' +
                            '<div class="col-12 text-center">' +
                                '<button id="btnGoLogin" type="button" class="btn btn-success">' +
                                    'Ir para Login' +
                                '</button>' +
                            '</div>' +
                        '</div>'
                    );
                    $("#btnGoLogin").on("click", function() {
                        window.location.href = $("[name=baseUrl]").val() + 'login';
                    });
                }

            } else if (retorno.status.substr(0, 5) === "ERROR") {
                // Exibe erros de campo
                if (retorno.errors) {
                    for (let x in retorno.errors) {
                        let id = retorno.errors[x].id;
                        let message = retorno.errors[x].message;
                        $('#' + id).addClass("is-invalid");
                        $("#divError-" + id).html(message).show();
                    }
                }
                $("#msgErroGeral").html(retorno.message).show();
                document.querySelector('#msgErroGeral').scrollIntoView({
                    behavior: 'smooth'
                });

            } else {
                // Demais casos
                $("#msgErroGeral").html('Retorno inesperado do servidor.').show();
                document.querySelector('#msgErroGeral').scrollIntoView({
                    behavior: 'smooth'
                });
            }
        },
        error: function(xhr) {
            $("#msgErroGeral").html("Código do erro: " + xhr.status + "<br />Erro: " + xhr.statusText).fadeIn(300);
            document.querySelector('#msgErroGeral').scrollIntoView({
                behavior: 'smooth'
            });
        },
        complete: function() {
            $('#divLoading').hide();
            $("#btnResetPassword").prop("disabled", false);
        }
    });
}
// === FIM: NOVA LÓGICA PARA RESETAR SENHA (AJAX) ===
