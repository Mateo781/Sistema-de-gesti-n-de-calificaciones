document.addEventListener("DOMContentLoaded", function() {
    const contenedorResumen = document.getElementById('calificacionesAlumno');
    
    if (contenedorResumen) {
        // Leemos el valor desde el atributo data-encontrado del HTML
        const mostrarResumen = contenedorResumen.dataset.encontrado === 'true';
        
        if (mostrarResumen) {
            // Removemos el atributo HTML 'hidden'
            contenedorResumen.removeAttribute('hidden');
            
            // Efecto de aparición fluido
            contenedorResumen.style.opacity = 0;
            contenedorResumen.style.transition = "opacity 0.8s ease-in-out";
            
            setTimeout(() => {
                contenedorResumen.style.opacity = 1;
                // Auto-scroll hacia el contenedor de resumen
                contenedorResumen.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 50);
        }
    }
});