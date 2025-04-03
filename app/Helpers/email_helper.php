<?php

use CodeIgniter\Email\Email;

helper('url'); // Garante que base_url() funcione corretamente
helper('sso'); // Garante que base_url() funcione corretamente

if (!function_exists('enviar_email_aprovador')) {
    function enviar_email_aprovador($email, $token, $eventoInfo)
    {
        $emailService = \Config\Services::email(true);

        $emailService->setFrom('eprefeitura.uerj@gmail.com', 'e-Prefeitura');
        $emailService->setTo($email);
        $emailService->setSubject('e-Prefeitura - Confirmação de Solicitação de Agendamento');

        // Cria um link exclusivo para o aprovador. Ajuste a rota conforme necessário.
        $url = base_url("aprovar_evento/$token");

        $nomeSolicitante = tradeNameByID($eventoInfo['id_solicitante'], 'usuarios', 'nome');
        $unidadeSolicitante = tradeNameByID($eventoInfo['id_unidade_solicitante'], 'unidades', 'nome');

        if ($eventoInfo['id_responsavel'] == 0) {
            $nomeResponsavel = $eventoInfo['nome_responsavel'];
            $nomeResponsavel = $eventoInfo['nome_unidade_responsavel'];
        } else {
            $nomeResponsavel = tradeNameByID($eventoInfo['id_responsavel'], 'usuarios', 'nome');
            $unidadeResponsavel = tradeNameByID($eventoInfo['id_unidade_responsavel'], 'unidades', 'nome');
        }

        $nomeAprovador = tradeNameByID($eventoInfo['id_aprovador'], 'usuarios', 'nome');
        $unidadeAprovador = tradeNameByID($eventoInfo['id_unidade_aprovador'], 'unidades', 'nome');

        // Monta o corpo do e-mail com todas as informações do evento.
        // Você pode ajustar o layout conforme sua necessidade.
        $message = "
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Aprovação de Evento</title>
        </head>
        <body style='font-family: Arial, sans-serif; color: #333;'>
            <h3'><strong>Prezado " . htmlspecialchars($nomeSolicitante) . "</strong></h3>
            <p>Sua solicitação de agendamento de espaço físico foi registrada com sucesso e aguarda autorização. Abaixo estão os detalhes informados no formulário:</p>
            <br>
            <h3><strong>Detalhes da Solicitação:</strong></h3>
            <br>
            <ul>
                <li><strong>Solicitante:</strong> " . htmlspecialchars($nomeSolicitante) . "</li>
                <li><strong>Unidade:</strong> " . htmlspecialchars($unidadeSolicitante) . "</li>
                <li><strong>Nome da Atividade/Evento:</strong> " . htmlspecialchars($eventoInfo['nome']) . "</li>
                <li><strong>Responsável pela Atividade:</strong> " . htmlspecialchars($nomeResponsavel) . "</li>
            </ul>


            
            <h3>Espaços e Horários</h3>";
        // Lista os intervalos de data/hora
        if (!empty($eventoInfo['horarios']) && is_array($eventoInfo['horarios'])) {
            $message .= "<ul>";
            foreach ($eventoInfo['horarios'] as $horario) {
                $message .= "<li><strong>Espaço:</strong> " . htmlspecialchars($horario['id_espaco']) . 
                            " | <strong>Início:</strong> " . date("d/m/Y H:i", strtotime($horario['data_hora_inicio'])) .
                            " | <strong>Término:</strong> " . date("d/m/Y H:i", strtotime($horario['data_hora_fim'])) . "</li>";
            }
            $message .= "</ul>";
        }
        
        $message .= "<h3>Recursos Selecionados</h3>";
        if (!empty($eventoInfo['recursos']) && is_array($eventoInfo['recursos'])) {
            $message .= "<ul>";
            foreach ($eventoInfo['recursos'] as $recurso) {
                $message .= "<li><strong>Espaço:</strong> " . htmlspecialchars($recurso['id_espaco']) .
                            " | <strong>Recurso:</strong> " . htmlspecialchars($recurso['id_recurso']) .
                            " | <strong>Quantidade:</strong> " . htmlspecialchars($recurso['quantidade']) . "</li>";
            }
            $message .= "</ul>";
        }

        $message .= "<p>Para acessar os detalhes do evento e aprovar a solicitação, clique no link abaixo:</p>
            <p><a href='" . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . "' style='color: #0056b3; text-decoration: none; font-weight: bold;'>Aprovar Evento</a></p>
            <p>Atenciosamente,<br>e-Prefeitura</p>
        </body>
        </html>
        ";

        $emailService->setMessage($message);
        $emailService->setMailType('html');

        return $emailService->send();
    }
}
