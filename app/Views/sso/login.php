<?php $this->extend('template/base'); ?>
<?php $this->section('content'); ?>

<style>
    main#pageContent {
        background-color: transparent;
        max-width: 540px;
    }

    section#loginPage {
        background-color: var(--bs-white);
        padding: calc(var(--bs-gutter-y) * 2);
    }

    section#loginPage:first-child {
        margin: 100px auto 0px auto;
    }

    @media (max-width: 767px) {
        section#loginPage:first-child {
            margin-top: 0px;
        }
    }
</style>

<section id="loginPage" class="rounded-2 shadow">
    <form id="formLoginSSO" name="formLoginSSO" role="form" class="form-login" method="post" accept-charset="utf-8">
        <?= csrf_field() ?>
        <input type="hidden" name="redirect" value="<?= esc($_GET['redirect'] ?? '') ?>" id="redirectUrl"/>
        <input type="hidden" name="baseUrl" value="<?php echo base_url(); ?>" id="baseUrl" />
        <input type="hidden" name="ssoBaseUrl" value="<?= esc($ssoBaseUrl) ?>" id="ssoBaseUrl" />
        <input type="hidden" name="idSistema" value="<?= esc($idSistema) ?>" id="idSistema" />

        <fieldset>
            <legend>Entrar com e-Prefeitura</legend>
            
            <!-- MENSAGENS GERAIS -->
            <div id="msgSucessoGeral" class="alert alert-success d-none"></div>
            <div id="msgAvisoGeral" class="alert alert-warning d-none"></div>
            <div id="msgErroGeral" class="alert alert-danger d-none"></div>

            <div class="row">
                <div class="col-12">
                    <label for="cpf" class="form-label">CPF 
                        <a href="#" class="bi-info-circle-fill"
                            data-bs-trigger="click"
                            data-bs-toggle="popover"
                            data-bs-content="Utilize seu CPF registrado no ID e-Prefeitura">
                        </a>
                    </label>
                    <input type="text" name="cpf" id="cpf" class="form-control mask-cpf" placeholder="Digite seu CPF" autocomplete="off" required value="12192209738" />
                    <div id="divError-cpf" class="invalid-feedback"></div>
                </div>
                <div class="col-12">
                    <label for="senha" class="form-label">Senha 
                        <i id="passwordEye" data-bs-toggle="tooltip" title="Exibir senha" class="bi-eye-fill"></i>
                    </label>
                    <input type="password" name="senha" id="senha" class="form-control" placeholder="Digite sua senha" autocomplete="off" required value="Teste@123456" />
                    <div id="divError-senha" class="invalid-feedback"></div>
                </div>
                <div class="col-12">
                    <button type="submit" id="btnValidateLogin" class="btn btn-primary float-end">Entrar</button>
                </div>
                <div class="col-12">
                    <button type="button" id="btnForgotPassword" class="btn btn-link btn-sm float-end">
                        <a href="<?php echo base_url('esqueceu_sua_senha'); ?>">Esqueci a senha</a>
                    </button>
                </div>
            </div><!-- .row -->
        </fieldset>
        <div style="display:none"><label>Preencha este campo</label><input type="text" name="honeypot" value=""/></div>
    </form>
</section>

<!-- LOADING -->
<div id="divLoading" class="text-center mt-3" style="display: none;">
    <span class="spinner-border text-primary"></span> Autenticando...
</div>

<?php $this->endSection(); ?>
