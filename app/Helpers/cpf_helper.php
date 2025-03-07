<?php

/**
 * Aplica a máscara de CPF no formato ###.###.###-##
 * 
 * @param string $cpf
 * @return string
 */
function maskCPF(string $cpf): string
{
    // Remove tudo que não for dígito
    $numeros = preg_replace('/\D/', '', $cpf);

    // Se não tiver 11 dígitos, retorna do jeito que está
    if (strlen($numeros) !== 11) {
        return $cpf;
    }

    // Formata no padrão ###.###.###-##
    return substr($numeros, 0, 3) . '.' 
         . substr($numeros, 3, 3) . '.' 
         . substr($numeros, 6, 3) . '-' 
         . substr($numeros, 9, 2);
}
