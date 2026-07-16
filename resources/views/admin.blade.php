<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel · Asistente de Configuración</title>
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
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 8px;
            border-bottom: 1px solid #e1ebf2;
            padding-bottom: 12px;
        }
        h1{color:#6e5a63;font-size:1.45rem;font-weight:700}
        .subtitle-desc {color:#8a7ba5;font-size:0.85rem;margin-top:2px}

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

        .btn-view-invite {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            background: #fff;
            color: #6e5a63;
            border: 1.5px solid #d4a3b3;
            padding: 8px 16px;
            border-radius: 999px;
            font-weight: bold;
            font-size: 0.82rem;
            transition: 0.2s;
        }
        .btn-view-invite:hover {
            background: #d4a3b3;
            color: #fff;
            transform: translateY(-2px);
        }

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
            header { flex-direction: column; align-items: flex-start !important; gap: 8px; }
            header > div:last-child { width: 100%; display: flex; gap: 6px; flex-wrap: wrap; }
            .btn-view-invite { flex: 1; text-align: center; padding: 8px 10px !important; font-size: 0.8rem !important; }

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

        /* Burbujas del Asistente de Configuración */
        .wizard-msg {
            max-width: 85%;
            padding: 10px 14px;
            border-radius: 14px;
            font-size: 0.88rem;
            line-height: 1.35;
            animation: popMsg 0.2s ease;
        }
        @keyframes popMsg {
            from { transform: scale(0.97); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .wizard-msg.bot {
            background: #fff;
            align-self: flex-start;
            border: 1px solid #e1ebf2;
            color: #2c2235;
            box-shadow: 0 4px 10px rgba(0,0,0,0.01);
            border-bottom-left-radius: 4px;
        }
        .wizard-msg.user {
            background: #6e5a63;
            color: #fff;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
            box-shadow: 0 4px 12px rgba(110,90,99,0.1);
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>Control de Invitación</h1>
            <p class="subtitle-desc">Configura tu evento mediante el asistente interactivo.</p>
        </div>
        <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            @if($event->is_published)
                <span class="published-badge">Publicada</span>
                <form method="POST" action="{{ route('event.publish') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="publish" value="0">
                    <button type="submit" class="btn-view-invite" style="background:#fdf2f2; color:#e63946; border-color:#fdf2f2; cursor:pointer;" onclick="return confirm('¿Despublicar y volver a modo borrador?')">Despublicar</button>
                </form>
            @else
                <form method="POST" action="{{ route('event.publish') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="publish" value="1">
                    <button type="submit" class="btn-view-invite" style="background:#086b55; color:#fff; border-color:#086b55; cursor:pointer;">Publicar invitación</button>
                </form>
            @endif
            <a href="{{ route('invitation.public', ['slug' => $event->slug]) }}?preview=1" target="_blank" class="btn-view-invite">Ver Invitación</a>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-view-invite" style="background:#fdf2f2; color:#e63946; border-color:#fdf2f2; cursor:pointer;">Cerrar Sesión</button>
            </form>
        </div>
    </div>

    @if(session('saved'))<div class="saved">{{ session('saved') }}</div>@endif

    <div class="admin-grid">
        
        {{-- COLUMNA IZQUIERDA: FORMULARIO DE EDICIÓN + ASISTENTE --}}
        <div>
            {{-- FORMULARIO CLÁSICO --}}
            <div class="card" style="padding:16px 20px; margin-bottom:16px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                    <div class="card-title" style="margin:0;">Detalles del evento</div>
                    <button type="button" onclick="toggleWizard()" style="background:#eae3f7; border:none; color:#5a4e8c; padding:6px 12px; border-radius:8px; font-size:0.75rem; font-weight:600; cursor:pointer;">💬 Asistente IA</button>
                </div>

                <form method="POST" action="{{ route('event.update') }}" id="edit-form">
                    @csrf
                    <input type="hidden" name="color_primary" value="{{ $event->color_primary }}">
                    <input type="hidden" name="color_accent" value="{{ $event->color_accent }}">
                    <input type="hidden" name="color_secondary" value="{{ $event->color_secondary }}">
                    <input type="hidden" name="theme_character" value="{{ $event->theme_character }}">
                    <input type="hidden" name="template" value="{{ $event->template ?? 'classic' }}">
                    <input type="hidden" name="is_published" value="{{ $event->is_published ? '1' : '0' }}">
                    <input type="hidden" name="rsvp_button_text" value="Confirmar asistencia">

                    <div class="edit-grid">
                        <div class="edit-field">
                            <label>Emoji</label>
                            <input type="text" name="emoji" maxlength="8" value="{{ $event->emoji }}" placeholder="🎂" style="text-align:center; font-size:1.3rem;">
                        </div>
                        <div class="edit-field" style="flex:1;">
                            <label>Tipo de evento</label>
                            <select name="event_type">
                                <option value="babyshower"   @selected($event->event_type==='babyshower')>🍼 Baby Shower</option>
                                <option value="cumple"       @selected($event->event_type==='cumple')>🎂 Cumpleaños</option>
                                <option value="bautizo"      @selected($event->event_type==='bautizo')>🕊️ Bautizo</option>
                                <option value="revelacion"   @selected($event->event_type==='revelacion')>🤰 Revelación de género</option>
                                <option value="bienvenida"   @selected($event->event_type==='bienvenida')>🍼 Bienvenida</option>
                                <option value="comunion"     @selected($event->event_type==='comunion')>🕯️ Comunión</option>
                            </select>
                        </div>
                    </div>

                    <div class="edit-field">
                        <label>Título</label>
                        <input type="text" name="title" required maxlength="120" value="{{ $event->title }}">
                    </div>

                    <div class="edit-field">
                        <label>Subtítulo (opcional)</label>
                        <textarea name="subtitle" rows="2" maxlength="200">{{ $event->subtitle }}</textarea>
                    </div>

                    <div class="edit-field">
                        <label>Fecha y hora</label>
                        <input type="datetime-local" name="date" required value="{{ optional($event->date)->format('Y-m-d\TH:i') }}">
                    </div>

                    <div class="edit-field">
                        <label>Dirección / lugar</label>
                        <input type="text" name="place" required maxlength="200" value="{{ $event->place }}" placeholder="Ej: Calle Los Sauces 123, Lima">
                    </div>

                    <div class="edit-grid">
                        <div class="edit-field">
                            <label>Latitud</label>
                            <input type="number" name="lat" step="any" required value="{{ $event->lat }}" id="edit-lat">
                        </div>
                        <div class="edit-field">
                            <label>Longitud</label>
                            <input type="number" name="lng" step="any" required value="{{ $event->lng }}" id="edit-lng">
                        </div>
                    </div>

                    <div class="edit-field">
                        <label>Ubicación en el mapa <small style="color:#8a7ba5;">— tocá para mover el pin</small></label>
                        <div id="edit-map" style="height:200px; border-radius:12px; overflow:hidden; border:1px solid #e1ebf2;"></div>
                    </div>

                    <div class="edit-field">
                        <label>Notas especiales (opcional)</label>
                        <textarea name="extra_info" rows="3" maxlength="1000" placeholder="Vestimenta, lluvia de sobres, sugerencia de regalos…">{{ $event->extra_info }}</textarea>
                    </div>

                    <button type="submit" class="btn-save-form">💾 Guardar cambios</button>
                </form>
            </div>

            {{-- ASISTENTE IA (oculto por default, se abre con el botón) --}}
            <div id="wizard-container" style="display:none;">
            <div class="card" style="height: 470px; display: flex; flex-direction: column; padding: 0; overflow: hidden; position: relative; border-radius: 18px;">
                <div class="card-title" style="margin: 0; padding: 12px 18px; background: #fff; border-bottom: 1.5px solid #eae3f7; display:flex; justify-content:space-between; align-items:center;">
                    <span>Asistente de Configuración</span>
                    <button type="button" onclick="toggleWizard()" style="background:transparent; border:none; color:#5a4e8c; cursor:pointer; font-size:1.2rem;">✕</button>
                </div>
                
                {{-- Contenedor de mensajes --}}
                <div id="wizard-chat-messages" style="flex: 1; padding: 12px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background: #faf8ff;">
                    <!-- Mensajes inyectados por JS -->
                </div>
                
                {{-- Formulario oculto para el guardado final --}}
                <form id="wizard-form" method="POST" action="{{ route('event.update') }}" style="display:none;">
                    @csrf
                    <input type="hidden" name="emoji" id="form-emoji" value="{{ $event->emoji }}">
                    <input type="hidden" name="title" id="form-title" value="{{ $event->title }}">
                    <input type="hidden" name="subtitle" id="form-subtitle" value="{{ $event->subtitle }}">
                    <input type="hidden" name="date" id="form-date" value="{{ optional($event->date)->format('Y-m-d\TH:i') }}">
                    <input type="hidden" name="place" id="form-place" value="{{ $event->place }}">
                    <input type="hidden" name="lat" id="form-lat" value="{{ $event->lat }}">
                    <input type="hidden" name="lng" id="form-lng" value="{{ $event->lng }}">
                    <input type="hidden" name="color_primary" id="form-color_primary" value="{{ $event->color_primary }}">
                    <input type="hidden" name="color_accent" id="form-color_accent" value="{{ $event->color_accent }}">
                    <input type="hidden" name="color_secondary" id="form-color_secondary" value="{{ $event->color_secondary }}">
                    <input type="hidden" name="extra_info" id="form-extra_info" value="{{ $event->extra_info }}">
                    <input type="hidden" name="is_published" id="form-is_published" value="{{ $event->is_published ? '1' : '0' }}">
                    <input type="hidden" name="theme_character" id="form-theme_character" value="{{ $event->theme_character }}">
                    <input type="hidden" name="template" id="form-template" value="{{ $event->template ?? 'classic' }}">
                    <input type="hidden" name="event_type" id="form-event_type" value="{{ $event->event_type }}">
                    
                    {{-- Compatibilidad --}}
                    <input type="hidden" name="rsvp_button_text" value="Confirmar asistencia">
                    <input type="hidden" name="share_message" value="">
                    <input type="hidden" name="dress_code" value="">
                    <input type="hidden" name="gift_info" value="">
                </form>

                {{-- Inputs dinámicos --}}
                <div id="wizard-input-area" style="padding: 10px 14px; background: #fff; border-top: 1.5px solid #eae3f7; display: flex; flex-direction: column; gap: 8px; z-index: 10;">
                    <!-- Renderizado dinámico en JS -->
                </div>
            </div>
            </div>{{-- fin #wizard-container --}}
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
                            <div style="border:1.5px dashed #d8d0c2; border-radius:10px; padding:6px; text-align:center; background:#fbf8f2;">
                                <div style="font-size:0.7rem; color:#8a7ba5; font-weight:700; margin-bottom:4px;">{{ $s['label'] }}</div>
                                @if($path)
                                    <img src="{{ asset($path) }}" alt="{{ $s['label'] }}" style="width:100%; height:70px; object-fit:contain; margin-bottom:4px;">
                                    <div style="display:flex; gap:4px;">
                                        <button type="button" class="btn-sm" style="flex:1; font-size:0.65rem; padding:4px;" onclick="openRevealSearch({{ $s['slot'] }})">🔄 Cambiar</button>
                                        <form method="POST" action="{{ route('event.reveal.remove') }}" style="flex:1; margin:0;">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="slot" value="{{ $s['slot'] }}">
                                            <button type="submit" class="btn-sm" style="width:100%; font-size:0.65rem; padding:4px;">🗑️</button>
                                        </form>
                                    </div>
                                @else
                                    <button type="button" onclick="openRevealSearch({{ $s['slot'] }})" style="width:100%; height:70px; background:transparent; border:none; cursor:pointer; color:#8a7ba5; font-size:0.7rem; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:2px;">
                                        <span style="font-size:1.4rem;">🔍</span>
                                        <span>Buscar imagen</span>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <small style="display:block; margin-top:6px; color:#8a7ba5; font-size:0.65rem; text-align:center;">Buscá por nombre: goku, kitty, angel…</small>
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

            {{-- Tabla de Invitados (compacta) --}}
            <div class="card" style="padding:14px 16px;">
                <div class="card-title" style="margin-bottom:8px;">Lista de confirmados</div>
                <div class="table-container" style="max-height:320px; overflow:auto;">
                    <table style="font-size:0.78rem;">
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
                                    <td style="padding:6px 8px;"><strong>{{ $g->name }}</strong></td>
                                    <td style="padding:6px 8px; text-align:center;" class="{{ $g->attending ? 'yes' : 'no' }}">{{ $g->attending ? 'Sí' : 'No' }}</td>
                                    <td style="padding:6px 8px; text-align:center;">{{ $g->companions }}</td>
                                    <td style="padding:6px 8px; max-width:120px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $g->message }}">{{ $g->message ?: '—' }}</td>
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
        </div>

    </div>

    <script>
        // Objeto global de estado de la configuración conversacional
        let wizardData = {
            emoji: @json($event->emoji),
            title: @json($event->title),
            subtitle: @json($event->subtitle),
            date: @json(optional($event->date)->format('Y-m-d\TH:i')),
            place: @json($event->place),
            lat: parseFloat("{{ $event->lat ?? -13.516799 }}".replace(',', '.')),
            lng: parseFloat("{{ $event->lng ?? -71.978817 }}".replace(',', '.')),
            color_primary: @json($event->color_primary),
            color_accent: @json($event->color_accent),
            color_secondary: @json($event->color_secondary),
            extra_info: @json($event->extra_info),
            is_published: {{ $event->is_published ? 'true' : 'false' }},
            theme_character: @json($event->theme_character ?: 'none'),
            event_type: @json($event->event_type ?: 'cumple')
        };

        let activePreset = 'babyshower';

        // Inicializar el chat con bienvenida
        window.addEventListener('DOMContentLoaded', () => {
            appendBotMessage("¡Hola! Soy tu asistente de eventos. Vamos a configurar la invitación de tu fiesta juntos.");
            appendBotMessage("Actualmente tienes guardado el evento: <strong>{{ $event->title ?: 'Sin título' }}</strong>.<br><br>¿Qué prefieres hacer?");
            renderWelcomeOptions();
        });

        // Auxiliares de chat
        function appendBotMessage(text) {
            const container = document.getElementById('wizard-chat-messages');
            const msg = document.createElement('div');
            msg.className = 'wizard-msg bot';
            msg.innerHTML = text;
            container.appendChild(msg);
            container.scrollTop = container.scrollHeight;
        }

        function appendUserMessage(text) {
            const container = document.getElementById('wizard-chat-messages');
            const msg = document.createElement('div');
            msg.className = 'wizard-msg user';
            msg.textContent = text;
            container.appendChild(msg);
            container.scrollTop = container.scrollHeight;
        }

        // Paso 1: Bienvenida
        function renderWelcomeOptions() {
            const inputArea = document.getElementById('wizard-input-area');
            inputArea.innerHTML = `
                <div style="display:flex; gap:10px; width:100%;">
                    <button type="button" class="btn" style="flex:1; margin:0; background:#eae3f7; color:#6e5a63; box-shadow:none;" onclick="startWizard(false)">Empezar de Cero</button>
                    <button type="button" class="btn" style="flex:1; margin:0; background:#d4a3b3; color:#fff;" onclick="startWizard(true)">Editar Evento Actual</button>
                </div>
            `;
        }

        function startWizard(keepCurrent) {
            if (!keepCurrent) {
                wizardData = {
                    emoji: '👶',
                    title: 'Mi Baby Shower',
                    subtitle: 'Te invitamos a celebrar con nosotros',
                    date: '',
                    place: 'Cusco, Perú',
                    lat: -13.516799,
                    lng: -71.978817,
                    color_primary: '#d4a3b3',
                    color_accent: '#e5c1cd',
                    color_secondary: '#6e5a63',
                    extra_info: '',
                    is_published: false,
                    theme_character: 'none'
                };
                appendUserMessage("Quiero configurar un evento desde cero.");
            } else {
                appendUserMessage("Quiero modificar los datos de mi evento actual.");
            }
            
            appendBotMessage("Excelente. ¿De qué tipo será la fiesta que vas a celebrar?");
            renderEventTypeOptions();
        }

        // Paso 2: Tipo de Evento
        function renderEventTypeOptions() {
            const inputArea = document.getElementById('wizard-input-area');
            inputArea.innerHTML = `
                <div style="display:flex; gap:8px; flex-wrap:wrap; width:100%;">
                    <button type="button" class="tag-btn" onclick="selectEventType('babyshower', '🍼 Baby Shower', '👶')">🍼 Baby Shower</button>
                    <button type="button" class="tag-btn" onclick="selectEventType('cumple', '🎂 Cumpleaños', '🎂')">🎂 Cumpleaños</button>
                    <button type="button" class="tag-btn" onclick="selectEventType('bautizo', '🕊️ Bautizo', '🕊️')">🕊️ Bautizo</button>
                    <button type="button" class="tag-btn" onclick="selectEventType('revelacion', '🤰 Revelación de Sexo', '🤰')">🤰 Revelación</button>
                    <button type="button" class="tag-btn" onclick="selectEventType('bienvenida', '🍼 Bienvenida', '🍼')">🍼 Bienvenida</button>
                    <button type="button" class="tag-btn" onclick="selectEventType('comunion', '🕯️ Comunión', '🕯️')">🕯️ Comunión</button>
                </div>
            `;
        }

        function selectEventType(presetName, labelText, emoji) {
            activePreset = presetName;
            wizardData.emoji = emoji;
            wizardData.event_type = presetName;
            
            // Sugerir títulos por defecto si fue borrado o es nuevo
            if (wizardData.title === 'Mi Baby Shower' || !wizardData.title) {
                if (presetName === 'cumple') wizardData.title = '¡Mi Cumpleaños!';
                else if (presetName === 'bautizo') wizardData.title = 'Mi Bautizo';
                else if (presetName === 'revelacion') wizardData.title = 'Revelación de Sexo';
                else if (presetName === 'bienvenida') wizardData.title = 'Bienvenida al Bebé';
                else if (presetName === 'comunion') wizardData.title = 'Mi Primera Comunión';
            }
            
            appendUserMessage("Será un: " + labelText);
            appendBotMessage("¡Fantástico! Guardé el tipo de evento y el emoji.<br><br>¿Qué título te gustaría ponerle a la invitación?");
            renderTitleOptions();
        }

        // Paso 3: Título
        function renderTitleOptions() {
            const inputArea = document.getElementById('wizard-input-area');
            const suggest = wizardData.title || 'Mi Fiesta Especial';
            inputArea.innerHTML = `
                <div style="display:flex; gap:8px; width:100%;">
                    <input type="text" id="wizard-text-input" value="${suggest}" placeholder="Escribe el título aquí..." style="flex:1;">
                    <button type="button" class="btn" style="margin:0; padding:10px 20px;" onclick="submitTitle()">Confirmar</button>
                </div>
                <button type="button" class="tag-btn" style="margin-top:6px; font-size:0.8rem; width:100%; text-align:center;" onclick="useSuggestedTitle('${suggest}')">Usar sugerencia: ${suggest}</button>
            `;
            document.getElementById('wizard-text-input').focus();
        }

        function useSuggestedTitle(title) {
            submitTitleValue(title);
        }

        function submitTitle() {
            const val = document.getElementById('wizard-text-input').value.trim();
            if (!val) {
                alert("Por favor escribe un título.");
                return;
            }
            submitTitleValue(val);
        }

        function submitTitleValue(val) {
            wizardData.title = val;
            appendUserMessage("Título: " + val);
            appendBotMessage("Perfecto. Ahora, ¿cuál será el subtítulo de la invitación? O puedes pedirme que redacte uno creativo con IA.");
            renderSubtitleOptions();
        }

        // Paso 4: Subtítulo
        function renderSubtitleOptions() {
            const inputArea = document.getElementById('wizard-input-area');
            const currentSub = wizardData.subtitle || '';
            inputArea.innerHTML = `
                <div style="display:flex; gap:8px; width:100%;">
                    <input type="text" id="wizard-text-input" value="${currentSub}" placeholder="Escribe el subtítulo..." style="flex:1;">
                    <button type="button" id="ai-btn" class="btn" style="margin:0; padding:10px 18px; background:#7c4dff;" onclick="generateAIForWizard('subtitle')">✨ IA</button>
                    <button type="button" class="btn" style="margin:0; padding:10px 20px;" onclick="submitSubtitle()">Enviar</button>
                </div>
                <button type="button" class="tag-btn" style="margin-top:6px; font-size:0.8rem; width:100%;" onclick="skipSubtitle()">Omitir / Dejar vacío ➔</button>
            `;
            document.getElementById('wizard-text-input').focus();
        }

        async function generateAIForWizard(field) {
            const btn = document.getElementById('ai-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Generando...';
            btn.disabled = true;
            
            try {
                const response = await fetch('{{ route("ai.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        field: field,
                        event_type: activePreset
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.text) {
                        document.getElementById('wizard-text-input').value = data.text;
                    }
                }
            } catch(e) {
                console.error(e);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        function submitSubtitle() {
            const val = document.getElementById('wizard-text-input').value.trim();
            wizardData.subtitle = val;
            appendUserMessage("Subtítulo: " + (val || "(Sin subtítulo)"));
            goToDateStep();
        }

        function skipSubtitle() {
            wizardData.subtitle = '';
            appendUserMessage("Omitir subtítulo.");
            goToDateStep();
        }

        function goToDateStep() {
            appendBotMessage("Entendido.<br><br>¿Qué día y a qué hora se celebrará el evento?");
            renderDateOptions();
        }

        // Paso 5: Fecha
        function renderDateOptions() {
            const inputArea = document.getElementById('wizard-input-area');
            const currentVal = wizardData.date || '';
            inputArea.innerHTML = `
                <div style="display:flex; gap:8px; width:100%;">
                    <input type="datetime-local" id="wizard-date-input" value="${currentVal}" style="flex:1;">
                    <button type="button" class="btn" style="margin:0; padding:10px 20px;" onclick="submitDate()">Confirmar 📅</button>
                </div>
            `;
        }

        function submitDate() {
            const val = document.getElementById('wizard-date-input').value;
            if (!val) {
                alert("Por favor selecciona una fecha y hora.");
                return;
            }
            wizardData.date = val;
            const formatted = new Date(val).toLocaleString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
            appendUserMessage("Fecha: " + formatted);
            appendBotMessage("Guardado.<br><br>¿Dónde se llevará a cabo la celebración? Escribe el nombre del salón o dirección física.");
            renderPlaceOptions();
        }

        // Paso 6: Dirección
        function renderPlaceOptions() {
            const inputArea = document.getElementById('wizard-input-area');
            const currentVal = wizardData.place || '';
            inputArea.innerHTML = `
                <div style="display:flex; gap:8px; width:100%;">
                    <input type="text" id="wizard-text-input" value="${currentVal}" placeholder="Ej: Salón Arcoíris, Av. Flores 123..." style="flex:1;">
                    <button type="button" class="btn" style="margin:0; padding:10px 20px;" onclick="submitPlace()">Enviar</button>
                </div>
            `;
            document.getElementById('wizard-text-input').focus();
        }

        function submitPlace() {
            const val = document.getElementById('wizard-text-input').value.trim();
            if (!val) {
                alert("Por favor escribe una dirección.");
                return;
            }
            wizardData.place = val;
            appendUserMessage("Lugar: " + val);
            appendBotMessage("¡Dirección registrada!<br><br>Ahora, verifiquemos la ubicación en el mapa. Puedes buscar direcciones exactas y mover el marcador del mapa para centrar el punto gps.");
            renderMapOptions();
        }

        // Paso 7: Ubicación en Mapa (Leaflet inline)
        function renderMapOptions() {
            const inputArea = document.getElementById('wizard-input-area');
            inputArea.innerHTML = `
                <div style="display:flex; flex-direction:column; gap:8px; width:100%;">
                    <div style="display:flex; gap:8px;">
                        <input type="text" id="wizard-map-search" placeholder="Escribe una calle y presiona Enter..." style="flex:1;">
                        <button type="button" class="btn" style="margin:0; padding:10px 16px; background:#7c4dff;" onclick="searchWizardMap()">Buscar</button>
                    </div>
                    <div id="picker" style="height:190px; width:100%; border-radius:12px; border:2px solid #eee;"></div>
                    <div class="hint">Marcador GPS actual: <span id="coords">${wizardData.lat.toFixed(5)}, ${wizardData.lng.toFixed(5)}</span></div>
                    <button type="button" class="btn" style="width:100%; margin-top:4px;" onclick="submitMapLocation()">Confirmar ubicación en el mapa 📍</button>
                </div>
            `;
            
            // Inicialización de mapa
            setTimeout(() => {
                const pickerEl = document.getElementById('picker');
                if (!pickerEl) return;
                
                window.map = L.map('picker').setView([wizardData.lat, wizardData.lng], 14);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(window.map);
                
                window.marker = L.marker([wizardData.lat, wizardData.lng], { draggable: true }).addTo(window.map);
                
                window.map.on('click', e => {
                    window.marker.setLatLng(e.latlng);
                    updateWizardCoords(e.latlng.lat, e.latlng.lng);
                });
                
                window.marker.on('dragend', e => {
                    const p = e.target.getLatLng();
                    updateWizardCoords(p.lat, p.lng);
                });
            }, 100);
        }

        function updateWizardCoords(lat, lng) {
            wizardData.lat = lat;
            wizardData.lng = lng;
            const coordsEl = document.getElementById('coords');
            if (coordsEl) coordsEl.textContent = lat.toFixed(5) + ', ' + lng.toFixed(5);
        }

        async function searchWizardMap() {
            const q = document.getElementById('wizard-map-search').value.trim();
            if (!q) return;
            try {
                const response = await fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(q));
                const data = await response.json();
                if (data[0]) {
                    const la = parseFloat(data[0].lat);
                    const ln = parseFloat(data[0].lon);
                    window.map.setView([la, ln], 15);
                    window.marker.setLatLng([la, ln]);
                    updateWizardCoords(la, ln);
                } else {
                    alert('No se encontró la dirección');
                }
            } catch(e) {
                console.error(e);
            }
        }

        function submitMapLocation() {
            appendUserMessage("Ubicación GPS confirmada: " + wizardData.lat.toFixed(5) + ", " + wizardData.lng.toFixed(5));
            if (window.map) {
                window.map.remove();
                window.map = null;
            }
            appendBotMessage("Ubicación guardada.<br><br>El estilo visual (colores, personaje y forma de la tarjeta) se elige desde la galería <b>“Elegí un modelo”</b> en la columna de la derecha. ¿Tienes alguna nota especial para tus invitados? (Ej: Vestimenta, lluvia de sobres, regalos… o pídemelo generar con IA).");
            renderNotesOptions();
        }

        // Paso 9: Notas especiales
        function renderNotesOptions() {
            const inputArea = document.getElementById('wizard-input-area');
            const currentNotes = wizardData.extra_info || '';
            inputArea.innerHTML = `
                <textarea id="wizard-textarea-input" rows="3" placeholder="Sugerencias de regalos, vestimenta..." style="width:100%;">${currentNotes}</textarea>
                <div style="display:flex; gap:8px; width:100%; margin-top:6px;">
                    <button type="button" id="ai-notes-btn" class="btn" style="flex:1; margin:0; background:#7c4dff;" onclick="generateNotesAIForWizard()">✨ Generar con IA</button>
                    <button type="button" class="btn" style="flex:1; margin:0;" onclick="submitNotes()">Enviar</button>
                </div>
                <button type="button" class="tag-btn" style="margin-top:6px; font-size:0.8rem; width:100%;" onclick="skipNotes()">Omitir / Siguiente ➔</button>
            `;
            document.getElementById('wizard-textarea-input').focus();
        }

        async function generateNotesAIForWizard() {
            const btn = document.getElementById('ai-notes-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Generando...';
            btn.disabled = true;
            
            try {
                const response = await fetch('{{ route("ai.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        field: 'extra_info',
                        event_type: activePreset
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.text) {
                        document.getElementById('wizard-textarea-input').value = data.text;
                    }
                }
            } catch(e) {
                console.error(e);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        function submitNotes() {
            const val = document.getElementById('wizard-textarea-input').value.trim();
            wizardData.extra_info = val;
            appendUserMessage("Notas Especiales: " + (val || "(Sin notas)"));
            goToPublishStep();
        }

        function skipNotes() {
            wizardData.extra_info = '';
            appendUserMessage("Sin notas adicionales.");
            goToPublishStep();
        }

        function goToPublishStep() {
            appendBotMessage("¡Excelente! Hemos configurado todos los detalles del evento.<br><br>¿Deseas publicar la invitación ahora para que sea visible públicamente, o la mantenemos como Borrador?");
            renderPublishOptions();
        }

        // Paso 10: Publicación y Guardado final
        function renderPublishOptions() {
            const inputArea = document.getElementById('wizard-input-area');
            inputArea.innerHTML = `
                <div style="display:flex; gap:8px; width:100%;">
                    <button type="button" class="btn" style="flex:1; margin:0; background:#eae3f7; color:#6e5a63; box-shadow:none;" onclick="submitPublish(false)">Borrador</button>
                    <button type="button" class="btn" style="flex:1; margin:0; background:#d4a3b3; color:#fff;" onclick="submitPublish(true)">Publicar</button>
                </div>
            `;
        }

        function submitPublish(isPublished) {
            wizardData.is_published = isPublished;
            appendUserMessage(isPublished ? "Guardar y publicar invitación" : "Guardar como borrador");
            appendBotMessage("¡Configuración lista! Presiona el botón de abajo para confirmar y aplicar todos los cambios en tu invitación.");
            
            const inputArea = document.getElementById('wizard-input-area');
            inputArea.innerHTML = `
                <button type="button" class="btn" style="width:100%; padding:14px; font-size:1.1rem;" onclick="saveAllConfig()">Confirmar y Guardar Cambios</button>
            `;
        }

        function saveAllConfig() {
            document.getElementById('form-emoji').value = wizardData.emoji;
            document.getElementById('form-title').value = wizardData.title;
            document.getElementById('form-subtitle').value = wizardData.subtitle;
            document.getElementById('form-date').value = wizardData.date;
            document.getElementById('form-place').value = wizardData.place;
            document.getElementById('form-lat').value = wizardData.lat;
            document.getElementById('form-lng').value = wizardData.lng;
            document.getElementById('form-color_primary').value = wizardData.color_primary;
            document.getElementById('form-color_accent').value = wizardData.color_accent;
            document.getElementById('form-color_secondary').value = wizardData.color_secondary;
            document.getElementById('form-extra_info').value = wizardData.extra_info;
            document.getElementById('form-is_published').value = wizardData.is_published ? '1' : '0';
            document.getElementById('form-theme_character').value = wizardData.theme_character;
            document.getElementById('form-event_type').value = wizardData.event_type;
            
            document.getElementById('wizard-form').submit();
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
            if (typeof wizardData !== 'undefined') {
                wizardData.color_primary   = p.color_primary;
                wizardData.color_secondary = p.color_secondary;
                wizardData.color_accent    = p.color_accent;
                wizardData.theme_character = p.character;
                wizardData.template        = p.template;
            }
            document.getElementById('wizard-form').submit();
        }

        document.addEventListener('DOMContentLoaded', renderPresets);

        // === Toggle Asistente IA ===
        function toggleWizard(){
            const box = document.getElementById('wizard-container');
            if (!box) return;
            const open = box.style.display === 'none' || box.style.display === '';
            box.style.display = open ? 'block' : 'none';
            if (open) box.scrollIntoView({ behavior:'smooth', block:'start' });
        }

        // === Mapa Leaflet en formulario clásico ===
        (function initEditMap(){
            const mapEl = document.getElementById('edit-map');
            if (!mapEl || typeof L === 'undefined') return;
            const latInput = document.getElementById('edit-lat');
            const lngInput = document.getElementById('edit-lng');
            let lat = parseFloat(latInput.value) || -12.046374;
            let lng = parseFloat(lngInput.value) || -77.042793;

            const map = L.map('edit-map').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution:'© OpenStreetMap', maxZoom:19,
            }).addTo(map);

            const marker = L.marker([lat, lng], { draggable:true }).addTo(map);

            function updateInputs(newLat, newLng){
                latInput.value = newLat.toFixed(6);
                lngInput.value = newLng.toFixed(6);
            }

            marker.on('dragend', e => {
                const p = e.target.getLatLng();
                updateInputs(p.lat, p.lng);
            });
            map.on('click', e => {
                marker.setLatLng(e.latlng);
                updateInputs(e.latlng.lat, e.latlng.lng);
            });

            // Recalcular tamaño cuando la card entra en viewport (fix leaflet dentro de flex/hidden)
            setTimeout(() => map.invalidateSize(), 250);
        })();

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
