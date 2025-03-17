<?php $this->extend('template/base'); ?>
<?php $this->section('content'); ?>

<section>
	<?php if (!empty($id)): ?>
		<h3 class="page-title">Editar Solicitação</h3>
	<?php else: ?>
		<h3 class="page-title">Formulário de Solicitação</h3>

	<?php endif; ?>

<div class="row">
	<div class="col-12 d-flex flex-row justify-content-end py-2 py-sm-0">
		<a href="<?php echo base_url('legado/inserir'); ?>">
			<button class="btn btn-primary"> Iniciar Novo Cadastro</button>
		</a>
	</div>
</div>

<form action="<?php echo base_url('legado/legadoValidade'); ?>" id="formLegado" name="formLegado" enctype="multipart/form-data" role="form" class="form-legado" method="post" accept-charset="utf-8">

	<!--
	 * -------------------------------------------------------------------
	 * ID_ARQUIVO SE FOR EDIÇÃO
	 * -------------------------------------------------------------------
	-->
	<input type="hidden" name="id_scheduling" id="id_scheduling" autocomplete="off" value="<?php echo (isset($registro->id)) ? $registro->id : '' ?>" />

	<div class="col-md-12">
		<fieldset>
			<legend>Dados do Agendamento</legend>
                <div class="row">
                    <div class="col-sm-4">
                        <label for="solicitante" class="form-label">Solicitante: *</label>
                        <input type="text" name="solicitante" id="solicitante" label="Solicitante:" class="form-control" autocomplete="off" required="required" value="<?php echo (isset($registro->solicitante)) ? $registro->solicitante : '' ?>" />
                        <div id="divError-solicitante" class="invalid-feedback"></div>
                        <div id="divNotice-solicitante" class="notice-feedback"></div>
                    </div>
                    <div class="col-sm-4">
                        <label for="solicitante" class="form-label">Solicitante: *</label>
                        <input type="text" name="solicitante" id="solicitante" label="Solicitante:" class="form-control" autocomplete="off" required="required" value="<?php echo (isset($registro->solicitante)) ? $registro->solicitante : '' ?>" />
                        <div id="divError-solicitante" class="invalid-feedback"></div>
                        <div id="divNotice-solicitante" class="notice-feedback"></div>
                    </div>
                    <div class="col-sm-4">
                        <label for="solicitante" class="form-label">Solicitante: *</label>
                        <input type="text" name="solicitante" id="solicitante" label="Solicitante:" class="form-control" autocomplete="off" required="required" value="<?php echo (isset($registro->solicitante)) ? $registro->solicitante : '' ?>" />
                        <div id="divError-solicitante" class="invalid-feedback"></div>
                        <div id="divNotice-solicitante" class="notice-feedback"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label for="responsavel" class="form-label">Responsavel: *</label>
                        <input type="text" name="responsavel" id="responsavel" label="Responsavel:" class="form-control" autocomplete="off" required="required" value="<?php echo (isset($registro->responsavel)) ? $registro->responsavel : '' ?>" />
                        <div id="divError-responsavel" class="invalid-feedback"></div>
                        <div id="divNotice-responsavel" class="notice-feedback"></div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-sm-2">
                        <label for="Prancha" class="form-label">Prancha:</label>
                        <input type="text" name="prancha" id="prancha" label="Prancha:" class="form-control" autocomplete="off" value="<?php echo (isset($registro->prancha)) ? $registro->prancha : '' ?>"/>
                    </div>
                    <div class="col-sm-10">
                        <label for="unidade" class="form-label">Unidade:</label>
                        <input type="text" name="unidade" id="unidade" label="Unidade:" class="form-control" autocomplete="off" value="<?php echo (isset($registro->unidade)) ? $registro->unidade : '' ?>"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label for="projeto" class="form-label">Projeto:</label>
                        <input type="text" name="projeto" id="projeto" label="Projeto:" class="form-control" autocomplete="off" value="<?php echo (isset($registro->projeto)) ? $registro->projeto : '' ?>"/>
                    </div>
                    <div class="col-sm-6">
                        <label for="assunto" class="form-label">Assunto:</label>
                        <input type="text" name="assunto" id="assunto" label="Assunto:" class="form-control" autocomplete="off" value="<?php echo (isset($registro->assunto)) ? $registro->assunto : '' ?>"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <label for="documento_ci_mm_etc" class="form-label">Documento (CI, MM, etc):</label>
                        <input type="text" name="documento_ci_mm_etc" id="documento_ci_mm_etc" label="Documento (CI, MM, etc):" class="form-control" autocomplete="off" value="<?php echo (isset($registro->documento_ci_mm_etc)) ? $registro->documento_ci_mm_etc : '' ?>"/>
                    </div>
                    <div class="col-sm-4">
                        <label for="departamento" class="form-label">Departamento (criador):</label>
                        <input type="text" name="departamento" id="departamento" label="Departamento:" class="form-control" autocomplete="off" value="<?php echo (isset($registro->departamento)) ? $registro->departamento : '' ?>"/>
                    </div>
                    <div class="col-sm-4">
                        <label for="data" class="form-label">Data:</label>
                        <input type="date" name="data" id="data" label="Data:" class="form-control" autocomplete="off" value="<?php echo (isset($registro->data)) ? $registro->data : '' ?>"/>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-sm-12">
                        <label for="observacoes" class="form-label">Observações:</label>
                        <textarea name="observacoes" id="observacoes" label="Observações" class="form-control" autocomplete="off" /><?php echo (isset($registro->observacoes)) ? $registro->observacoes : '' ?></textarea>
                    </div>
                </div>
                <hr>
                
                <div class="row">
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" checked name="flg_confirmacao_envio" value="S" id="flg_confirmacao_envio" class="form-check-input" data-required="*" />
                            <label for="flg_confirmacao_envio" class="form-check-label">
                            Estou ciente de que tomei os cuidados necessários e as informações cadastradas condizem com a realidade. <span class="requerido">*</span>
                            </label>
                        </div>
                        <div id="divError-flg_confirmacao_envio" class="invalid-feedback"></div>
                        <div id="divNotice-flg_confirmacao_envio" class="notice-feedback"></div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-3 d-flex flex-row justify-content-start py-2 py-sm-0">
                        <a href="<?php echo base_url('legado/inserir'); ?>" class="btn btn-primary">Iniciar Novo Cadastro</a>
                    </div>
                    <div class="col-9 d-flex flex-row justify-content-end py-2 py-sm-0">
                        <button name="btnLimpar" type="button" id="btnLimpar" value="true" class="btn btn-warning">Limpar</button>
                        <button name="btnCancel" type="button" id="btnCancel" value="true" class="btn btn-danger">Cancelar</button>
                        <button name="btnValidateArquivo" type="button" id="btnValidateArquivo" value="true" class="btn btn-primary">Salvar</button>
                    </div>
                </div>
			</div>
		</fieldset>
    </form>
</section>

<?php $this->endSection('content'); ?>