
# ✅ `README.md` — Banner System (Adclic Hosting)

# 🖼️ Banner System – Adclic Hosting
Sistema profesional de gestión de banners para múltiples sitios web y empresas.  
Permite administrar zonas, asignaciones, banners (imagen, video, HTML/script),  
estadísticas de vistas/clics y provee un script externo para integrarse fácilmente 
en cualquier página web.

---

## 🚀 Características principales

- 🎨 **Gestión visual de banners** (imagen, video y código HTML/script).
- 🧩 **Zonas configurables** con tamaños y prioridad.
- 🔗 **Asignaciones** para vincular banners a zonas y dominios remotos.
- 📡 **API pública** para servir banners dinámicamente.
- 🖥️ **Panel administrativo** con AdminLTE (customizado).
- 📊 **Dashboard de estadísticas**:
  - Vistas totales
  - Clics totales
  - CTR%
  - Vistas/clics por día
  - Vistas por zona
- 📈 Registro real-time de métricas (vistas y clics).
- 🔐 Autenticación con Laravel Authentication.
- 🧰 Integración con cualquier sitio vía `banner.js`.

---

## 🏗️ Tecnologías utilizadas

- **Laravel 12.x**
- **PHP 8.3**
- **MySQL**
- **AdminLTE 4 RC3**
- **Bootstrap 5**
- **FontAwesome 6**
- **Vite**
- **Laragon** (entorno recomendado)
- **Chart.js** (dashboard)

---

## 📁 Estructura del proyecto

```

app/
├─ Models/
│   ├─ Banner.php
│   ├─ BannerView.php
│   ├─ BannerClick.php
│   ├─ Assignment.php
│   └─ Zone.php
│
├─ Http/
│   ├─ Controllers/
│   │   ├─ BannerController.php
│   │   ├─ ZoneController.php
│   │   ├─ AssignmentController.php
│   │   ├─ DashboardController.php
│   │   └─ Api/
│   │       ├─ BannerApiController.php
│   │       └─ TrackController.php
│   └─ Requests/
│       ├─ StoreBannerRequest.php
│       ├─ UpdateBannerRequest.php
│       ├─ StoreZoneRequest.php
│       └─ StoreAssignmentRequest.php
│
public/
├─ js/
│   └─ banner.js
└─ storage/ban | storage/videos
resources/
└─ views/
├─ banners/
├─ zones/
├─ assignments/
├─ dashboard/
└─ layouts/admin.blade.php
routes/
├─ web.php
├─ api.php
└─ auth.php

````

---

## ⚙️ Instalación local con Laragon (Windows)

### 1️⃣ Clonar repositorio

```bash
git clone https://github.com/tu-usuario/banner-system.git
cd banner-system
````

### 2️⃣ Instalar dependencias del backend

```bash
composer install
```

### 3️⃣ Crear archivo de entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 4️⃣ Configurar `.env`

Ejemplo:

```
APP_NAME="Banner System"
APP_URL=http://banner-system.test:8080
DB_DATABASE=banner_system
DB_USERNAME=root
DB_PASSWORD=
```

### 5️⃣ Crear base de datos en Laragon

Abrir **phpMyAdmin** → crear base: `banner_system`

### 6️⃣ Ejecutar migraciones y seed

```bash
php artisan migrate --seed
```

### 7️⃣ Instalar dependencias frontend

```bash
npm install
```

### 8️⃣ Ejecutar Vite

```bash
npm run dev
```

Verás algo como:

```
VITE v7.x.x  ready in ...
APP_URL: http://banner-system.test:8080
```

### 9️⃣ Iniciar Laragon

Solo asegúrate de que Apache/MySQL estén activos.

---

## 🧑‍💼 Acceso al Panel Administrativo

```
http://banner-system.test:8080/login
```

Usuario inicial (seed):

```
email: admin@admin.com
password: admin123
```

---

## 🔗 API Pública – Obtener Banner

```
GET /api/banners?zone={zoneName}&site={domain}
```

Ejemplo:

```
http://banner-system.test:8080/api/banners?zone=header&site=blog.com
```

Respuesta JSON:

```json
{
  "id": 5,
  "assignment_id": 12,
  "html": "<a href='...'>...</a> <script>fetch('...')</script>"
}
```

---

## 📡 Script externo (`banner.js`)

Permite cargar banners dinámicos en **cualquier página externa**.

### 📌 Código para insertar en otra web

```html
<div id="zone-header"></div>
<script>
  (function(){
    var s=document.createElement('script');
    s.src="http://banner-system.test:8080/js/banner.js?zone=header&site="+window.location.hostname;
    document.currentScript.parentNode.appendChild(s);
  })();
</script>
```

---

## 🧠 Flujo de tracking (vistas & clics)

### 1. El sitio remoto inserta el `<div id="zone-...">` → se carga `banner.js`

### 2. `banner.js` llama:

```
/api/banners?zone=header&site=blog.com
```

### 3. Backend retorna HTML con:

* `<a>` que apunta a: `/api/track/click/{id}`
* `<script>` que llama: `/api/track/view/{id}`

### 4. Laravel registra:

| Tabla           | Acción                            |
| --------------- | --------------------------------- |
| `banner_views`  | Cada vez que un banner se muestra |
| `banner_clicks` | Cada vez que alguien hace clic    |

### Campos registrados:

* banner_id
* assignment_id
* zone_id
* site_domain
* ip
* user_agent
* timestamps

---

## 📊 Dashboard (Chart.js)

Muestra:

* Vistas totales
* Clics totales
* CTR
* Gráfica de vistas vs clics por día (últimos 7 días)
* Distribución de vistas por zona

---

## 🎯 Tipos de Banners

| Tipo    | Descripción                                        |
| ------- | -------------------------------------------------- |
| `image` | Imagen estática                                    |
| `video` | Video MP4 con clic tracking                        |
| `html`  | Código HTML/JS (Adsense, iframe, scripts externos) |

---

## 🔐 Autenticación y Seguridad

* Middleware `auth` para el panel
* Logout vía `POST /logout`
* Rate limit para rutas de tracking `60/min`
* Validación completa en Requests

---

## 🧩 Cómo extender el sistema

1. Añadir nuevos tipos de banners → modificar `generateBannerHtml()`
2. Añadir nuevas zonas → desde panel → snippet se actualiza automáticamente
3. Añadir filtros por fecha en dashboard → ampliar consultas en `DashboardController`
4. Agregar permisos → integrar `spatie/laravel-permission`

---

## 🛠️ Comandos útiles

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan migrate:fresh --seed
php artisan storage:link
npm run dev
```

---

## © Copyright

**Adclic Hosting**
Sistema de banners multiempresa.
Versión inicial generada con soporte de IA (ChatGPT).

````

