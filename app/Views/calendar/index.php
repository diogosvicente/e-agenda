<?php $this->extend('template/base'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="<?php echo base_url('public/assets/css/fullCalendar/style.css'); ?>">
<script src="<?php echo base_url('public/assets/vendor/fullcalendar-scheduler-6.1.15/dist/index.global.js'); ?>"></script>


<script>
  $(document).ready(function () {
    $("#exitFullScreenBtn").hide();
    let calendarEl = document.getElementById('calendar');
    let today = new Date().toISOString().split('T')[0];

    $.getJSON("<?php echo base_url('calendario/data'); ?>", function (data) {

      // Cria um mapa de espaços para buscas rápidas
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

        // Clique no evento: exibe os detalhes no modal Bootstrap
        eventClick: function (info) {
          let evento = info.event;
          let resourceId = evento.getResources()[0]?.id || "Desconhecido"; 
          let espacoNome = espacosMap[resourceId] || "Não encontrado";

          $("#eventTitle").text(evento.title);
          $("#eventStart").text(evento.start.toLocaleString());
          $("#eventEnd").text(evento.end ? evento.end.toLocaleString() : "Não definido");
          $("#eventResource").text(espacoNome);

          $("#eventModal").modal("show");
        }
      });

      calendar.render();
    });

    // Ativa o modo tela cheia
    $("#fullScreenBtn").on("click", function () {
      $("#calendarContainer").addClass("full-screen");
      $("#exitFullScreenBtn").fadeIn();
    });

    // Sai do modo tela cheia
    $("#exitFullScreenBtn").on("click", function () {
      $("#calendarContainer").removeClass("full-screen");
      $("#exitFullScreenBtn").fadeOut();
    });
  });
</script>

<!-- Estrutura dos Accordions -->
<div class="container mt-3">

  <div class="accordion" id="accordionExample">
    <!-- Accordion do Calendário -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingCalendar">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCalendar" aria-expanded="true" aria-controls="collapseCalendar">
          Calendário de Agendamentos
        </button>
      </h2>
      <div id="collapseCalendar" class="accordion-collapse collapse show" aria-labelledby="headingCalendar" data-bs-parent="#accordionExample">
        <div class="accordion-body">
          <!-- Botões de controle -->
          <div class="button-container">
            <button id="fullScreenBtn" class="btn btn-primary"><i class="fa fa-expand"></i></button>
            <a href="<?php echo base_url('agendamento/novo'); ?>">
              <button class="btn btn-primary">Fazer Agendamento</button>
            </a>
          </div>
          <!-- Botão para sair da tela cheia -->
          <button id="exitFullScreenBtn" class="exit-fullscreen">
              <i class="fa fa-compress"></i>
          </button>
          <!-- Container do calendário -->
          <div id="calendarContainer">
            <div id="calendar"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Accordion da Tabela -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingTable">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTable" aria-expanded="false" aria-controls="collapseTable">
          Solicitações
        </button>
      </h2>
      <div id="collapseTable" class="accordion-collapse collapse" aria-labelledby="headingTable" data-bs-parent="#accordionExample">
        <div class="accordion-body">
          
        <table id="listaEventos" class="display" style="width:100%;">
          <thead>
            <tr>
              <th scope="col">data</th>
              <th scope="col">Nome</th>
              <th scope="col">Departamento</th>
              <th scope="col">Data Solicitação</th>
              <th scope="col">Status</th>
              <th scope="col">Detalhes</th>
            </tr>
          </thead>
            <tbody>
            <?php foreach ($eventList as $evento): ?>
              <tr>
                <td scope="col"><?php echo $evento->created_at; ?></td>
                <td scope="col"><?php echo $evento->evento_nome; ?></td>
                <td scope="col"><?php echo tradeNameByID($evento->id_unidade_solicitante, 'unidades', 'nome'); ?></td>
                <td scope="col"><?php echo date("d/m/Y à\s h:i", strtotime($evento->created_at)); ?></td>
                <td scope="col"><?php echo $evento->nome_status; ?></td>
                <td scope="col"> <!-- style="padding-left: 0 !important;" -->
                  <a href="<?php echo base_url('agendamento/acompanhamento/' . $evento->token); ?>"
                    class="btn btn-primary" target="_blank" rel="noopener noreferrer">Exibir</a>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>

        <script type="text/javascript">
          $(document).ready(function () {
            $('#listaEventos').DataTable({
              language: { url: '<?php echo base_url('public/assets/vendor/datatables/pt-BR.json'); ?>' },
              responsive: true,
              lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
              order: [0, 'desc'],
              columnDefs: [
                {
                  targets: [0],
                  visible: false
                }
              ],
            });
          });
        </script>

        </div>
      </div>
    </div>
  </div>

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

<?php $this->endSection(); ?>
