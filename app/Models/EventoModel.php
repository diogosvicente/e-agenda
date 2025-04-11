<?php

namespace App\Models;

use CodeIgniter\Model;

class EventoModel extends Model
{
    protected $table = 'eventos';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_solicitante',
        'id_unidade_solicitante',
        'id_responsavel',
        'nome_responsavel',
        'id_unidade_responsavel',
        'nome_unidade_responsavel',
        'email_responsavel',
        'telefone1_responsavel',
        'telefone2_responsavel',
        'id_aprovador',
        'id_unidade_aprovador',
        'email_aprovador',
        'telefone1_aprovador',
        'telefone2_aprovador',
        'nome',
        'quantidade_participantes',
        'assinado_solicitante',
        'assinado_componente_org',
        'observacoes'
    ];
    protected $returnType = 'object';

    public function getEventosWithEspacosAndPredios($isAdmin = false)
    {
        $builder = $this->select('
            eventos.id AS evento_id,
            eventos.nome AS evento_nome,
            evento_espaco_data_hora.id AS evento_espaco_id,
            evento_espaco_data_hora.id_espaco AS resource_id,
            evento_espaco_data_hora.data_hora_inicio AS start,
            evento_espaco_data_hora.data_hora_fim AS end,
            predio.id AS predio_id,
            predio.nome AS predio_nome,
            (SELECT es.id_status 
            FROM evento_status AS es 
            WHERE es.id_evento = eventos.id 
            ORDER BY es.created_at DESC 
            LIMIT 1) AS evento_status
        ')
        ->join('evento_espaco_data_hora', 'evento_espaco_data_hora.id_evento = eventos.id')
        ->join('predio', 'predio.id = evento_espaco_data_hora.id_predio', 'left');

        if (!$isAdmin) {
            // Se o usuário não for administrador, exibe apenas os eventos confirmados (status 4)
            $builder->where("EXISTS(SELECT 1 FROM evento_status WHERE evento_status.id_evento = eventos.id AND evento_status.id_status = 4)");
        }

        return $builder->findAll();
    }

    /**
     * Método para buscar eventos com token de verificação e status.
     * Traz:
     * - Nome do evento
     * - created_at (da tabela eventos)
     * - id_unidade_solicitante
     * - token (da tabela evento_verificacao)
     * - id_status, id_usuario e observacoes (da tabela evento_stauts)
     */
    public function getEventos()
    {
        $this->select('
            eventos.id AS evento_id,
            eventos.nome AS evento_nome,
            eventos.created_at,
            eventos.id_solicitante,
            eventos.id_unidade_solicitante,
            evento_verificacao.token,
            evento_status.id_status AS evento_status,
            evento_status.id_usuario AS usuario_status,
            evento_status.observacoes AS status_observacoes,
            status_definicao.nome AS nome_status
        ');
        
        $this->join('evento_verificacao', 'evento_verificacao.id_evento = eventos.id');
        $this->join('evento_status', 'evento_status.id_evento = eventos.id');
        $this->join('status_definicao', 'evento_status.id_status = status_definicao.id');
        
        // Restringe os registros de evento_status para aquele com o maior created_at para cada evento
        $this->where("evento_status.created_at = (
                    SELECT MAX(es2.created_at) 
                    FROM evento_status AS es2 
                    WHERE es2.id_evento = eventos.id
                )", null, false);
        
        return $this->findAll();
    }

    public function getEventosPorUsuario($id_usuario)
    {
        $this->select('
                eventos.id AS evento_id,
                eventos.nome AS evento_nome,
                eventos.created_at,
                eventos.id_solicitante,
                eventos.id_unidade_solicitante,
                evento_verificacao.token,
                evento_status.id_status AS evento_status,
                evento_status.id_usuario AS usuario_status,
                evento_status.observacoes AS status_observacoes,
                status_definicao.nome AS nome_status
            ')
            ->join('evento_verificacao', 'evento_verificacao.id_evento = eventos.id')
            ->join('evento_status', 'evento_status.id_evento = eventos.id')
            ->join('status_definicao', 'evento_status.id_status = status_definicao.id')
            ->where('eventos.id_solicitante', $id_usuario)
            ->where("evento_status.created_at = (
                    SELECT MAX(es2.created_at)
                    FROM evento_status AS es2
                    WHERE es2.id_evento = eventos.id
                )", null, false);
        
        return $this->findAll();
    }
}
