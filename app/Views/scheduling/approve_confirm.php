<?php $this->extend('template/base'); ?>
<?php $this->section('content'); ?>

<section>
    <h3 class="page-title">Aprovação de Solicitação de Agendamento</h3>
</section>

<div class="container my-4">

    <!-- Linha para mensagem de boas-vindas e botão de visualização do PDF -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-9">
            <div class="alert alert-info">
                <p>
                    Bem-vindo, <?= isset($usuario) ? esc($usuario['nome']) : 'Usuário' ?>!<br>
                    Você tem permissão para aprovar esta solicitação de agendamento.
                </p>
            </div>
        </div>
        <div class="col-md-3 text-end">
            <a href="<?= base_url('pdf/gerar/' . esc($token)) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                Visualizar PDF
            </a>
        </div>
    </div>

    <!-- Dados do evento: aprovador(es) e data de cadastro -->
    <?php if(isset($detalhes) && !empty($detalhes)): ?>
    <div class="event-meta mb-4">
        <p><strong>Aprovador(es):</strong>
            <?php 
                if(is_array($detalhes['aprovador'])) {
                    echo esc(implode(', ', $detalhes['aprovador']));
                } else {
                    echo esc($detalhes['aprovador']);
                }
            ?>
        </p>
        <p><strong>Data de Cadastro:</strong> <?= date('d/m/Y H:i', strtotime($detalhes['data_cadastro'])) ?></p>
    </div>
    <?php endif; ?>

    <!-- Detalhes do evento -->
    <div class="evento-info card p-4 mb-4">
        <?= $textoEvento ?>
    </div>

    <!-- Formulário de aprovação -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form id="formAprovacao" action="<?= base_url('agendamento/confirmar_aprovacao') ?>" method="post">
                <!-- Campo oculto para enviar o token -->
                <input type="hidden" name="token" value="<?= esc($token) ?>">
                
                <div class="form-group mb-3">
                    <label for="senha" class="form-label">Digite sua senha para confirmar a aprovação:</label>
                    <input type="password" id="senha" name="senha" required class="form-control">
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Aprovar Solicitação</button>
            </form>
            <!-- Área para exibir a resposta da aprovação via AJAX -->
            <div id="aprovacaoResult" class="mt-3"></div>
        </div>
    </div>

</div>

<script>
$(function(){
    $('#formAprovacao').on('submit', function(e){
        e.preventDefault(); // Impede o envio tradicional do formulário
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Se "success" = true, exibe alerta verde
                    $('#aprovacaoResult').html('<div class="alert alert-success">' + response.message + '</div>');
                } else {
                    // Se "success" = false, exibe alerta vermelho
                    $('#aprovacaoResult').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function(xhr) {
                // Em caso de erro, exibe a mensagem de erro
                $('#aprovacaoResult').html('<div class="alert alert-danger">Ocorreu um erro ao processar a aprovação.</div>');
            }
        });
    });
});
</script>


<?php $this->endSection(); ?>
