<?php $this->extend('template/base'); ?>
<?php $this->section('content'); ?>

<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <title>Calendário com FullCalendar (Local)</title>
    <!-- Inclua o CSS local -->
    <link href="<?php echo base_url('public/assets/vendor/fullcalendar-scheduler-6.1.15/dist/main.css'); ?>" rel="stylesheet">
    <!-- Inclua o JS local -->
    <script src="<?php echo base_url('public/assets/vendor/fullcalendar-scheduler-6.1.15/dist/index.global.min.js'); ?>"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth'
        });
        calendar.render();
      });
    </script>
  </head>
  <body>
    <div id="calendar"></div>
  </body>
</html>
      
<?php $this->endSection(); ?>
