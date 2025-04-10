<?php

namespace App\Models;

use CodeIgniter\Model;

class EventoStatusModel extends Model
{
    protected $table = 'evento_status';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_evento', 'id_status', 'id_usuario', 'observacoes'];
    protected $returnType = 'object';

    /**
     * Insere um novo status para o evento indicando que a solicitação foi assinada pelo aprovador.
     *
     * @param int $idEvento
     * @param int $idUsuario
     * @return mixed ID do novo registro ou false em caso de falha.
     */
    public function inserirStatusAprovacao($idEvento, $idUsuario)
    {
        $data = [
            'id_evento'  => $idEvento,
            'id_status'     => 2,
            'id_usuario' => $idUsuario
        ];

        return $this->insert($data);
    }
    
    /**
     * Obtém todos os status de um evento, ordenados pela ordem de inserção (ou por created_at se houver).
     *
     * @param int $idEvento
     * @return array|object|null
     */
    public function getStatusByEvento(int $idEvento)
    {
        $builder = $this->builder();
        // Seleciona os campos da tabela de status do evento e os campos da definição do status
        $builder->select('evento_status.*, status_definicao.nome AS status_nome, status_definicao.descricao AS status_descricao, status_definicao.ordem AS status_ordem');
        $builder->join('status_definicao', 'evento_status.id_status = status_definicao.id', 'left');
        $builder->where('evento_status.id_evento', $idEvento);
        // Ordena de acordo com o campo 'ordem' da tabela de definição ou por created_at
        $builder->orderBy('status_definicao.ordem', 'ASC');
        return $builder->get()->getResult();
    }

    public function getUltimoStatusByEvento(int $idEvento)
    {
        $builder = $this->builder();
        $builder->select('evento_status.*, status_definicao.nome AS status_nome, status_definicao.descricao AS status_descricao, status_definicao.ordem AS status_ordem');
        $builder->join('status_definicao', 'evento_status.id_status = status_definicao.id', 'left');
        $builder->where('evento_status.id_evento', $idEvento);
        // Ordena de forma decrescente para que o primeiro registro seja o último inserido
        $builder->orderBy('evento_status.created_at', 'DESC');
        
        return $builder->get()->getRow();
    }

}
