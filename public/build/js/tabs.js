document.addEventListener("DOMContentLoaded", function () {
  const tabs = document.querySelectorAll(".tab-button");
  const contents = document.querySelectorAll(".tab-content");

  tabs.forEach((tab) => {
    tab.addEventListener("click", function () {
      const target = tab.getAttribute("data-tab");

      // Desactivar todas las pestañas y contenidos
      tabs.forEach((t) => t.classList.remove("active"));
      contents.forEach((c) => c.classList.remove("active"));

      // Activar la pestaña y el contenido seleccionados
      tab.classList.add("active");
      document.getElementById(target).classList.add("active");
    });
  });
});
