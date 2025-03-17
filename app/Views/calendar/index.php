<?php $this->extend('template/base'); ?>
<?php $this->section('content'); ?>

<script src="<?php echo base_url('public/assets/vendor/fullcalendar-scheduler-6.1.15/dist/index.global.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- Adicionando jQuery -->

<script>
  $(document).ready(function () {
    $("#exitFullScreenBtn").hide();
    let calendarEl = document.getElementById('calendar');
    let today = new Date().toISOString().split('T')[0];

    $.getJSON("<?php echo base_url('calendario/data'); ?>", function (data) {
      
      // 🔹 Criar um mapa de espaços para buscas rápidas
      let espacosMap = {};
      data.resources.forEach(predio => {
          if (predio.children) {
              predio.children.forEach(espaco => {
                  espacosMap[espaco.id] = espaco.title; // Associa ID do espaço ao nome
              });
          }
      });

      let calendar = new FullCalendar.Calendar(calendarEl, {
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        locale: 'pt-br',
        initialDate: today,
        initialView: 'resourceTimelineDay',
        editable: true,
        selectable: true,
        nowIndicator: true,
        aspectRatio: 1.8,
        scrollTime: '08:00',
        headerToolbar: {
          left: 'today prev,next',
          center: 'title',
          right: 'resourceTimelineDay,resourceTimelineThreeDays,resourceTimelineWeek,resourceTimelineMonth,resourceTimelineYear,listWeek'
        },
        buttonText: {
          today: 'Hoje',
          month: 'Mês',
          week: 'Semana',
          day: 'Dia',
          list: 'Lista',
          year: 'Ano'
        },
        views: {
          resourceTimelineThreeDays: { type: 'resourceTimeline', duration: { days: 3 }, buttonText: '3 Dias' },
          resourceTimelineWeek: { type: 'resourceTimeline', duration: { weeks: 1 }, buttonText: 'Semana' },
          resourceTimelineMonth: { type: 'resourceTimeline', duration: { months: 1 }, buttonText: 'Mês' },
          resourceTimelineYear: { type: 'resourceTimeline', duration: { years: 1 }, buttonText: 'Ano' }
        },
        resourceGroupField: 'building',
        resources: data.resources,
        events: data.events,
        resourceOrder: false,

        // Evento de clique no evento
        eventClick: function (info) {
          let evento = info.event;
          let resourceId = evento.getResources()[0]?.id || "Desconhecido"; 

          // 🔹 Busca instantânea no mapa de espaços (O(1) complexidade)
          let espacoNome = espacosMap[resourceId] || "Não encontrado";

          // Preenche os dados no modal
          $("#eventTitle").text(evento.title);
          $("#eventStart").text(evento.start.toLocaleString());
          $("#eventEnd").text(evento.end ? evento.end.toLocaleString() : "Não definido");
          $("#eventResource").text(espacoNome);

          // Exibe o modal Bootstrap
          $("#eventModal").modal("show");
        }
      });

      calendar.render();
    });

    // Função para ativar modo tela cheia
    $("#fullScreenBtn").on("click", function () {
      $("#calendarContainer").addClass("full-screen");
      $("#exitFullScreenBtn").fadeIn(); // 🔹 Mostra suavemente o botão de saída
    });

    // Função para sair do modo tela cheia
    $("#exitFullScreenBtn").on("click", function () {
      $("#calendarContainer").removeClass("full-screen");
      $("#exitFullScreenBtn").fadeOut(); // 🔹 Esconde suavemente o botão de saída
    });

  });
</script>

<!-- Botões de controle -->
<div class="button-container">
  <button id="fullScreenBtn" class="btn btn-primary"><i class="fa fa-expand"></i></button>
  <a href="<?php echo base_url('agendamento/novo'); ?>">
    <button class="btn btn-primary">Fazer Agendamento</button>
  </a>
</div>

<!-- Botão para sair da tela cheia (aparece somente quando estiver em tela cheia) -->
<button id="exitFullScreenBtn" class="exit-fullscreen">
    <i class="fa fa-compress"></i> <!-- Ícone de saída -->
</button>

<!-- Container do calendário -->
<div id="calendarContainer">
  <div id="calendar"></div>
</div>

<!-- Modal Bootstrap para exibir detalhes do evento -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventModalLabel">Detalhes do Evento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Título:</strong> <span id="eventTitle"></span></p>
        <p><strong>Início:</strong> <span id="eventStart"></span></p>
        <p><strong>Fim:</strong> <span id="eventEnd"></span></p>
        <p><strong>Espaço:</strong> <span id="eventResource"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<style>
  body {
    margin: 0;
    padding: 0;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
  }

  #calendar {
    max-width: 1100px;
    margin: 50px auto;
  }

  .button-container {
    text-align: center;
    margin-bottom: 10px;
  }

  .button-container button, .button-container a {
    margin: 5px;
  }

  /* Estilos para o modo tela cheia */
  #calendarContainer {
    transition: all 0.3s ease-in-out;
  }

  .full-screen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: white;
    z-index: 1000;
    padding: 10px;
  }

  .full-screen #calendar {
    width: 100%;
    height: 100%;
    max-width: 100%;
    margin: 0;
  }

  /* Botão "Sair da Tela Cheia" - flutuante e moderno */
  .exit-fullscreen {
      display: none; /* 🔹 Inicialmente oculto */
      position: fixed;
      bottom: 20px; /* 🔹 Canto inferior direito */
      right: 20px;
      width: 50px;
      height: 50px;
      background-color: rgba(255, 0, 0, 0.8);
      color: white;
      border: none;
      border-radius: 50%;
      font-size: 18px;
      cursor: pointer;
      z-index: 1100;
      transition: all 0.3s ease-in-out;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
  }

  .exit-fullscreen:hover {
      background-color: red;
  }

  .exit-fullscreen i {
      font-size: 24px;
  }
</style>

<?php $this->endSection(); ?>
