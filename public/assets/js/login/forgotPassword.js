$(document).ready(function () {

    $("#btnValidatePasswordSend").click(function () {
        validatePasswordSend();
    });

    $("#btnCancel").click(function () {
        window.location.href = $("[name=baseUrl]").val() + '/';
    });


});

    function validatePasswordSend() {
        $("#btnValidatePasswordSend").prop("disabled", true);
        $('#divLoading').show();

        if (!dataValidation()) {
            $("#btnValidatePasswordSend").prop("disabled", false);
            $('#divLoading').hide();
            return;
        }

        var params = $("#formForgotPassword").serialize();

        $("[id^=msg]").html("").hide();
        $.ajax({
            type: 'post',
            url: $("[name=baseUrl]").val() + 'esqueceu_sua_senha/validar',
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
                    $("[id^='divError-']").html("").hide();
                    $("input").removeClass("is-invalid");
                    $('#btnCancel').text('Fechar');
                    $("#msgSucessoGeral").html(retorno.message).show();
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
                $("#btnValidatePasswordSend").prop("disabled", false);
            }
        });
    } /* fim function validatePasswordSend() */


    function dataValidation() {
        var totalErros = 0;
        $("[id^='divError-']").html("").hide();
        $("input").removeClass("is-invalid");
        if (jQuery.trim($('#identificador').val()) == "") {
            totalErros++;
            $('#identificador').addClass("is-invalid");
            var labelLogin = $('label[for="identificador"]').text().replace(/\*/g,'').toUpperCase();
            $('#divError-identificador').html('O campo '+labelLogin+' é obrigatório.').show();
        }

        if (totalErros > 0) {
            setFocusError();
        }

        return !(totalErros > 0);
    }

