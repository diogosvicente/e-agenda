$(document).ready(function () {
    let passo = $("fieldset").prop('id');
    switch (passo) {
        case "passo1":
            prepararPasso1();
            break;
        case "passo2":
            prepararPasso2();
            break;
    }
});

/**
 * Configurações e eventos do Passo 1.
 */
function prepararPasso1() {
    console.log("Preparar Passo 1");

    $('input').removeClass("is-invalid");

    // Quando clicar no botão "Continuar"
    $('#btnContinue1').click(function (e) {
        e.preventDefault();
        firstAccessPasso1();
    });
    
    // Botão "Cancelar"
    $('#btnCancel').click(function () {
        window.location.href = $("[name=baseUrl]").val() + '/';
    });
}

/**
 * Configurações e eventos do Passo 2.
 */
function prepararPasso2() {
    console.log("Preparar Passo 2");

    // Quando clicar no botão "Continuar"
    $('#btnContinue').click(function (e) {
        e.preventDefault();
        firstAccessPasso2();
    });

    // Botão "Cancelar"
    $('#btnCancel').click(function () {
        window.location.href = $("[name=baseUrl]").val() + '/';
    });
}

/**
 * Envia os dados do Passo 1 para validação via AJAX.
 */
function firstAccessPasso1() {

    // Valida campos localmente antes de enviar
    if (!validarDadosPasso1()) {
        $('#divLoading').hide();
        return;
    }

    let params = $("#formFirstAccess").serialize();

    $.ajax({
        type: 'post',
        url: $("[name=baseUrl]").val() + 'primeiro_acesso/validar',
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
            let id, message, x;

            // Se deu certo
            if (retorno.status == "SUCCESS") {
                $("[id^='divError-']").html("").hide();
                $("input").removeClass("is-invalid");

                $("[id^=msg]").html("").hide();
                $("#msgSucessoGeral").html(retorno.message).show();
                document.querySelector('#msgSucessoGeral').scrollIntoView({
                    behavior: 'smooth'
                });

            // Se deu erro (incluindo token expirado, cadastro completo, etc.)
            } else if (retorno.status.substr(0, 5) == "ERROR") {

                $("[id^='divError-']").html("").hide();
                $("input").removeClass("is-invalid");

                // Exibe os erros de campo (se houver)
                for (x in retorno.errors) {
                    id = retorno.errors[x].id;
                    message = retorno.errors[x].message;
                    $('#' + id).addClass("is-invalid");
                    $("#divError-" + id).html(message).show();
                }

                $("[id^=msg]").html("").hide();
                $("#msgErroGeral").html(retorno.message).show();
                document.querySelector('#msgErroGeral').scrollIntoView({
                    behavior: 'smooth'
                });

                console.log(retorno);

                // --- SE EXISTIREM BOTÕES (actions), CRIAMOS DINAMICAMENTE ---
                if (retorno.actions && retorno.actions.length > 0) {
                    $('#tokenActions').show().empty();
                    retorno.actions.forEach(function(action) {
                        let btn = $('<button class="btn btn-secondary me-2">')
                            .text(action.label)
                            .on('click', function() {
                                if (action.type === 'reenviar') {
                                    reenviarTokenAjax(action.idUsuario);
                                } else if (action.type === 'excluir') {
                                    excluirRegistroAjax(action.idUsuario);
                                }
                            });
                        $('#tokenActions').append(btn);
                    });
                }

            // Se for "NOTICE"
            } else if (retorno.status.substr(0, 6) == "NOTICE") {
                for (x in retorno.errors) {
                    id = retorno.errors[x].id;
                    message = retorno.errors[x].message;
                    $("#divNotice-" + id).html(message).show();
                }
                $("[id^=msg]").html("").hide();
                $("#msgAvisoGeral").html(retorno.message).show();
                document.querySelector('#msgAvisoGeral').scrollIntoView({
                    behavior: 'smooth'
                });

            // Caso seja outro status não previsto
            } else {
                $("[id^=msg]").html("").hide();
                $("#msgErroGeral").html(debug(retorno)).show();
                document.querySelector('#msgErroGeral').scrollIntoView({
                    behavior: 'smooth'
                });
            }

        },
        error: function (xhr) {
            $("#divLoading").hide();
            $("[id^=msg]").html("").hide();
            $("#msgErroGeral").html("Código do erro: " + xhr.status + "<br />Erro: " + xhr.statusText).fadeIn(300);
            document.querySelector('#msgErroGeral').scrollIntoView({
                behavior: 'smooth'
            });
        },
        complete: function () {
            $('#divLoading').hide();
            $("#btnSend").prop("disabled", false);
        }
    });

}

/**
 * Envia os dados do Passo 2 para validação via AJAX.
 */
function firstAccessPasso2() {
    let params = $("#formFirstAccess").serialize();
    $.ajax({
        type: 'post',
        url: $("[name=baseUrl]").val() + 'primeiro_acesso/validar_p2',
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
            let id, message, x;

            if (retorno.status == "SUCCESS") {
                $("[id^='divError-']").html("").hide();
                $("input").removeClass("is-invalid");
                $("[id^='msg']").html("").hide();
                $("#msgSucessoGeral").html(retorno.message + "<br />Utilize o botão <b>Entrar</b> para logar no sistema!").show();
                document.querySelector('#msgSucessoGeral').scrollIntoView({
                    behavior: 'smooth'
                });
                // Desabilita os inputs do formulário para impedir novas alterações
                $("#formFirstAccess :input").prop("disabled", true);
                // Adiciona um botão para redirecionar para a página de login, se ainda não existir
                if ($("#btnLogin").length === 0) {
                     $("#formFirstAccess").append(
                         '<div class="row mt-3">' +
                             '<div class="col-12 text-center">' +
                                 '<button id="btnLogin" type="button" class="btn btn-success" onclick="window.location.href=\'' + 
                                 $("[name=baseUrl]").val() + 'login' + '\'">Entrar</button>' +
                             '</div>' +
                         '</div>'
                     );
                }

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
                document.querySelector('#msgErroGeral').scrollIntoView({
                    behavior: 'smooth'
                });

            } else if (retorno.status.substr(0, 6) == "NOTICE") {
                for (x in retorno.errors) {
                    id = retorno.errors[x].id;
                    message = retorno.errors[x].message;
                    $("#divNotice-" + id).html(message).show();
                }
                $("[id^=msg]").html("").hide();
                $("#msgAvisoGeral").html(retorno.message).show();
                document.querySelector('#msgAvisoGeral').scrollIntoView({
                    behavior: 'smooth'
                });
            } else {
                $("[id^=msg]").html("").hide();
                $("#msgErroGeral").html(debug(retorno)).show();
                document.querySelector('#msgErroGeral').scrollIntoView({
                    behavior: 'smooth'
                });
            }
        },
        error: function (xhr) {
            $("#divLoading").hide();
            $("[id^=msg]").html("").hide();
            $("#msgErroGeral").html("Código do erro: " + xhr.status + "<br />Erro: " + xhr.statusText).fadeIn(300);
            document.querySelector('#msgErroGeral').scrollIntoView({
                behavior: 'smooth'
            });
        },
        complete: function () {
            $('#divLoading').hide();
            $("#btnSend").prop("disabled", false);
        }
    });
}

/**
 * Verifica se o email termina em:
 *   - @uerj.br
 *   - @graduacao.uerj.br
 *   - @posgraduacao.uerj.br
 *   - @ce.uerj.br
 */
function isEmailUerjValido(email) {
    const pattern = /^[^@\s]+@(uerj\.br|graduacao\.uerj\.br|posgraduacao\.uerj\.br|ce\.uerj\.br)$/i;
    return pattern.test(email);
}

/**
 * Valida dados básicos do Passo 1 (CPF e E-mail) antes de enviar ao servidor.
 */
function validarDadosPasso1() {

    let totalErros = 0;
    $("[id^='divError-']").html("").hide();

    // Verificar CPF
    if ($('#identificador').val() === '') {
        $('#identificador').addClass("is-invalid");
        let labelCpf = $('label[for="identificador"]').text().replace(/\*/g,'').toUpperCase();
        $('#divError-identificador').html('O campo '+ labelCpf +' é obrigatório').show();
        totalErros++;
    } else if (!isCpfValido($('#identificador').val())) {
        $('#identificador').addClass("is-invalid");
        $('#divError-identificador').html('CPF inválido').show();
        totalErros++;
    }

    // Verificar Email
    const emailValue = $.trim($('#email').val());
    if (emailValue === '') {
        $('#email').addClass("is-invalid");
        let labelEmail = $('label[for="email"]').text().replace(/\*/g,'').toUpperCase();
        $('#divError-email').html('O campo '+ labelEmail +' é obrigatório').show();
        totalErros++;
    } else if (!isEmail(emailValue)) {
        // Checagem genérica de formato de e-mail
        $('#email').addClass("is-invalid");
        $('#divError-email').html('Email inválido').show();
        totalErros++;
    } else if (!isEmailUerjValido(emailValue)) {
        // Checagem específica para domínios permitidos (@uerj.br, @graduacao..., etc.)
        $('#email').addClass("is-invalid");
        $('#divError-email').html('E-mail deve ser um e-mail UERJ válido (@uerj.br, @graduacao.uerj.br, @posgraduacao.uerj.br ou @ce.uerj.br).').show();
        totalErros++;
    }

    if (totalErros > 0) {
        setFocusError();
    }
    return !(totalErros > 0);
}

/**
 * Faz a requisição AJAX para renovar o token e reenviar o e-mail.
 */
function reenviarTokenAjax(idUsuario) {
    let csrfToken = $('input[name="csrf_test_name"]').val();

    $.ajax({
        url: $("[name=baseUrl]").val() + 'primeiro_acesso/renovar_registro',
        method: 'POST',
        dataType: 'json',
        data: {
            idUsuario: idUsuario,
            csrf_test_name: csrfToken
        },
        beforeSend: function() {
            $("#divLoading").show();
        },
        success: function(resp) {
            $("#divLoading").hide();

            if (resp.csrf_hash) {
                $('input[name="csrf_test_name"]').val(resp.csrf_hash);
            }

            $("[id^='divError-']").html("").hide();
            $("input").removeClass("is-invalid");
            $("[id^=msg]").html("").hide();

            if (resp.status === 'SUCCESS') {
                $("#msgSucessoGeral").html(resp.message).show();
                document.querySelector('#msgSucessoGeral').scrollIntoView({ behavior: 'smooth' });

                // Desabilita todos os inputs e botões do form
                $("#formFirstAccess :input").prop("disabled", true);

                // Desabilita também os botões adicionados em #tokenActions
                $("#tokenActions button").prop("disabled", true);

                if ($("#btnRestart").length === 0) {
                    $("#formFirstAccess").append(
                        '<div class="row mt-3">' +
                            '<div class="col-12 text-center">' +
                                '<button id="btnRestart" type="button" class="btn btn-info">' +
                                    'Reiniciar Cadastro' +
                                '</button>' +
                            '</div>' +
                        '</div>'
                    );
                    $("#btnRestart").on("click", function() {
                        window.location.href = $("[name=baseUrl]").val() + 'primeiro_acesso';
                    });
                }

            } else if (resp.status.substr(0, 5) === 'ERROR') {
                $("#msgErroGeral").html(resp.message).show();
                document.querySelector('#msgErroGeral').scrollIntoView({ behavior: 'smooth' });
            } else {
                $("#msgErroGeral").html(resp.message).show();
                document.querySelector('#msgErroGeral').scrollIntoView({ behavior: 'smooth' });
            }
        },
        error: function(xhr) {
            $("#divLoading").hide();
            $("[id^=msg]").html("").hide();
            $("#msgErroGeral").html("Código do erro: " + xhr.status + "<br />Erro: " + xhr.statusText).show();
            document.querySelector('#msgErroGeral').scrollIntoView({ behavior: 'smooth' });
        }
    });
}

/**
 * Faz a requisição AJAX para excluir o registro do usuário.
 */
function excluirRegistroAjax(idUsuario) {
    let csrfToken = $('input[name="csrf_test_name"]').val();

    $.ajax({
        url: $("[name=baseUrl]").val() + 'primeiro_acesso/excluir_registro',
        method: 'POST',
        dataType: 'json',
        data: {
            idUsuario: idUsuario,
            csrf_test_name: csrfToken
        },
        beforeSend: function() {
            $("#divLoading").show();
        },
        success: function(resp) {
            $("#divLoading").hide();

            if (resp.csrf_hash) {
                $('input[name="csrf_test_name"]').val(resp.csrf_hash);
            }

            $("[id^='divError-']").html("").hide();
            $("input").removeClass("is-invalid");
            $("[id^=msg]").html("").hide();

            if (resp.status === 'SUCCESS') {
                $("#msgSucessoGeral").html(resp.message).show();
                document.querySelector('#msgSucessoGeral').scrollIntoView({ behavior: 'smooth' });

                // Desabilita todos os inputs e botões do form
                $("#formFirstAccess :input").prop("disabled", true);

                // Desabilita também os botões adicionados em #tokenActions
                $("#tokenActions button").prop("disabled", true);

                if ($("#btnRestart").length === 0) {
                    $("#formFirstAccess").append(
                        '<div class="row mt-3">' +
                            '<div class="col-12 text-center">' +
                                '<button id="btnRestart" type="button" class="btn btn-info">' +
                                    'Reiniciar Cadastro' +
                                '</button>' +
                            '</div>' +
                        '</div>'
                    );
                    $("#btnRestart").on("click", function() {
                        window.location.href = $("[name=baseUrl]").val() + 'primeiro_acesso';
                    });
                }

            } else if (resp.status.substr(0, 5) === 'ERROR') {
                $("#msgErroGeral").html(resp.message).show();
                document.querySelector('#msgErroGeral').scrollIntoView({ behavior: 'smooth' });
            } else {
                $("#msgErroGeral").html(resp.message).show();
                document.querySelector('#msgErroGeral').scrollIntoView({ behavior: 'smooth' });
            }
        },
        error: function(xhr) {
            $("#divLoading").hide();
            $("[id^=msg]").html("").hide();
            $("#msgErroGeral").html("Código do erro: " + xhr.status + "<br />Erro: " + xhr.statusText).show();
            document.querySelector('#msgErroGeral').scrollIntoView({ behavior: 'smooth' });
        }
    });
}
