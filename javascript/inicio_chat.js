document.addEventListener('DOMContentLoaded', function() {
    const botonRegistro = document.getElementById('botonRegistro');
    const botonIngresar = document.getElementById('botonIngresar');
    const ladoIzquierdo = document.querySelector('.lado-izquierdo');
    const ladoDerecho = document.querySelector('.lado-derecho');

    // Redirigir a la página de inicio de sesión con animación
    if (botonIngresar) {
        botonIngresar.addEventListener('click', function(evento) {
            evento.preventDefault();
            
            ladoIzquierdo.classList.add('mover-lado-izquierdo');
            ladoDerecho.classList.add('mover-lado-derecho');

            setTimeout(function() {
                window.location.href = 'inicio.php';
            }, 2000);
        });
    }

    // Redirigir a la página de registro con animación
    if (botonRegistro) {
        botonRegistro.addEventListener('click', function(evento) {
            evento.preventDefault();
            
            ladoIzquierdo.classList.add('mover-lado-izquierdo');
            ladoDerecho.classList.add('mover-lado-derecho');

            setTimeout(function() {
                window.location.href = 'registro.php';
            }, 2000);
        });
    }
});