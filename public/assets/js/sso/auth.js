// Obtém as URLs e o token JWT do meta tag no HTML
const BASE_URL = document.querySelector('meta[name="base-url"]').getAttribute('content');
const SSO_BASE_URL = document.querySelector('meta[name="sso-base-url"]').getAttribute('content');
const ID_SISTEMA = document.querySelector('meta[name="id-sistema"]').getAttribute('content');
const JWT_TOKEN = document.querySelector('meta[name="jwt-token"]').getAttribute('content');

$(document).ready(function() {
    // getUserInfo();
    $("#formLoginSSO").submit(function(event) {
        event.preventDefault(); // Impede o envio tradicional do formulário
        
        $("#btnValidateLogin").prop("disabled", true);
        $('#divLoading').show();
        limparMensagens();

        if (!dataValidation()) {
            $("#btnValidateLogin").prop("disabled", false);
            $('#divLoading').hide();
            return;
        }

        let cpf = $("#cpf").val().replace(/\D/g, ''); // Remove caracteres não numéricos
        let senha = $("#senha").val();

        $.ajax({
            url: SSO_BASE_URL + "/api/auth/login",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                cpf: cpf,
                senha: senha,
                sistema: ID_SISTEMA
            }),
            success: function(response) {
                if (response.auth_code) {
                    localStorage.setItem("auth_code", response.auth_code);
                    trocarCodigoPorToken(BASE_URL, SSO_BASE_URL);

                    setTimeout(function () {
                        window.location.href = BASE_URL;
                    }, 1000);
                } else {
                    $("#msgErroGeral").html("Erro: Código de autorização não recebido.").show();
                }
            },
            error: function(xhr) {
                $("#btnValidateLogin").prop("disabled", false);
                $('#divLoading').hide();

                // Verifica se a resposta tem um JSON válido
                if (xhr.responseJSON && xhr.responseJSON.messages && xhr.responseJSON.messages.error) {
                    alert(xhr.responseJSON.messages.error);
                    $("#msgErroGeral").html(xhr.responseJSON.messages.error).show();
                } else {
                    $("#msgErroGeral").html("Erro desconhecido ao autenticar.").show();
                }
            },
            complete: function() {
                $("#btnValidateLogin").prop("disabled", false);
                $('#divLoading').hide();
            }
        });
    });

    function trocarCodigoPorToken(BASE_URL, SSO_BASE_URL) {
        let authCode = localStorage.getItem("auth_code");

        if (!authCode) {
            $("#msgErroGeral").html("Código de autenticação não encontrado.").show();
            return;
        }

        $.ajax({
            url: SSO_BASE_URL + "/api/auth/token",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                code: authCode,
                sistema: ID_SISTEMA
            }),
            success: function(response) {
                if (response.access_token) {
                    localStorage.setItem("jwt_token", response.access_token);
                    $("#msgSucessoGeral").html("Autenticação bem-sucedida! Redirecionando...").show();
                    setTimeout(function () {
                        window.location.href = BASE_URL;
                    }, 1000);
                } else {
                    $("#msgErroGeral").html("Erro ao obter token de acesso.").show();
                }
            },
            error: function(xhr) {
                $("#btnValidateLogin").prop("disabled", false);
                $('#divLoading').hide();

                if (xhr.responseJSON && xhr.responseJSON.messages && xhr.responseJSON.messages.error) {
                    $("#msgErroGeral").html(xhr.responseJSON.messages.error).show();
                } else {
                    $("#msgErroGeral").html("Erro desconhecido ao trocar código por token.").show();
                }
            }
        });
    }
    
    function dataValidation() {
        let totalErros = 0;
        limparMensagens();

        if ($.trim($('#cpf').val()) == "") {
            totalErros++;
            $('#cpf').addClass("is-invalid");
            $('#divError-cpf').html('O campo CPF é obrigatório.').show();
        }
        if ($.trim($("#senha").val()) == "") {
            totalErros++;
            $('#senha').addClass("is-invalid");
            $('#divError-senha').html('O campo Senha é obrigatório.').show();
        }

        return totalErros === 0;
    }

    function limparMensagens() {
        $("#msgSucessoGeral, #msgAvisoGeral, #msgErroGeral").html("").hide();
        $("#divError-cpf, #divError-senha").html("").hide();
        $("#cpf, #senha").removeClass("is-invalid");
    }
});

function logout() {
    if (!JWT_TOKEN) {
        console.log("Nenhum token JWT encontrado, redirecionando para login.");
        window.location.href = BASE_URL + "/login"; 
        return;
    }

    fetch(SSO_BASE_URL + "/api/auth/logout", {
        method: "POST",
        headers: {
            "Authorization": "Bearer " + JWT_TOKEN,
            "Content-Type": "application/json"
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error === false) {
            window.location.href = BASE_URL + "login";
        } else {
            console.error("Erro ao tentar sair:", data.message);
            alert("Erro ao tentar sair: " + data.message);
        }
    })
    .catch(error => {
        console.error("Erro na requisição de logout:", error);
        alert("Erro ao tentar sair. Tente novamente.");
    });
}

// function getUserInfo() {
//     const jwtToken = localStorage.getItem("jwt_token");

//     if (!jwtToken) {
//         console.warn("Nenhum token encontrado, usuário não autenticado.");
//         return;
//     }

//     $.ajax({
//         url: SSO_BASE_URL + "/api/userinfo",
//         type: "GET",
//         headers: {
//             "Authorization": "Bearer " + jwtToken,
//             "Content-Type": "application/json"
//         },
//         success: function (data) {
//             if (!data.error) {
//                 console.log("Usuário autenticado:", data);
//                 atualizarInterfaceUsuario(data);
//             } else {
//                 console.warn("Erro ao obter informações do usuário:", data.message);
//             }
//         },
//         error: function (xhr) {
//             console.error("Erro na requisição de informações do usuário:", xhr.responseText);
//         }
//     });
// }

function atualizarInterfaceUsuario(userData) {
    $("#usuario-nome").text(userData.nome);
    $("#usuario-detalhes").html(`
        <strong>CPF:</strong> ${userData.cpf} <br>
        <strong>Nível de Acesso:</strong> ${userData.nivel}
    `);
    $("#jwt-auth-container").show();
}