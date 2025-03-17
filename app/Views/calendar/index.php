<?php $this->extend('template/base'); ?>
<?php $this->section('content'); ?>

<script src='<?php echo base_url('public/assets/vendor/fullcalendar-scheduler-6.1.15/dist/index.global.js'); ?>'></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
      let calendarEl = document.getElementById('calendar');
      let today = new Date().toISOString().split('T')[0];

      fetch('<?php echo base_url('fullcalendar/data'); ?>')
        .then(response => response.json())
        .then(data => {
          
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
              resourceTimelineThreeDays: {
                type: 'resourceTimeline',
                duration: { days: 3 },
                buttonText: '3 Dias'
              },
              resourceTimelineWeek: {
                type: 'resourceTimeline',
                duration: { weeks: 1 },
                buttonText: 'Semana'
              },
              resourceTimelineMonth: {
                type: 'resourceTimeline',
                duration: { months: 1 },
                buttonText: 'Mês'
              },
              resourceTimelineYear: {
                type: 'resourceTimeline',
                duration: { years: 1 },
                buttonText: 'Ano'
              }
            },
            resourceGroupField: 'building',
            resources: data.resources,
            events: data.events,
            resourceOrder: false,

            // Evento de clique no evento
            eventClick: function(info) {
              let evento = info.event;
              let resourceId = evento.getResources()[0]?.id || "Desconhecido"; 

              // 🔹 Busca instantânea no mapa de espaços (O(1) complexidade)
              let espacoNome = espacosMap[resourceId] || "Não encontrado";

              // Preenche os dados no modal
              document.getElementById("eventTitle").innerText = evento.title;
              document.getElementById("eventStart").innerText = evento.start.toLocaleString();
              document.getElementById("eventEnd").innerText = evento.end ? evento.end.toLocaleString() : "Não definido";
              document.getElementById("eventResource").innerText = espacoNome;

              // Exibe o modal Bootstrap
              let eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
              eventModal.show();
            }
          });

          calendar.render();
        })
        .catch(error => console.error('Erro ao carregar os dados:', error));
  });
</script>

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
    font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 14px;
  }

  #calendar {
    max-width: 1100px;
    margin: 50px auto;
  }

</style>

<div id='calendar'></div>
      
<?php $this->endSection(); ?>
