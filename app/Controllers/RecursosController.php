<?php

namespace App\Controllers;

use App\Models\RecursosModel;
use CodeIgniter\RESTful\ResourceController;

class RecursosController extends ResourceController
{
    protected $recursosModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->recursosModel = new RecursosModel();
    }

    /**
     * Lista todos os recursos disponíveis
     */
    public function index()
    {
        $recursos = $this->recursosModel->getRecursosComLocalizacao();
        return $this->respond(['success' => true, 'recursos' => $recursos]);
    }

    /**
     * Obtém os recursos de espaços ou prédios selecionados
     */
    public function getByEspacos()
    {
        $selecionados = $this->request->getPost('espacos');

        if (empty($selecionados)) {
            return $this->respond(['success' => false, 'message' => 'Nenhum espaço ou prédio selecionado.']);
        }

        $recursosPorEspaco = [];
        $recursosPorPredio = [];
        $recursosGerais = [];

        foreach ($selecionados as $item) {
            if (strpos($item, "P-") === 0) {
                // É um prédio (exemplo: P-3)
                $id_predio = intval(str_replace("P-", "", $item));
                $recursos = $this->recursosModel->getRecursosByPredio($id_predio);
                if (!empty($recursos)) {
                    $recursosPorPredio[$id_predio] = $recursos;
                }
            } else {
                // É um espaço normal (exemplo: 5)
                $id_espaco = intval($item);
                $recursos = $this->recursosModel->getRecursosByEspaco($id_espaco);
                if (!empty($recursos)) {
                    $recursosPorEspaco[$id_espaco] = $recursos;
                }
            }
        }

        // Recursos gerais (sem espaço ou prédio específico)
        $recursosGerais = $this->recursosModel->getRecursosGerais();

        return $this->respond([
            'success' => true,
            'recursos_por_espaco' => $recursosPorEspaco,
            'recursos_por_predio' => $recursosPorPredio,
            'recursos_gerais' => $recursosGerais
        ]);
    }

    /**
     * Obtém os recursos de um determinado prédio
     */
    public function getByPredio($id_predio)
    {
        $recursos = $this->recursosModel->getRecursosByPredio($id_predio);
        return $this->respond(['success' => true, 'recursos' => $recursos]);
    }

    /**
     * Obtém os recursos gerais (sem vínculo com um espaço ou prédio específico)
     */
    public function getRecursosGerais()
    {
        $recursos = $this->recursosModel->getRecursosGerais();
        return $this->respond(['success' => true, 'recursos' => $recursos]);
    }

    /**
     * Obtém todos os recursos disponíveis para um espaço
     */
    public function getDisponiveisPorEspaco($id_espaco)
    {
        $recursos = $this->recursosModel->getRecursosDisponiveis($id_espaco);
        return $this->respond(['success' => true, 'recursos' => $recursos]);
    }

    /**
     * Cria um novo recurso
     */
    public function create()
    {
        $data = $this->request->getJSON(true);

        if (!$this->validate([
            'nome'       => 'required|string|max_length[255]',
            'quantidade' => 'required|integer',
            'tipo'       => 'required|in_list[Audiovisual,Mobiliário]',
            'status'     => 'required|in_list[disponivel,em manutencao,indisponivel]',
        ])) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $this->recursosModel->insert($data);
        return $this->respondCreated(['success' => true, 'message' => 'Recurso criado com sucesso.']);
    }

    /**
     * Atualiza um recurso existente
     */
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        if (!$this->recursosModel->find($id)) {
            return $this->failNotFound('Recurso não encontrado.');
        }

        if (!$this->validate([
            'nome'       => 'required|string|max_length[255]',
            'quantidade' => 'required|integer',
            'tipo'       => 'required|in_list[Audiovisual,Mobiliário]',
            'status'     => 'required|in_list[disponivel,em manutencao,indisponivel]',
        ])) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $this->recursosModel->update($id, $data);
        return $this->respond(['success' => true, 'message' => 'Recurso atualizado com sucesso.']);
    }

    /**
     * Exclui um recurso pelo ID
     */
    public function delete($id = null)
    {
        if (!$this->recursosModel->find($id)) {
            return $this->failNotFound('Recurso não encontrado.');
        }

        $this->recursosModel->delete($id);
        return $this->respondDeleted(['success' => true, 'message' => 'Recurso removido com sucesso.']);
    }
}
