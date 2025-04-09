$(document).ready(function () {

    $("#btnValidateLogin").click(function () {
        validateLogin();
    });

    $("#btnForgotPassword").click(function () {
        window.location.href = $("[name=baseUrl]").val() + 'esqueceu_sua_senha';
    });

    $("#btnFirstAccess").click(function () {
        window.location.href = $("[name=baseUrl]").val() + 'primeiro_acesso';
    });

    $("#passwordEye").click(function () {
        console.log("cliquei!");
        exibeSenha($("#senha"), $(this));
    });

});


document.addEventListener('keypress', (event) => {
    var tecla = event.which;
    if (tecla == 13) {
        // event.preventDefault();
        validateLogin();
    }
});

function validateLogin() {
    $("#btnValidateLogin").prop("disabled", true);
    $('#divLoading').show();

    if (!dataValidation()) {
        $("#btnValidateLogin").prop("disabled", false);
        $('#divLoading').hide();
        return;
    }

    var params = $("#formLogin").serialize();

    $("[id^=msg]").html("").hide();
    $.ajax({
        type: 'post',
        url: $("[name=baseUrl]").val() + 'login/validar',
        data: params,
        dataType: 'json',
        beforeSend: function () {
            $("#divLoading").show();
            $("[id^='divError-']").html("").hide();
            $("[id^='divNotice-']").html("").hide();
            $("[id^='msg']").html("").hide();
            $("input").removeClass("is-invalid");
        },
        success: function (retorno) {
            $("[name='csrf_test_name']").val(retorno.csrf_hash);
            // $("#msgErroGeral").html((retorno.status)).show();

            var id, message, x;
            if (retorno.status == "SUCCESS") {
                // window.location.href = $("[name=baseUrl]").val() + '/inicio';
                $("[id^='divError-']").html("").hide();
                $("input").removeClass("is-invalid");
                $("#msgSucessoGeral").html(retorno.message).show();
                // $("#msgAvisoGeral").html('Você será direcionado para a página inicial em 3 segundos.').show();
                setTimeout(function () {
                    window.location.href = $("[name=baseUrl]").val() + '/calendario';
                }, 1000);

            } else if (retorno.status.substr(0, 5) == "ERROR") {
                $("[id^='divError-']").html("").hide();
                $("input").removeClass("is-invalid");
                for (x in retorno.errors) {
                    id = retorno.errors[x].id;
                    message = retorno.errors[x].message;
                    $('#' + id).addClass("is-invalid");
                    $("#divError-" + id).html(message).show();
                }
                $("[id^=msg]").html("").hide();
                $("#msgErroGeral").html(retorno.message).show();
                $('html, body').animate({scrollTop: $(document).height()});
            } else if (retorno.status.substr(0, 6) == "NOTICE") {
                for (x in retorno.errors) {
                    id = retorno.errors[x].id;
                    message = retorno.errors[x].message;
                    $("#divNotice-" + id).html(message).show();
                }
                $("[id^=msg]").html("").hide();
                $("#msgAvisoGeral").html(retorno.message).show();
                $('html, body').animate({scrollTop: $(document).height()});
            } else {
                $("[id^=msg]").html("").hide();
                $("#msgErroGeral").html(debug(retorno)).show();
                $('html, body').animate({scrollTop: $(document).height()});
            }
        },
        error: function (xhr) { //erro
            $("#divLoading").hide();
            $("[id^=msg]").html("").hide();
            $("#msgErroGeral").html("Código do erro: " + xhr.status + "<br />Erro: " + xhr.statusText).fadeIn(300);
            $('html, body').animate({scrollTop: $(document).height()});
        },
        complete: function () {
            $('#divLoading').hide();
            $("#btnValidateLogin").prop("disabled", false);
        }
    });
} /* fim function validateLogin() */


function dataValidation() {
    var totalErros = 0;
    $("[id^='divError-']").html("").hide();
    $("input").removeClass("is-invalid");
    if (jQuery.trim($('#login').val()) == "") {
        totalErros++;
        $('#login').addClass("is-invalid");
        var labelLogin = $('label[for="login"]').text().replace(/\*/g, '').toUpperCase();
        $('#divError-login').html('O campo ' + labelLogin + ' é obrigatório.').show();
    }
    if (jQuery.trim($("#password").val()) == "") {
        totalErros++;
        $('#password').addClass("is-invalid");
        var labelPasswd = $('label[for="password"]').text().replace(/\*/g, '').toUpperCase();
        $('#divError-password').html('O campo ' + labelPasswd + ' é obrigatório.').show();
    }
    if (totalErros > 0) {
        setFocusError();
    }
    return !(totalErros > 0);
}
