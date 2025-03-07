let div_detalhes_usuario_logado = document.querySelector("#usuario-detalhes")

let span_nome_usuario_logado = document.querySelector("span[class='usuario-informacoes']")

if (span_nome_usuario_logado !== null) {

    span_nome_usuario_logado.addEventListener('mouseenter', () => {
        div_detalhes_usuario_logado.style.display = 'block';
    })

    span_nome_usuario_logado.addEventListener('mouseleave', () => {
        div_detalhes_usuario_logado.style.display = 'none';
    })
}
