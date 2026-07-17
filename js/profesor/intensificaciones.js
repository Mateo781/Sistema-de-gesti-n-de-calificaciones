'use strict';

const $ = id => document.getElementById(id);

document.addEventListener('DOMContentLoaded', () => {
  // Mobile sidebar toggle callback
  const toggle = $('menuToggle') || $('hamburger');
  const sidebar = $('sidebar');
  const overlay = $('sidebarOverlay') || $('overlay');

  if (toggle && sidebar && overlay) {
    toggle.addEventListener('click', () => {
      sidebar.classList.add('open');
      overlay.classList.add('open');
      document.body.style.overflow = 'hidden';
    });

    overlay.addEventListener('click', () => {
      sidebar.classList.remove('open');
      overlay.classList.remove('open');
      document.body.style.overflow = '';
    });
  }

  // Modal logic
  const modal = $('riteModal');
  const btnClose = $('btnCloseModal');
  const btnCancel = $('btnCancelModal');

  const openModal = () => {
    if (modal) modal.classList.add('show');
  };
  const closeModal = () => {
    if (modal) modal.classList.remove('show');
  };

  if (btnClose) btnClose.addEventListener('click', closeModal);
  if (btnCancel) btnCancel.addEventListener('click', closeModal);

  // Checkbox field toggles
  const chkInt = $('modalChkInt');
  const divInt = $('modalIntFields');
  if (chkInt && divInt) {
    chkInt.addEventListener('change', () => {
      divInt.style.display = chkInt.checked ? 'flex' : 'none';
    });
  }

  const chkRec = $('modalChkRec');
  const divRec = $('modalRecFields');
  if (chkRec && divRec) {
    chkRec.addEventListener('change', () => {
      divRec.style.display = chkRec.checked ? 'block' : 'none';
    });
  }

  // Bind edit buttons
  document.querySelectorAll('.btn-action-edit').forEach(btn => {
    btn.addEventListener('click', () => {
      const data = btn.dataset;
      
      $('modalAlumnoId').value = data.id;
      $('modalAlumnoNombre').value = data.nombre;
      $('modalEstadoRITE').value = data.estado;
      $('modalPromedio').value = (data.promedio && data.promedio !== '') ? parseFloat(data.promedio).toFixed(2) : '';
      $('modalObservaciones').value = data.obs;

      // Handle checkboxes
      if (chkInt) {
        chkInt.checked = data.int === '1';
        divInt.style.display = chkInt.checked ? 'flex' : 'none';
      }
      if (chkRec) {
        chkRec.checked = data.rec === '1';
        divRec.style.display = chkRec.checked ? 'block' : 'none';
      }

      openModal();
    });
  });
});
