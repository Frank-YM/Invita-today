<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de administración</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:'Montserrat',sans-serif}
        body{
            background-color:#f6f4f0;
            background-image: radial-gradient(circle at 10% 20%, rgba(212, 163, 179, 0.04) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(159, 184, 199, 0.04) 0%, transparent 40%);
            background-attachment: fixed;
            padding:16px 14px 30px;
            color:#2c2235;
            max-width:1100px;
            margin:0 auto;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
            background: #fff;
            padding: 14px 18px;
            border-radius: 16px;
            box-shadow: 0 4px 14px rgba(110,90,99,0.06);
            border: 1px solid #f0e8ea;
        }
        .header-left { display:flex; align-items:center; gap:12px; }
        .header-brand {
            width:36px; height:36px; border-radius:12px;
            background: linear-gradient(135deg,#d4a3b3,#7c4dff);
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-weight:900; font-size:1.05rem;
            box-shadow: 0 4px 10px rgba(124,77,255,0.25);
        }
        h1{color:#3d2c40;font-size:1.15rem;font-weight:800;line-height:1.1}
        .subtitle-desc {color:#8a7ba5;font-size:0.72rem;margin-top:2px;font-weight:500}

        .header-actions { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }

        /* Chip de usuario */
        .user-chip {
            display:flex; align-items:center; gap:8px;
            background:#faf8f5; border:1px solid #efe6e9;
            padding:4px 6px 4px 4px; border-radius:999px;
        }
        .user-avatar {
            width:30px; height:30px; border-radius:50%;
            background: linear-gradient(135deg, #d4a3b3, #7c4dff);
            color:#fff; font-weight:800; font-size:0.82rem;
            display:flex; align-items:center; justify-content:center;
            flex-shrink:0;
        }
        .user-name {
            font-size:0.82rem; font-weight:700; color:#6e5a63;
            padding-right:4px; max-width:120px; overflow:hidden;
            text-overflow:ellipsis; white-space:nowrap;
        }
        .user-logout {
            background:transparent; border:none; cursor:pointer;
            color:#a48b93; padding:4px 8px; border-radius:999px;
            font-size:0.75rem; font-weight:600;
            transition: background .15s, color .15s;
        }
        .user-logout:hover { background:#fdf2f2; color:#e63946; }

        /* Formulario clásico de edición */
        .edit-field{ margin-bottom:12px; }
        .edit-field label{
            display:block; font-size:0.72rem; font-weight:700;
            color:#6e5a63; letter-spacing:0.5px; text-transform:uppercase;
            margin-bottom:5px;
        }
        .edit-field input, .edit-field textarea, .edit-field select{
            width:100%; padding:9px 12px; font-size:0.9rem;
            border:1.5px solid #e1ebf2; border-radius:10px;
            background:#fff; font-family:inherit;
            transition:border-color .15s;
        }
        .edit-field input:focus, .edit-field textarea:focus, .edit-field select:focus{
            outline:none; border-color:#7c4dff;
        }
        .edit-field textarea{ resize:vertical; }
        .edit-grid{ display:grid; grid-template-columns:80px 1fr; gap:10px; }
        .edit-grid .edit-field:first-child{ margin-bottom:12px; }

        .btn-save-form{
            width:100%; padding:12px; margin-top:6px;
            background:linear-gradient(135deg, #7c4dff, #5a4e8c);
            color:#fff; border:none; border-radius:12px;
            font-size:0.95rem; font-weight:700; cursor:pointer;
            box-shadow:0 6px 18px rgba(124,77,255,0.25);
            transition:transform .12s, box-shadow .12s;
            font-family:inherit;
        }
        .btn-save-form:hover{ transform:translateY(-2px); box-shadow:0 10px 24px rgba(124,77,255,0.35); }

        .published-badge{
            background:rgba(8,107,85,0.1); color:#086b55;
            padding:6px 12px; border-radius:999px; font-size:0.78rem; font-weight:700;
            display:inline-flex; align-items:center; gap:6px;
        }
        .published-badge::before{
            content:""; width:7px; height:7px; border-radius:50%; background:#086b55;
            box-shadow:0 0 0 3px rgba(8,107,85,0.2);
            animation:pulse-dot 2s ease-in-out infinite;
        }
        @keyframes pulse-dot{
            0%,100%{ box-shadow:0 0 0 3px rgba(8,107,85,0.2); }
            50%    { box-shadow:0 0 0 5px rgba(8,107,85,0.35); }
        }

        .btn-nav {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            background: #fff;
            color: #6e5a63;
            border: 1.5px solid #efe6e9;
            padding: 7px 14px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.78rem;
            cursor: pointer;
            transition: 0.15s;
            font-family: inherit;
        }
        .btn-nav:hover { border-color:#d4a3b3; transform: translateY(-1px); }
        .btn-nav-primary { background:#086b55; color:#fff; border-color:#086b55; }
        .btn-nav-primary:hover { background:#075c48; border-color:#075c48; color:#fff; }
        .btn-nav-danger { background:#fdf2f2; color:#e63946; border-color:#fdf2f2; }
        .btn-nav-danger:hover { background:#fce4e4; border-color:#fce4e4; color:#c32d3a; }

        /* Alias legacy para no romper otros usos de .btn-view-invite */
        .btn-view-invite { display:inline-flex; align-items:center; text-decoration:none; background:#fff; color:#6e5a63; border:1.5px solid #efe6e9; padding:7px 14px; border-radius:999px; font-weight:700; font-size:0.78rem; cursor:pointer; transition:.15s; font-family:inherit; }
        .btn-view-invite:hover { border-color:#d4a3b3; transform: translateY(-1px); }

        .admin-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 20px;
        }
        @media (max-width: 850px) {
            .admin-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ===== Responsive: móvil (≤600px) ===== */
        @media (max-width: 600px) {
            body { padding: 10px !important; }
            .card { padding: 12px 14px !important; margin-bottom: 12px !important; }
            .card-title { font-size: 0.95rem !important; }

            /* Header: apilar título y botones */
            .header { flex-direction: column; align-items: stretch !important; gap: 10px; padding: 12px 14px !important; }
            .header-left { justify-content: flex-start; }
            .header-actions { width: 100%; justify-content: space-between; }
            .btn-nav { flex: 1; justify-content: center; padding: 7px 10px; font-size: 0.75rem; }
            .btn-view-invite { flex: 1; justify-content: center; padding: 7px 10px !important; font-size: 0.75rem !important; }
            .user-chip { order: -1; margin-left: auto; flex: 0; }
            .user-name { max-width: 90px; }

            /* Galería de modelos: 3 columnas en vez de 5 */
            #preset-grid { grid-template-columns: repeat(3, 1fr) !important; gap: 6px !important; }
            #preset-grid button { font-size: 0.55rem; }

            /* Card del enlace: apilar input y botón */
            #shareUrl { font-size: 0.7rem !important; }

            /* Resumen de confirmaciones: números más chicos */
            .card [style*="grid-template-columns:repeat(4,1fr)"] div { padding: 6px 4px !important; }

            /* Tabla de confirmados: forzar scroll horizontal si excede */
            .table-container table { min-width: 380px; }

            /* Botón música: más pequeño y más pegado a la esquina */
            body > button[aria-label="Música de fondo"] {
                width: 40px !important; height: 40px !important;
                bottom: 12px !important; right: 12px !important;
                font-size: 1.1rem !important;
            }
        }

        /* ===== Pantallas muy chicas (≤360px) ===== */
        @media (max-width: 360px) {
            #preset-grid { grid-template-columns: repeat(2, 1fr) !important; }
        }

        .card{background:#fff;border-radius:18px;padding:16px 20px;
            box-shadow:0 10px 30px rgba(110,90,99,0.04);
            border: 1px solid #e1ebf2;
            margin-bottom: 16px;}
        .card-title {
            font-size: 1.02rem;
            color: #6e5a63;
            font-weight: 700;
            margin-bottom: 12px;
            border-bottom: 1.5px solid #f6f4f0;
            padding-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        label{display:block;font-weight:bold;margin:8px 0 4px;font-size:.8rem;color:#6e5a63}
        
        input[type=text],input[type=datetime-local],input[type=number],textarea,select{
            width:100%;padding:8px 12px;border:2.5px solid #eee;border-radius:10px;font-size:0.9rem;font-family:inherit;
            transition: 0.2s;}
        input:focus,textarea:focus,select:focus{outline:none;border-color:#d4a3b3;background:#fdfcff}
        
        .hint{font-size:.74rem;color:#8a7ba5;margin-top:2px}
        
        /* Botones */
        .btn{border:none;cursor:pointer;background:linear-gradient(90deg,#d4a3b3,#6e5a63);
            color:#fff;font-weight:bold;font-size:0.88rem;padding:10px 20px;border-radius:999px;
            transition: 0.2s;box-shadow: 0 4px 15px rgba(110,90,99,0.12)}
        .btn:hover{transform: translateY(-2px); box-shadow: 0 6px 20px rgba(110,90,99,0.18)}
        .btn-sm{padding:6px;font-size:.75rem;background:#fdf2f2;border-radius:6px;color:#e63946;border:none;cursor:pointer;transition:0.2s}
        .btn-sm:hover{background:#e63946;color:#fff}
        
        .saved{background:rgba(51, 217, 178, 0.12);color:#065f4c;padding:10px 14px;border-radius:10px;font-weight:bold;margin-bottom:12px;border:1px solid rgba(51, 217, 178, 0.25);font-size:0.88rem;}
        
        /* Tabla de invitados */
        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #e1ebf2;
        }
        table{width:100%;border-collapse:collapse;background:#fff;font-size:.82rem}
        th,td{padding:8px 10px;text-align:left;border-bottom:1px solid #f6f4f0}
        th{background:#6e5a63;color:#fff;font-weight:bold}
        .yes{color:#086b55;font-weight:bold}.no{color:#e63946;font-weight:bold}
        
        /* Resumen dashboard */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-bottom: 12px;
        }
        .summary-card {
            padding: 8px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #e1ebf2;
        }
        .summary-card span {
            font-size: 1.3rem;
            font-weight: bold;
            display: block;
        }
        .summary-card small {
            color: #8a7ba5;
            font-size: 0.65rem;
            text-transform: uppercase;
            font-weight: bold;
            display: block;
            margin-top: 1px;
        }

        /* Etiquetas Preset */
        .tag-btn {
            background: #fff;
            border: 2px solid #e1ebf2;
            padding: 6px 12px;
            border-radius: 999px;
            color: #6e5a63;
            font-weight: bold;
            font-size: 0.78rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .tag-btn:hover {
            background: #f6f4f0;
            border-color: #d4a3b3;
        }
        .tag-btn.active {
            background: #6e5a63;
            color: #fff;
            border-color: #6e5a63;
            box-shadow: 0 4px 10px rgba(110,90,99,0.15);
        }

        /* Botón IA inline (al lado del label) */
        .btn-ai-inline {
            background: linear-gradient(135deg, #7c4dff 0%, #b388ff 100%);
            color: #fff;
            border: none;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.72rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(124,77,255,0.25);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            white-space: nowrap;
        }
        .btn-ai-inline:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(124,77,255,0.3); }
        .btn-ai-inline:disabled { opacity: 0.7; cursor: wait; transform: none; }

        /* Botón mi ubicación */
        .btn-mylocation {
            background:#faf8f5; border:1px solid #efe6e9;
            color:#6e5a63; padding:4px 10px; border-radius:8px;
            font-size:0.72rem; font-weight:600; cursor:pointer;
            font-family:inherit; white-space:nowrap;
            transition: background .15s, border-color .15s;
        }
        .btn-mylocation:hover { background:#eae3f7; border-color:#c9c3e6; color:#7c4dff; }
        .btn-mylocation:disabled { opacity:0.6; cursor:wait; }

        /* Buscador de dirección (Nominatim) */
        .place-search-wrap { position:relative; }
        .place-search-status {
            position:absolute; right:10px; top:50%; transform:translateY(-50%);
            font-size:0.7rem; color:#8a7ba5; pointer-events:none;
        }
        .place-search-results {
            position:absolute; top:calc(100% + 4px); left:0; right:0;
            background:#fff; border:1px solid #efe6e9; border-radius:12px;
            box-shadow: 0 8px 24px rgba(110,90,99,0.12);
            max-height:260px; overflow-y:auto; z-index:1000;
            display:none;
        }
        .place-search-results.open { display:block; }
        .place-search-item {
            padding:10px 12px; cursor:pointer; font-size:0.82rem;
            border-bottom:1px solid #faf8f5; color:#3d2c40;
            display:flex; align-items:flex-start; gap:8px;
            transition: background .12s;
        }
        .place-search-item:last-child { border-bottom:none; }
        .place-search-item:hover, .place-search-item.active { background:#faf8f5; }
        .place-search-item .icon { color:#7c4dff; flex-shrink:0; margin-top:2px; }
        .place-search-item .main { font-weight:600; }
        .place-search-item .sub { color:#8a7ba5; font-size:0.72rem; margin-top:1px; font-weight:500; }
        .place-badge { display:inline-block; background:#e8f7f0; color:#086b55; font-size:0.6rem; font-weight:700; padding:1px 6px; border-radius:999px; margin-left:4px; letter-spacing:0.3px; vertical-align:middle; }
        .place-search-empty { padding:14px 14px 6px; text-align:center; color:#8a7ba5; font-size:0.8rem; font-style:italic; }
        .place-search-global {
            display:block; width:calc(100% - 20px); margin:6px 10px 10px;
            background:#faf8f5; border:1px solid #efe6e9; color:#6e5a63;
            padding:8px 12px; border-radius:10px; font-size:0.78rem; font-weight:600;
            cursor:pointer; font-family:inherit; transition: background .15s, color .15s;
        }
        .place-search-global:hover { background:#eae3f7; color:#7c4dff; border-color:#c9c3e6; }

        /* Mapa expandible: crece en hover para seleccionar más fácil */
        .edit-map-expandable {
            height:200px;
            border-radius:12px;
            overflow:hidden;
            border:1px solid #e1ebf2;
            transition: height .3s ease;
        }
        .edit-map-expandable:hover,
        .edit-map-expandable.expanded {
            height:420px;
            border-color:#7c4dff;
            box-shadow: 0 8px 24px rgba(124,77,255,0.12);
        }
        @media (max-width: 600px) {
            .edit-map-expandable:hover,
            .edit-map-expandable.expanded { height:340px; }
        }

        /* Fila de toggle (checkbox estilizado) */
        .toggle-row {
            display:flex; align-items:flex-start; gap:10px;
            background:#faf8f5; border:1px solid #efe6e9;
            border-radius:12px; padding:10px 12px; margin:6px 0 12px;
            cursor:pointer; transition: border-color .15s;
        }
        .toggle-row:hover { border-color:#d4a3b3; }
        .toggle-row input[type=checkbox] { margin-top:3px; accent-color:#7c4dff; width:16px; height:16px; cursor:pointer; }
        .toggle-row span { display:flex; flex-direction:column; gap:2px; font-size:0.82rem; color:#6e5a63; }
        .toggle-row small { color:#8a7ba5; font-size:0.7rem; font-weight:500; }

        /* Grid de galería en admin */
        .admin-gallery-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-top:8px; }
        .admin-gallery-item { position:relative; aspect-ratio:1/1; border-radius:8px; overflow:hidden; background:#f6f4f0; }
        .admin-gallery-item img { width:100%; height:100%; object-fit:cover; display:block; }
        .admin-gallery-item .name-tag { position:absolute; bottom:0; left:0; right:0; background:linear-gradient(to top, rgba(0,0,0,.7), transparent); color:#fff; padding:10px 5px 3px; font-size:0.6rem; font-weight:600; text-align:center; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .admin-gallery-item .del-btn { position:absolute; top:4px; right:4px; width:22px; height:22px; border-radius:50%; background:rgba(230,57,70,0.9); color:#fff; border:none; font-size:0.8rem; line-height:1; cursor:pointer; display:flex; align-items:center; justify-content:center; padding:0; }
        .admin-gallery-empty { padding:20px 10px; text-align:center; color:#8a7ba5; font-size:0.8rem; background:#faf8f5; border-radius:10px; }

        /* Fila sutil de coordenadas debajo del mapa */
        .coords-row {
            display:flex; align-items:center; gap:8px;
            margin-top:6px; padding:2px 4px;
            font-size:0.7rem; color:#a48b93;
        }
        .coords-label { font-weight:600; text-transform:uppercase; letter-spacing:0.4px; }
        .coords-row input {
            flex:1; min-width:0; width:auto !important;
            background:transparent !important;
            border:none !important;
            border-bottom:1px dashed #e1d6da !important;
            border-radius:0 !important;
            padding:2px 0 !important;
            font-size:0.7rem !important;
            font-family: 'SFMono-Regular', Menlo, monospace !important;
            color:#a48b93 !important;
        }
        .coords-row input:focus {
            outline:none; color:#6e5a63 !important;
            border-bottom-color:#7c4dff !important;
        }
        /* Ocultar spinner del input number */
        .coords-row input::-webkit-inner-spin-button,
        .coords-row input::-webkit-outer-spin-button { -webkit-appearance:none; margin:0; }
        .coords-row input { -moz-appearance:textfield; }
    </style>
</head>
<body>
    @php
        $firstName = trim(explode(' ', trim(auth()->user()->name ?? ''))[0] ?? '') ?: 'Usuario';
        $initial   = mb_strtoupper(mb_substr($firstName, 0, 1));
    @endphp
    <div class="header">
        <div class="header-left">
            <a href="{{ route('events.index') }}" class="header-brand" title="Volver a Mis eventos" style="text-decoration:none;">←</a>
            <div>
                <h1>{{ $event->title }}</h1>
                <p class="subtitle-desc">
                    <a href="{{ route('events.index') }}" style="color:#8a7ba5; text-decoration:none;">Mis eventos</a>
                    <span style="opacity:.5">›</span>
                    Configurando este evento
                    @if($userEvents->count() > 1)
                        <span style="opacity:.5"> · </span>
                        <select onchange="if(this.value) location.href=this.value" style="border:none; background:transparent; color:#7c4dff; font-weight:600; cursor:pointer; font-size:0.72rem; padding:0;">
                            <option value="">Cambiar evento…</option>
                            @foreach($userEvents as $e)
                                @if($e->id !== $event->id)
                                    <option value="{{ route('events.select', $e) }}">{{ $e->emoji }} {{ $e->title }}</option>
                                @endif
                            @endforeach
                        </select>
                    @endif
                </p>
            </div>
        </div>
        <div class="header-actions">
            @if($event->is_published)
                <span class="published-badge">Publicada</span>
                <form method="POST" action="{{ route('event.publish') }}" style="display:inline; margin:0;">
                    @csrf
                    <input type="hidden" name="publish" value="0">
                    <button type="submit" class="btn-nav btn-nav-danger" onclick="return confirm('¿Despublicar y volver a modo borrador?')">Despublicar</button>
                </form>
            @else
                <form method="POST" action="{{ route('event.publish') }}" style="display:inline; margin:0;">
                    @csrf
                    <input type="hidden" name="publish" value="1">
                    <button type="submit" class="btn-nav btn-nav-primary">Publicar</button>
                </form>
            @endif
            <a href="{{ route('invitation.public', ['slug' => $event->slug]) }}?preview=1" target="_blank" class="btn-nav">Ver invitación</a>

            <div class="user-chip" title="{{ auth()->user()->name }}">
                <div class="user-avatar">{{ $initial }}</div>
                <span class="user-name">{{ $firstName }}</span>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="user-logout" title="Cerrar sesión">Salir</button>
                </form>
            </div>
        </div>
    </div>

    @if(session('saved'))<div class="saved">{{ session('saved') }}</div>@endif

    <div class="admin-grid">
        
        {{-- COLUMNA IZQUIERDA: FORMULARIO DE EDICIÓN + ASISTENTE --}}
        <div>
            {{-- FORMULARIO CLÁSICO --}}
            <div class="card" style="padding:16px 20px; margin-bottom:16px;">
                <div style="margin-bottom:14px;">
                    <div class="card-title" style="margin:0;">Detalles del evento</div>
                </div>

                <form method="POST" action="{{ route('event.update') }}" id="edit-form">
                    @csrf
                    <input type="hidden" name="color_primary" id="form-color_primary" value="{{ $event->color_primary }}">
                    <input type="hidden" name="color_accent" id="form-color_accent" value="{{ $event->color_accent }}">
                    <input type="hidden" name="color_secondary" id="form-color_secondary" value="{{ $event->color_secondary }}">
                    <input type="hidden" name="theme_character" id="form-theme_character" value="{{ $event->theme_character }}">
                    <input type="hidden" name="template" id="form-template" value="{{ $event->template ?? 'classic' }}">
                    <input type="hidden" name="is_published" value="{{ $event->is_published ? '1' : '0' }}">
                    <input type="hidden" name="rsvp_button_text" value="Confirmar asistencia">

                    <div class="edit-grid">
                        <div class="edit-field">
                            <label>Emoji</label>
                            <input type="text" name="emoji" id="form-emoji" maxlength="8" value="{{ $event->emoji }}" placeholder="🎂" style="text-align:center; font-size:1.3rem;">
                        </div>
                        <div class="edit-field" style="flex:1;">
                            <label>Tipo de evento</label>
                            <select name="event_type" id="form-event_type">
                                <optgroup label="Bebés y niños">
                                    <option value="babyshower"   @selected($event->event_type==='babyshower')>🍼 Baby Shower</option>
                                    <option value="revelacion"   @selected($event->event_type==='revelacion')>🤰 Revelación de género</option>
                                    <option value="bienvenida"   @selected($event->event_type==='bienvenida')>👶 Bienvenida</option>
                                </optgroup>
                                <optgroup label="Religiosos">
                                    <option value="bautizo"      @selected($event->event_type==='bautizo')>🕊️ Bautizo</option>
                                    <option value="comunion"     @selected($event->event_type==='comunion')>🕯️ Comunión</option>
                                </optgroup>
                                <optgroup label="Celebraciones">
                                    <option value="cumple"       @selected($event->event_type==='cumple')>🎂 Cumpleaños</option>
                                    <option value="quinceanero"  @selected($event->event_type==='quinceanero')>👑 Quinceañero / XV años</option>
                                    <option value="boda"         @selected($event->event_type==='boda')>💍 Boda</option>
                                    <option value="aniversario"  @selected($event->event_type==='aniversario')>💛 Aniversario</option>
                                    <option value="graduacion"   @selected($event->event_type==='graduacion')>🎓 Graduación</option>
                                    <option value="despedida"    @selected($event->event_type==='despedida')>🥂 Despedida de soltera/o</option>
                                    <option value="general"      @selected($event->event_type==='general')>🎉 Evento general</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <div class="edit-field">
                        <label>Título</label>
                        <input type="text" name="title" required maxlength="120" value="{{ $event->title }}">
                    </div>

                    <div class="edit-field">
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
                            <label style="margin:0;">Subtítulo (opcional)</label>
                            <button type="button" class="btn-ai-inline" onclick="generateField('subtitle')" data-field-btn="subtitle">
                                <span data-field-label="subtitle">✨ IA</span>
                            </button>
                        </div>
                        <textarea name="subtitle" id="form-subtitle" rows="2" maxlength="200">{{ $event->subtitle }}</textarea>
                    </div>

                    <div class="edit-field">
                        <label>Fecha y hora</label>
                        <input type="datetime-local" name="date" required value="{{ optional($event->date)->format('Y-m-d\TH:i') }}">
                    </div>

                    <div class="edit-field">
                        <label>Dirección / lugar <small style="color:#8a7ba5; font-weight:500;">— escribí y elegí de la lista para ubicar en el mapa</small></label>
                        <div class="place-search-wrap">
                            <input type="text" name="place" id="edit-place" required maxlength="200" value="{{ $event->place }}" placeholder="Ej: Calle Los Sauces 123, Lima" autocomplete="off">
                            <div class="place-search-status" id="edit-place-status"></div>
                            <div class="place-search-results" id="edit-place-results"></div>
                        </div>
                    </div>

                    <div class="edit-field">
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:5px;">
                            <label style="margin:0;">Ubicación en el mapa <small style="color:#8a7ba5; font-weight:500;">— tocá para mover el pin</small></label>
                            <button type="button" class="btn-mylocation" onclick="useMyLocation()" id="btn-mylocation">
                                <span id="btn-mylocation-label">📍 Mi ubicación</span>
                            </button>
                        </div>
                        <div id="edit-map" class="edit-map-expandable"></div>
                        <div class="coords-row">
                            <span class="coords-label">Coord.</span>
                            <input type="number" name="lat" step="any" required value="{{ $event->lat }}" id="edit-lat" aria-label="Latitud" title="Latitud">
                            <input type="number" name="lng" step="any" required value="{{ $event->lng }}" id="edit-lng" aria-label="Longitud" title="Longitud">
                        </div>
                    </div>

                    <div class="edit-field">
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
                            <label style="margin:0;">Notas especiales (opcional)</label>
                            <button type="button" class="btn-ai-inline" onclick="generateField('extra_info')" data-field-btn="extra_info">
                                <span data-field-label="extra_info">✨ IA</span>
                            </button>
                        </div>
                        <textarea name="extra_info" id="form-extra_info" rows="3" maxlength="1000" placeholder="Vestimenta, lluvia de sobres, sugerencia de regalos…">{{ $event->extra_info }}</textarea>
                    </div>

                    <label class="toggle-row">
                        <input type="checkbox" name="show_gallery" value="1" @checked($event->show_gallery)>
                        <span>
                            <strong>Galería de invitados</strong>
                            <small>Permitir que los invitados suban fotos a la invitación</small>
                        </span>
                    </label>

                    <button type="submit" class="btn-save-form">💾 Guardar cambios</button>
                </form>
            </div>

        </div>

        {{-- COLUMNA DERECHA: ENLACE COMPARTIR + INVITADOS --}}
        <div>
            {{-- Selector de Modelos --}}
            <div class="card" style="padding:14px 16px;">
                <div class="card-title" style="margin-bottom:8px;">Elegí un modelo</div>
                <div id="preset-grid" style="display:grid; grid-template-columns:repeat(5,1fr); gap:8px;"></div>
                <small style="display:block; margin-top:8px; color:#8a7ba5; font-size:0.7rem; text-align:center;">Al elegir un modelo se aplica y guarda automáticamente.</small>
            </div>

            {{-- Imágenes de revelación (solo para eventos tipo 'revelacion') --}}
            @if($event->event_type === 'revelacion')
                <div class="card" style="padding:14px 16px;">
                    <div class="card-title" style="margin-bottom:8px;">Imágenes de revelación</div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        @foreach([['slot'=>1,'label'=>'Niño'], ['slot'=>2,'label'=>'Niña']] as $s)
                            @php $path = $event->{'reveal_image_'.$s['slot']}; @endphp
                            <div style="border:1.5px dashed #d8d0c2; border-radius:12px; padding:8px; text-align:center; background:#fbf8f2; display:flex; flex-direction:column; justify-content:space-between; min-height:120px;">
                                <div style="font-size:0.7rem; color:#8a7ba5; font-weight:700; margin-bottom:4px;">{{ $s['label'] }}</div>
                                @if($path)
                                    <div style="flex:1; display:flex; align-items:center; justify-content:center; margin-bottom:6px;">
                                        <img src="{{ asset($path) }}" alt="{{ $s['label'] }}" style="max-width:100%; max-height:60px; object-fit:contain; border-radius:4px;">
                                    </div>
                                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:4px; margin-bottom:4px;">
                                        <button type="button" style="background:#e8e3f2; color:#5a4e8c; border:none; border-radius:6px; cursor:pointer; padding:4px; font-size:0.65rem; font-weight:600; display:inline-flex; align-items:center; justify-content:center; gap:2px;" onclick="openRevealSearch({{ $s['slot'] }})">🔍 Buscar</button>
                                        
                                        <form method="POST" action="{{ route('event.reveal.upload') }}" enctype="multipart/form-data" id="revealUploadForm{{ $s['slot'] }}" style="margin:0;">
                                            @csrf
                                            <input type="hidden" name="slot" value="{{ $s['slot'] }}">
                                            <input type="file" name="image" accept="image/*" style="display:none;" onchange="this.form.submit()">
                                            <button type="button" style="background:#e8e3f2; color:#5a4e8c; border:none; border-radius:6px; cursor:pointer; padding:4px; font-size:0.65rem; font-weight:600; width:100%; display:inline-flex; align-items:center; justify-content:center; gap:2px;" onclick="this.previousElementSibling.click()">📤 Subir</button>
                                        </form>
                                    </div>
                                    <form method="POST" action="{{ route('event.reveal.remove') }}" onsubmit="return confirm('¿Quitar esta imagen?')" style="margin:0;">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="slot" value="{{ $s['slot'] }}">
                                        <button type="submit" class="btn-sm" style="width:100%; font-size:0.65rem; padding:4px 8px; display:inline-flex; align-items:center; justify-content:center; gap:2px;">🗑️ Quitar</button>
                                    </form>
                                @else
                                    <div style="flex:1; display:flex; flex-direction:column; justify-content:center; gap:6px; padding:4px 0;">
                                        <button type="button" style="background:#e8e3f2; color:#5a4e8c; border:none; border-radius:6px; cursor:pointer; padding:6px; font-size:0.7rem; font-weight:600; display:inline-flex; align-items:center; justify-content:center; gap:3px;" onclick="openRevealSearch({{ $s['slot'] }})">
                                            <span>🔍</span>
                                            <span>Buscar del Catálogo</span>
                                        </button>
                                        
                                        <form method="POST" action="{{ route('event.reveal.upload') }}" enctype="multipart/form-data" id="revealUploadForm{{ $s['slot'] }}" style="margin:0;">
                                            @csrf
                                            <input type="hidden" name="slot" value="{{ $s['slot'] }}">
                                            <input type="file" name="image" accept="image/*" style="display:none;" onchange="this.form.submit()">
                                            <button type="button" style="background:#e8e3f2; color:#5a4e8c; border:none; border-radius:6px; cursor:pointer; padding:6px; font-size:0.7rem; font-weight:600; width:100%; display:inline-flex; align-items:center; justify-content:center; gap:3px;" onclick="this.previousElementSibling.click()">
                                                <span>📤</span>
                                                <span>Subir Imagen</span>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <small style="display:block; margin-top:6px; color:#8a7ba5; font-size:0.65rem; text-align:center;">Buscá o subí tu propio archivo (PNG/JPG/WEBP hasta 4MB).</small>
                </div>

                {{-- Modal buscador de imágenes --}}
                <div id="revealSearchModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9990; align-items:center; justify-content:center; padding:20px;">
                    <div style="background:#fff; border-radius:16px; padding:20px; max-width:520px; width:100%; max-height:80vh; display:flex; flex-direction:column;">
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                            <input type="text" id="revealSearchInput" placeholder="Buscar: goku, kitty, shrek…" autofocus
                                   style="flex:1; padding:10px 14px; border-radius:24px; border:1.5px solid #e1ebf2; font-size:0.9rem;">
                            <button type="button" onclick="closeRevealSearch()" style="background:#f6f4f0; border:none; width:36px; height:36px; border-radius:50%; cursor:pointer; font-size:1.1rem;">✕</button>
                        </div>
                        <div style="display:flex; gap:6px; margin-bottom:10px; border-bottom:1.5px solid #f0edee; padding-bottom:8px;">
                            <button type="button" id="revealTabLocal" onclick="switchRevealTab('local')" class="reveal-tab reveal-tab-active">📁 Catálogo</button>
                            <button type="button" id="revealTabWeb" onclick="switchRevealTab('web')" class="reveal-tab">🌐 Buscar en la web</button>
                        </div>
                        <div id="revealSearchResults" style="overflow-y:auto; display:grid; grid-template-columns:repeat(4, 1fr); gap:8px; padding:4px;"></div>
                        <div id="revealSearchEmpty" style="text-align:center; color:#8a7ba5; padding:20px; font-size:0.85rem; display:none;">Sin resultados</div>
                        <div id="revealSearchLoading" style="text-align:center; color:#8a7ba5; padding:20px; font-size:0.85rem; display:none;">Buscando…</div>
                    </div>
                </div>

                <form id="revealSelectForm" method="POST" action="{{ route('event.reveal.set') }}" style="display:none;">
                    @csrf
                    <input type="hidden" name="slot" id="revealSelectSlot">
                    <input type="hidden" name="path" id="revealSelectPath">
                </form>
                <form id="revealImportForm" method="POST" action="{{ route('event.reveal.import') }}" style="display:none;">
                    @csrf
                    <input type="hidden" name="slot" id="revealImportSlot">
                    <input type="hidden" name="url" id="revealImportUrl">
                    <input type="hidden" name="title" id="revealImportTitle">
                </form>
                <style>
                    .reveal-tab{ flex:1; padding:6px 10px; background:transparent; border:none; border-radius:8px; cursor:pointer; font-size:0.8rem; color:#8a7ba5; font-weight:600; }
                    .reveal-tab-active{ background:#eae3f7; color:#3a2b4d; }
                    .reveal-thumb{
                        position:relative; padding:0; background:#f7f5f0;
                        border:1.5px solid #e1ebf2; border-radius:10px;
                        cursor:pointer; overflow:hidden;
                        display:flex; flex-direction:column;
                        transition: transform .12s, border-color .12s, box-shadow .12s;
                    }
                    .reveal-thumb:hover{ transform:translateY(-2px); border-color:#c9b5f0; box-shadow:0 4px 10px rgba(0,0,0,0.08); }
                    .reveal-thumb img{
                        width:100%; height:80px; object-fit:cover; display:block;
                        background:#fff;
                    }
                    .reveal-thumb span{
                        font-size:0.6rem; color:#4a3b6b;
                        padding:3px 4px; text-align:center;
                        overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
                        text-transform:capitalize;
                    }
                    .reveal-thumb-badge{
                        position:absolute !important; top:3px; right:3px;
                        background:rgba(0,0,0,0.55); color:#fff !important;
                        font-size:0.55rem !important; padding:1px 5px !important;
                        border-radius:8px; text-transform:none !important;
                    }
                </style>
            @endif

            {{-- Fotos para adornar la tarjeta --}}
            <div class="card" style="padding:14px 16px;">
                <div class="card-title" style="margin-bottom:8px;">Fotos de la tarjeta</div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    @foreach([1,2] as $slot)
                        @php $photoPath = $event->{'photo_'.$slot}; @endphp
                        <div style="border:1.5px dashed #d8d0c2; border-radius:12px; padding:8px; text-align:center; background:#fbf8f2;">
                            @if($photoPath)
                                <img src="{{ Storage::url($photoPath) }}" alt="Foto {{ $slot }}" style="width:100%; height:70px; object-fit:cover; border-radius:6px; margin-bottom:4px;">
                                <form method="POST" action="{{ route('event.photo.delete') }}" onsubmit="return confirm('¿Eliminar foto {{ $slot }}?')" style="margin:0;">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="slot" value="{{ $slot }}">
                                    <button type="submit" class="btn-sm" style="font-size:0.7rem; padding:4px 8px; width:100%;">🗑️ Quitar</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('event.photo.upload') }}" enctype="multipart/form-data" id="photoForm{{ $slot }}" style="margin:0;">
                                    @csrf
                                    <input type="hidden" name="slot" value="{{ $slot }}">
                                    <input type="file" name="photo" accept="image/*" style="display:none;" onchange="this.form.submit()">
                                    <button type="button" onclick="this.previousElementSibling.click()" style="display:flex; flex-direction:column; align-items:center; justify-content:center; width:100%; height:70px; background:transparent; border:none; cursor:pointer; color:#8a7ba5; font-size:0.7rem; gap:2px;">
                                        <span style="font-size:1.4rem;">📷</span>
                                        <span>Subir foto {{ $slot }}</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
                <small style="display:block; margin-top:6px; color:#8a7ba5; font-size:0.65rem; text-align:center;">JPG/PNG/WEBP hasta 4MB.</small>
            </div>

            {{-- Música de fondo --}}
            <div class="card" style="padding:14px 16px;">
                <div class="card-title" style="margin-bottom:8px;">Música de fondo</div>
                @if($event->music_path)
                    <div style="background:#faf8f5; border:1px solid #efe6e9; border-radius:12px; padding:10px 12px;">
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                            <span style="font-size:1.2rem;">🎵</span>
                            <span style="font-size:0.78rem; font-weight:600; color:#3d2c40; flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ basename($event->music_path) }}</span>
                        </div>
                        <audio controls preload="metadata" src="{{ Storage::url($event->music_path) }}" style="width:100%; height:32px; margin-bottom:8px;"></audio>
                        <form method="POST" action="{{ route('event.music.delete') }}" onsubmit="return confirm('¿Eliminar la música?')" style="margin:0;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm" style="width:100%; font-size:0.72rem; padding:6px; background:#fdf2f2; color:#e63946; border:1px solid #fdf2f2; border-radius:8px; cursor:pointer; font-weight:600;">🗑️ Quitar música</button>
                        </form>
                    </div>
                @else
                    <form method="POST" action="{{ route('event.music.upload') }}" enctype="multipart/form-data" id="musicForm">
                        @csrf
                        <label for="music-file" style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:20px 12px; background:#faf8f5; border:1.5px dashed #d8d0c2; border-radius:12px; cursor:pointer; gap:6px; transition: border-color .15s, background .15s;">
                            <span style="font-size:1.8rem;">🎵</span>
                            <span style="font-size:0.85rem; font-weight:700; color:#6e5a63;" id="music-label-text">Subir canción</span>
                            <span style="font-size:0.68rem; color:#8a7ba5;">MP3, M4A, OGG · hasta 10 MB</span>
                            <input type="file" id="music-file" name="music" accept="audio/mpeg,audio/mp3,audio/mp4,audio/m4a,audio/aac,audio/ogg,audio/wav,.mp3,.m4a,.aac,.ogg,.wav" style="display:none;" onchange="onMusicPicked(this)">
                        </label>
                    </form>
                @endif
                <small style="display:block; margin-top:6px; color:#8a7ba5; font-size:0.65rem; text-align:center;">Se reproduce en la invitación al tocar el botón 🎵</small>
            </div>

            {{-- Tarjeta de Enlace (compacta) --}}
            <div class="card" style="padding:14px 16px;">
                <div class="card-title" style="margin-bottom:8px;">Enlace de tu invitación</div>
                <div style="display:flex; gap:6px; align-items:center;">
                    <input type="text" id="shareUrl" value="{{ route('invitation.public', ['slug' => $event->slug]) }}" readonly style="background:#f7f5f0; border-color:#e1ebf2; font-family:monospace; font-size:0.78rem; padding:6px 8px; flex:1; min-width:0;">
                    <button type="button" class="btn" style="margin:0; padding:6px 12px; font-size:0.78rem;" onclick="copyLink()">Copiar</button>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:6px; font-size:0.75rem;">
                    <span id="copy-success" style="color:#086b55; display:none; font-weight:bold;">¡Copiado!</span>
                    <a href="{{ route('invitation.public', ['slug' => $event->slug]) }}?preview=1" target="_blank" style="color:#6e5a63; text-decoration:underline; margin-left:auto;">Vista previa</a>
                </div>
            </div>

            {{-- Resumen Estadístico (compacto en una fila) --}}
            <div class="card" style="padding:12px 16px;">
                <div class="card-title" style="margin-bottom:8px;">Confirmaciones</div>
                <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:6px;">
                    <div style="background:#f6f4f0; border-radius:10px; padding:8px 6px; text-align:center;">
                        <div style="font-size:1.2rem; font-weight:700; line-height:1;">{{ $totalPeople }}</div>
                        <small style="font-size:0.65rem; color:#6e5a63;">Total</small>
                    </div>
                    <div style="background:rgba(51,217,178,0.1); border-radius:10px; padding:8px 6px; text-align:center;">
                        <div style="font-size:1.2rem; font-weight:700; color:#086b55; line-height:1;">{{ $guests->where('attending', true)->count() }}</div>
                        <small style="font-size:0.65rem; color:#086b55;">Sí</small>
                    </div>
                    <div style="background:rgba(230,57,70,0.1); border-radius:10px; padding:8px 6px; text-align:center;">
                        <div style="font-size:1.2rem; font-weight:700; color:#e63946; line-height:1;">{{ $guests->where('attending', false)->count() }}</div>
                        <small style="font-size:0.65rem; color:#e63946;">No</small>
                    </div>
                    <div style="background:rgba(212,163,179,0.15); border-radius:10px; padding:8px 6px; text-align:center;">
                        <div style="font-size:1.2rem; font-weight:700; color:#6e5a63; line-height:1;">{{ $guests->where('attending', true)->sum('companions') }}</div>
                        <small style="font-size:0.65rem; color:#6e5a63;">Acomp.</small>
                    </div>
                </div>
            </div>

            {{-- Galería de invitados --}}
            <div class="card" style="padding:14px 16px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                    <div class="card-title" style="margin:0;">Galería de invitados</div>
                    <span style="font-size:0.7rem; color:#8a7ba5;">{{ $galleryPhotos->count() }} foto{{ $galleryPhotos->count() === 1 ? '' : 's' }}</span>
                </div>
                @if(!$event->show_gallery)
                    <div class="admin-gallery-empty">Activá "Galería de invitados" en el formulario para permitir subidas.</div>
                @elseif($galleryPhotos->isEmpty())
                    <div class="admin-gallery-empty">Aún nadie subió fotos. Compartí el enlace de la invitación.</div>
                @else
                    <div class="admin-gallery-grid">
                        @foreach($galleryPhotos as $photo)
                            <div class="admin-gallery-item" title="Subida por {{ $photo->guest_name }}">
                                <img src="{{ asset($photo->path) }}" alt="Foto de {{ $photo->guest_name }}" loading="lazy">
                                <div class="name-tag">{{ $photo->guest_name }}</div>
                                <form method="POST" action="{{ route('gallery.delete', $photo) }}" style="margin:0;" onsubmit="return confirm('¿Eliminar esta foto de {{ $photo->guest_name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="del-btn" title="Eliminar foto">×</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>

    {{-- Tabla de Invitados (ancho completo) --}}
    <div class="card" style="padding:14px 16px;">
        <div class="card-title" style="margin-bottom:8px;">Lista de confirmados</div>
        <div class="table-container" style="max-height:420px; overflow:auto;">
            <table style="font-size:0.82rem;">
                <thead>
                    <tr>
                        <th style="padding:6px 8px;">Invitado</th>
                        <th style="padding:6px 8px; text-align:center;">Asiste</th>
                        <th style="padding:6px 8px; text-align:center;">Ac.</th>
                        <th style="padding:6px 8px;">Mensaje</th>
                        <th style="padding:6px 4px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guests as $g)
                        <tr>
                            <td style="padding:6px 8px; white-space:nowrap;"><strong>{{ $g->name }}</strong></td>
                            <td style="padding:6px 8px; text-align:center;" class="{{ $g->attending ? 'yes' : 'no' }}">{{ $g->attending ? 'Sí' : 'No' }}</td>
                            <td style="padding:6px 8px; text-align:center;">{{ $g->companions }}</td>
                            <td style="padding:6px 8px; word-wrap:break-word; overflow-wrap:break-word; white-space:normal; line-height:1.4;">{{ $g->message ?: '—' }}</td>
                            <td style="padding:6px 4px;">
                                <form method="POST" action="{{ route('guest.delete',$g) }}" onsubmit="return confirm('¿Eliminar a {{ $g->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-sm" title="Eliminar" style="padding:4px;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;padding:16px;color:#8a7ba5; font-size:0.8rem;">Aún no hay confirmaciones.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>

        // === Generación por campo con IA ===
        const FIELD_TARGETS = { subtitle: 'form-subtitle', extra_info: 'form-extra_info' };

        async function generateField(field) {
            const btn   = document.querySelector('[data-field-btn="' + field + '"]');
            const label = document.querySelector('[data-field-label="' + field + '"]');
            const target = document.getElementById(FIELD_TARGETS[field]);
            if (!btn || !label || !target) return;

            const original = label.textContent;
            btn.disabled = true;
            label.textContent = '✨ Generando...';

            const eventType = document.getElementById('form-event_type').value || 'cumple';
            const title = (document.querySelector('input[name="title"]')?.value || '').trim();

            try {
                const res = await fetch('{{ route("ai.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ field, event_type: eventType, title }),
                });
                const data = res.ok ? await res.json() : { text: null, source: 'error' };
                const text = (data.text || '').trim();

                if (text) target.value = text;

                if (data.source === 'mock' && data.error) {
                    const msgs = {
                        cuota_excedida:    '⚠️ Cuota agotada',
                        servicio_saturado: '⚠️ IA saturada',
                        key_invalida:      '⚠️ Key inválida',
                    };
                    label.textContent = msgs[data.error] || '⚠️ Texto genérico';
                } else if (text) {
                    label.textContent = data.source === 'mock' ? '⚠️ Sin IA' : '✅ Listo';
                } else {
                    label.textContent = '⚠️ Sin resultado';
                }
                setTimeout(() => { label.textContent = original; btn.disabled = false; }, 2000);
            } catch (e) {
                label.textContent = '⚠️ Error';
                setTimeout(() => { label.textContent = original; btn.disabled = false; }, 2000);
            }
        }

        function onMusicPicked(input) {
            const label = document.getElementById('music-label-text');
            if (input.files && input.files[0]) {
                if (label) label.textContent = 'Subiendo ' + input.files[0].name + '...';
                input.form.submit();
            }
        }

        // Copiar enlace al portapapeles
        function copyLink() {
            const copyText = document.getElementById("shareUrl");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value).then(() => {
                const msg = document.getElementById("copy-success");
                msg.style.display = "block";
                setTimeout(() => { msg.style.display = "none"; }, 3000);
            });
        }

        // === Selector de modelos (presets de estilo) ===
        const PRESETS = [
            { id:'baby-rosa',     nombre:'Baby rosa',   emoji:'👶', character:'elefantito',   template:'classic',  color_primary:'#d4a3b3', color_secondary:'#6e5a63', color_accent:'#e5c1cd' },
            { id:'cielo-bebe',    nombre:'Cielo bebé',  emoji:'👶', character:'elefantito',   template:'bubble',   color_primary:'#9fb8c7', color_secondary:'#3d5a6c', color_accent:'#cfe0ea' },
            { id:'angelito-nube', nombre:'Angelito',    emoji:'👼', character:'angelito',     template:'ribbon',   color_primary:'#c9c3e6', color_secondary:'#5a4e8c', color_accent:'#eae3f7' },
            { id:'kitty-pink',    nombre:'Kitty pink',  emoji:'🎀', character:'hello_kitty',  template:'sticker',  color_primary:'#ff6fa5', color_secondary:'#8a2957', color_accent:'#ffd1e0' },
            { id:'kitty-pastel',  nombre:'Kitty lila',  emoji:'🌸', character:'hello_kitty',  template:'polaroid', color_primary:'#d9a7d3', color_secondary:'#6b4a75', color_accent:'#f2dff0' },
            { id:'osito-miel',    nombre:'Osito miel',  emoji:'🐻', character:'osito',        template:'tag',      color_primary:'#e0b56a', color_secondary:'#6b4a1f', color_accent:'#f5e0b0' },
            { id:'osito-bosque',  nombre:'Osito bosque',emoji:'🌿', character:'osito',        template:'postcard', color_primary:'#7fa87a', color_secondary:'#2f4a2b', color_accent:'#c9dcbf' },
            { id:'guerrero-z',    nombre:'Guerrero Z',  emoji:'🐉', character:'goku_nino',    template:'dragon',   color_primary:'#e88b3a', color_secondary:'#3a2b4d', color_accent:'#f4a5b8' },
            { id:'cumple-neon',   nombre:'Neón',        emoji:'🎉', character:'goku_nino',    template:'banner',   color_primary:'#8a4dff', color_secondary:'#2a0e5a', color_accent:'#c6ff5c' },
            { id:'crema-elegante',nombre:'Elegante',    emoji:'✨', character:'none',         template:'frame',    color_primary:'#c9a56a', color_secondary:'#5a4a2a', color_accent:'#f2e6cf' },
            { id:'disco-neon',    nombre:'Disco neón',  emoji:'🕺', character:'none',         template:'disco',    color_primary:'#ff2d95', color_secondary:'#0a0a14', color_accent:'#00e5ff' },
            { id:'neon-party',    nombre:'Neon party',  emoji:'😈', character:'emogimalo',    template:'neonparty',color_primary:'#ff1744', color_secondary:'#0a0000', color_accent:'#ff4d6d' },
        ];

        function renderPresets() {
            const grid = document.getElementById('preset-grid');
            if (!grid) return;
            const current = {
                color_primary:   (document.getElementById('form-color_primary').value || '').toLowerCase(),
                color_secondary: (document.getElementById('form-color_secondary').value || '').toLowerCase(),
                color_accent:    (document.getElementById('form-color_accent').value || '').toLowerCase(),
                character:       document.getElementById('form-theme_character').value || 'none',
                template:        document.getElementById('form-template').value || 'classic',
            };
            grid.innerHTML = PRESETS.map(p => {
                const active = p.color_primary.toLowerCase() === current.color_primary
                            && p.character === current.character
                            && p.template === current.template;
                const imgSrc = p.character !== 'none'
                    ? `{{ asset('images/themes') }}/${p.character}.png`
                    : '';
                return `
                    <button type="button" data-preset="${p.id}" title="${p.nombre}"
                        style="cursor:pointer; padding:0; border:2px solid ${active ? p.color_secondary : 'transparent'};
                               border-radius:10px; background:linear-gradient(135deg, ${p.color_primary} 0%, ${p.color_accent} 100%);
                               aspect-ratio:1/1; display:flex; flex-direction:column; align-items:center; justify-content:center;
                               box-shadow:${active ? '0 4px 10px rgba(0,0,0,0.15)' : '0 2px 4px rgba(0,0,0,0.06)'};
                               overflow:hidden; position:relative;">
                        ${imgSrc
                            ? `<img src="${imgSrc}" alt="" style="width:60%; height:60%; object-fit:contain; pointer-events:none;">`
                            : `<span style="font-size:1.4rem;">${p.emoji}</span>`}
                        <span style="font-size:0.6rem; font-weight:700; color:${p.color_secondary}; background:rgba(255,255,255,0.75); padding:1px 4px; border-radius:6px; margin-top:2px; max-width:90%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${p.nombre}</span>
                    </button>`;
            }).join('');
            grid.querySelectorAll('button[data-preset]').forEach(btn => {
                btn.addEventListener('click', () => applyPreset(btn.dataset.preset));
            });
        }

        function applyPreset(id) {
            const p = PRESETS.find(x => x.id === id);
            if (!p) return;
            document.getElementById('form-color_primary').value   = p.color_primary;
            document.getElementById('form-color_secondary').value = p.color_secondary;
            document.getElementById('form-color_accent').value    = p.color_accent;
            document.getElementById('form-theme_character').value = p.character;
            document.getElementById('form-template').value = p.template;
            if (!document.getElementById('form-emoji').value) {
                document.getElementById('form-emoji').value = p.emoji;
            }
            document.getElementById('edit-form').submit();
        }

        document.addEventListener('DOMContentLoaded', renderPresets);

        // === Mapa Leaflet en formulario clásico ===
        let editMap = null, editMarker = null;

        (function initEditMap(){
            const mapEl = document.getElementById('edit-map');
            if (!mapEl || typeof L === 'undefined') return;
            const latInput = document.getElementById('edit-lat');
            const lngInput = document.getElementById('edit-lng');
            const startLat = parseFloat(latInput.value) || -12.046374;
            const startLng = parseFloat(lngInput.value) || -77.042793;

            editMap = L.map('edit-map').setView([startLat, startLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution:'© OpenStreetMap', maxZoom:19,
            }).addTo(editMap);

            editMarker = L.marker([startLat, startLng], { draggable:true }).addTo(editMap);

            editMarker.on('dragend', e => {
                const p = e.target.getLatLng();
                latInput.value = p.lat.toFixed(6);
                lngInput.value = p.lng.toFixed(6);
            });
            editMap.on('click', e => {
                editMarker.setLatLng(e.latlng);
                latInput.value = e.latlng.lat.toFixed(6);
                lngInput.value = e.latlng.lng.toFixed(6);
            });

            setTimeout(() => editMap.invalidateSize(), 250);

            // Reajustar el mapa cuando el contenedor cambia de altura (hover / touch)
            mapEl.addEventListener('transitionend', e => {
                if (e.propertyName === 'height') editMap.invalidateSize();
            });
            // Soporte touch: primer tap expande, segundo permite mover el pin
            mapEl.addEventListener('touchstart', () => {
                if (!mapEl.classList.contains('expanded')) {
                    mapEl.classList.add('expanded');
                }
            }, { passive: true });

            // Auto-disparar geolocalización si el evento aún tiene las coords por defecto (Cusco)
            const isDefault = Math.abs(startLat - (-13.516799)) < 0.0005
                           && Math.abs(startLng - (-71.978817)) < 0.0005;
            if (isDefault) {
                setTimeout(useMyLocation, 800);
            }
        })();

        // === Autocomplete de dirección (Nominatim / OpenStreetMap) ===
        (function initPlaceSearch(){
            const input   = document.getElementById('edit-place');
            const results = document.getElementById('edit-place-results');
            const status  = document.getElementById('edit-place-status');
            if (!input || !results) return;

            let timer = null;
            let lastQuery = '';
            let activeIdx = -1;
            let items = [];

            function clearResults() {
                results.innerHTML = '';
                results.classList.remove('open');
                activeIdx = -1;
                items = [];
            }

            function renderResults(hits) {
                if (!hits.length) {
                    results.innerHTML = '<div class="place-search-empty">Sin resultados</div>';
                    results.classList.add('open');
                    return;
                }
                items = hits;
                results.innerHTML = hits.map((h, i) => {
                    const parts = (h.display_name || '').split(',');
                    const main = parts[0]?.trim() || h.display_name;
                    const sub  = parts.slice(1, 4).join(',').trim();
                    const badge = h.__nearby ? '<span class="place-badge">cerca</span>' : '';
                    return `<div class="place-search-item" data-idx="${i}" onclick="selectPlace(${i})">
                        <span class="icon">📍</span>
                        <div style="flex:1; min-width:0;">
                            <div class="main">${escapeHtml(main)} ${badge}</div>
                            ${sub ? `<div class="sub">${escapeHtml(sub)}</div>` : ''}
                        </div>
                    </div>`;
                }).join('');
                results.classList.add('open');
            }

            function escapeHtml(s){ return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

            function currentCenter() {
                if (editMarker) {
                    const p = editMarker.getLatLng();
                    return { lat: p.lat, lng: p.lng };
                }
                const lat = parseFloat(document.getElementById('edit-lat').value);
                const lng = parseFloat(document.getElementById('edit-lng').value);
                return isNaN(lat) || isNaN(lng) ? null : { lat, lng };
            }

            let lastQueryText = '';
            async function search(q, { forceGlobal = false } = {}) {
                lastQueryText = q;
                status.textContent = 'buscando...';
                try {
                    const c = currentCenter();
                    let extra = '';
                    // Por defecto restringimos a un viewbox alrededor del pin (~90 km de lado).
                    if (c && !forceGlobal) {
                        const d = 0.8;
                        extra = `&viewbox=${c.lng - d},${c.lat + d},${c.lng + d},${c.lat - d}&bounded=1`;
                    }
                    const url = `https://nominatim.openstreetmap.org/search?format=json&limit=6&accept-language=es&addressdetails=1${extra}&q=${encodeURIComponent(q)}`;
                    const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    status.textContent = '';
                    if (!r.ok) { clearResults(); return; }
                    const data = await r.json();

                    if (!Array.isArray(data) || data.length === 0) {
                        if (!forceGlobal && c) {
                            renderEmptyWithGlobal();
                        } else {
                            renderResults([]);
                        }
                        return;
                    }

                    if (c) {
                        data.forEach(h => {
                            const dLat = parseFloat(h.lat) - c.lat;
                            const dLng = parseFloat(h.lon) - c.lng;
                            h.__dist = Math.sqrt(dLat*dLat + dLng*dLng);
                            h.__nearby = !forceGlobal;
                        });
                        data.sort((a, b) => a.__dist - b.__dist);
                    }
                    renderResults(data);
                } catch (e) {
                    status.textContent = '';
                    clearResults();
                }
            }

            function renderEmptyWithGlobal() {
                items = [];
                results.innerHTML = `
                    <div class="place-search-empty">Sin resultados cerca de tu ubicación</div>
                    <button type="button" class="place-search-global" onclick="searchGlobalPlace()">🌎 Buscar en cualquier lugar</button>
                `;
                results.classList.add('open');
            }

            window.searchGlobalPlace = function() {
                if (lastQueryText) search(lastQueryText, { forceGlobal: true });
            };

            input.addEventListener('input', () => {
                const q = input.value.trim();
                if (q.length < 3) { clearResults(); status.textContent = ''; return; }
                if (q === lastQuery) return;
                lastQuery = q;
                clearTimeout(timer);
                timer = setTimeout(() => search(q), 450);
            });

            input.addEventListener('keydown', e => {
                if (!results.classList.contains('open') || items.length === 0) return;
                if (e.key === 'ArrowDown') { e.preventDefault(); activeIdx = (activeIdx + 1) % items.length; highlight(); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); activeIdx = (activeIdx - 1 + items.length) % items.length; highlight(); }
                else if (e.key === 'Enter' && activeIdx >= 0) { e.preventDefault(); selectPlace(activeIdx); }
                else if (e.key === 'Escape') { clearResults(); }
            });

            function highlight() {
                results.querySelectorAll('.place-search-item').forEach((el, i) => {
                    el.classList.toggle('active', i === activeIdx);
                });
            }

            // Cerrar al hacer click fuera
            document.addEventListener('click', e => {
                if (!input.contains(e.target) && !results.contains(e.target)) clearResults();
            });

            // Exponer selectPlace globalmente
            window.selectPlace = function(idx) {
                const it = items[idx];
                if (!it) return;
                const lat = parseFloat(it.lat);
                const lng = parseFloat(it.lon);
                if (isNaN(lat) || isNaN(lng)) return;

                // Actualizar campo con nombre acortado
                const parts = (it.display_name || '').split(',').slice(0, 3);
                input.value = parts.join(',').trim();
                lastQuery = input.value;

                // Actualizar mapa + coords
                if (editMap && editMarker) {
                    editMap.setView([lat, lng], 16);
                    editMarker.setLatLng([lat, lng]);
                }
                document.getElementById('edit-lat').value = lat.toFixed(6);
                document.getElementById('edit-lng').value = lng.toFixed(6);
                clearResults();
            };
        })();

        // Función global: usar mi ubicación actual
        function useMyLocation() {
            const btn   = document.getElementById('btn-mylocation');
            const label = document.getElementById('btn-mylocation-label');
            if (!navigator.geolocation) {
                label.textContent = '⚠️ No disponible';
                setTimeout(() => label.textContent = '📍 Mi ubicación', 2000);
                return;
            }
            btn.disabled = true;
            label.textContent = '📡 Ubicando...';

            navigator.geolocation.getCurrentPosition(
                async pos => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    if (editMap && editMarker) {
                        editMap.setView([lat, lng], 16);
                        editMarker.setLatLng([lat, lng]);
                    }
                    document.getElementById('edit-lat').value = lat.toFixed(6);
                    document.getElementById('edit-lng').value = lng.toFixed(6);
                    label.textContent = '✅ Ubicación fijada';

                    // Reverse geocoding: llenar el campo "lugar" si está vacío o con el default
                    try {
                        const placeInput = document.querySelector('input[name="place"]');
                        const currentPlace = placeInput?.value?.trim() || '';
                        if (placeInput && (currentPlace === '' || currentPlace === 'Cusco, Perú')) {
                            const r = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=es`, {
                                headers: { 'Accept': 'application/json' }
                            });
                            if (r.ok) {
                                const d = await r.json();
                                if (d.display_name) placeInput.value = d.display_name.split(',').slice(0,3).join(',').trim();
                            }
                        }
                    } catch (e) { /* silencioso */ }

                    setTimeout(() => { label.textContent = '📍 Mi ubicación'; btn.disabled = false; }, 2500);
                },
                err => {
                    const msgs = {
                        1: '⚠️ Permiso denegado',
                        2: '⚠️ No se pudo obtener',
                        3: '⚠️ Tardó demasiado',
                    };
                    label.textContent = msgs[err.code] || '⚠️ Error';
                    setTimeout(() => { label.textContent = '📍 Mi ubicación'; btn.disabled = false; }, 2500);
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
            );
        }

        // === Buscador de imágenes de revelación ===
        let revealCurrentSlot = null;
        let revealSearchTimer = null;
        let revealCurrentTab = 'local';

        function openRevealSearch(slot) {
            revealCurrentSlot = slot;
            const modal = document.getElementById('revealSearchModal');
            if (!modal) return;
            modal.style.display = 'flex';
            const input = document.getElementById('revealSearchInput');
            input.value = '';
            input.focus();
            switchRevealTab('local');
        }

        function closeRevealSearch() {
            const modal = document.getElementById('revealSearchModal');
            if (modal) modal.style.display = 'none';
            revealCurrentSlot = null;
        }

        function switchRevealTab(tab) {
            revealCurrentTab = tab;
            document.getElementById('revealTabLocal').classList.toggle('reveal-tab-active', tab === 'local');
            document.getElementById('revealTabWeb').classList.toggle('reveal-tab-active', tab === 'web');
            const input = document.getElementById('revealSearchInput');
            input.placeholder = tab === 'web' ? 'Buscar en Google: goku, shrek, toy story…' : 'Buscar en tu catálogo…';
            runRevealSearch(input.value);
        }

        function runRevealSearch(q) {
            clearTimeout(revealSearchTimer);
            revealSearchTimer = setTimeout(() => {
                if (revealCurrentTab === 'web') fetchRevealWeb(q);
                else fetchRevealResults(q);
            }, 250);
        }

        async function fetchRevealResults(q) {
            showRevealLoading(false);
            try {
                const url = "{{ route('reveal.search') }}?q=" + encodeURIComponent(q);
                const res = await fetch(url);
                if (!res.ok) return;
                const items = await res.json();
                const grid = document.getElementById('revealSearchResults');
                const empty = document.getElementById('revealSearchEmpty');
                if (items.length === 0) {
                    grid.innerHTML = '';
                    empty.style.display = 'block';
                    empty.textContent = 'Sin resultados en tu catálogo. Probá la pestaña "Buscar en la web".';
                    return;
                }
                empty.style.display = 'none';
                grid.innerHTML = items.map(it => `
                    <button type="button" onclick="pickRevealImage('${it.path.replace(/'/g, "\\'")}')" class="reveal-thumb">
                        <img src="/${it.path}" alt="${it.name}">
                        <span>${it.name}</span>
                    </button>`).join('');
            } catch (e) { console.warn('reveal search failed:', e); }
        }

        async function fetchRevealWeb(q) {
            const grid = document.getElementById('revealSearchResults');
            const empty = document.getElementById('revealSearchEmpty');
            empty.style.display = 'none';
            if (!q || q.trim().length < 2) {
                grid.innerHTML = '';
                empty.style.display = 'block';
                empty.textContent = 'Escribí al menos 2 letras para buscar en la web.';
                return;
            }
            showRevealLoading(true);
            grid.innerHTML = '';
            try {
                const url = "{{ route('reveal.search.web') }}?q=" + encodeURIComponent(q);
                const res = await fetch(url);
                showRevealLoading(false);
                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    empty.style.display = 'block';
                    empty.textContent = err.error || 'La búsqueda web falló. Intentá otra vez en un momento.';
                    return;
                }
                const items = await res.json();
                if (!items.length) {
                    empty.style.display = 'block';
                    empty.textContent = 'Sin resultados para "' + q + '".';
                    return;
                }
                grid.innerHTML = items.map(it => `
                    <button type="button" onclick="importRevealImage('${it.url.replace(/'/g,"\\'")}','${(it.title||'').replace(/'/g,"\\'").slice(0,60)}')" class="reveal-thumb">
                        <img src="${it.thumbnail}" alt="" loading="lazy" onerror="this.style.opacity=0.2;">
                        <span>${it.title || 'imagen'}</span>
                        <span class="reveal-thumb-badge">🌐</span>
                    </button>`).join('');
            } catch (e) {
                showRevealLoading(false);
                empty.style.display = 'block';
                empty.textContent = 'Error de red: ' + e.message;
            }
        }

        function showRevealLoading(show) {
            const el = document.getElementById('revealSearchLoading');
            if (el) el.style.display = show ? 'block' : 'none';
        }

        function pickRevealImage(path) {
            if (!revealCurrentSlot) return;
            document.getElementById('revealSelectSlot').value = revealCurrentSlot;
            document.getElementById('revealSelectPath').value = path;
            document.getElementById('revealSelectForm').submit();
        }

        function importRevealImage(url, title) {
            if (!revealCurrentSlot) return;
            document.getElementById('revealImportSlot').value = revealCurrentSlot;
            document.getElementById('revealImportUrl').value = url;
            document.getElementById('revealImportTitle').value = title || '';
            document.getElementById('revealImportForm').submit();
        }

        document.addEventListener('input', (e) => {
            if (e.target && e.target.id === 'revealSearchInput') {
                runRevealSearch(e.target.value);
            }
        });
        document.addEventListener('click', (e) => {
            const modal = document.getElementById('revealSearchModal');
            if (modal && e.target === modal) closeRevealSearch();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeRevealSearch();
        });

        // === Música de fondo relajante ===
        (function initAmbientAudio(){
            const audio = document.createElement('audio');
            audio.src = "{{ asset('audio/admin-ambient.mp3') }}";
            audio.loop = true;
            audio.volume = 0.22;
            audio.preload = 'auto';
            document.body.appendChild(audio);

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.setAttribute('aria-label', 'Música de fondo');
            btn.style.cssText = `
                position:fixed; bottom:18px; right:18px; z-index:9999;
                width:48px; height:48px; border-radius:50%; border:none;
                background:#fff; box-shadow:0 6px 18px rgba(0,0,0,0.18);
                cursor:pointer; font-size:1.4rem; line-height:1;
                display:flex; align-items:center; justify-content:center;
                transition:transform 0.15s;
            `;
            btn.onmouseenter = () => btn.style.transform = 'scale(1.08)';
            btn.onmouseleave = () => btn.style.transform = 'scale(1)';
            document.body.appendChild(btn);

            let playing = false;
            const KEY = 'admin_ambient_on';

            function render(){
                btn.textContent = playing ? '🔊' : '🔈';
                btn.title = playing ? 'Pausar música' : 'Activar música relajante';
                btn.style.background = playing ? '#eae3f7' : '#fff';
            }

            async function toggle(){
                if (playing) {
                    audio.pause();
                    playing = false;
                    localStorage.setItem(KEY, '0');
                } else {
                    try {
                        await audio.play();
                        playing = true;
                        localStorage.setItem(KEY, '1');
                    } catch (e) {
                        console.warn('No se pudo reproducir:', e);
                    }
                }
                render();
            }
            btn.addEventListener('click', toggle);

            // Restaurar preferencia (requiere primer gesto del usuario en algunos navegadores)
            if (localStorage.getItem(KEY) === '1') {
                const resume = () => {
                    audio.play().then(() => { playing = true; render(); }).catch(() => {});
                    document.removeEventListener('click', resume);
                };
                document.addEventListener('click', resume, { once:true });
            }
            render();
        })();
    </script>
</body>
</html>
