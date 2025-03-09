<?php $this->extend('template/base'); ?>
<?php $this->section('content'); ?>

<section>
    <h3 class="page-title">Bem-vindo</h3>
</section>

<!-- Exibe o conteúdo com base na autenticação -->
<div id="auth-section">
    <p>Verificando autenticação...</p>
</div>

<!-- Botão de Login -->
<a id="login-button" href="<?= esc($ssoBaseUrl) ?>/sso/login?redirect=<?= base_url('callback'); ?>&sistema=<?= esc($idSistema) ?>">
    <button>Logar com e-Prefeitura</button>
</a>

<!-- Botão de Logout (só aparece se estiver logado) -->
<button id="logout-button" style="display:none;" onclick="logout()">Sair</button>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        checkAuthStatus();
    });

    function checkAuthStatus() {
        const token = localStorage.getItem("jwt_token");

        if (!token) {
            // Usuário não está logado
            document.getElementById("auth-section").innerHTML = "<p>Você não está logado.</p>";
            document.getElementById("login-button").style.display = "inline-block";
            document.getElementById("logout-button").style.display = "none";
        } else {
            // Usuário logado - Decodificar token para obter informações do usuário
            const userData = parseJwt(token);
            if (!userData) {
                logout();
                return;
            }

            document.getElementById("auth-section").innerHTML = `
                <p>Bem-vindo, <strong>${userData.nome}</strong>!</p>
                <p>Nível de Acesso: <strong>${userData.nivel}</strong></p>
            `;
            document.getElementById("login-button").style.display = "none";
            document.getElementById("logout-button").style.display = "inline-block";
        }
    }

    function parseJwt(token) {
        try {
            const base64Url = token.split('.')[1];
            const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            return JSON.parse(atob(base64));
        } catch (e) {
            console.error("Token inválido", e);
            return null;
        }
    }

    function logout() {
        localStorage.removeItem("jwt_token");
        window.location.reload(); // Recarrega a página para refletir o logout
    }
</script>

<?= $this->endSection(); ?>
