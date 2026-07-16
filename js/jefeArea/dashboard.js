function agregarPunto() {
        const contenedor = document.getElementById('contenedorDetalles');
        const count = contenedor.querySelectorAll('input').length + 1;
        
        const div = document.createElement('div');
        div.style.display = 'flex';
        div.style.gap = '10px';
        div.style.marginTop = '5px';
        
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'detalle[]';
        input.placeholder = 'Punto ' + count + '...';
        input.style.padding = '12px';
        input.style.border = '1px solid #cbd5e1';
        input.style.borderRadius = '6px';
        input.style.flex = '1';
        input.style.fontFamily = 'inherit';
        
        div.appendChild(input);
        contenedor.appendChild(div);
    }