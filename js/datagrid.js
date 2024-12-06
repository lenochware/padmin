
function dataGrid(id) {
  let isSelecting = false;
  let firstSel = null;
  
  let startX, startY;
  let mouseButton;

  // Funkce pro získání hranic obdélníka
  function getRectangleBounds(x1, y1, x2, y2) {
    return {
      left: Math.min(x1, x2),
      top: Math.min(y1, y2),
      right: Math.max(x1, x2),
      bottom: Math.max(y1, y2),
    };
  }

  function hideColumns()
  {
    const indices = {};

    $("td.sel").each(function() {
      indices[this.cellIndex + 1] = true;
    });

    for (let i in indices) {
      $('td:nth-child('+ i +')').hide(); $('th:nth-child('+ i +')').hide();
    }
  }

  function showAllColumns()
  {
    $('td,th').show();
  }

  function createRowsSelectionMenu(e)
  {
    e.preventDefault();
    new Contextual({
        isSticky: false,
        items: [
          {label: 'Unselect', onClick: clearSelection },
        ]
    });    
  }

  function createColumnsSelectionMenu(e)
  {
    e.preventDefault();
    
    //Add contextual menu here
    new Contextual({
        isSticky: false,
        items: [
            {label: 'Unselect', onClick: clearSelection },
            {label: 'Hide columns', onClick: hideColumns },
            {label: 'Show all columns', onClick: showAllColumns },
            {type: 'seperator'},
            {label: 'Item 3', onClick: () => {}, shortcut: 'Ctrl+A'},
            {type: 'hovermenu', label: 'Hover menu', items: [
                {label: 'Subitem 1', onClick: () => {}},
                {label: 'Subitem 2', onClick: () => {}},
                {label: 'Subitem 3', onClick: () => {}},
            ]},
        ]
    });    
  }

  $("tr").on("click", function (e) {
    $(this).toggleClass("sel", !$(this).hasClass("sel"));
  });  

  $(id).on("contextmenu", function (e) {
    if ($(e.target.parentElement).hasClass('sel')) return createRowsSelectionMenu(e);
    if ($(e.target).hasClass('sel'))  return createColumnsSelectionMenu(e);
  });

  // Zahájení výběru
  $(id).on("mousedown", function (e) {

  	if (e.button > 1) return;
  	mouseButton = e.button;

    isSelecting = true;
    startX = e.pageX;
    startY = e.pageY;

    e.preventDefault(); // Zabrání výběru textu
  });

  // Pohyb myši (výběrový obdélník)
  $(document).on("mousemove", function (e) {
    if (!isSelecting) return;

    // Vypočítej rozměry výběrového obdélníku
    const rect = getRectangleBounds(startX, startY, e.pageX, e.pageY);

    const cellType = mouseButton == 1 ? "td" : "tr";
    // Vyber buňky, které jsou v oblasti obdélníka
    $(cellType).each(function () {
      const cell = this.getBoundingClientRect();
      const cellRect = {
        left: cell.left + window.scrollX,
        top: cell.top + window.scrollY,
        right: cell.right + window.scrollX,
        bottom: cell.bottom + window.scrollY,
      };

      const isOverlapping =
        rect.left < cellRect.right &&
        rect.right > cellRect.left &&
        rect.top < cellRect.bottom &&
        rect.bottom > cellRect.top;

      if (isOverlapping && !firstSel)  firstSel = $(this).hasClass("sel")? "sel" : "unsel";

      //$(this).toggleClass("sel", isOverlapping);
      if (isOverlapping) $(this).toggleClass("sel", firstSel == "unsel");
    });
  });

  // Ukončení výběru
  $(document).on("mouseup", function () {
    if (isSelecting) {
      isSelecting = false;
      firstSel = null;
    }
  });
};