document.addEventListener('DOMContentLoaded', function() {
    const botonIngresar = document.getElementById('botonIngresar');
    const contenedor = document.querySelector('.contenedor');
    const ladoIzquierdo = document.querySelector('.lado-izquierdo');
    const ladoDerecho = document.querySelector('.lado-derecho');
    const rolSelect = document.getElementById('rol');
    const cursoContainer = document.getElementById('curso-container');
    const cursoSelect = document.getElementById('curso');
    const fotoInput = document.getElementById('foto_perfil');
    const fotoPreview = document.getElementById('foto-preview');

    if (botonIngresar && contenedor && ladoIzquierdo && ladoDerecho) {
        botonIngresar.addEventListener('click', function(evento) {
            evento.preventDefault();
            contenedor.style.opacity = '0';
            ladoIzquierdo.classList.add('mover-lado-izquierdo');
            ladoDerecho.classList.add('mover-lado-derecho');
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 1000);
        });
    } else {
        console.error('Error: No se encontraron los elementos necesarios en el DOM.');
    }

    if (rolSelect && cursoContainer && cursoSelect) {
        rolSelect.addEventListener('change', function() {
            if (this.value === 'Estudiante') {
                cursoContainer.style.display = 'block';
                cursoSelect.setAttribute('required', 'required');
            } else {
                cursoContainer.style.display = 'none';
                cursoSelect.removeAttribute('required');
                cursoSelect.value = '';
            }
        });
    }

    if (fotoInput && fotoPreview) {
        fotoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    fotoPreview.src = e.target.result;
                    fotoPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                fotoPreview.style.display = 'none';
                fotoPreview.src = '#';
            }
        });
    }
});