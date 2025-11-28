// Enviar un mensaje al servidor
function enviarMensaje() {
    var mensaje = document.getElementById('entradaMensaje').value;
    // Verificar que el mensaje no esté vacío
    if (mensaje.trim() !== '') {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'enviar_mensaje.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                cargarMensajes();
            } else {
                console.log('Error al enviar mensaje: ' + xhr.status);
            }
        };
        xhr.onerror = function() {
            console.log('Error en la solicitud AJAX al enviar mensaje');
        };
        xhr.send('mensaje=' + encodeURIComponent(mensaje));
        document.getElementById('entradaMensaje').value = '';
    }
}

// Cargar los mensajes desde el servidor
function cargarMensajes() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'obtener_mensajes.php', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Actualizar el contenedor de mensajes y desplazar hacia abajo
            document.getElementById('contenedorChat').innerHTML = this.responseText;
            document.getElementById('contenedorChat').scrollTop = document.getElementById('contenedorChat').scrollHeight;
        } else {
            console.log('Error al cargar mensajes: ' + xhr.status);
        }
    };
    xhr.onerror = function() {
        console.log('Error en la solicitud AJAX al cargar mensajes');
    };
    xhr.send();
}

// Verificar si se presionó la tecla Enter para enviar el mensaje
function verificarEnter(evento) {
    if (evento.key === 'Enter') {
        enviarMensaje();
    }
}

// Actualizar los mensajes cada 1 segundos
setInterval(() => {
    console.log('Actualizando mensajes...');
    cargarMensajes();
}, 1000);
cargarMensajes();

// Manejo del menú desplegable
document.querySelectorAll('.slide ul li:has(ul)').forEach(item => {
    item.addEventListener('click', (e) => {
        e.preventDefault();
        item.classList.toggle('active');
    });
});

document.addEventListener('click', (e) => {
    if (!e.target.closest('.slide ul li:has(ul)')) {
        document.querySelectorAll('.slide ul li').forEach(item => {
            item.classList.remove('active');
        });
    }
});

document.querySelector('.menu_desplegable').addEventListener('change', function() {
    const contenido = document.querySelector('.content');
    if (this.checked) {
        contenido.classList.add('shifted');
    } else {
        contenido.classList.remove('shifted');
    }
});