<!doctype html>
<html lang="pt-br">
<head>
	<!-- head -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="E-AGENDA">
	<meta name="author" content="Prefeitura dos CAMPI (PREFEI)">
	<meta name="Access-Control-Allow-Origin" content="*">
	<meta name="base-url" content="<?= base_url(); ?>">
	<meta name="sso-base-url" content="<?= $ssoBaseUrl; ?>">
	<meta name="id-sistema" content="<?= $idSistema; ?>">
	<meta name="jwt-token" content="<?= isset($_COOKIE['jwt_token']) ? $_COOKIE['jwt_token'] : ''; ?>">

	<title>E-AGENDA</title>

	<!-- Estilos -->
	<link href="<?php echo base_url('public/assets/images/favicon.png'); ?>" rel="icon" type="image/png" /><!-- Bootstrap core CSS -->
	<link href="<?php echo base_url('public/assets/css/bootstrap.css'); ?>" rel="stylesheet" type="text/css" /><!-- Bootstrap Icons -->
	<link href="<?php echo base_url('public/assets/vendor/bootstrap-icons/font/bootstrap-icons.css'); ?>" rel="stylesheet" type="text/css" />
	<!-- Font Awesome -->
	<link href="<?php echo base_url('public/assets/vendor/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet">
	<!-- Folha de estilos padrão do sistema -->
	<link href="<?php echo base_url('public/assets/css/style_system.css'); ?>" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url('public/assets/css/style.css'); ?>" rel="stylesheet" type="text/css" />
	<!-- Bandeiras dos países (www.freakflagsprite.com) -->
	<link href="<?php echo base_url('public/assets/css/freakflags.css'); ?>" rel="stylesheet" type="text/css" />
	<!-- Regras de estilo para acessibilidade (alto contraste) -->
	<link href="<?php echo base_url('public/assets/css/accessibility.css'); ?>" rel="stylesheet" type="text/css" />
	<!-- Regras CSS para impressão -->
	<link href="<?php echo base_url('public/assets/css/print.css'); ?>" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url('public/assets/css/jquery-ui.css'); ?>" rel="stylesheet" type="text/css" />
	<!-- Data Tables -->
	<link href="<?php echo base_url('public/assets/vendor/datatables/dataTables.dataTables.css'); ?>" rel="stylesheet" type="text/css" />
	<!-- Tags Input -->
	<link href="<?php echo base_url('public/assets/css/bootstrap-tagsinput.css'); ?>" rel="stylesheet" type="text/css" />

	<script src="<?php echo base_url('public/assets/js/jquery.js'); ?>" type="text/javascript"></script>
	<!-- FIM - head -->
	<!-- Estilo da página -->
	<!-- Fim - Estilo da página -->

</head>
<body class="">
	<!-- Header -->
	<header>
		<div class="navbar">
			<div class="container ">
				<a href="<?php echo base_url(); ?>" class="navbar-brand marca">
					<span class="logo-uerj">
						<img src="<?php echo base_url('public/assets/images/logo_uerj_branca.png'); ?>" alt="">
					</span>
					<div class="titulo">
						<p class="sigla">E-AGENDA</p>
						<p class="nome">Prefeitura Digital</p>
					</div>
				</a>

				<!-- Menu de itens para melhorar a acessibilidade (tamanho da fonte e contraste) -->
				<div id="menuAcessibilidade">
					<span title="Restaurar fonte" data-bs-placement="bottom" data-bs-toggle="tooltip" class="font-reset">A</span>
					<span class="visually-hidden">Restaurar fonte</span>
					<span title="Diminuir fonte" data-bs-placement="bottom" data-bs-toggle="tooltip" class="font-minus">A-</span>
					<span class="visually-hidden">Diminuir fonte</span>
					<span title="Aumentar fonte" data-bs-placement="bottom" data-bs-toggle="tooltip" class="font-plus">A+</span>
					<span class="visually-hidden">Aumentar fonte</span>
					<span title="Alto contraste" data-bs-placement="bottom" data-bs-toggle="tooltip" class="contrast">
						<i class="bi-circle-half"></i><span class="visually-hidden">Alto contraste</span>
					</span>
				</div>
				<span id="btnAcessibilidade" title="Menu de acessibilidade">
					<svg xmlns="http://www.w3.org/2000/svg" height="1.25rem" viewBox="0 0 24 24" width="1.25rem" fill="currentColor">
						<path d="M0 0h24v24H0V0z" fill="none"/>
						<path d="M12 2c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm9 7h-6v13h-2v-6h-2v6H9V9H3V7h18v2z"/>
					</svg>
					<span class="visually-hidden">Menu de Acessibilidade</span>
				</span>
				<!-- FIM - Menu acessibilidade -->

				<!-- MENU OFFCANVAS -->
				<button 
					class="navbar-toggler"
					type="button"
					id="btnMenu"
					data-bs-toggle="offcanvas"
					data-bs-target="#menuPrincipal"
					aria-controls="menuPrincipal">
					<i class="bi-list"></i>
				</button>
				<div class="offcanvas offcanvas-end" data-bs-scroll="true" data-bs-backdrop="true" tabindex="-1" id="menuPrincipal">
					<div class="offcanvas-header">
						<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
						<h5 class="offcanvas-title" id="menuLateralLabel">Menu</h5>
					</div>
					<div class="offcanvas-body text-end">
						<nav>
							<ul>
								<li><a href="<?= base_url(); ?>">Página inicial</a></li>
								<?php if (!isset($_COOKIE['jwt_token'])) : ?>
									<li><a href="<?= base_url('login'); ?>">Entrar</a></li>
								<?php else : ?>
									<li><a href="<?= base_url('calendario'); ?>">Calendário</a></li>
									<li><a href="#" onclick="logout()">Sair</a></li>
								<?php endif; ?>
								<li><a href="<?= base_url('sobre'); ?>">Sobre o sistema</a></li>
							</ul>
						</nav>
					</div>
				</div>
				<!-- FIM - MENU OFFCANVAS -->
			</div>
		</div>
	</header>

	<?php if (isset($userInfo) && $userInfo): ?>
		<div class="container mt-0 is-logged">
			<div class="row">
				<div class="col-12 text-end">
					<span class="small float-end fst-italic">
						<!-- Exibe o nome do usuário -->
						<span class="usuario-informacoes">
							<?= esc($userInfo['nome']) ?>
							<a href="#" class="bi-info-circle-fill"
							data-bs-trigger="click"
							data-bs-toggle="popover"
							data-bs-placement="bottom"
							data-bs-content="Informações do usuário"
							title="Usuário Logado">
							</a>
						</span>
						<a href="#" onclick="logout()">
							<i class="bi-box-arrow-right"></i> Sair
						</a>
					</span>
				</div>
				<div id="usuario-detalhes" style="display: none;" class="col-12 text-end">
					e-Prefeitura: <?= esc(maskCPF($userInfo['cpf'])) ?>
				</div>
			</div>
		</div>
	<?php endif; ?>


	<div class="container caminho-migalhas">
		<!--
		<div class="row">
			<div class="col-12 mt-0 py-1">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb mb-0">
						<li class="breadcrumb-item"><a href="#"><i class="bi bi-house-fill"></i></a></li>
						<li class="breadcrumb-item"><a href="#">Caminho</a></li>
						<li class="breadcrumb-item"><a href="#">Menu</a></li>
						<li class="breadcrumb-item"><a href="#">SubMenu</a></li>
						<li class="breadcrumb-item active" aria-current="page">Tela</li>
					</ol>
				</nav>
			</div>
		</div>
		-->
	</div>
	<!-- FIM - Header -->

	<main id="pageContent" class="container">

		<!-- Conteúdo da página -->
			<?php echo $this->renderSection('content'); ?>
		<!-- FIM - Conteúdo da página -->

		<!--  Mensagens do Sistema  -->
		<div id="divLoading" class="col-12 alert" style="display: none;">
			<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <span class="load">Carregando dados...</span>
		</div>
		<div id="msgErroGeral" class="col-12 alert alert-danger alert-dismissible" role="alert" style="display: none;"></div>
		<div id="msgSucessoGeral" class="col-12 alert alert-success alert-dismissible" role="alert" style="display: none;"></div>
		<div id="msgAvisoGeral" class="col-12 alert alert-warning alert-dismissible" role="alert" style="display: none;"></div>    
		<!-- FIM - Mensagens do Sistema  -->
	</main>

	<?php
		// Captura o tempo de carregamento no final do conteúdo principal
		$end_time = microtime(true);
		$load_time = number_format($end_time - $_SERVER['REQUEST_TIME_FLOAT'], 2);
	?>

	<!-- footer -->
	 <footer>
		<div class="container">
			<div class="row justify-content-between">
				<div class="col-8 col-xs-12">
					E-AGENDA - <small><span class="idTela">v1</span></small>
					<br>
					<small>Página renderizada em <strong><?php echo $load_time; ?></strong> segundos.</small>
				</div>

				<div class="col-4 col-xs-12">
					&copy; 2024-2025. <a href="https://www.dgti.uerj.br/" target="_blank">DGTI</a>. Todos os direitos reservados<br>
					<strong><i>a clone from <a href="https://github.com/diogosvicente/dgti_template_clone" target="_blank" rel="noopener">PREFEI</a></i></strong>
				</div>
			</div>
		</div>
	</footer>
	<!-- FIM - footer -->

	<!-- Javascripts comuns a todas as páginas -->
	<!-- Scripts Javascript comuns a todas as páginas -->

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="<?php echo base_url('public/assets/js/html5shiv.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('public/assets/js/respond.min.js'); ?>" type="text/javascript"></script><![endif]-->

	<script src="<?php echo base_url('public/assets/js/bootstrap.bundle.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('public/assets/vendor/datatables/dataTables.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('public/assets/js/cleave.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('public/assets/js/jquery-ui.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('public/assets/js/tinymce.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('public/assets/js/theme.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('public/assets/js/pt_BR.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('public/assets/js/bootstrap-tagsinput.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('public/assets/js/jquery.maskedinput.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('public/assets/js/base.mask.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('public/assets/js/phpjs.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('public/assets/js/accessibility.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('public/assets/js/functions.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('public/assets/js/functions_layout.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('public/assets/js/layout.js'); ?>" type="text/javascript"></script>
	<!-- JS Auth JWT -->
	<script src="<?= base_url('public/assets/js/sso/auth.js'); ?>"></script>
	<!-- FIM - Javascripts comuns a todas as páginas -->

	<!-- Javascript da página -->
	<?php 
		$scripts = [
			'login'             => 'public/assets/js/login/login.js',
			'esqueceu_sua_senha'=> 'public/assets/js/login/forgotPassword.js',
			'primeiro_acesso'   => 'public/assets/js/login/firstAccess.js',
			'alterar_a_senha'   => 'public/assets/js/login/changePassword.js'
		];

		// Captura a string da URI, ex.: "primeiro_acesso/4026210d9cdd86f2c97cc1e084e0d3ca"
		$uri = uri_string();

		// Quebra a URI em segmentos separados por '/'
		$segments = explode('/', $uri);

		// Primeiro segmento (ou vazio, se não houver)
		$firstSegment = $segments[0] ?? '';

		// Se existir no array de scripts, carrega o arquivo correspondente
		if (isset($scripts[$firstSegment])) : 
	?>
		<script src="<?php echo base_url($scripts[$firstSegment]); ?>" type="text/javascript"></script>
	<?php endif; ?>
	<!-- FIM - Javascript da página -->

	</body>
</html>
