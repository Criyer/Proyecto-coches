// Esta funcion pilla todos los coches de la base de datos y los pinta en la web
async function cargarCatalogo() {
    const vCont = document.getElementById('contenedor-venta');
    const sCont = document.getElementById('contenedor-vendidos');
    if (!vCont) return; // Si no estamos en la pagina del catalogo, pasamos de todo

    // Pedimos los coches a nuestra API
    const res = await fetch('../PHP/api_gestion_coches.php');
    const coches = await res.json();

    // Vaciamos los contenedores por si acaso
    vCont.innerHTML = '';
    sCont.innerHTML = '';

    // Vamos coche por coche montando el HTML
    coches.forEach(c => {
        const html = `
            <div class="tarjeta-coche ${c.estado == 1 ? 'tarjeta-vendida' : ''}">
                ${c.estado == 1 ? '<div class="etiqueta-vendido">Vendido</div>' : ''}
                <div class="contenedor-imagen"><img src="${c.imagen}" class="imagen-vehiculo"></div>
                <div class="info-coche">
                    <h3 class="nombre-modelo">${c.modelo}</h3>
                    <p class="precio-vehiculo">${c.estado == 1 ? 'VENDIDO' : c.precio}</p>
                    <div class="datos-tecnicos"><span>${c.anio}</span><span>${c.kms}</span><span>${c.motor}</span></div>
                    <a href="contacto.html" class="boton-contacto">${c.estado == 1 ? 'MÁS INFO' : 'CONTACTAR'}</a>
                </div>
            </div>`;

        // Si el estado es 0 esta en venta, si es 1 es que ya lo hemos vendido
        c.estado == 0 ? vCont.insertAdjacentHTML('beforeend', html) : sCont.insertAdjacentHTML('beforeend', html);
    });
}

// En cuanto se cargue la pagina, disparamos la carga de coches
document.addEventListener('DOMContentLoaded', cargarCatalogo);