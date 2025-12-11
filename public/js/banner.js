(function() {
  const script = document.currentScript;
  const params = new URLSearchParams(script.src.split('?')[1]);
  const zoneId = params.get('zone_id');
  const zone = params.get('zone'); // compat nombre
  const site = params.get('site'); // opcional
  const BASE_URL = script.src.split('/js/')[0]; // ej: http://banner-system.test:8080

  const qs = new URLSearchParams();
  if (zoneId) {
    qs.set('zone_id', zoneId);
  } else if (zone) {
    qs.set('zone', zone);
  }
  if (site) {
    qs.set('site', site);
  }

  fetch(`${BASE_URL}/api/banners?${qs.toString()}`)
    .then(r => r.json())
    .then(data => {
      if (!data || !data.html) return;
      const containerId = zoneId ? `zone-${zoneId}` : (zone ? `zone-${zone}` : null);
      if (!containerId) return;
      const container = document.getElementById(containerId);
      if (!container) return;

      // Insertar HTML del banner
      container.innerHTML = data.html;

      // Registrar la vista en el backend (usar endpoint GET existente)
      // Solo si el servidor no indicó que el banner ya hace su propio tracking
      try {
        const assignmentParam = data.assignment_id ? `&assignment=${encodeURIComponent(data.assignment_id)}` : '';
        const shouldSkip = data.view_tracked === true;
        if (!shouldSkip) {
          const trackQs = new URLSearchParams();
          if (zoneId) trackQs.set('zone_id', zoneId);
          if (zone) trackQs.set('zone', zone);
          if (site) trackQs.set('site', site);
          if (assignmentParam) {
            // assignmentParam ya viene con '&assignment=..', pero aquí lo añadimos limpio
            const aid = data.assignment_id;
            if (aid) trackQs.set('assignment', aid);
          }
          fetch(`${BASE_URL}/api/track/view/${data.id}?${trackQs.toString()}`)
            .catch(() => {});
        }
      } catch (e) {
        // silenciar errores de tracking
      }
    })
    .catch(err => console.error('Error cargando banner:', err));
})();
