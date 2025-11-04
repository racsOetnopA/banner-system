(function() {
  const script = document.currentScript;
  const params = new URLSearchParams(script.src.split('?')[1]);
  const zone = params.get('zone');
  const site = params.get('site');
  const BASE_URL = script.src.split('/js/')[0]; // ej: http://banner-system.test:8080

  fetch(`${BASE_URL}/api/banners?zone=${zone}&site=${site}`)
    .then(r => r.json())
    .then(data => {
      if (!data || !data.html) return;
      const container = document.getElementById(`zone-${zone}`);
      if (!container) return;

      // Insertar HTML del banner
      container.innerHTML = data.html;

      // Registrar la vista en el backend
      fetch(`${BASE_URL}/api/track/view`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          banner_id: data.id,
          assignment_id: data.assignment_id ?? null,
          zone: zone,
          site: site
        })
      });
    })
    .catch(err => console.error('Error cargando banner:', err));
})();
