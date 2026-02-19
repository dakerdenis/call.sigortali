<?php include('inc/header.php'); ?>



      <div class="projects-section">



        <div id='calendar' style="overflow-y: auto;"></div>



      </div>



</body>



</html>



<? include('inc/footer.php'); ?>



<script>



document.addEventListener('DOMContentLoaded', function() {

    var initialLocaleCode = 'tr';

    var calendarEl = document.getElementById('calendar');



    var calendar = new FullCalendar.Calendar(calendarEl, {

        locale: initialLocaleCode,

      initialView: 'dayGridMonth',

      initialDate: '2023-10-07',

      headerToolbar: {

        left: 'prev,next today',

        center: 'title',

        right: 'dayGridMonth,timeGridWeek,timeGridDay'

      },

      events: [

        {

          title: '99RX849',

          url: '/call/99RX849',

          start: '2023-10-28'

        }

      ]

    });



    calendar.render();

  });



</script>