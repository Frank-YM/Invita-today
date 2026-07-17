<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis eventos</title>
    <link rel="icon" type="image/png" href="{{ asset('images/themes/icono.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:'Montserrat',sans-serif}
        body{
            background-color:#f6f4f0;
            background-image: radial-gradient(circle at 10% 20%, rgba(212,163,179,0.04) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(159,184,199,0.04) 0%, transparent 40%);
            background-attachment: fixed;
            padding:16px 14px 40px;
            color:#2c2235;
            max-width:1100px;
            margin:0 auto;
        }
        .header {
            display:flex; justify-content:space-between; align-items:center;
            margin-bottom:20px; flex-wrap:wrap; gap:12px;
            background:#fff; padding:14px 18px; border-radius:16px;
            box-shadow:0 4px 14px rgba(110,90,99,0.06);
            border:1px solid #f0e8ea;
        }
        .header-left { display:flex; align-items:center; gap:12px; }
        .header-brand {
            width:40px; height:40px; border-radius:12px;
            background:#fff;
            display:flex; align-items:center; justify-content:center;
            overflow:hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
        .header-brand img { width:100%; height:100%; object-fit:contain; }
        h1{color:#3d2c40;font-size:1.15rem;font-weight:800;line-height:1.1}
        .subtitle-desc {color:#8a7ba5;font-size:0.72rem;margin-top:2px;font-weight:500}

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
        }
        .user-name { font-size:0.82rem; font-weight:700; color:#6e5a63; padding-right:4px; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .user-logout { background:transparent; border:none; cursor:pointer; color:#a48b93; padding:4px 8px; border-radius:999px; font-size:0.75rem; font-weight:600; transition: background .15s, color .15s; }
        .user-logout:hover { background:#fdf2f2; color:#e63946; }

        .page-head {
            display:flex; justify-content:space-between; align-items:center;
            margin-bottom:16px; flex-wrap:wrap; gap:10px;
        }
        .page-head h2 { color:#3d2c40; font-size:1.4rem; font-weight:800; }
        .page-head p { color:#8a7ba5; font-size:0.85rem; margin-top:2px; }

        .btn-primary {
            background: linear-gradient(135deg, #7c4dff, #5a4e8c);
            color:#fff; border:none; padding:10px 18px; border-radius:999px;
            font-weight:700; font-size:0.85rem; cursor:pointer;
            box-shadow:0 4px 12px rgba(124,77,255,0.25);
            font-family:inherit; text-decoration:none;
            display:inline-flex; align-items:center; gap:6px;
            transition: transform .15s;
        }
        .btn-primary:hover { transform: translateY(-2px); }

        .saved { background:#e8f7f0; color:#086b55; padding:10px 14px; border-radius:12px; margin-bottom:14px; font-size:0.85rem; font-weight:600; border:1px solid #c9edd7; }

        .events-grid {
            display:grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap:14px;
        }
        .event-card {
            background:#fff; border-radius:16px; padding:16px;
            border:1px solid #f0e8ea; position:relative;
            transition: transform .15s, box-shadow .15s;
            display:flex; flex-direction:column; gap:12px;
        }
        .event-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(110,90,99,0.1); border-color:#d4a3b3; }
        .event-card.active { border-color:#7c4dff; box-shadow: 0 6px 18px rgba(124,77,255,0.15); }
        .event-card.active::before {
            content:"Activo"; position:absolute; top:-8px; right:12px;
            background:#7c4dff; color:#fff; font-size:0.65rem; font-weight:700;
            padding:3px 10px; border-radius:999px; letter-spacing:0.3px;
        }
        .event-emoji { font-size:2rem; line-height:1; }
        .event-title { font-size:1rem; font-weight:800; color:#3d2c40; line-height:1.2; }
        .event-meta { display:flex; flex-direction:column; gap:4px; font-size:0.75rem; color:#8a7ba5; }
        .event-meta .item { display:flex; align-items:center; gap:6px; }
        .event-badge {
            display:inline-flex; padding:2px 8px; border-radius:999px;
            font-size:0.65rem; font-weight:700; letter-spacing:0.3px;
            align-self:flex-start;
        }
        .event-badge.published { background:rgba(8,107,85,0.1); color:#086b55; }
        .event-badge.draft { background:#f6f4f0; color:#8a7ba5; }

        .event-actions { display:flex; gap:6px; margin-top:auto; padding-top:8px; border-top:1px solid #f6f4f0; }
        .btn-card {
            flex:1; text-align:center; padding:8px 10px; border-radius:10px;
            font-size:0.75rem; font-weight:700; cursor:pointer; border:none;
            text-decoration:none; font-family:inherit;
            display:inline-flex; align-items:center; justify-content:center; gap:4px;
            transition: background .15s, color .15s;
        }
        .btn-card-manage { background:#faf8f5; color:#6e5a63; border:1px solid #efe6e9; }
        .btn-card-manage:hover { background:#7c4dff; color:#fff; border-color:#7c4dff; }
        .btn-card-icon { background:#faf8f5; color:#6e5a63; border:1px solid #efe6e9; padding:8px; flex:0 0 auto; }
        .btn-card-icon:hover { background:#eae3f7; color:#7c4dff; border-color:#c9c3e6; }
        .btn-card-icon.copied { background:#e8f7f0; color:#086b55; border-color:#c9edd7; }
        .btn-card-delete { background:transparent; color:#c48b93; padding:8px; flex:0 0 auto; }
        .btn-card-delete:hover { background:#fdf2f2; color:#e63946; }
        .copy-toast {
            position:fixed; bottom:20px; left:50%; transform:translateX(-50%);
            background:#3d2c40; color:#fff; padding:10px 18px; border-radius:999px;
            font-size:0.82rem; font-weight:600; box-shadow:0 8px 24px rgba(0,0,0,0.2);
            opacity:0; pointer-events:none; transition: opacity .2s, transform .2s;
            z-index:10000;
        }
        .copy-toast.show { opacity:1; transform:translateX(-50%) translateY(-4px); }

        .empty-state {
            background:#fff; border-radius:16px; padding:48px 24px;
            text-align:center; border:2px dashed #efe6e9;
        }
        .empty-state .icon { font-size:3rem; margin-bottom:12px; }
        .empty-state h3 { color:#3d2c40; margin-bottom:6px; font-size:1.05rem; }
        .empty-state p { color:#8a7ba5; font-size:0.85rem; margin-bottom:18px; }

        /* Modal de crear evento */
        .modal-overlay {
            position:fixed; inset:0; background:rgba(0,0,0,0.5);
            display:none; align-items:center; justify-content:center;
            padding:20px; z-index:9999;
        }
        .modal-overlay.open { display:flex; }
        .modal {
            background:#fff; border-radius:20px; padding:24px;
            max-width:420px; width:100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        }
        .modal h3 { color:#3d2c40; font-size:1.1rem; margin-bottom:14px; font-weight:800; }
        .modal label { display:block; font-size:0.72rem; font-weight:700; color:#6e5a63; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:5px; margin-top:12px; }
        .modal input, .modal select {
            width:100%; padding:10px 12px; font-size:0.9rem;
            border:1.5px solid #e1ebf2; border-radius:10px;
            background:#fff; font-family:inherit;
        }
        .modal input:focus, .modal select:focus { outline:none; border-color:#7c4dff; }
        .modal-actions { display:flex; gap:8px; margin-top:20px; }
        .modal-actions button { flex:1; padding:10px; border-radius:10px; font-weight:700; font-size:0.85rem; cursor:pointer; border:none; font-family:inherit; }
        .btn-cancel { background:#f6f4f0; color:#6e5a63; }
        .btn-confirm { background:#7c4dff; color:#fff; }

        @media(max-width:600px){
            body{padding:10px}
            .events-grid{grid-template-columns:1fr}
            .page-head{flex-direction:column;align-items:flex-start}
        }
    </style>
</head>
<body>
    @php
        $firstName = trim(explode(' ', trim(auth()->user()->name ?? ''))[0] ?? '') ?: 'Usuario';
        $initial   = mb_strtoupper(mb_substr($firstName, 0, 1));
    @endphp
    <div class="header">
        <div class="header-left">
            <div class="header-brand"><img src="{{ asset('images/themes/icono.png') }}" alt="Invita"></div>
            <div>
                <h1>Mis eventos</h1>
                <p class="subtitle-desc">Gestioná todas tus invitaciones</p>
            </div>
        </div>
        <div class="user-chip" title="{{ auth()->user()->name }}">
            <div class="user-avatar">{{ $initial }}</div>
            <span class="user-name">{{ $firstName }}</span>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="user-logout" title="Cerrar sesión">Salir</button>
            </form>
        </div>
    </div>

    @if(session('saved'))<div class="saved">{{ session('saved') }}</div>@endif

    <div class="page-head">
        <div>
            <h2>Tus eventos ({{ $events->count() }})</h2>
            <p>Click en "Gestionar" para configurar la invitación de ese evento.</p>
        </div>
        <button type="button" class="btn-primary" onclick="openCreateModal()">+ Crear nuevo evento</button>
    </div>

    @if($events->isEmpty())
        <div class="empty-state">
            <div class="icon">🎉</div>
            <h3>Todavía no tenés eventos</h3>
            <p>Creá tu primer evento y personalizá la invitación en minutos.</p>
            <button type="button" class="btn-primary" onclick="openCreateModal()">+ Crear mi primer evento</button>
        </div>
    @else
        <div class="events-grid">
            @foreach($events as $event)
                @php
                    $typeLabels = [
                        'babyshower'=>'🍼 Baby Shower','cumple'=>'🎂 Cumpleaños','bautizo'=>'🕊️ Bautizo',
                        'revelacion'=>'🤰 Revelación','bienvenida'=>'👶 Bienvenida','comunion'=>'🕯️ Comunión',
                        'boda'=>'💍 Boda','quinceanero'=>'👑 Quinceañero','graduacion'=>'🎓 Graduación',
                        'aniversario'=>'💛 Aniversario','despedida'=>'🥂 Despedida','general'=>'🎉 Evento general',
                    ];
                    $typeLabel = $typeLabels[$event->event_type] ?? $event->event_type;
                @endphp
                <div class="event-card {{ $activeId === $event->id ? 'active' : '' }}">
                    <div class="event-emoji">{{ $event->emoji ?: '🎉' }}</div>
                    <div>
                        <div class="event-title">{{ $event->title }}</div>
                        <div class="event-meta" style="margin-top:6px;">
                            <div class="item">{{ $typeLabel }}</div>
                            <div class="item">
                                📅
                                @if($event->date)
                                    {{ $event->date->translatedFormat('d M Y') }}
                                @else
                                    Sin fecha
                                @endif
                            </div>
                            <div class="item">👥 {{ $event->guests_count }} invitado{{ $event->guests_count === 1 ? '' : 's' }}</div>
                        </div>
                        <span class="event-badge {{ $event->is_published ? 'published' : 'draft' }}" style="margin-top:8px;">
                            {{ $event->is_published ? '● Publicada' : 'Borrador' }}
                        </span>
                    </div>
                    @php
                        $publicUrl  = route('invitation.public', ['slug' => $event->slug]);
                        $previewUrl = $publicUrl . ($event->is_published ? '' : '?preview=1');
                    @endphp
                    <div class="event-actions">
                        <a href="{{ route('events.select', $event) }}" class="btn-card btn-card-manage">Gestionar →</a>
                        <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="btn-card btn-card-icon" title="Ver invitación">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                        <button type="button" class="btn-card btn-card-icon" title="Copiar enlace" onclick="copyEventLink(this, '{{ $publicUrl }}')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        </button>
                        <form method="POST" action="{{ route('events.destroy', $event) }}" style="margin:0;" onsubmit="return confirm('¿Eliminar el evento &quot;{{ $event->title }}&quot; con todos sus invitados y fotos? Esta acción no se puede deshacer.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-card btn-card-delete" title="Eliminar evento">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Modal crear evento --}}
    <div class="modal-overlay" id="create-modal" onclick="if(event.target===this) closeCreateModal()">
        <div class="modal">
            <h3>Crear nuevo evento</h3>
            <form method="POST" action="{{ route('events.store') }}">
                @csrf
                <label>Tipo de evento</label>
                <select name="event_type" required>
                    <optgroup label="Celebraciones">
                        <option value="cumple">🎂 Cumpleaños</option>
                        <option value="quinceanero">👑 Quinceañero / XV años</option>
                        <option value="boda">💍 Boda</option>
                        <option value="aniversario">💛 Aniversario</option>
                        <option value="graduacion">🎓 Graduación</option>
                        <option value="despedida">🥂 Despedida de soltera/o</option>
                        <option value="general">🎉 Evento general</option>
                    </optgroup>
                    <optgroup label="Bebés y niños">
                        <option value="babyshower">🍼 Baby Shower</option>
                        <option value="revelacion">🤰 Revelación de género</option>
                        <option value="bienvenida">👶 Bienvenida</option>
                    </optgroup>
                    <optgroup label="Religiosos">
                        <option value="bautizo">🕊️ Bautizo</option>
                        <option value="comunion">🕯️ Comunión</option>
                    </optgroup>
                </select>

                <label>Título del evento</label>
                <input type="text" name="title" required maxlength="120" placeholder="Ej: Los 30 de Frank" autofocus>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeCreateModal()">Cancelar</button>
                    <button type="submit" class="btn-confirm">Crear evento</button>
                </div>
            </form>
        </div>
    </div>

    <div class="copy-toast" id="copy-toast">Enlace copiado 📋</div>

    <script>
        function openCreateModal(){ document.getElementById('create-modal').classList.add('open'); }
        function closeCreateModal(){ document.getElementById('create-modal').classList.remove('open'); }
        document.addEventListener('keydown', e => { if(e.key==='Escape') closeCreateModal(); });

        async function copyEventLink(btn, url) {
            try {
                await navigator.clipboard.writeText(url);
            } catch (e) {
                const ta = document.createElement('textarea');
                ta.value = url; document.body.appendChild(ta);
                ta.select(); document.execCommand('copy'); ta.remove();
            }
            btn.classList.add('copied');
            const toast = document.getElementById('copy-toast');
            toast.classList.add('show');
            setTimeout(() => btn.classList.remove('copied'), 1500);
            setTimeout(() => toast.classList.remove('show'), 1800);
        }
    </script>
</body>
</html>
