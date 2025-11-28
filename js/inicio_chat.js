document.addEventListener('DOMContentLoaded', function() {
    const botonRegistro = document.getElementById('botonRegistro');
    const ladoIzquierdo = document.querySelector('.lado-izquierdo');
    const ladoDerecho = document.querySelector('.lado-derecho');

    if (botonRegistro && ladoIzquierdo && ladoDerecho) {
        botonRegistro.addEventListener('click', function(evento) {
            evento.preventDefault();
            ladoDerecho.classList.add('fade-out');
            ladoIzquierdo.classList.add('slide-left');
            setTimeout(function() {
                window.location.href = 'register.php';
            }, 800);
        });
    } else {
        console.error('Error: No se encontraron los elementos necesarios en el DOM.');
    }
});