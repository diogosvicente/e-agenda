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

        // Insere o bloco de assinatura eletrônica, se houver status com id 2 (significa aprovado/assinado)
        $html .= formatar_assinatura_eletronica_aprovador(array(
            'status'        => $status,
            'id_aprovador'  => $evento->id_aprovador,
            'created_at'    => $evento->created_at
        ));

        $html .= formatar_assinatura_eletronica_confirmado(array(
            'status'        => $status,
            'id_aprovador'  => $evento->id_aprovador,
            'created_at'    => $evento->created_at
        ));
        
        $html .= '</div>';
        return $html;
    }
}

if (!function_exists('formatar_assinatura_eletronica_aprovador')) {
    /**
     * Formata e retorna o bloco da assinatura eletrônica (semelhante à do SEI) para eventos assinados.
     *
     * Esta função busca em $status algum registro com id_status igual a 2 e, caso encontre,
     * gera um bloco HTML com a assinatura eletrônica. É importante que, no registro de status,
     * haja a informação do nome do aprovador (ex.: no campo "nome_aprovador") e a data da assinatura (ex.: "created_at").
     *
     * @param array $status Array de objetos de status do evento.
     *
     * @return string Bloco HTML da assinatura eletrônica ou string vazia se não houver.
     */
    function formatar_assinatura_eletronica_aprovador($data)
    {
        // Percorre os status para verificar se existe um status com id 2 (assinado)
        foreach ($data['status'] as $s) {
            if ($s->id_status == 2) {
                // Se houver, obtém os dados necessários
                $nomeAssinante   = isset($data['id_aprovador']) ? tradeNameByID($data['id_aprovador'], 'usuarios', 'nome') : 'Assinante Desconhecido';
                $dataAssinatura  = isset($s->created_at) ? date("d/m/Y H:i", strtotime($s->created_at)) : '';

                // Cria um SVG inline para a imagem de assinatura com a marca e-Prefeitura
                $svg = '<svg width="400" height="100" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0" y="0" width="400" height="100" fill="none" stroke="#000" stroke-width="2"/>
                    <text x="20" y="40" font-family="Arial, sans-serif" font-size="24" fill="#000">e-Prefeitura</text>
                    <text x="20" y="70" font-family="Arial, sans-serif" font-size="12" fill="#000">Documento assinado digitalmente</text>
                    <g transform="translate(300,20)">
                        <!-- Desenha a curva da parte superior do cadeado -->
                        <path d="M20,30 C20,10 60,10 60,30" fill="none" stroke="#000" stroke-width="2"/>
                        <!-- Corpo do cadeado com cantos arredondados -->
                        <rect x="20" y="30" width="40" height="40" rx="5" ry="5" fill="none" stroke="#000" stroke-width="2"/>
                        <!-- Chave do cadeado: círculo e linha -->
                        <circle cx="40" cy="50" r="3" fill="#000"/>
                        <line x1="40" y1="53" x2="40" y2="60" stroke="#000" stroke-width="2"/>
                    </g>
                </svg>';
                // Codifica o SVG para usar como Data URI
                $imgData = 'data:image/svg+xml;base64,' . base64_encode($svg);

                
                // Monta o bloco de assinatura semelhante ao do SEI
                $assinaturaHTML = '
                <div class="assinatura-eletronica" style="border:1px solid #000; padding:10px; margin-top:20px; font-size:10px;">
                <div style="text-align:right; font-size:9px; color:#555; margin-bottom:5px;">Assinatura do Diretor(a)</div>
                    <table style="width:100%; border-collapse: collapse;">
                        <tr style="vertical-align: middle;">
                            <td style="width:60px; text-align: center;">
                                <img src="' . $imgData . '" alt="Assinatura Eletrônica e-Prefeitura" style="height:50px;">
                            </td>
                            <td style="padding-left:10px;">
                                <strong>' . $nomeAssinante . '</strong><br>
                                Em: ' . $dataAssinatura . '<br>
                                <em>Documento assinado eletronicamente por meio de senha.</em>
                            </td>
                        </tr>
                    </table>
                </div>';
                
                // Retorna o bloco assim que encontrar o status 2
                return $assinaturaHTML;
            }
        }
        // Se não houver status de assinatura, retorna vazio.
        return "";
    }
}

if (!function_exists('formatar_assinatura_eletronica_confirmado')) {
    /**
     * Formata e retorna o bloco da assinatura eletrônica para eventos confirmados (status 4).
     *
     * Esta função verifica se há algum registro de status com id_status igual a 4 e, caso encontre,
     * gera um bloco HTML com a assinatura eletrônica de confirmação, utilizando a marca e-Prefeitura.
     * É importante que, no registro de status, haja a informação do nome do confirmador e a data da confirmação.
     *
     * @param array $data Dados passados com as chaves 'status', 'id_confirmador' e 'created_at'
     *
     * @return string Bloco HTML da assinatura eletrônica de confirmação ou string vazia se não houver.
     */
    function formatar_assinatura_eletronica_confirmado($data)
    {
        // Percorre os status para verificar se existe um status com id_status igual a 4 (confirmado)
        foreach ($data['status'] as $s) {
            if ($s->id_status == 4) {
                // Obtém os dados necessários
                $nomeConfirmador = 'DESEG/DIPOC (Divisão de Programação e Controle)';
                $dataConfirmacao = isset($s->created_at) ? date("d/m/Y H:i", strtotime($s->created_at)) : '';

                // Cria um SVG inline para a imagem de confirmação com a marca e-Prefeitura
                $svg = '<svg width="400" height="100" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0" y="0" width="400" height="100" fill="none" stroke="#000" stroke-width="2"/>
                    <text x="20" y="40" font-family="Arial, sans-serif" font-size="24" fill="#000">e-Prefeitura</text>
                    <text x="20" y="70" font-family="Arial, sans-serif" font-size="12" fill="#000">Documento confirmado digitalmente</text>
                    <g transform="translate(300,20)">
                        <!-- Desenha a curva da parte superior do cadeado -->
                        <path d="M20,30 C20,10 60,10 60,30" fill="none" stroke="#000" stroke-width="2"/>
                        <!-- Corpo do cadeado com cantos arredondados -->
                        <rect x="20" y="30" width="40" height="40" rx="5" ry="5" fill="none" stroke="#000" stroke-width="2"/>
                        <!-- Chave do cadeado: círculo e linha -->
                        <circle cx="40" cy="50" r="3" fill="#000"/>
                        <line x1="40" y1="53" x2="40" y2="60" stroke="#000" stroke-width="2"/>
                    </g>
                </svg>';
                // Codifica o SVG para usar como Data URI
                $imgData = 'data:image/svg+xml;base64,' . base64_encode($svg);

                // Monta o bloco de assinatura para confirmação, com um cabeçalho discreto informando o confirmador
                $assinaturaHTML = '
                <div class="assinatura-eletronica-confirmado" style="border:1px solid #000; padding:10px; margin-top:20px; font-size:10px;">
                    <div style="text-align:right; font-size:9px; color:#555; margin-bottom:5px;">Assinatura do Confirmador</div>
                    <table style="width:100%; border-collapse: collapse;">
                        <tr style="vertical-align: middle;">
                            <td style="width:60px; text-align: center;">
                                <img src="' . $imgData . '" alt="Assinatura Eletrônica e-Prefeitura Confirmado" style="height:50px;">
                            </td>
                            <td style="padding-left:10px;">
                                <strong>' . $nomeConfirmador . '</strong><br>
                                Em: ' . $dataConfirmacao . '<br>
                                <em>Documento assinado eletronicamente por meio de senha.</em>
                            </td>
                        </tr>
                    </table>
                </div>';

                return $assinaturaHTML;
            }
        }
        // Se não houver status de confirmação, retorna vazio.
        return "";
    }
}

