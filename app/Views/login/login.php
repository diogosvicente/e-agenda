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
    <form action="<?php echo base_url('login'); ?>" id="formLogin" name="formLogin" role="form" class="form-login" method="post" accept-charset="utf-8">
        <?php echo csrf_field() ?>
        <input type="hidden" name="baseUrl" value="<?php echo base_url(); ?>" id="baseUrl" />
        <fieldset>
            <legend>Entrar</legend>
            <div class="row">
                <div class="col-12">
                    <label for="login" class="form-label">Login <a href="#" class="bi-info-circle-fill"
                        data-bs-trigger="click"
                        data-bs-toggle="popover"
                        data-bs-content="Utilize as credenciais do ID e-Prefeitura para realizar o login"></a>
                    </label>
                    <input type="text" name="login" value="" id="login" label="Login" class="form-control mask-cpf" placeholder="Digite seu ID e-Prefeitura" autocomplete="off" />
                    <div id="divError-login" class="invalid-feedback"></div>
                    <div id="divNotice-login" class="notice-feedback"></div>
                </div>
                <div class="col-12">
                    <label for="password" class="form-label">Senha <i id="passwordEye"
                        data-bs-toggle="tooltip"
                        title="Exibir senha"
                        class="bi-eye-fill"></i>
                    </label>
                    <input type="password" name="password" value="" id="password" label="Senha" class="form-control" placeholder="Digite sua senha" autocomplete="off" />
                    <div id="divError-password" class="invalid-feedback"></div>
                    <div id="divNotice-password" class="notice-feedback"></div>
                </div>
                <div class="col-12">
                    <button name="btnValidateLogin" type="button" id="btnValidateLogin" value="true" class="btn btn-primary float-end">Entrar</button>
                    <button name="btnFirstAccess" type="button" id="btnFirstAccess" value="true" class="btn btn-secondary float-end">Primeiro acesso</button>
                </div>
                <div class="col-12">
                    <button name="btnForgotPassword" type="button" id="btnForgotPassword" value="true" class="btn btn-link btn-sm float-end">Esqueci a senha</button>
                </div>
                
            </div><!-- .row -->
        </fieldset>
    <div style="display:none"><label>Preencha este campo</label><input type="text" name="honeypot" value=""/></div>
    </form>
</section>

<?php $this->endSection(); ?>
