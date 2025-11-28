function togglePerfil() {
  const popup = document.getElementById("perfilPopup");
  popup.classList.toggle("hidden");
}

window.addEventListener("click", function (e) {
  const popup = document.getElementById("perfilPopup");
  if (popup && !e.target.closest('.user-icon') && !e.target.closest('#perfilPopup')) {
      popup.classList.add("hidden");
  }
});

document.addEventListener('DOMContentLoaded', () => {
  const buscarInput = document.getElementById("buscarUsuario");

  if (buscarInput) {
      buscarInput.addEventListener("input", function() {
          const busqueda = this.value.trim().toLowerCase();
          filtrarUsuarios(busqueda);
      });
  }
});

function filtrarUsuarios(busqueda) {
  const sidebarUsers = document.querySelectorAll("#sidebarUsers li");
  const seleccionUsers = document.querySelectorAll("#seleccion-container .listaUsuarios li");

  sidebarUsers.forEach(li => {
      const nombres = li.dataset.nombres || li.textContent.toLowerCase();
      li.style.display = nombres.includes(busqueda) ? "block" : "none";
  });

  seleccionUsers.forEach(li => {
      const nombres = li.dataset.nombres || li.textContent.toLowerCase();
      li.style.display = nombres.includes(busqueda) ? "block" : "none";
  });
}