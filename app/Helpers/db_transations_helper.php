<?php

if (!function_exists('getNameById')) {
    /**
     * Retorna o valor de uma coluna específica de uma tabela para um determinado ID.
     *
     * @param mixed  $id    O ID do registro.
     * @param string $table O nome da tabela destino (somente tabelas permitidas).
     * @param string $field O nome da coluna que se deseja obter (somente colunas permitidas para a tabela).
     *
     * @return mixed|null Retorna o valor da coluna ou null em caso de erro/inexistência.
     */
    function getNameById($id, $table, $field)
    {
        // Lista de tabelas e colunas permitidas para consulta
        $allowed = [
            'espacos' => ['nome', 'sigla'],
            'predio' => ['nome', 'sigla'],
            'recursos' => ['nome'],
            'status_definicao' => ['nome', 'descricao'],
            // Adicione outras tabelas permitidas conforme necessário.
        ];
        
        // Valida se a tabela e a coluna estão na lista permitida
        if (!isset($allowed[$table]) || !in_array($field, $allowed[$table])) {
            return null;
        }
        
        $db = \Config\Database::connect();
        $builder = $db->table($table);
        $builder->select($field);
        $builder->where('id', $id);
        $result = $builder->get()->getRow();
        
        return $result ? $result->$field : null;
    }
}