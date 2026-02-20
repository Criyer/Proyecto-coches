// MOSTRAR NOMBRE DE USUARIO EN EL BOTON DE INICIAR SESION
(async function actualizarBotonSesion() {
    try {
        const base = document.currentScript
            ? new URL(document.currentScript.src).pathname.replace(/\/JS\/app\.js$/, '')
            : '';
        const res = await fetch(base + '/PHP/sesion.php');
        const datos = await res.json();

        const boton = document.querySelector('a.botonlogin');
        if (!boton) return;

        if (datos.logueado) {
            boton.textContent = '¡Hola, ' + datos.nombre + '!';
            boton.href = base + '/logout.php';
            boton.style.pointerEvents = 'auto';
        }
    } catch (e) {
    }
})();


//BOTON ENVIAR FORMULARIO MENSAJE
document.addEventListener('DOMContentLoaded', function () {
    const formularioContacto = document.querySelector('.form-bonito');

    if (formularioContacto) {
        formularioContacto.addEventListener('submit', function (event) {
            event.preventDefault();

            Swal.fire({
                title: '¡Mensaje Enviado!',
                text: 'Nos pondremos en contacto contigo lo antes posible.',
                icon: 'success',
                confirmButtonColor: '#c40000',
                confirmButtonText: 'GRACIAS POR TU CONTACTO'
            }).then((result) => {
                if (result.isConfirmed) {
                    formularioContacto.reset();
                }
            });
        });
    }
});
