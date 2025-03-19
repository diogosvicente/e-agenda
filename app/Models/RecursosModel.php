<?php

namespace App\Models;

use CodeIgniter\Model;

class RecursosModel extends Model
{
    protected $table = 'recursos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_espaco', 'id_predio', 'nome', 'quantidade', 'tipo', 'status'];
    protected $returnType = 'object';

    /**
     * Obtém todos os recursos com os respectivos espaços ou prédios (se existirem)
     */
    public function getRecursosComLocalizacao()
    {
        return $this->select('recursos.*, 
                             espacos.nome as nome_espaco, 
                             predio.nome as nome_predio')
            ->join('espacos', 'espacos.id = recursos.id_espaco', 'left')
            ->join('predio', 'predio.id = recursos.id_predio', 'left')
            ->findAll();
    }

    /**
     * Obtém os recursos vinculados a espaços ou prédios selecionados
     */
    public function getRecursosByEspacos($espacosSelecionados)
    {
        // Separando IDs de espaços e prédios
        $ids_espacos = [];
        $ids_predios = [];

        foreach ($espacosSelecionados as $item) {
            if (is_string($item) && strpos($item, "P-") === 0) {
                $id_predio = intval(str_replace("P-", "", $item));
                if ($id_predio > 0) {
                    $ids_predios[] = $id_predio;
                }
            } else {
                $id_espaco = intval($item);
                if ($id_espaco > 0) {
                    $ids_espacos[] = $id_espaco;
                }
            }
        }

        return $this->select('recursos.*, espacos.nome as nome_espaco, predio.nome as nome_predio')
            ->join('espacos', 'espacos.id = recursos.id_espaco', 'left')
            ->join('predio', 'predio.id = recursos.id_predio', 'left')
            ->groupStart()
                ->whereIn('recursos.id_espaco', $ids_espacos ?: [0]) // Evita erro caso array esteja vazio
                ->orWhereIn('recursos.id_predio', $ids_predios ?: [0]) // Evita erro caso array esteja vazio
                ->orWhere('recursos.id_espaco IS NULL AND recursos.id_predio IS NULL') // Recursos gerais
            ->groupEnd()
            ->findAll();
    }

    /**
     * Obtém os recursos gerais sem vínculo com espaço ou prédio
     */
    public function getRecursosGerais()
    {
        return $this->where('id_espaco', NULL)
                    ->where('id_predio', NULL)
                    ->findAll();
    }

    /**
     * Obtém os recursos vinculados a um prédio específico
     */
    public function getRecursosByPredio($id_predio)
    {
        return $this->select('recursos.*, predio.nome as nome_predio')
            ->join('predio', 'predio.id = recursos.id_predio', 'left')
            ->where('recursos.id_predio', $id_predio)
            ->findAll();
    }

    /**
     * Obtém os recursos vinculados a um espaço específico
     */
    public function getRecursosByEspaco($id_espaco)
    {
        return $this->select('recursos.*, espacos.nome as nome_espaco')
            ->join('espacos', 'espacos.id = recursos.id_espaco', 'left')
            ->where('recursos.id_espaco', $id_espaco)
            ->findAll();
    }


    /**
     * Obtém os recursos disponíveis para um espaço, incluindo:
     * - Recursos do próprio espaço
     * - Recursos do prédio ao qual o espaço pertence
     * - Recursos gerais disponíveis para todos
     */
    public function getRecursosDisponiveis($id_espaco)
    {
        // Buscar o prédio ao qual esse espaço pertence
        $db = db_connect();
        $query = $db->query("SELECT id_predio FROM espacos WHERE id = ?", [$id_espaco]);
        $resultado = $query->getRow();
        $id_predio = $resultado ? $resultado->id_predio : null;

        return $this->select('recursos.*, espacos.nome as nome_espaco, predio.nome as nome_predio')
            ->join('espacos', 'espacos.id = recursos.id_espaco', 'left')
            ->join('predio', 'predio.id = recursos.id_predio', 'left')
            ->groupStart()
                ->where('recursos.id_espaco', $id_espaco) // Recursos do próprio espaço
                ->orWhere('recursos.id_predio', $id_predio) // Recursos do prédio
                ->orGroupStart()
                    ->where('recursos.id_espaco', NULL)
                    ->where('recursos.id_predio', NULL)
                ->groupEnd() // Recursos gerais
            ->groupEnd()
            ->findAll();
    }
}
