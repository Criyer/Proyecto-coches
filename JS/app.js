// Esta funcion sirve para que el boton de arriba a la derecha cambie si el usuario ha entrado
(async function actualizarBotonSesion() {
    try {
        // Sacamos la ruta base para no liarla con las carpetas
        const base = document.currentScript
            ? new URL(document.currentScript.src).pathname.replace(/\/JS\/app\.js$/, '')
            : '';
        // Le preguntamos al servidor si hay alguien logueado
        const res = await fetch(base + '/PHP/sesion.php');
        const datos = await res.json();

        const boton = document.querySelector('a.botonlogin');
        if (!boton) return;

        // Si hay sesion, ponemos su nombre y cambiamos el enlace para que pueda cerrar sesion
        if (datos.logueado) {
            boton.textContent = '¡Hola, ' + datos.nombre + '!';
            boton.href = base + '/logout.php';
            boton.style.pointerEvents = 'auto';
        }
    } catch (e) {
        // Si falla algo, pues que siga como estaba
    }
})();


// Aqui manejamos el envio del formulario de contacto para que quede mas pro
document.addEventListener('DOMContentLoaded', function () {
    const formularioContacto = document.querySelector('.form-bonito');

    if (formularioContacto) {
        formularioContacto.addEventListener('submit', function (event) {
            event.preventDefault();

            // Soltamos la alerta de que se ha enviado
            Swal.fire({
                title: '¡Mensaje Enviado!',
                text: 'Nos pondremos en contacto contigo lo antes posible.',
                icon: 'success',
                confirmButtonColor: '#c40000',
                confirmButtonText: 'GRACIAS POR TU CONTACTO'
            }).then((result) => {
                // Cuando aceptan, limpiamos los campos del formulario
                if (result.isConfirmed) {
                    formularioContacto.reset();
                }
            });
        });
    }
});
