# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Proyecto

Plataforma multi-tenant de invitaciones animadas (cumpleaños, baby shower, etc.) construida con Laravel 11 + Blade + SQLite. Cada usuario autenticado gestiona sus propios eventos, y cada evento se publica en una URL pública con slug (`/e/{slug}`). El idioma de la UI y los comentarios es español.

## Comandos

```bash
php artisan serve --port=8123          # servidor de desarrollo
php artisan migrate                    # aplica migraciones
php artisan migrate:fresh --seed       # reset total de la BD (SQLite en database/database.sqlite)
php artisan tinker                     # REPL para inspeccionar modelos
php artisan test                       # PHPUnit (tests en tests/)
php artisan test --filter=NombreTest   # correr un solo test
php artisan config:clear               # limpiar cache de config tras editar .env
```

Frontend: no hay build de assets — todo son vistas Blade con `<style>` inline y librerías vía CDN (Leaflet, Google Fonts). **No hay** pipeline Vite en uso aunque exista `vite.config.js`.

## Variables de entorno relevantes

- `DB_CONNECTION=sqlite` (sin `DB_DATABASE`; usa `database/database.sqlite`)
- `APP_LOCALE=es` — necesario para que las fechas en la invitación se formateen en español
- `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` — OAuth de Google (Laravel Socialite)
- `GEMINI_API_KEY` — llamadas a `gemini-1.5-flash` desde `AIController`

## Arquitectura

### Modelo de datos (multi-tenant)

- **User** ── posee muchos ──> **Event** ── posee muchos ──> **Guest**
- Los invitados (`guests`) están asociados a un `event_id`; el evento a un `user_id`.
- Cada `Event` tiene un `slug` único que determina su URL pública `/e/{slug}`.
- La migración `2026_07_14_184748_make_system_multitenant.php` es el corte histórico: convirtió el modelo mono-evento inicial en multi-tenant. Cualquier migración nueva que toque `events` o `guests` debe respetar la FK `user_id` / `event_id`.
- El esquema del evento fue creciendo por migraciones incrementales (colores del tema, campos de personalización, `is_published`, `theme_character`). Los campos `fillable` y `casts` de `App\Models\Event` deben mantenerse sincronizados al agregar columnas.

### Flujo de rutas (ver `routes/web.php`)

- **Públicas**: `/` y `/e/{slug}` (invitación), `/rsvp` (POST), `/login`, callbacks OAuth Google, `/ai/chat`.
- **Protegidas (middleware `auth`)**: todo `/admin/*` — edición del evento, actualización de slug, eliminar invitados, generación con IA.
- `InvitationController::show($slug = null)` es polimórfico: sin slug muestra el evento del usuario logueado; con slug muestra el evento público correspondiente.

### Controladores

- **`InvitationController`**: CRUD del evento propio + RSVP público. Al agregar campos configurables, se actualizan en tres lugares: migración → `Event::$fillable` → validación en `updateEvent()`.
- **`GoogleAuthController`**: OAuth con Socialite. Existe una ruta `/auth/bypass` para desarrollo — **no exponer en producción**.
- **`AIController`**: llama directamente a la API de Gemini vía `Http::post` (sin SDK). La key vive en `config/services.php` o `.env` como `GEMINI_API_KEY`. Hay un endpoint público (`/ai/chat`) y uno admin (`/admin/ai/generate`).

### Vistas

Solo cuatro Blade views, todas con CSS embebido y JS inline (sin componentes ni Vite):

- `invitation.blade.php` — página pública animada. Lee TODO del modelo `Event` (colores, textos, toggles `show_*`). Incluye Leaflet para el mapa y JS de confeti/countdown.
- `admin.blade.php` — panel de configuración con mapa interactivo (Leaflet + Nominatim para buscar direcciones). El diseño de este archivo es intencional; no revertir sin pedirlo.
- `login.blade.php`, `welcome.blade.php`.

Al modificar `invitation.blade.php` o `admin.blade.php`, respetar el patrón: variables CSS custom (`--p1`, `--p2`…) inyectadas desde los campos `color_*` del evento, y `@if($event->show_xxx)` para las secciones toggleables.

### Convenciones específicas

- Los campos booleanos de visibilidad se llaman `show_countdown`, `show_map`, `show_messages`, `show_confirmed_count`. Al leerlos desde un `<form>` HTML se procesan con `$request->boolean(...)` porque los checkboxes desmarcados no envían nada.
- El "evento actual" en contexto admin se obtiene mediante `Event::current()` (o el equivalente por usuario en la versión multi-tenant); no crear queries ad-hoc `Event::first()`.
- Mapas: se usa OpenStreetMap + Leaflet vía CDN. Nominatim para geocoding no requiere API key pero exige atribución (`© OpenStreetMap`).
