/*
* Máscaras para campos de formulário utilizando a biblioteca cleave.js
*
* @see https://github.com/nosir/cleave.js
* Opções de uso:
* @see https://github.com/nosir/cleave.js/blob/master/doc/options.md
*
* */
document.addEventListener("DOMContentLoaded", function (e) {

	/*  Máscara de CPF para o campo input.mask-cpf */
	if (document.querySelectorAll('.mask-cpf').length > 0) {
		new Cleave('.mask-cpf', {
			blocks: [3, 3, 3, 2],
			delimiters: ['.', '.', '-'],
			numericOnly: true
		});
	}

	/*  Máscara de Telefone para o campo input.mask-telefone */
	if (document.querySelectorAll('.mask-telefone').length > 0) {
        document.querySelectorAll('.mask-telefone').forEach(function (el) {
            new Cleave(el, {
                delimiters: ['(', ') ', '-'],
                blocks: [0, 2, 5, 4],
                numericOnly: true
            });
        });
    }
});
