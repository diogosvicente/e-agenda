<?php

if (!function_exists('formatar_evento_aprovacao')) {
    /**
     * Formata os dados do evento para exibição na tela de aprovação.
     *
     * @param object $evento Objeto com os dados gerais do evento (obtido via EventoModel).
     * @param array  $datasHorarios Array de objetos com os horários do evento (obtidos via EventoEspacoDataHoraModel).
     * @param array  $recursos Array de objetos com os recursos do evento (obtidos via EventoRecursosModel).
     * @param array  $status Array de objetos com os status do evento (obtidos via EventoStatusModel).
     *
     * @return string Bloco HTML com as informações formatadas.
     */
    function formatar_evento_aprovacao($evento, $datasHorarios, $recursos, $status)
    {
        $responsavel = "";
        if ($evento->id_responsavel == 0) {
            $nome_responsavel = $evento->nome_responsavel;
            $unidade_responsavel = $evento->nome_unidade_responsavel;
        } else {
            $nome_responsavel = tradeNameByID($evento->id_responsavel, 'usuarios', 'nome');
            $unidade_responsavel = tradeNameByID($evento->id_unidade_responsavel, 'unidades', 'nome');
        }

        $html = '<div class="evento-aprovacao">';
        
        // Dados gerais do evento
        $html .= '<h3>Detalhes do Evento</h3>';
        $html .= '<p><strong>Nome:</strong> ' . $evento->nome . '</p>';
        $html .= '<p><strong>Solicitante:</strong> ' . tradeNameByID($evento->id_solicitante, 'usuarios', 'nome') . '</p>';
        $html .= '<p><strong>Unidade do Solicitante:</strong> ' . tradeNameByID($evento->id_unidade_solicitante, 'unidades', 'nome') . '</p>';
        $html .= '<p><strong>Responsável:</strong> ' . $nome_responsavel . '</p>';
        $html .= '<p><strong>Unidade do Responsável:</strong> ' . $unidade_responsavel . '</p>';
        $html .= '<p><strong>Observações:</strong> ' . $evento->observacoes . '</p>';
        
        // Horários
        $html .= '<h3>Horários Solicitados</h3>';
        if (!empty($datasHorarios)) {
            $html .= '<ul class="horarios">';
            foreach ($datasHorarios as $horario) {
                $espaco = "";
                if (empty($horario->id_espaco)) {
                    $espaco = getNameById($horario->id_predio, 'predio', 'nome');
                } else {
                    $espaco = getNameById($horario->id_espaco, 'espacos', 'nome');
                }
                $data   = date("d/m/Y", strtotime($horario->data_hora_inicio));
                $inicio = date("H:i", strtotime($horario->data_hora_inicio));
                $fim    = date("H:i", strtotime($horario->data_hora_fim));
                $html .= '<li>Espaço: ' . $espaco . ' | Data: ' . $data . ' | Início: ' . $inicio . ' | Fim: ' . $fim . '</li>';
            }
            $html .= '</ul>';
        } else {
            $html .= '<p>Nenhum horário informado.</p>';
        }
        
        // Recursos
        $html .= '<h3>Recursos Solicitados</h3>';
        if (!empty($recursos)) {
            $html .= '<ul class="recursos">';
            foreach ($recursos as $recurso) {
                $nomeRecurso = getNameById($recurso->id_recurso, 'recursos', 'nome');
                $quantidade  = $recurso->quantidade;
                $html .= '<li>Recurso: ' . $nomeRecurso . ' | Quantidade: ' . $quantidade . '</li>';
            }
            $html .= '</ul>';
        } else {
            $html .= '<p>Nenhum recurso solicitado.</p>';
        }
        
        // Status
        $html .= '<h3>Status do Evento</h3>';
        if (!empty($status)) {
            $html .= '<ul class="status">';
            foreach ($status as $s) {
                $nomeStatus = "<strong>" . getNameById($s->id_status, 'status_definicao', 'nome') . "</strong> - <strong>" . getNameById($s->id_status, 'status_definicao', 'descricao') . "</strong>";
                $dataStatus = isset($s->created_at) ? date("d/m/Y H:i", strtotime($s->created_at)) : "";
                $html .= '<li>Status: ' . $nomeStatus . ' em ' . $dataStatus . '</li>';
            }
            $html .= '</ul>';
        } else {
            $html .= '<p>Sem status registrado.</p>';
        }
        
        $html .= '</div>';
        return $html;
    }
}
