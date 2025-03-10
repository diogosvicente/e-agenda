document.addEventListener("DOMContentLoaded", function () {
    checkAuthStatus();
    initTooltips(); // Inicializa tooltips do Bootstrap

    // Se existir o link de logout, adicionamos o evento de clique
    const logoutLink = document.getElementById("logout-link");
    if (logoutLink) {
        logoutLink.addEventListener("click", logout);
    }
});

/**
 * Verifica se há um token JWT no localStorage.
 * - Se não houver, exibe "Entrar" e esconde os elementos de usuário.
 * - Se houver, decodifica o token e exibe nome do usuário + CPF + botões de sair.
 */
function checkAuthStatus() {
    const token = localStorage.getItem("jwt_token");

    // Referências aos elementos no layout
    const menuLogin      = document.getElementById("login-link");
    const authContainer  = document.getElementById("jwt-auth-container");
    const userNameSpan   = document.getElementById("usuario-nome");
    const userDetails    = document.getElementById("usuario-detalhes");
    const logoutLink     = document.getElementById("logout-link");

    // Se não há token => usuário NÃO logado
    if (!token) {
        // Exibe o link "Entrar"
        if (menuLogin) {
            menuLogin.style.display = "inline";
        }
        // Esconde o container de usuário e o link "Sair"
        if (authContainer) authContainer.style.display = "none";
        if (logoutLink)    logoutLink.style.display    = "none";
        return;
    }

    // Se há token, tentamos decodificar
    const userData = parseJwt(token);
    if (!userData) {
        // Token inválido ou expirado → faz logout
        logout();
        return;
    }

    // Esconde o link "Entrar"
    if (menuLogin) {
        menuLogin.style.display = "none";
    }

    // Exibe o container com nome do usuário
    if (authContainer) {
        authContainer.style.display = "block";
    }

    // Monta o HTML do usuário
    if (userNameSpan) {
        let cpfTooltip = "";
        // Se existir CPF, define o tooltip
        if (userData.cpf) {
            cpfTooltip = `
                <span class="bi-info-circle-fill ms-2"
                      data-bs-toggle="tooltip"
                      data-bs-placement="top"
                      title="${maskCPF(userData.cpf)}">
                </span>
            `;
        }

        userNameSpan.innerHTML = `
            ${userData.nome}
            ${cpfTooltip}
            <a href="#" id="logout-link-inline" class="ms-3">
                <i class="bi-box-arrow-right"></i> Sair
            </a>
        `;
    }

    // Exibe CPF em outro lugar também, se quiser
    if (userData.cpf && userDetails) {
        userDetails.innerHTML = `CPF: ${maskCPF(userData.cpf)}`;
        userDetails.style.display = "block";
    }

    // Exibe o link "Sair" do menu lateral, se desejar
    if (logoutLink) {
        logoutLink.style.display = "inline";
    }

    // Adiciona evento de logout no link recém-criado
    const logoutInline = document.getElementById("logout-link-inline");
    if (logoutInline) {
        logoutInline.addEventListener("click", logout);
    }

    // Reinicializa tooltips (pois inserimos novos elementos no DOM)
    initTooltips();
}

/**
 * Decodifica um token JWT
 */
function parseJwt(token) {
    try {
        const base64Url = token.split('.')[1];
        const base64    = base64Url.replace(/-/g, '+').replace(/_/g, '/');
        return JSON.parse(atob(base64));
    } catch (e) {
        console.error("Token inválido:", e);
        return null;
    }
}

/**
 * Remove o token e recarrega a página
 */
function logout() {
    localStorage.removeItem("jwt_token");
    window.location.reload();
}

/**
 * Formata CPF no padrão 000.000.000-00
 */
function maskCPF(cpf) {
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
}

/**
 * Inicializa tooltips do Bootstrap 5
 */
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}
