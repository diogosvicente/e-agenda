<?php $this->extend('template/base'); ?>
<?php $this->section('content'); //echo "<pre>"; dd(print_r($units));?>

<link rel="stylesheet" href="<?php echo base_url('public/assets/css/scheduling/style.css'); ?>">

<section>
	<?php if (!empty($id)): ?>
		<h3 class="page-title">Editar Solicitação</h3>
	<?php else: ?>
		<h3 class="page-title">Formulário de Solicitação</h3>
	<?php endif; ?>

	<div class="col-md-12 mt-2">
		<form action="<?php echo base_url('scheduling/validate'); ?>" id="formScheduling" name="formScheduling" enctype="multipart/form-data" role="form" class="form-scheduling" method="post" accept-charset="utf-8">

        <?= csrf_field() ?>
        <script>
            window.loggedUser = <?= json_encode($userInfo); ?>;
            window.users = <?= json_encode($users); ?>;
        </script>
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
                            <div class="col-sm-9">
                                <label for="titulo_evento" class="form-label">Nome da Atividade / Evento: *</label>
                                <input value="Evento Teste 1" type="text" name="titulo_evento" id="titulo_evento" label="titulo_evento:" class="form-control" autocomplete="off" required="required" value="<?php echo (isset($registro->titulo_evento)) ? $registro->titulo_evento : '' ?>" />
                                <div id="divError-titulo_evento" class="invalid-feedback"></div>
                                <div id="divNotice-titulo_evento" class="notice-feedback"></div>
                            </div>
                            <div class="col-sm-3">
                                <label for="quantidade_participantes" class="form-label">Quantidade de Participantes: *</label>
                                <input value="42" type="text" name="quantidade_participantes" id="quantidade_participantes" label="quantidade_participantes:" class="form-control" autocomplete="off" required="required" value="<?php echo (isset($registro->quantidade_participantes)) ? $registro->quantidade_participantes : '' ?>" />
                                <div id="divError-quantidade_participantes" class="invalid-feedback"></div>
                                <div id="divNotice-quantidade_participantes" class="notice-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!--
                    * -------------------------------------------------------------------
                    * INÍCIO DA TAB 2: SOLICITANTE
                    * -------------------------------------------------------------------
                    -->
                    <div class="tab-pane fade" id="solicitante">
                        <!-- Solicitante -->
                        <fieldset class="fieldset-child">
                            <legend class="fieldset-child">Solicitante</legend>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" checked name="eu_sou_o_solicitante" value="S" id="eu_sou_o_solicitante" class="form-check-input" data-required="*" disabled />
                                        <label for="eu_sou_o_solicitante" class="form-check-label">
                                            Eu sou o solicitante
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Nome do Solicitante (apenas leitura, vindo do usuário logado) -->
                                <div class="col-sm-6">
                                    <input type="hidden" name="id_solicitante" id="id_solicitante" readonly />
                                    <label for="solicitante_nome" class="form-label">Nome: *</label>
                                    <input type="text" name="solicitante_nome" id="solicitante_nome" class="form-control" autocomplete="off" required="required" 
                                        value="<?php echo (isset($registro->solicitante_nome)) ? $registro->solicitante_nome : '' ?>" readonly />
                                    <div id="divError-solicitante_nome" class="invalid-feedback"></div>
                                    <div id="divNotice-solicitante_nome" class="notice-feedback"></div>
                                </div>
                                <!-- Unidade do Solicitante (select preenchido com as unidades) -->
                                <div class="col-sm-6">
                                    <input type="hidden" name="id_unidade_solicitante" id="id_unidade_solicitante" readonly />
                                    <label for="solicitante_unidade" class="form-label">Unidade: *</label>
                                    <select name="solicitante_unidade" id="solicitante_unidade" class="form-control" required="required" disabled >
                                        <option value="">Selecione a unidade</option>
                                        <?php if(isset($units) && is_array($units)): ?>
                                            <?php foreach($units as $unit): ?>
                                                <option value="<?php echo $unit['id']; ?>" <?php echo (isset($registro->solicitante_unidade) && $registro->solicitante_unidade == $unit['nome']) ? 'selected' : ''; ?>>
                                                    <?php echo $unit['nome']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div id="divError-solicitante_unidade" class="invalid-feedback"></div>
                                    <div id="divNotice-solicitante_unidade" class="notice-feedback"></div>
                                </div>
                            </div>
                        </fieldset>

                        <!-- Responsável -->
                        <fieldset class="fieldset-child">
                            <legend class="fieldset-child">Responsável</legend>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-check">
                                        <!-- O checkbox define se os dados do responsável serão os do usuário logado -->
                                        <input type="checkbox" name="eu_sou_o_responsavel" value="S" id="eu_sou_o_responsavel" class="form-check-input" />
                                        <label for="eu_sou_o_responsavel" class="form-check-label">
                                            Eu sou o responsável
                                        </label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check">
                                        <!-- O checkbox define se os dados do responsável serão de uma pessoa externa -->
                                        <input type="checkbox" name="responsavel_externo" value="S" id="responsavel_externo" class="form-check-input" />
                                        <label for="responsavel_externo" class="form-check-label">
                                            O responsável é externo
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Nome do Responsável - INTERNO -->
                                <div class="col-sm-6" id="grupo-responsavel-nome-interno">
                                    <input type="hidden" name="responsavel_nome_id" id="responsavel_nome_id" readonly>
                                    <label for="responsavel_nome" class="form-label">Nome: *</label>
                                    <select name="responsavel_nome" id="responsavel_nome" class="form-control" required onchange="fillResponsavelDetails()">
                                        <option value="">Selecione o responsável</option>
                                        <?php if(isset($users) && is_array($users)): ?>
                                            <?php foreach($users as $user): ?>
                                                <option value="<?php echo $user['id']; ?>" <?php echo (isset($registro->responsavel_nome) && $registro->responsavel_nome == $user['nome']) ? 'selected' : ''; ?>>
                                                    <?php echo $user['nome']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div id="divError-responsavel_nome" class="invalid-feedback"></div>
                                    <div id="divNotice-responsavel_nome" class="notice-feedback"></div>
                                </div>

                                <!-- Nome do Responsável - EXTERNO -->
                                <div class="col-sm-6" id="grupo-responsavel-nome-externo">
                                    <label for="responsavel_nome_externo" class="form-label">Nome: *</label>
                                    <input type="text" name="responsavel_nome_externo" id="responsavel_nome_externo" class="form-control" placeholder="Digite o nome do responsável externo">
                                    <div id="divError-responsavel_nome_externo" class="invalid-feedback"></div>
                                    <div id="divNotice-responsavel_nome_externo" class="notice-feedback"></div>
                                </div>

                                <!-- Unidade do Responsável - INTERNO -->
                                <div class="col-sm-6" id="grupo-responsavel-unidade-interno">
                                    <input type="hidden" name="responsavel_unidade_id" id="responsavel_unidade_id" readonly>
                                    <label for="responsavel_unidade" class="form-label">Unidade/Departamento: *</label>
                                    <select name="responsavel_unidade" id="responsavel_unidade" class="form-control" required disabled>
                                        <option value="">Selecione a unidade</option>
                                        <?php if(isset($units) && is_array($units)): ?>
                                            <?php foreach($units as $unit): ?>
                                                <option value="<?php echo $unit['id']; ?>" <?php echo (isset($registro->responsavel_unidade) && $registro->responsavel_unidade == $unit['nome']) ? 'selected' : ''; ?>>
                                                    <?php echo $unit['nome']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div id="divError-responsavel_unidade" class="invalid-feedback"></div>
                                    <div id="divNotice-responsavel_unidade" class="notice-feedback"></div>
                                </div>

                                <!-- Unidade do Responsável - EXTERNO -->
                                <div class="col-sm-6" id="grupo-responsavel-unidade-externo">
                                    <label for="responsavel_unidade_externo" class="form-label">Unidade/Departamento: *</label>
                                    <input type="text" name="responsavel_unidade_externo" id="responsavel_unidade_externo" class="form-control" placeholder="Digite a unidade do responsável externo">
                                    <div id="divError-responsavel_unidade_externo" class="invalid-feedback"></div>
                                    <div id="divNotice-responsavel_unidade_externo" class="notice-feedback"></div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- E-mail e Telefones do responsável -->
                                <div class="col-sm-4">
                                    <label for="responsavel_email" class="form-label">E-mail: *</label>
                                    <input type="text" name="responsavel_email" id="responsavel_email" class="form-control" autocomplete="off" required="required" 
                                        value="<?php echo (isset($registro->responsavel_email)) ? $registro->responsavel_email : '' ?>" readonly/>
                                    <div id="divError-responsavel_email" class="invalid-feedback"></div>
                                    <div id="divNotice-responsavel_email" class="notice-feedback"></div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="responsavel_telefone1" class="form-label">Telefone 1: *</label>
                                    <input type="text" name="responsavel_telefone1" id="responsavel_telefone1" class="form-control" autocomplete="off" required="required" 
                                        value="<?php echo (isset($registro->responsavel_telefone1)) ? $registro->responsavel_telefone1 : '' ?>" readonly/>
                                    <div id="divError-responsavel_telefone1" class="invalid-feedback"></div>
                                    <div id="divNotice-responsavel_telefone1" class="notice-feedback"></div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="responsavel_telefone2" class="form-label">Telefone 2: </label>
                                    <input type="text" name="responsavel_telefone2" id="responsavel_telefone2" class="form-control" autocomplete="off" 
                                        value="<?php echo (isset($registro->responsavel_telefone2)) ? $registro->responsavel_telefone2 : '' ?>" readonly/>
                                    <div id="divError-responsavel_telefone2" class="invalid-feedback"></div>
                                    <div id="divNotice-responsavel_telefone2" class="notice-feedback"></div>
                                </div>
                            </div>
                        </fieldset>

                        <!-- Aprovador -->
                        <fieldset class="fieldset-child">
                            <legend class="fieldset-child">Aprovador</legend>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-check">
                                        <!-- O checkbox define se os dados do aprovador serão os do usuário logado -->
                                        <input type="checkbox" name="eu_sou_o_aprovador" value="S" id="eu_sou_o_aprovador" class="form-check-input" data-required="*" />
                                        <label for="eu_sou_o_aprovador" class="form-check-label">
                                            Eu sou o aprovador
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Select para escolher o aprovador dentre os usuários do sistema pai -->
                                <div class="col-sm-6">
                                    <input type="hidden" name="aprovador_nome_id" id="aprovador_nome_id" readonly>
                                    <label for="aprovador_nome" class="form-label">Nome: *</label>
                                    <select name="aprovador_nome" id="aprovador_nome" class="form-control" required="required" onchange="fillAprovadorDetails()">
                                        <option value="">Selecione o aprovador</option>
                                        <?php if(isset($users) && is_array($users)): ?>
                                            <?php foreach($users as $user): ?>
                                                <option value="<?php echo $user['id']; ?>" <?php echo (isset($registro->aprovador_nome) && $registro->aprovador_nome == $user['nome']) ? 'selected' : ''; ?>>
                                                    <?php echo $user['nome']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div id="divError-aprovador_nome" class="invalid-feedback"></div>
                                    <div id="divNotice-aprovador_nome" class="notice-feedback"></div>
                                </div>
                                <!-- Unidade/Departamento do aprovador -->
                                <div class="col-sm-6">
                                    <input type="hidden" name="aprovador_unidade_id" id="aprovador_unidade_id" readonly>
                                    <label for="aprovador_unidade" class="form-label">Unidade/Departamento: *</label>
                                    <select name="aprovador_unidade" id="aprovador_unidade" class="form-control" required="required" disabled >
                                        <option value="">Selecione a unidade</option>
                                        <?php if(isset($units) && is_array($units)): ?>
                                            <?php foreach($units as $unit): ?>
                                                <option value="<?php echo $unit['id']; ?>" <?php echo (isset($registro->aprovador_unidade) && $registro->aprovador_unidade == $unit['nome']) ? 'selected' : ''; ?>>
                                                    <?php echo $unit['nome']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div id="divError-aprovador_unidade" class="invalid-feedback"></div>
                                    <div id="divNotice-aprovador_unidade" class="notice-feedback"></div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- E-mail e Telefones do aprovador -->
                                <div class="col-sm-4">
                                    <label for="aprovador_email" class="form-label">E-mail: *</label>
                                    <input type="text" name="aprovador_email" id="aprovador_email" class="form-control" autocomplete="off" required="required" 
                                        value="<?php echo (isset($registro->aprovador_email)) ? $registro->aprovador_email : '' ?>" readonly/>
                                    <div id="divError-aprovador_email" class="invalid-feedback"></div>
                                    <div id="divNotice-aprovador_email" class="notice-feedback"></div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="aprovador_telefone1" class="form-label">Telefone 1: *</label>
                                    <input type="text" name="aprovador_telefone1" id="aprovador_telefone1" class="form-control" autocomplete="off" required="required" 
                                        value="<?php echo (isset($registro->aprovador_telefone1)) ? $registro->aprovador_telefone1 : '' ?>" readonly/>
                                    <div id="divError-aprovador_telefone1" class="invalid-feedback"></div>
                                    <div id="divNotice-aprovador_telefone1" class="notice-feedback"></div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="aprovador_telefone2" class="form-label">Telefone 2: </label>
                                    <input type="text" name="aprovador_telefone2" id="aprovador_telefone2" class="form-control" autocomplete="off" 
                                        value="<?php echo (isset($registro->aprovador_telefone2)) ? $registro->aprovador_telefone2 : '' ?>" readonly/>
                                    <div id="divError-aprovador_telefone2" class="invalid-feedback"></div>
                                    <div id="divNotice-aprovador_telefone2" class="notice-feedback"></div>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <!--
                    * -------------------------------------------------------------------
                    * INÍCIO DA TAB 3: ESPACOS
                    * -------------------------------------------------------------------
                    -->
                    <div class="tab-pane fade" id="espacos">
                        <fieldset class="fieldset-child" id="espacos-container">
                            <legend class="fieldset-child">Espaços</legend>
                            <div class="espaco-entry" data-espaco="0">
                                <fieldset class="fieldset-child">
                                    <legend class="fieldset-child">Espaço 1</legend>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label for="espacos_0_id" class="form-label">Espaço Disponível: *</label>
                                            <select name="espacos[0][id]" id="espacos_0_id" class="form-control espaco-select" required>
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
                                            <div id="divError-espacos_0_id" class="invalid-feedback"></div>
                                            <div id="divNotice-espacos_0_id" class="notice-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="datas_horarios">
                                        <div class="row mt-3 data-hora-entry">
                                            <div class="col-sm-4">
                                                <label for="espacos_0_data_inicio" class="form-label">Data Início: *</label>
                                                <input value="2025-04-04" type="date" name="espacos[0][data_inicio]" id="espacos_0_data_inicio" class="form-control" required>
                                                <div id="divError-espacos_0_data_inicio" class="invalid-feedback"></div>
                                                <div id="divNotice-espacos_0_data_inicio" class="notice-feedback"></div>
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="espacos_0_hora_inicio" class="form-label">Hora de Início: *</label>
                                                <input value="10:30" type="time" name="espacos[0][hora_inicio]" id="espacos_0_hora_inicio" class="form-control" required>
                                                <div id="divError-espacos_0_hora_inicio" class="invalid-feedback"></div>
                                                <div id="divNotice-espacos_0_hora_inicio" class="notice-feedback"></div>
                                            </div>
                                            <div class="col-sm-3">
                                                <label for="espacos_0_hora_fim" class="form-label">Hora de Fim: *</label>
                                                <input value="16:30" type="time" name="espacos[0][hora_fim]" id="espacos_0_hora_fim" class="form-control" required>
                                                <div id="divError-espacos_0_hora_fim" class="invalid-feedback"></div>
                                                <div id="divNotice-espacos_0_hora_fim" class="notice-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-sm-6">
                                            <button type="button" class="btn btn-success addDataHora">+ Adicionar Data/Hora</button>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>

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
                                <div id="recursos-gerais">
                                    <!-- Os recursos serão gerados via AJAX com a estrutura: 
                                        recursos[][id_espaco], recursos[][id_recurso] e recursos[][quantidade] -->
                                </div>
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
