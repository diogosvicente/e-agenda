// Exibe o modal ao clicar no link
$("#openModal").click(function(e){
    e.preventDefault();
    let modal = new bootstrap.Modal(document.getElementById("modalTermo"));
    modal.show();
});

function printModal() {
    // Exibe o modal, caso ele não esteja visível
    let modal = new bootstrap.Modal(document.getElementById("modalTermo"));
    modal.show();
    
    // Aguarda um pequeno delay para a renderização completa com os estilos de impressão
    setTimeout(function(){
        window.print();
    }, 500); // ajuste o delay conforme necessário
}

$(document).ready(function(){

    $("#matricula, #id_funcional").keyup(function () {
        somenteNumero($(this));
    });

    // Lógica para navegação por abas (se aplicável)
    $('.nav.nav-tabs .nav-item a').click(function (e) {
        e.preventDefault();
        $('.nav.nav-tabs .nav-item a').removeClass('active').removeAttr('aria-current');
        $(this).addClass('active').attr('aria-current', 'page');
        let tabId = $(this).attr('href');
        $(tabId).addClass('show active').siblings().removeClass('show active');
    });

    let email = $("#email").val().toLowerCase();
    if (/@uerj\.br$/.test(email)) {
        // Se o e-mail terminar com @uerj.br, exibe os campos e os marca como obrigatórios
        $("#servidorFields").slideDown();
        $("#matricula, #id_funcional").prop("required", true);
    } else {
        // Caso contrário, oculta os campos e remove a obrigatoriedade
        $("#servidorFields").slideUp();
        $("#matricula, #id_funcional").prop("required", false);
    }

    // Toggle para o campo #senha
  $("#toggleSenha").click(function(){
    let input = $("#senha");
    let icon = $(this).find("i");
    if(input.attr("type") === "password"){
      input.attr("type", "text");
      icon.removeClass("fa-eye").addClass("fa-eye-slash");
    } else {
      input.attr("type", "password");
      icon.removeClass("fa-eye-slash").addClass("fa-eye");
    }
  });

  // Toggle para o campo #senha2
  $("#toggleSenha2").click(function(){
    let input = $("#senha2");
    let icon = $(this).find("i");
    if(input.attr("type") === "password"){
      input.attr("type", "text");
      icon.removeClass("fa-eye").addClass("fa-eye-slash");
    } else {
      input.attr("type", "password");
      icon.removeClass("fa-eye-slash").addClass("fa-eye");
    }
  });

  // Validação em tempo real da força da senha
  $("#senha").on("keyup", function(){
    let pwd = $(this).val();

    // Requisitos:
    let minLength    = pwd.length >= 8;
    let hasUpperCase = /[A-Z]/.test(pwd);
    let hasLowerCase = /[a-z]/.test(pwd);
    let hasNumber    = /[0-9]/.test(pwd);
    let hasSpecial   = /[\W_]/.test(pwd); // caractere especial

    // Atualiza cada requisito:
    if(minLength){
      $("#reqLength").removeClass("text-danger").addClass("text-success");
    } else {
      $("#reqLength").removeClass("text-success").addClass("text-danger");
    }

    if(hasUpperCase){
      $("#reqUpper").removeClass("text-danger").addClass("text-success");
    } else {
      $("#reqUpper").removeClass("text-success").addClass("text-danger");
    }

    if(hasLowerCase){
      $("#reqLower").removeClass("text-danger").addClass("text-success");
    } else {
      $("#reqLower").removeClass("text-success").addClass("text-danger");
    }

    if(hasNumber){
      $("#reqNumber").removeClass("text-danger").addClass("text-success");
    } else {
      $("#reqNumber").removeClass("text-success").addClass("text-danger");
    }

    if(hasSpecial){
      $("#reqSpecial").removeClass("text-danger").addClass("text-success");
    } else {
      $("#reqSpecial").removeClass("text-success").addClass("text-danger");
    }
  });

});