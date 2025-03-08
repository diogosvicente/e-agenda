<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Autenticando...</title>
    <script>
        // Armazena o token no LocalStorage
        localStorage.setItem('jwt_token', '<?= esc($access_token) ?>');

        // Obtém a base_url do sistema filho
        let baseURL = "<?= base_url(); ?>"; 

        // Redireciona para a página inicial SEM O TOKEN NA URL
        window.location.href = baseURL;
    </script>
</head>
<body>
    <p>Autenticando...</p>
</body>
</html>
