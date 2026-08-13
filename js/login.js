// Alternar pestañas de login/registro
function switchTab(tab){
  document.querySelectorAll('.auth-tab').forEach((t, i) => {
    t.classList.toggle('active', (i === 0 && tab === 'login') || (i === 1 && tab === 'registro'));
  });
  document.getElementById('sec-login').classList.toggle('active', tab === 'login');
  document.getElementById('sec-registro').classList.toggle('active', tab === 'registro');
}

// Medidor de seguridad de contraseña
function checkStrength(val){
  let score = 0;
  if(val.length >= 8) score++;
  if(/[A-Z]/.test(val)) score++;
  if(/[0-9]/.test(val)) score++;
  if(/[^A-Za-z0-9]/.test(val)) score++;
  
  const colors = ['#e8eaf0', '#f87171', '#fbbf24', '#34d399', '#22c55e'];
  const labels = ['', 'Débil', 'Regular', 'Buena', 'Fuerte'];
  const txtColors = ['', '#c0302b', '#b07805', '#1f8c45', '#166534'];
  
  for(let i = 1; i <= 4; i++){
    document.getElementById('bar' + i).style.background = i <= score ? colors[score] : colors[0];
  }
  
  const lbl = document.getElementById('pw-label');
  lbl.textContent = val.length ? labels[score] : '';
  lbl.style.color = txtColors[score];
}