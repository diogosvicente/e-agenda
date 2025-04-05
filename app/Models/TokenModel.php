<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;

class TokenModel extends Model
{
    protected $table         = 'tokens';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'id_usuario',
        'token',
        'criado_em',
        'expira_em',
        'tipo'
    ];
    protected $useTimestamps = false;
    protected $returnType    = 'object';

    /**
     * Retorna a data/hora atual.
     *
     * @return DateTime
     */
    public function getNow(): DateTime
    {
        return new DateTime();
    }

    /**
     * Gera (ou atualiza) um token para o usuário, definindo a expiração via expira_em.
     * No sistema filho, o token é utilizado para aprovação da solicitação de agendamento.
     *
     * @param int $idUsuario
     * @param string $tipo
     * @return string|bool O token gerado ou false em caso de falha.
     */
    public function gerarToken(int $idUsuario, string $tipo = 'aprovacao', int $extraID = null)
    {
        $token  = bin2hex(random_bytes(16));
        
        // Se $extraID não for null, adiciona o valor antes do token
        if ($extraID !== null) {
            $token = $extraID . '.' . $token;
        }

        $agora  = $this->getNow();
        // Define expiração como uma data distante no futuro, para que o token não expire
        $expira = new DateTime('9999-12-31 23:59:59');

        $data = [
            'id_usuario' => $idUsuario,
            'token'      => $token,
            'criado_em'  => $agora->format('Y-m-d H:i:s'),
            'expira_em'  => $expira->format('Y-m-d H:i:s'),
            'tipo'       => $tipo,
        ];

        // Se já existir um token deste tipo para o usuário, atualiza-o
        /*$row = $this->where('id_usuario', $idUsuario)
                    ->where('tipo', $tipo)
                    ->first();

        if ($row) {
            $this->update($row->id, $data);
            return $token;
        }*/

        // Cria um novo registro na tabela de tokens
        if ($this->insert($data)) {
            return $token;
        }
        return false;
    }


    /**
     * Obtém um token se ele for válido (não expirado).
     * Se o token estiver expirado ou não existir, retorna null.
     *
     * @param string $token
     * @return object|null
     */
    public function obterTokenValido(string $token)
    {
        $row = $this->where('token', $token)->first();

        if (!$row) {
            return null;
        }

        $agora    = $this->getNow();
        $expiraEm = new DateTime($row->expira_em);

        if ($agora > $expiraEm) {
            return null;
        }

        return $row;
    }
}
