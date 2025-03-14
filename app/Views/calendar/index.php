<?php $this->extend('template/base'); ?>
<?php $this->section('content'); ?>

<script src='<?php echo base_url('public/assets/vendor/fullcalendar-scheduler-6.1.15/dist/index.global.js'); ?>'></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    let calendarEl = document.getElementById('calendar');

    fetch('<?php echo base_url('fullcalendar/data'); ?>')
      .then(response => response.json())
      .then(data => {
        let calendar = new FullCalendar.Calendar(calendarEl, {
          schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
          locale: 'pt-br',
          initialDate: '2025-03-14',
          initialView: 'resourceTimelineDay',
          editable: true,
          selectable: true,
          nowIndicator: true,
          aspectRatio: 1.8,
          scrollTime: '00:00',
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
              duration: { months: 1 }, // Corrigido para 'months'
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
          events: data.events
        });

        calendar.render();
      })
      .catch(error => console.error('Erro ao carregar os dados:', error));
  });
</script>

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
