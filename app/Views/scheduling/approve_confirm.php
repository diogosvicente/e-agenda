<?php $this->extend('template/base'); ?>
<?php $this->section('content'); ?>

<div class="evento-info">
    <h2>Aprovação de Solicitação de Agendamento</h2>
    <?= $textoEvento ?>
</div>

<form action="<?= base_url('agendamento/confirmar_aprovacao') ?>" method="post">
    <!-- Campo oculto para enviar o token -->
    <input type="hidden" name="token" value="<?= esc($token) ?>">
    
    <div class="form-group">
        <label for="senha">Digite sua senha para confirmar a aprovação:</label>
        <input type="password" id="senha" name="senha" required class="form-control">
    </div>
    
    <button type="submit" class="btn btn-primary">Aprovar Solicitação</button>
</form>

<?php $this->endSection(); ?>
