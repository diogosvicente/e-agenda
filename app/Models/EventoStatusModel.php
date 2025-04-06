<?php

namespace App\Models;

use CodeIgniter\Model;

class EventoStatusModel extends Model
{
    protected $table = 'evento_status';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_evento', 'status', 'id_usuario', 'observacoes'];
    protected $returnType = 'object';

    /**
     * Insere um novo status para o evento indicando que a solicitação foi assinada pelo aprovador.
     *
     * @param int $idEvento
     * @return mixed ID do novo registro ou false em caso de falha.
     */
    public function inserirStatusAprovacao($idEvento, $idUsuario)
    {
        $data = [
            'id_evento'     => $idEvento,
            'status'        => 'solicitacao assinada pelo aprovador',
            'id_usuario'    => $idUsuario
        ];

        return $this->insert($data);
    }
}
