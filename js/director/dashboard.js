function abrirModalDirector(id, estado) { 
        document.getElementById('modalDirector').style.display = 'flex'; 
        document.getElementById('criterio_id').value = id; 
        document.getElementById('criterio_estado').value = estado; 
        
        const modalTitulo = document.getElementById('modalTitulo');
        const btnConfirmar = document.getElementById('btnConfirmar');
        
        if(estado === 'Aprobado') {
            modalTitulo.innerText = 'Aprobar Criterio';
            modalTitulo.style.color = '#16a34a';
            btnConfirmar.style.backgroundColor = '#16a34a';
            btnConfirmar.style.borderColor = '#16a34a';
            btnConfirmar.innerText = 'Confirmar Aprobación';
        } else {
            modalTitulo.innerText = 'Rechazar Criterio';
            modalTitulo.style.color = '#dc2626';
            btnConfirmar.style.backgroundColor = '#dc2626';
            btnConfirmar.style.borderColor = '#dc2626';
            btnConfirmar.innerText = 'Confirmar Rechazo';
        }
    }
    
    function cerrarModalDirector() {
        document.getElementById('modalDirector').style.display = 'none';
        document.getElementById('observacion_director').value = '';
    }