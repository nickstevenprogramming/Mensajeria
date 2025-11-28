// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    // Obtener los elementos del DOM
    const botonRegistro = document.getElementById('botonRegistro');
    const botonIngresar = document.getElementById('botonIngresar');
    const ladoIzquierdo = document.querySelector('.lado-izquierdo');
    const ladoDerecho = document.querySelector('.lado-derecho');

    // Verificar que los elementos existan para evitar errores
    if (!botonIngresar || !ladoIzquierdo || !ladoDerecho) {
        console.error('Error: No se encontraron los elementos necesarios en el DOM.');
        return;
    }

    // Evento para el botón "Registrarse"
    if (botonRegistro) {
        botonRegistro.addEventListener('click', function(evento) {
            // No hacemos nada aquí porque el formulario se envía normalmente
            // El formulario en registro.php maneja el envío de datos a registrar.php
        });
    }

    // Evento para el botón "Iniciar sesión"
    if (botonIngresar) {
        botonIngresar.addEventListener('click', function(evento) {
            // Prevenir el comportamiento por defecto del botón
            evento.preventDefault();

            // Agregar las clases de animación para mover los lados
            ladoIzquierdo.classList.add('mover-lado-izquierdo');
            ladoDerecho.classList.add('mover-lado-derecho');

            // Redirigir a la página de inicio de sesión después de 2 segundos (duración de la animación)
            setTimeout(function() {
                window.location.href = 'inicio.php';
            }, 2000);
        });
    }
});