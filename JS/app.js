//FUNCIONES DE CATALOGO
const inventarioVehiculos = [
    {
        id: 1,
        modelo: "BMW Serie 3 M-Sport",
        precio: "32.900 €",
        anio: "2021",
        kms: "45.000 KM",
        motor: "DIÉSEL",
        imagen: "https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&q=80&w=600",
        vendido: false
    },
    {
        id: 2,
        modelo: "Audi A4 Avant",
        precio: "27.500 €",
        anio: "2020",
        kms: "60.000 KM",
        motor: "HÍBRIDO",
        imagen: "https://images.unsplash.com/photo-1603584173870-7f3ca9128146?auto=format&fit=crop&q=80&w=600",
        vendido: false
    },
    {
        id: 3,
        modelo: "Mercedes Clase C",
        precio: "24.000 €",
        anio: "2019",
        kms: "85.000 KM",
        motor: "GASOLINA",
        imagen: "https://images.unsplash.com/photo-1583121274602-3e2820c69888?auto=format&fit=crop&q=80&w=600",
        vendido: true
    },
    {
        id: 4,
        modelo: "Volkswagen Golf GTI",
        precio: "29.900 €",
        anio: "2022",
        kms: "15.000 KM",
        motor: "GASOLINA",
        imagen: "https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&q=80&w=600",
        vendido: false
    }
];

function crearPlantillaTarjeta(coche) {
    const claseVendida = coche.vendido ? "tarjeta-vendida" : "";
    const etiquetaVendido = coche.vendido ? '<div class="etiqueta-vendido">Vendido</div>' : "";
    const textoBoton = coche.vendido ? "CONSULTAR SIMILARES" : "CONTACTAR PARA MÁS INFORMACIÓN";

    return `
        <article class="tarjeta-coche ${claseVendida}" id="vehiculo-${coche.id}">
            ${etiquetaVendido}
            <div class="contenedor-imagen">
                <img src="${coche.imagen}" class="imagen-vehiculo" alt="${coche.modelo}">
            </div>
            <div class="info-coche">
                <h3 class="nombre-modelo">${coche.modelo}</h3>
                <p class="precio-vehiculo">${coche.precio}</p>
                <div class="datos-tecnicos">
                    <span>${coche.anio}</span>
                    <span>${coche.kms}</span>
                    <span>${coche.motor}</span>
                </div>
                <a href="./contacto.html" class="boton-contacto">${textoBoton}</a>
            </div>
        </article>
    `;
}

function renderizarCatalogo() {
    const contenedorVenta = document.getElementById('contenedor-venta');
    const contenedorVendidos = document.getElementById('contenedor-vendidos');

    if (contenedorVenta) 
        contenedorVenta.innerHTML = "";
    if (contenedorVendidos) 
        contenedorVendidos.innerHTML = "";

    inventarioVehiculos.forEach(coche => {
        const htmlTarjeta = crearPlantillaTarjeta(coche);
        if (coche.vendido) {
            if (contenedorVendidos) 
                contenedorVendidos.insertAdjacentHTML('beforeend', htmlTarjeta);
        } else {
            if (contenedorVenta) 
                contenedorVenta.insertAdjacentHTML('beforeend', htmlTarjeta);
        }
    });
}

document.addEventListener('DOMContentLoaded', renderizarCatalogo);