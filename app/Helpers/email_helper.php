<?php

use CodeIgniter\Email\Email;

helper('url');      // Garante que base_url() funcione corretamente
helper('sso');      // Carrega funções do SSO, como tradeNameByID
helper('db_transations'); // Caso necessário

if (!function_exists('enviar_email_aprovador')) {
    /**
     * Envia um e-mail ao aprovador com todos os dados da solicitação de agendamento.
     *
     * @param string $email       E-mail do aprovador.
     * @param string $token       Token exclusivo para acesso.
     * @param array  $eventoInfo  Array contendo os dados gerais, horários e recursos do evento.
     *
     * @return bool True se o e-mail for enviado com sucesso, false caso contrário.
     */
    function enviar_email_aprovador($email, $token, $eventoInfo)
    {
        // Para testes: exibe o array completo de dados do evento
        // echo "<pre>";
        // dd(print_r($eventoInfo, true));
        
        $emailService = \Config\Services::email(true);

        $emailService->setFrom('eprefeitura.uerj@gmail.com', 'e-Prefeitura');
        $emailService->setTo($email);
        $emailService->setSubject('e-Prefeitura - Confirmação de Solicitação de Agendamento');

        // Gera o link exclusivo para o aprovador (ajuste a rota conforme necessário)
        $url = base_url("aprovar_evento/$token");

        // Converte os IDs em nomes para solicitante e unidade
        $nomeSolicitante    = tradeNameByID($eventoInfo['id_solicitante'], 'usuarios', 'nome') 
                              ?? '[Nome do Solicitante]';
        $unidadeSolicitante = tradeNameByID($eventoInfo['id_unidade_solicitante'], 'unidades', 'nome') 
                              ?? '[Nome da Unidade]';

        // Dados do responsável
        if ($eventoInfo['id_responsavel'] == 0) {
            // Responsável informado manualmente
            $nomeResponsavel    = $eventoInfo['nome_responsavel'] ?? '[Nome do Responsável]';
            $unidadeResponsavel = $eventoInfo['nome_unidade_responsavel'] ?? '[Unidade/Departamento]';
        } else {
            $nomeResponsavel    = tradeNameByID($eventoInfo['id_responsavel'], 'usuarios', 'nome') 
                                ?? '[Nome do Responsável]';
            $unidadeResponsavel = tradeNameByID($eventoInfo['id_unidade_responsavel'], 'unidades', 'nome') 
                                ?? '[Unidade/Departamento]';
        }

        // Dados do aprovador
        $nomeAprovador    = tradeNameByID($eventoInfo['id_aprovador'], 'usuarios', 'nome') 
                          ?? '[Nome do Aprovador]';
        $unidadeAprovador = tradeNameByID($eventoInfo['id_unidade_aprovador'], 'unidades', 'nome') 
                          ?? '[Unidade/Departamento do Aprovador]';

        // Monta a lista de espaços e horários
        $espacosInfo = "";
        if (!empty($eventoInfo['horarios']) && is_array($eventoInfo['horarios'])) {
            $espacosInfo .= "<ul>";
            foreach ($eventoInfo['horarios'] as $horario) {
                $nomeEspaco = tradeNameByID($horario['id_espaco'], 'espacos', 'nome');
                if (!$nomeEspaco) {
                    $nomeEspaco = $horario['id_espaco'];
                }
                $espacosInfo .= "<li><strong>Espaço Solicitado:</strong> " . htmlspecialchars($nomeEspaco) .
                                " | <strong>Data:</strong> " . date("d/m/Y", strtotime($horario['data_hora_inicio'])) .
                                " | <strong>Horário:</strong> " . date("H:i", strtotime($horario['data_hora_inicio'])) .
                                " a " . date("H:i", strtotime($horario['data_hora_fim'])) . "</li>";
            }
            $espacosInfo .= "</ul>";
        } else {
            $espacosInfo = "<p>Nenhum espaço informado.</p>";
        }

        // Monta a lista de recursos solicitados
        $recursosInfo = "";
        if (!empty($eventoInfo['recursos']) && is_array($eventoInfo['recursos'])) {
            $recursosInfo .= "<ul>";
            foreach ($eventoInfo['recursos'] as $recurso) {
                $nomeRecurso = tradeNameByID($recurso['id_recurso'], 'recursos', 'nome');
                if (!$nomeRecurso) {
                    $nomeRecurso = $recurso['id_recurso'];
                }
                $recursosInfo .= "<li><strong>Recurso:</strong> " . htmlspecialchars($nomeRecurso) .
                                " | <strong>Quantidade:</strong> " . htmlspecialchars($recurso['quantidade']) . "</li>";
            }
            $recursosInfo .= "</ul>";
        } else {
            $recursosInfo = "<p>Nenhum recurso solicitado.</p>";
        }

        // Monta o corpo do e-mail conforme o padrão
        $message = "
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Confirmação de Solicitação de Agendamento</title>
        </head>
        <body style='font-family: Arial, sans-serif; color: #333;'>
            <p><strong>Assunto: Confirmação de Solicitação de Agendamento</strong></p>
            <br>
            <p>Prezado(a) <strong>" . htmlspecialchars($nomeSolicitante) . "</strong>,</p>
            <p>Sua solicitação de agendamento de espaço físico foi registrada com sucesso e aguarda autorização. Abaixo estão os detalhes informados no formulário:</p>
            <h4>Detalhes da Solicitação:</h4>
            <ul>
                <li><strong>Solicitante:</strong> " . htmlspecialchars($nomeSolicitante) . "</li>
                <li><strong>Unidade:</strong> " . htmlspecialchars($unidadeSolicitante) . "</li>
                <li><strong>Nome da Atividade/Evento:</strong> " . htmlspecialchars($eventoInfo['nome']) . "</li>
                <li><strong>Responsável pela Atividade:</strong> " . htmlspecialchars($nomeResponsavel) . " (" . htmlspecialchars($unidadeResponsavel) . ")</li>
                <li><strong>Quantidade de Participantes:</strong> " . htmlspecialchars($eventoInfo['quantidade_participantes']) . "</li>
                <li><strong>Observações Adicionais:</strong> " . htmlspecialchars($eventoInfo['observacoes']) . "</li>
            </ul>
            
            <h4>Espaço(s) e Horário(s) Solicitado(s):</h4>
            " . $espacosInfo . "
            
            <h4>Recurso(s) Solicitado(s):</h4>
            " . $recursosInfo . "
            
            <p>A solicitação de agendamento está agora em análise pela Direção da sua unidade, que poderá autorizar, negar ou editar o pedido por meio do link abaixo. Assim que houver uma decisão, você será notificado(a) por e-mail sobre a conclusão do processo e os dados serão encaminhados para o setor responsável pelo agendamento do espaço físico.</p>
            
            <p><a href='" . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . "' style='color: #0056b3; text-decoration: none; font-weight: bold;'>Clique aqui para acessar a solicitação</a></p>
            
            <br>
            <p>Atenciosamente,<br>
               [Nome da Instituição/Setor Responsável]<br>
               [E-mail de Contato]<br>
               [Telefone de Contato]
            </p>
        </body>
        </html>
        ";

        // Para testes, exibe o corpo do e-mail e interrompe a execução.
        echo "<pre>";
        dd(print_r($message));

        $emailService->setMessage($message);
        $emailService->setMailType('html');

        return $emailService->send();
    }
}
