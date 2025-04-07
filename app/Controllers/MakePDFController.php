<?php

namespace App\Controllers;

use App\Models\CampusModel;
use App\Models\PredioModel;
use App\Models\EspacosModel;
use App\Models\RecursosModel;
use App\Models\EventoModel;
use App\Models\EventoEspacoDataHoraModel;
use App\Models\EventoRecursosModel;
use App\Models\EventoStatusModel;
use App\Models\EventoVerificacaoModel;
use App\Models\TokenModel;
use CodeIgniter\Controller;
use \Mpdf\Mpdf;

class MakePDFController extends BaseController
{
    protected $campusModel;
    protected $predioModel;
    protected $espacoModel;
    protected $recursoModel;
    protected $eventoModel;
    protected $eventoEspacoDataHoraModel;
    protected $eventoRecursosModel;
    protected $eventoStatusModel;
    protected $eventoVerificacaoModel;
    protected $tokenModel;
    protected $idSistema;
    protected $ssoBaseUrl;
    protected $userInfo;

    public function __construct()
    {
        $this->campusModel                  = new CampusModel();
        $this->predioModel                  = new PredioModel();
        $this->espacoModel                  = new EspacosModel();
        $this->recursoModel                 = new RecursosModel();
        $this->eventoModel                  = new EventoModel();
        $this->eventoEspacoDataHoraModel    = new EventoEspacoDataHoraModel();
        $this->eventoRecursosModel          = new EventoRecursosModel();
        $this->eventoStatusModel            = new EventoStatusModel();
        $this->eventoVerificacaoModel       = new EventoVerificacaoModel();
        $this->tokenModel                   = new TokenModel();

        $this->idSistema  = getenv('SISTEMA_ID');
        $this->ssoBaseUrl = getenv('SSO_BASE_URL');

        // Obtém os dados do usuário via helper (definido, por exemplo, em auth_helper.php)
        $this->userInfo = (isset($_COOKIE['jwt_token']) && !empty($_COOKIE['jwt_token'])) ? getUserInfo() : null;

        helper(['email_helper', 'evento_format_helper']);
        require_once ROOTPATH . '/vendor/autoload.php';
    }

    public function setHeader() {
		$logo = base_url('/public/assets/images/LOGO_PREFEITURA.jpg');
		return '
			<table width="100%" style="border-collapse: collapse;">
				<tr>
					<td width="100">
						<img src="'.$logo.'" width="170">
					</td>
					<td align="right">
						<p>UERJ - PREFEITURA DOS CAMPI&nbsp;</p>
						<p>e-Preitura | e-Agenda</p>
					</td>
				</tr>
			</table>
			<hr>';
	}

	public function setFooter() {
		return '<hr>
			<table width="100%" style="font-size: 11px;">
				<tr>
					<td width="33%">Impresso em {DATE j/m/Y à\s H:i:s}<br> Um Sistema e-Prefeitura UERJ</td>
					<td width="33%" align="center">{PAGENO}/{nbpg}</td>
					<td width="33%" style="text-align: right;">e-Agenda</td>
				</tr>
			</table>';
	}

	public function setPag1($evento, $datasHorarios, $recursos, $status) {
		return formatar_evento_aprovacao($evento, $datasHorarios, $recursos, $status);
	}

	public function generatePDF($token)
    {
        // Obtém o token válido
        $row = $this->tokenModel->obterTokenValido($token);

        if (!$row) {
            return view('errors/invalid_token', [
                'mensagem'   => 'O link fornecido é inválido, expirou ou já foi utilizado.',
                'ssoBaseUrl' => $this->ssoBaseUrl,
                'idSistema'  => $this->idSistema,
            ]);
        }

        // Verifica se o token expirou
        $expira_em   = $row->expira_em;
        $currentDate = date('Y-m-d H:i:s');
        if ($currentDate > $expira_em) {
            return view('errors/invalid_token', [
                'mensagem'   => 'O token expirou.',
                'ssoBaseUrl' => $this->ssoBaseUrl,
                'idSistema'  => $this->idSistema,
            ]);
        }

        // Verifica se o usuário logado tem o mesmo id do usuário associado ao token
        if (!isset($this->userInfo['id_usuario']) || $this->userInfo['id_usuario'] != $row->id_usuario) {
            return view('errors/invalid_token', [
                'mensagem'   => 'Você não tem permissão para confirmar essa solicitação.',
                'ssoBaseUrl' => $this->ssoBaseUrl,
                'idSistema'  => $this->idSistema,
            ]);
        }

        // Extrai o id do evento a partir do token (formato "id.token")
        $partes   = explode('.', $token);
        $eventoId = $partes[0];

        // Obtém os dados do evento e relacionados via models
        $evento        = $this->eventoModel->find($eventoId);
        $datasHorarios = $this->eventoEspacoDataHoraModel->where('id_evento', $eventoId)->findAll();
        $recursos      = $this->eventoRecursosModel->where('id_evento', $eventoId)->findAll();
        $status        = $this->eventoStatusModel->where('id_evento', $eventoId)->findAll();

        $this->response->setHeader('Content-Type', 'application/pdf');

		$mpdf = new \Mpdf\Mpdf(
			[
				'mode' => 'utf-8',
				'format' => 'A4-P',
				'margin_top' => 30,
				'margin_bottom' => 30,
				'default_font_size' => 8
			]
		);

		$mpdf->SetHTMLHeader($this->setHeader());
		$mpdf->SetHTMLFooter($this->setFooter());

		$mpdf->WriteHTML($this->setPag1($evento, $datasHorarios, $recursos, $status));

        // Exibe o PDF no navegador
        $filename = "relatorio.pdf";
        $mpdf->Output($filename, "I");
    }

    public function generatePDFFollowUp($token)
    {
        // Obtém o token válido
        $row = $this->eventoVerificacaoModel->obterTokenValido($token);

        if (!$row) {
            return view('errors/invalid_token', [
                'mensagem'   => 'O link fornecido é inválido, expirou ou já foi utilizado.',
                'ssoBaseUrl' => $this->ssoBaseUrl,
                'idSistema'  => $this->idSistema,
            ]);
        }

        // Extrai o id do evento a partir do token (formato "id.token")
        $partes   = explode('.', $token);
        $eventoId = $partes[0];

        // Obtém os dados do evento e relacionados via models
        $evento        = $this->eventoModel->find($eventoId);
        $datasHorarios = $this->eventoEspacoDataHoraModel->where('id_evento', $eventoId)->findAll();
        $recursos      = $this->eventoRecursosModel->where('id_evento', $eventoId)->findAll();
        $status        = $this->eventoStatusModel->where('id_evento', $eventoId)->findAll();

        $this->response->setHeader('Content-Type', 'application/pdf');

		$mpdf = new \Mpdf\Mpdf(
			[
				'mode' => 'utf-8',
				'format' => 'A4-P',
				'margin_top' => 30,
				'margin_bottom' => 30,
				'default_font_size' => 8
			]
		);

		$mpdf->SetHTMLHeader($this->setHeader());
		$mpdf->SetHTMLFooter($this->setFooter());

		$mpdf->WriteHTML($this->setPag1($evento, $datasHorarios, $recursos, $status));

        // Exibe o PDF no navegador
        $filename = "relatorio.pdf";
        $mpdf->Output($filename, "I");
    }
}
