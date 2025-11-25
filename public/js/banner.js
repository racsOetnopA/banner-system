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

      // Registrar la vista en el backend (usar endpoint GET existente)
      // Solo si el servidor no indicó que el banner ya hace su propio tracking
      try {
        const assignmentParam = data.assignment_id ? `&assignment=${encodeURIComponent(data.assignment_id)}` : '';
        const shouldSkip = data.view_tracked === true;
        if (!shouldSkip) {
          fetch(`${BASE_URL}/api/track/view/${data.id}?zone=${encodeURIComponent(zone)}&site=${encodeURIComponent(site)}${assignmentParam}`)
            .catch(() => {});
        }
      } catch (e) {
        // silenciar errores de tracking
      }
    })
    .catch(err => console.error('Error cargando banner:', err));
})();
