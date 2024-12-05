
function dataGrid(id) {
  let isSelecting = false;
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

  // Zahájení výběru
  $(id).on("mousedown", function (e) {

  	if (e.button > 1) return;
  	mouseButton = e.button;

    isSelecting = true;
    startX = e.pageX;
    startY = e.pageY;

    // Zrušení aktuálního výběru buněk
    $("td,tr").removeClass("sel");

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

      $(this).toggleClass("sel", isOverlapping);
      //if (isOverlapping) $(this).addClass("sel"); //ctrl - add sel?
    });
  });

  // Ukončení výběru
  $(document).on("mouseup", function () {
    if (isSelecting) {
      isSelecting = false;
    }
  });
};