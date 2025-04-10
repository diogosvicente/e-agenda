<?php $this->extend('template/base'); ?>
<?php $this->section('content'); ?>

<link rel="stylesheet" href="<?= base_url('public/assets/css/scheduling/followUp.css'); ?>">

<section class="mb-5 col-sm-12">
    <h6 class="page-title">Acompanhamento da Solicitação de Agendamento<hr></h6>
</section>

<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
            <div>
                <?php if(isset($evento) && !empty($evento)): ?>
                    <span class="text-muted">Evento: <strong><?= esc($evento->nome); ?></strong></span>
                <?php endif; ?>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="<?= base_url('pdf/acompanhar/'.esc($token)); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                    Visualizar PDF do Evento
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Progress Tracker Dinâmico -->
            <?php
                // O status atual a ser destacado é sempre aquele do último registro inserido
                $ordemAtual = 0;
                if (isset($ultimoStatus) && !empty($ultimoStatus)) {
                    $ordemAtual = (int)$ultimoStatus->status_ordem;
                }
            ?>
            <div class="progress-track">
                <?php foreach ($statusPossiveis as $status): 
                    if ($status->ordem < $ordemAtual) {
                        $classe = 'completed';
                    } elseif ($status->ordem == $ordemAtual) {
                        $classe = 'active';
                    } else {
                        $classe = 'pending';
                    }
                ?>
                <div class="step <?= $classe; ?>">
                    <div class="step-icon">
                        <?php
                        // Escolha os ícones conforme a lógica do seu sistema
                        switch ($status->id) {
                            case 1:
                                echo '<i class="bi bi-pencil-fill"></i>';
                                break;
                            case 2:
                                echo '<i class="fa fa-check"></i>';
                                break;
                            case 3:
                                echo '<i class="fa fa-hourglass-half"></i>';
                                break;
                            case 4:
                                echo '<i class="bi bi-calendar-check"></i>';
                                break;
                            case 5:
                                echo '<i class="fa fa-times-circle"></i>';
                                break;
                            case 6:
                                echo '<i class="fa fa-ban"></i>';
                                break;
                            default:
                                echo '<i class="bi bi-info-circle"></i>';
                                break;
                        }
                        ?>
                    </div>
                    <div class="step-label">
                        <h5><?= ucfirst($status->nome); ?></h5>
                        <p><?= $status->descricao; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <!-- Fim do Progress Tracker -->
        </div>
    </div>
</div>

<!-- Restante do conteúdo da view (como calendários, tabelas de solicitações, etc.) -->
<?php $this->endSection(); ?>
