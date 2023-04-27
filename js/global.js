function init_global()
{
    init_datepicker();
    $('.message').click(function() { $(this).hide(); });
}

function init_datepicker()
{
    var format;
    if (!jQuery().Zebra_DatePicker) {
        return false;
    }

    format = "d. m. Y";
    $('input.calendar').each(function() {
        if($(this).parent().has('.Zebra_DatePicker_Icon').length === 0) {
            if($(this).data("format")) {
                format = $(this).data("format");
            }
            $(this).Zebra_DatePicker({
                format: format,
                days_abbr: ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'],
                months: ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'],
                readonly_element: false,
                show_select_today: 'Dnes',
                show_clear_date: false,
            });
        }
    });
}

function filterTable(name)
{
  let input = document.getElementById("search");
  let filter = input.value.toUpperCase();
  let table = document.getElementById(name);
  let tr = table.getElementsByTagName("tr");

  $('#'+name).toggleClass('strips', filter.length == 0);

  for (let i = 1; i < tr.length; i++) {
  let txtValue = tr[i].textContent;
  if (txtValue.toUpperCase().indexOf(filter) > -1) {
    tr[i].style.display = "";
  } else {
    tr[i].style.display = "none";
  }
  }
}   
