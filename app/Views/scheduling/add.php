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

        <?= csrf_field() ?>
        <input type="hidden" name="baseUrl" value="<?php echo base_url(); ?>" id="baseUrl" />

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
                    <input type="hidden" name="id_evento" id="id_evento" autocomplete="off" value="<?php echo (isset($registro->id_evento)) ? $registro->id_evento : '' ?>" />

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
                                <label for="titulo_evento" class="form-label">Nome da Atividade / Evento: *</label>
                                <input type="text" name="titulo_evento" id="titulo_evento" label="titulo_evento:" class="form-control" autocomplete="off" required="required" value="<?php echo (isset($registro->titulo_evento)) ? $registro->titulo_evento : '' ?>" />
                                <div id="divError-titulo_evento" class="invalid-feedback"></div>
                                <div id="divNotice-titulo_evento" class="notice-feedback"></div>
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
                                    <label for="solicitante_unidade" class="form-label">Unidade: *</label>
                                    <input type="text" name="solicitante_unidade" id="solicitante_unidade" class="form-control" autocomplete="off" required="required" 
                                        value="<?php echo (isset($registro->solicitante_unidade)) ? $registro->solicitante_unidade : '' ?>" />
                                    <div id="divError-solicitante_unidade" class="invalid-feedback"></div>
                                    <div id="divNotice-solicitante_unidade" class="notice-feedback"></div>
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
                                    <label for="responsavel_nome" class="form-label">Nome: *</label>
                                    <input type="text" name="responsavel_nome" id="responsavel_nome" class="form-control" autocomplete="off" required="required" 
                                        value="<?php echo (isset($registro->responsavel_nome)) ? $registro->responsavel_nome : '' ?>" />
                                    <div id="divError-responsavel_nome" class="invalid-feedback"></div>
                                    <div id="divNotice-responsavel_nome" class="notice-feedback"></div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="responsavel_unidade" class="form-label">Unidade: *</label>
                                    <input type="text" name="responsavel_unidade" id="responsavel_unidade" class="form-control" autocomplete="off" required="required" 
                                        value="<?php echo (isset($registro->responsavel_unidade)) ? $registro->responsavel_unidade : '' ?>" />
                                    <div id="divError-responsavel_unidade" class="invalid-feedback"></div>
                                    <div id="divNotice-responsavel_unidade" class="notice-feedback"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="responsavel_email" class="form-label">E-mail: *</label>
                                    <input type="text" name="responsavel_email" id="responsavel_email" class="form-control" autocomplete="off" required="required" 
                                        value="<?php echo (isset($registro->responsavel_email)) ? $registro->responsavel_email : '' ?>" />
                                    <div id="divError-responsavel_email" class="invalid-feedback"></div>
                                    <div id="divNotice-responsavel_email" class="notice-feedback"></div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="responsavel_telefone1" class="form-label">Telefone 1: *</label>
                                    <input type="text" name="responsavel_telefone1" id="responsavel_telefone1" class="form-control" autocomplete="off" required="required" 
                                        value="<?php echo (isset($registro->responsavel_telefone1)) ? $registro->responsavel_telefone1 : '' ?>" />
                                    <div id="divError-responsavel_telefone1" class="invalid-feedback"></div>
                                    <div id="divNotice-responsavel_telefone1" class="notice-feedback"></div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="responsavel_telefone2" class="form-label">Telefone 2: </label>
                                    <input type="text" name="responsavel_telefone2" id="responsavel_telefone2" class="form-control" autocomplete="off" 
                                        value="<?php echo (isset($registro->responsavel_telefone2)) ? $registro->responsavel_telefone2 : '' ?>" />
                                    <div id="divError-responsavel_telefone2" class="invalid-feedback"></div>
                                    <div id="divNotice-responsavel_telefone2" class="notice-feedback"></div>
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
                                                    <optgroup label="<?php echo esc($campusItem->nome); ?>">
                                                        <?php foreach ($campusItem->predios as $predio): ?>
                                                            <?php if (!empty($predio->espacos)): ?>
                                                                <optgroup label="➝ <?php echo esc($predio->nome); ?>">
                                                                    <?php foreach ($predio->espacos as $espaco): ?>
                                                                        <option value="<?php echo esc($espaco->id); ?>">
                                                                            <?php echo esc($espaco->nome) . " (Capacidade: " . esc($espaco->capacidade) . ")"; ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </optgroup>
                                                            <?php else: ?>
                                                                <option value="P-<?php echo esc($predio->id); ?>">
                                                                    <?php echo esc($predio->nome); ?>
                                                                </option>
                                                            <?php endif; ?>
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
                    * -------------------------------------------------------------------
                    -->
                    <!-- INÍCIO DA TAB 4: RECURSOS -->
                    <div class="tab-pane fade" id="recursos">
                        <h4 class="mt-3">Selecione os Recursos Necessários</h4>

                        <!-- Mensagem de aviso -->
                        <div id="mensagem-recursos" class="alert alert-info">
                            Selecione um espaço ou prédio primeiro para visualizar os recursos disponíveis.
                        </div>

                        <div id="recursos-lista" style="display: none;">
                            <fieldset class="fieldset-child">
                                <legend class="fieldset-child">Recursos Disponíveis</legend>
                                <div id="recursos-gerais"></div>
                            </fieldset>
                        </div>
                    </div>

                </div>
            </fieldset> <!-- fim do fieldset do tabbar -->
            <div class="row">
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" checked name="flg_confirmacao_envio" value="S" id="flg_confirmacao_envio" class="form-check-input" data-required="*" required />
                        <label for="flg_confirmacao_envio" class="form-check-label">
                            <b>Texto de Confirmação aqui</b><br>
                                Exiba aqui um texto para o usuário sobre a confirmação dos dados acima!
                        </label>
                    </div>
                    <div id="divError-flg_confirmacao_envio" class="invalid-feedback"></div>
                    <div id="divNotice-flg_confirmacao_envio" class="notice-feedback"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 d-flex flex-row justify-content-end py-2 py-sm-0">
                    <button name="btnLimpar" type="button" id="btnLimpar" value="true" class="btn btn-warning">Limpar</button>
                    <button name="btnCancel" type="button" id="btnCancel" value="true" class="btn btn-danger">Cancelar</button>
                    <button name="btnValidateEvento" type="button" id="btnValidateEvento" value="true" class="btn btn-primary">Salvar</button>
                </div>
            </div>
		</form>
	</div>
</section>

<script src="<?php echo base_url('public/assets/js/scheduling/add.js'); ?>"></script>
<script src="<?php echo base_url('public/assets/js/scheduling/validate.js'); ?>"></script>

<?php $this->endSection('content'); ?>
