<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Super Admin · Invita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        *{ box-sizing:border-box; margin:0; padding:0; }
        body{
            font-family:'Inter', sans-serif; color:#0f172a; background:#fafaf7;
            -webkit-font-smoothing:antialiased; min-height:100vh;
            background-image:
              radial-gradient(circle at 12% 8%, rgba(232,139,58,0.05), transparent 40%),
              radial-gradient(circle at 88% 92%, rgba(159,184,199,0.06), transparent 40%);
        }
        .container{ max-width:1140px; margin:0 auto; padding:24px 28px 60px; }

        header{
            display:flex; justify-content:space-between; align-items:center;
            padding-bottom:20px; border-bottom:1px solid rgba(15,23,42,0.06); margin-bottom:28px;
        }
        .brand{ display:flex; align-items:center; gap:10px; }
        .brand-mark{
            width:32px; height:32px; border-radius:9px;
            background:linear-gradient(135deg, #e88b3a, #d4a3b3);
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-family:'Playfair Display', serif; font-weight:700; font-size:1rem;
        }
        .brand-name{ font-family:'Playfair Display', serif; font-weight:700; font-size:1.15rem; }
        .badge{
            font-size:0.65rem; letter-spacing:1.5px; text-transform:uppercase;
            color:#e88b3a; font-weight:700; margin-left:6px;
            background:#fff4e6; padding:3px 8px; border-radius:6px;
        }
        .head-links{ display:flex; gap:16px; align-items:center; }
        .head-links a{
            font-size:0.85rem; color:#475569; text-decoration:none; font-weight:500;
        }
        .head-links a:hover{ color:#0f172a; }
        form.inline{ display:inline; }
        .logout-btn{
            background:transparent; border:none; cursor:pointer;
            font-size:0.85rem; color:#475569; font-weight:500; font-family:inherit;
        }
        .logout-btn:hover{ color:#0f172a; }

        h1{
            font-family:'Playfair Display', serif; font-size:1.8rem;
            letter-spacing:-0.5px; margin-bottom:6px;
        }
        .lead{ color:#64748b; font-size:0.95rem; margin-bottom:26px; }

        /* KPIs */
        .kpi-grid{
            display:grid; grid-template-columns:repeat(6, 1fr); gap:12px; margin-bottom:32px;
        }
        .kpi{
            background:#fff; border:1px solid rgba(15,23,42,0.06); border-radius:14px;
            padding:16px 18px;
        }
        .kpi-lbl{
            font-size:0.7rem; color:#64748b; letter-spacing:0.5px;
            text-transform:uppercase; font-weight:600; margin-bottom:6px;
        }
        .kpi-val{
            font-family:'Playfair Display', serif; font-weight:700; font-size:1.6rem;
            color:#0f172a; line-height:1;
        }
        .kpi.accent .kpi-val{ color:#e88b3a; }

        /* Layout de 2 columnas para las tablas */
        .cols{ display:grid; grid-template-columns:1.4fr 1fr; gap:20px; margin-bottom:28px; }
        .card{
            background:#fff; border:1px solid rgba(15,23,42,0.06); border-radius:16px;
            padding:20px 22px; overflow:hidden;
        }
        .card h3{
            font-family:'Playfair Display', serif; font-weight:700; font-size:1.05rem;
            color:#0f172a; margin-bottom:14px;
            display:flex; align-items:baseline; justify-content:space-between;
        }
        .card h3 small{ color:#94a3b8; font-family:'Inter', sans-serif; font-weight:500; font-size:0.72rem; }

        table{ width:100%; border-collapse:collapse; }
        th, td{
            text-align:left; padding:8px 6px; font-size:0.82rem;
            border-bottom:1px solid rgba(15,23,42,0.05);
        }
        th{ color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; font-size:0.66rem; }
        tr:last-child td{ border-bottom:none; }
        td.right, th.right{ text-align:right; }
        .row-user{ display:flex; align-items:center; gap:8px; }
        .avatar{
            width:26px; height:26px; border-radius:50%;
            background:linear-gradient(135deg, #e88b3a, #d4a3b3);
            color:#fff; display:flex; align-items:center; justify-content:center;
            font-weight:700; font-size:0.72rem;
        }
        .row-user small{ color:#94a3b8; font-size:0.72rem; }

        .tag{
            font-size:0.68rem; padding:2px 7px; border-radius:6px;
            background:#f1f5f9; color:#475569; font-weight:600;
        }
        .tag.on{ background:#e0f2eb; color:#086b55; }
        .tag.off{ background:#f8f4ec; color:#a05d20; }
        .tag.super{ background:#fff4e6; color:#e88b3a; }

        .bar-row{ display:flex; align-items:center; gap:10px; margin:8px 0; }
        .bar-lbl{ font-size:0.82rem; color:#334155; flex:0 0 100px; text-transform:capitalize; }
        .bar-track{
            flex:1; height:8px; background:#f1f5f9; border-radius:999px; overflow:hidden;
        }
        .bar-fill{
            height:100%; background:linear-gradient(90deg, #e88b3a, #f5b06d); border-radius:999px;
        }
        .bar-num{ font-size:0.78rem; color:#64748b; font-weight:600; min-width:24px; text-align:right; }

        .empty{ color:#94a3b8; font-size:0.85rem; padding:12px; text-align:center; }

        @media (max-width:900px){
            .kpi-grid{ grid-template-columns:repeat(3, 1fr); }
            .cols{ grid-template-columns:1fr; }
        }
        @media (max-width:560px){
            .container{ padding:18px 20px 50px; }
            .kpi-grid{ grid-template-columns:repeat(2, 1fr); }
            .kpi-val{ font-size:1.35rem; }
            h1{ font-size:1.4rem; }
            th, td{ font-size:0.75rem; padding:6px 4px; }
            .head-links{ gap:12px; }
            .head-links a, .logout-btn{ font-size:0.78rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="brand">
                <div class="brand-mark">i</div>
                <div class="brand-name">Invita <span class="badge">Super admin</span></div>
            </div>
            <div class="head-links">
                <a href="{{ route('admin') }}">← Mi panel</a>
                <form class="inline" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">Salir</button>
                </form>
            </div>
        </header>

        <h1>Panel de control</h1>
        <p class="lead">Visión general del sistema, usuarios y modelos más usados.</p>

        <div class="kpi-grid">
            <div class="kpi accent">
                <div class="kpi-lbl">Usuarios</div>
                <div class="kpi-val">{{ $stats['total_users'] }}</div>
            </div>
            <div class="kpi">
                <div class="kpi-lbl">Activos 7d</div>
                <div class="kpi-val">{{ $stats['active_last_7d'] }}</div>
            </div>
            <div class="kpi">
                <div class="kpi-lbl">Eventos</div>
                <div class="kpi-val">{{ $stats['total_events'] }}</div>
            </div>
            <div class="kpi">
                <div class="kpi-lbl">Publicados</div>
                <div class="kpi-val">{{ $stats['published'] }}</div>
            </div>
            <div class="kpi">
                <div class="kpi-lbl">Invitados</div>
                <div class="kpi-val">{{ $stats['total_guests'] }}</div>
            </div>
            <div class="kpi">
                <div class="kpi-lbl">Confirmados</div>
                <div class="kpi-val">{{ $stats['attending'] }}</div>
            </div>
        </div>

        <div class="cols">
            <div class="card">
                <h3>Usuarios registrados <small>{{ count($users) }} total</small></h3>
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th class="right">Eventos</th>
                            <th class="right">Último login</th>
                            <th class="right">Registrado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr>
                                <td>
                                    <div class="row-user">
                                        <div class="avatar">{{ strtoupper(substr($u['name'] ?? $u['email'], 0, 1)) }}</div>
                                        <div>
                                            <div style="font-weight:600;">
                                                {{ $u['name'] ?: '—' }}
                                                @if($u['is_super_admin'])<span class="tag super" style="margin-left:4px;">super</span>@endif
                                            </div>
                                            <small>{{ $u['email'] }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="right">{{ $u['events_count'] }}</td>
                                <td class="right">{{ $u['last_login_at'] ? $u['last_login_at']->diffForHumans() : '—' }}</td>
                                <td class="right"><small style="color:#94a3b8;">{{ $u['created_at']->format('d/m/y') }}</small></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty">Sin usuarios aún.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h3>Modelos más usados</h3>
                @php $topTplTotal = $templates->sum('total') ?: 1; @endphp
                @forelse($templates as $t)
                    <div class="bar-row">
                        <div class="bar-lbl">{{ $t->template ?: 'sin definir' }}</div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width: {{ round(($t->total / $topTplTotal) * 100) }}%;"></div>
                        </div>
                        <div class="bar-num">{{ $t->total }}</div>
                    </div>
                @empty
                    <div class="empty">Aún no hay datos.</div>
                @endforelse

                <h3 style="margin-top:22px;">Tipos de evento</h3>
                @php $topTypeTotal = $eventTypes->sum('total') ?: 1; @endphp
                @forelse($eventTypes as $t)
                    <div class="bar-row">
                        <div class="bar-lbl">{{ str_replace('_', ' ', $t->event_type) }}</div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width: {{ round(($t->total / $topTypeTotal) * 100) }}%;"></div>
                        </div>
                        <div class="bar-num">{{ $t->total }}</div>
                    </div>
                @empty
                    <div class="empty">Nadie eligió tipo aún.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <h3>Eventos recientes <small>últimos 10</small></h3>
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Dueño</th>
                        <th>Modelo</th>
                        <th>Tipo</th>
                        <th class="right">Estado</th>
                        <th class="right">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentEvents as $e)
                        <tr>
                            <td>
                                <div style="font-weight:600;">{{ $e['title'] }}</div>
                                <small style="color:#94a3b8;">/e/{{ $e['slug'] }}</small>
                            </td>
                            <td>
                                <div>{{ $e['user_name'] ?: '—' }}</div>
                                <small style="color:#94a3b8;">{{ $e['user_email'] }}</small>
                            </td>
                            <td><span class="tag">{{ $e['template'] ?: 'classic' }}</span></td>
                            <td><small>{{ $e['event_type'] ?: '—' }}</small></td>
                            <td class="right">
                                @if($e['is_published'])
                                    <span class="tag on">publicado</span>
                                @else
                                    <span class="tag off">borrador</span>
                                @endif
                            </td>
                            <td class="right"><small style="color:#94a3b8;">{{ $e['created_at']->format('d/m/y') }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">Sin eventos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
