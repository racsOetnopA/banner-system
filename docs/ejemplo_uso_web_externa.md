Reemplaza https://banner-system.test:8080 por tu dominio real.




<!-- Opción A: parámetros en el SRC -->
<div id="zone-header"></div>
<script src="https://banner-system.test:8080/js/banner.js?zone=header&site=blog.com&interval=12000"></script>

<!-- Opción B: data-attributes (más legible) -->
<div id="mi-contenedor"></div>
<script
  src="https://banner-system.test:8080/js/banner.js"
  data-zone="sidebar"
  data-site="landing.com"
  data-target="#mi-contenedor"
  data-interval="15000">
</script>
