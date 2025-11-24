
# ✅ `CONTRIBUTING.md`

# Contribución – Banner System (Adclic Hosting)

Este documento explica cómo contribuir, extender o modificar el sistema de banners,  
incluyendo cómo trabajar de forma eficiente con la IA de VSCode.

---

## 📌 Estándares del proyecto

- PHP 8.3+
- Laravel 12.x
- MySQL 5.7+ / MariaDB 10+
- Node 18+
- Vite para frontend
- AdminLTE 4 RC3 como base UI

---

## 📁 Organización del Código

- Controladores HTTP → `app/Http/Controllers`
- Controladores API → `app/Http/Controllers/Api`
- Modelos → `app/Models`
- Vistas → `resources/views`
- Script embebible → `public/js/banner.js`
- Dashboard → `DashboardController` + Views Chart.js
- Tracking → `Api/TrackController`

---

## 🧪 Cómo desarrollar

### 1. Encender Vite
````

npm run dev

```

### 2. Servidor backend
Usar Laragon:

```

[http://banner-system.test:8080](http://banner-system.test:8080)

```

### 3. Regenerar modelo/requests
```

php artisan make:model Banner -m
php artisan make:request StoreBannerRequest

```

---

## 🧠 Colaboración con IA (VSCode)

Para que la IA tenga contexto completo:

- Mantén `README.md` actualizado.
- Incluye rutas principales en este archivo.
- Documenta cada método complejo en los controladores.
- Usa nombres consistentes en funciones y variables.
- Indica siempre:
  - qué archivo modificar,
  - qué parte del código,
  - y qué comportamiento esperas.

Ejemplo:

```

IA: agrega tracking de scroll al banner en banner.js
Ubicación: public/js/banner.js
Debajo de: fetch(...)
Comportamiento: registrar un evento cada vez que el banner esté visible.

```

---

## 📦 Estilo de commits

```

feat: nueva funcionalidad
fix: corregido error en tracking
refactor: mejoras internas sin cambiar funciones
docs: actualización de documentación
style: cambios visuales
chore: tareas de mantenimiento

```

---

## 🧩 Extender la API

Para agregar un nuevo tipo de banner:

1. Migración → añadir campo si es necesario  
2. Modelo `Banner` → actualizar fillable  
3. `generateBannerHtml()` → agregar nuevo case  
4. Formularios → permitir nuevo tipo  
5. TrackController → si requiere registro especial  
6. Dashboard → si necesita nuevo gráfico

---

## 🔐 Seguridad

- Nunca exponer claves API en snippets.
- Sanitizar HTML si se permite código de terceros.
- Rate limit en las rutas `/api/track/*`.

---

Gracias por contribuir al proyecto Banner System 🚀
```
