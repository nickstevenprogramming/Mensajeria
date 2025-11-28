const formBox = document.querySelector('.form-box');
const loginContainer = document.querySelector('.login-form-container');
const registerContainer = document.querySelector('.register-form-container');
const showRegisterLink = document.getElementById('show-register');
const showLoginLink = document.getElementById('show-login');
const cursoSelect = document.getElementById('cursoSelectRegister');
const rolSelect = document.getElementById('rolSelectRegister');
const cursoField = document.getElementById('cursoField');

if (showRegisterLink) {
    showRegisterLink.addEventListener('click', (e) => {
        e.preventDefault();
        formBox.classList.add('right-panel-active');
    });
}

if (showLoginLink) {
    showLoginLink.addEventListener('click', (e) => {
        e.preventDefault();
        formBox.classList.remove('right-panel-active');
    });
}

function mostrarCursoRegistro() {
    if (rolSelect.value === 'Estudiante') {
        cursoField.style.display = 'block';
        cursoSelect.setAttribute('required', 'required');
    } else {
        cursoField.style.display = 'none';
        cursoSelect.removeAttribute('required');
        cursoSelect.value = '';
    }
}

document.addEventListener('DOMContentLoaded', mostrarCursoRegistro);