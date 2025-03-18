<?php $this->extend('template/base'); ?>
<?php $this->section('content'); ?>

<link rel="stylesheet" href="<?php echo base_url('public/assets/css/scheduling/style.css'); ?>">

<section>
	<?php if (!empty($id)): ?>
		<h3 class="page-title">Editar Solicitação</h3>
	<?php else: ?>
		<h3 class="page-title">Formulário de Solicitação</h3>
	<?php endif; ?>

	<div class="col-md-12 mt-2">
		<form action="<?php echo base_url('legado/legadoValidade'); ?>" id="formLegado" name="formLegado" enctype="multipart/form-data" role="form" class="form-legado" method="post" accept-charset="utf-8">

			<ul class="nav nav-tabs">
				<li class="nav-item"><a class="nav-link active" id="evento-tab" data-toggle="tab" href="#evento">Nome do Evento</a></li>
				<li class="nav-item"><a class="nav-link" id="solicitante-tab" data-toggle="tab" href="#solicitante">Solicitante e Responsável</a></li>
				<li class="nav-item"><a class="nav-link" id="espacos-tab" data-toggle="tab" href="#espacos">Espaços</a></li>
				<li class="nav-item"><a class="nav-link" id="recursos-tab" data-toggle="tab" href="#recursos">Recursos</a></li>
			</ul>

			<fieldset>
                <div class="tab-content mt-3">

                    <!--
                    * -------------------------------------------------------------------
                    * ID_EVENTO SE FOR EDIÇÃO
                    * -------------------------------------------------------------------
                    -->
                    <input type="hidden" name="id_pessoa" id="id_pessoa" autocomplete="off" value="<?php echo (isset($registro->id_pessoa)) ? $registro->id_pessoa : '' ?>" />

                    <!--
                    * -------------------------------------------------------------------
                    * INÍCIO DA TAB 1: NOME DO EVENTO
                    *
                    * NOME
                    * -------------------------------------------------------------------
                    -->
                    <div class="tab-pane fade show active" id="evento">
                        <div class="row mb-3">
                            <div class="col-sm-12">
                                <label for="responsavel" class="form-label">Nome da Atividade / Evento: *</label>
                                <input type="text" name="responsavel" id="responsavel" label="Responsavel:" class="form-control" autocomplete="off" required="required" value="<?php echo (isset($registro->responsavel)) ? $registro->responsavel : '' ?>" />
                                <div id="divError-responsavel" class="invalid-feedback"></div>
                                <div id="divNotice-responsavel" class="notice-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!--
                    * -------------------------------------------------------------------
                    * INÍCIO DA TAB 2: SOLICITANTE
                    *
                    * SOLICITANTE
                    * -------------------------------------------------------------------
                    -->
                    <div class="tab-pane fade" id="solicitante">
                        <fieldset class="fieldset-child">
                            <legend class="fieldset-child">Solicitante</legend>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" checked name="eu_sou_o_solicitante" value="S" id="eu_sou_o_solicitante" class="form-check-input" data-required="*" />
                                        <label for="eu_sou_o_solicitante" class="form-check-label">
                                            Eu sou o solicitante
                                        </label>
                                    </div>
                                    <div id="divError-eu_sou_o_solicitante" class="invalid-feedback"></div>
                                    <div id="divNotice-eu_sou_o_solicitante" class="notice-feedback"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="solicitante" class="form-label">Nome: *</label>
                                    <input type="text" name="solicitante" id="solicitante" class="form-control" autocomplete="off" required="required" 
                                        value="<?php echo (isset($registro->solicitante)) ? $registro->solicitante : '' ?>" />
                                    <div id="divError-solicitante" class="invalid-feedback"></div>
                                    <div id="divNotice-solicitante" class="notice-feedback"></div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="unidade" class="form-label">Unidade: *</label>
                                    <input type="text" name="unidade" id="unidade" class="form-control" autocomplete="off" required="required" 
                                        value="<?php echo (isset($registro->unidade)) ? $registro->unidade : '' ?>" />
                                    <div id="divError-unidade" class="invalid-feedback"></div>
                                    <div id="divNotice-unidade" class="notice-feedback"></div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="fieldset-child">
                            <legend class="fieldset-child">Responsável</legend>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" checked name="eu_sou_o_responsavel" value="S" id="eu_sou_o_responsavel" class="form-check-input" data-required="*" />
                                        <label for="eu_sou_o_responsavel" class="form-check-label">
                                            Eu sou o responsável
                                        </label>
                                    </div>
                                    <div id="divError-eu_sou_o_responsavel" class="invalid-feedback"></div>
                                    <div id="divNotice-eu_sou_o_responsavel" class="notice-feedback"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="solicitante" class="form-label">Nome: *</label>
                                    <input type="text" name="solicitante" id="solicitante" class="form-control" autocomplete="off" required="required" 
                                        value="<?php echo (isset($registro->solicitante)) ? $registro->solicitante : '' ?>" />
                                    <div id="divError-solicitante" class="invalid-feedback"></div>
                                    <div id="divNotice-solicitante" class="notice-feedback"></div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="unidade" class="form-label">Unidade: *</label>
                                    <input type="text" name="unidade" id="unidade" class="form-control" autocomplete="off" required="required" 
                                        value="<?php echo (isset($registro->unidade)) ? $registro->unidade : '' ?>" />
                                    <div id="divError-unidade" class="invalid-feedback"></div>
                                    <div id="divNotice-unidade" class="notice-feedback"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="email" class="form-label">E-mail: *</label>
                                    <input type="text" name="email" id="email" class="form-control" autocomplete="off" required="required" 
                                        value="<?php echo (isset($registro->email)) ? $registro->email : '' ?>" />
                                    <div id="divError-email" class="invalid-feedback"></div>
                                    <div id="divNotice-email" class="notice-feedback"></div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="telefone" class="form-label">Telefone 1: *</label>
                                    <input type="text" name="telefone" id="telefone" class="form-control" autocomplete="off" required="required" 
                                        value="<?php echo (isset($registro->telefone)) ? $registro->telefone : '' ?>" />
                                    <div id="divError-telefone" class="invalid-feedback"></div>
                                    <div id="divNotice-telefone" class="notice-feedback"></div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="telefone" class="form-label">Telefone 2: *</label>
                                    <input type="text" name="telefone" id="telefone" class="form-control" autocomplete="off" required="required" 
                                        value="<?php echo (isset($registro->telefone)) ? $registro->telefone : '' ?>" />
                                    <div id="divError-telefone" class="invalid-feedback"></div>
                                    <div id="divNotice-telefone" class="notice-feedback"></div>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <!--
                    * -------------------------------------------------------------------
                    * INÍCIO DA TAB 3: ESPACOS
                    *
                    * SOLICITANTE
                    * -------------------------------------------------------------------
                    -->
                    <div class="tab-pane fade" id="espacos">
                        <!-- Espaços serão adicionados dinamicamente -->
                        <fieldset class="fieldset-child" id="espacos-container">
                            <legend class="fieldset-child">Espaços</legend>

                            <!-- Primeiro espaço fixo -->
                            <div class="espaco-entry" data-espaco="1">
                                <fieldset class="fieldset-child">
                                    <legend class="fieldset-child">Espaço 1</legend>

                                    <!-- Seleção de Espaço -->
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label for="espaco_1" class="form-label">Espaço Disponível: *</label>
                                            <select name="espaco[]" class="form-control espaco-select" required>
                                                <option value="">Selecione um espaço</option>
                                                <?php foreach ($campus as $campusItem): ?>
                                                    <optgroup label="<?php echo $campusItem->nome; ?>">
                                                        <?php foreach ($campusItem->predios as $predio): ?>
                                                            <optgroup label="➝ <?php echo $predio->nome; ?>">
                                                                <?php foreach ($predio->espacos as $espaco): ?>
                                                                    <option value="<?php echo $espaco->id; ?>">
                                                                        <?php echo $espaco->nome . " (Capacidade: " . $espaco->capacidade . ")"; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </optgroup>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Seleção de Datas e Horários -->
                                    <div class="datas_horarios">
                                        <div class="row mt-3 data-hora-entry">
                                            <div class="col-sm-4">
                                                <label for="data_inicio[]" class="form-label">Data Início:</label>
                                                <input type="date" name="data_inicio[]" class="form-control" required>
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="hora_inicio[]" class="form-label">Hora de Início:</label>
                                                <input type="time" name="hora_inicio[]" class="form-control" required>
                                            </div>
                                            <div class="col-sm-3">
                                                <label for="hora_fim[]" class="form-label">Hora de Fim:</label>
                                                <input type="time" name="hora_fim[]" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Botões para adicionar/remover -->
                                    <div class="row mt-3">
                                        <div class="col-sm-6">
                                            <button type="button" class="btn btn-success addDataHora">+ Adicionar Data/Hora</button>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>

                            <!-- Botão para adicionar novo espaço -->
                            <div class="row mt-3">
                                <div class="col-sm-12 text-end">
                                    <button type="button" class="btn btn-primary" id="addEspaco">+ Adicionar Novo Espaço</button>
                                </div>
                            </div>
                        </fieldset>
                         
                    </div>

                    <!--
                    * -------------------------------------------------------------------
                    * INÍCIO DA TAB 4: RECURSOS
                    *
                    * SOLICITANTE
                    * -------------------------------------------------------------------
                    -->
                    <div class="tab-pane fade" id="recursos">
                        <!-- Recursos futuros -->
                    </div>
                </div>
            </fieldset>
		</form>
	</div>
</section>

<script src="<?php echo base_url('public/assets/js/scheduling/add.js'); ?>"></script>

<?php $this->endSection('content'); ?>
