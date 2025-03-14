document.addEventListener("DOMContentLoaded", function () {
  const hamburgerBtn = document.getElementById("hamburgerBtn");
  const menu = document.getElementById("burger-menu");
  hamburgerBtn.classList.add("closedMenu");

  hamburgerBtn.addEventListener("click", manageMenu);
});

function manageMenu() {
  const hamburgerBtn = document.getElementById("hamburgerBtn");
  const menu = document.getElementById("burger-menu");
  hamburgerBtn.classList.toggle("closedMenu");
  hamburgerBtn.classList.toggle("openedMenu");
  menu.classList.toggle("open");

  const expanded = hamburgerBtn.getAttribute("aria-expanded") === "true" || false;
  hamburgerBtn.setAttribute("aria-expanded", !expanded);
}
  