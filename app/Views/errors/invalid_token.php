<?php $this->extend('template/base'); ?>
<?php $this->section('content'); ?>

<div class="alert alert-danger">
    <h4>Token inválido</h4>
    <p><?= esc($mensagem) ?></p>
    <a href="<?= base_url('/') ?>" class="btn btn-primary">Voltar à página inicial</a>
</div>

<?php $this->endSection(); ?>
