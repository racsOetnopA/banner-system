


<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel de Administración')</title>

    {{-- CSS AdminLTE + Bootstrap --}}
    <link rel="stylesheet" href="{{ asset('vendor/admin-lte/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    {{-- Font Awesome --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer"/>

    {{-- 💡 Importante: esta línea inyecta el cliente de Vite --}}
    @vite([
        // 'resources/css/app.css',
        'resources/assets/css/styles.css',
        'resources/js/app.js',
        // Dashboard SCSS (requires `sass` devDependency installed)
        'resources/assets/scss/dashboard.scss'
    ])

    @stack('styles')
    <style>
        /* Modern hover for top navigation links */
        .navbar {
            padding-top: 0.45rem;
            padding-bottom: 0.45rem;
        }
        .navbar .nav-link.nav-hover {
            position: relative;
            transition: color .2s ease, transform .15s ease;
            color: #f8f8fa; /* texto claro para fondo oscuro */
            padding-bottom: .6rem; /* espacio para la línea sin superposición */
        }
        .navbar .nav-link.nav-hover::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -6px; /* colocar la línea por debajo del texto */
            transform: translateX(-50%) scaleX(0);
            transform-origin: center;
            width: 60%;
            height: 3px;
            background: linear-gradient(90deg,#06b6d4,#6366f1);
            border-radius: 2px;
            transition: transform .25s ease;
        }
        .navbar .nav-link.nav-hover:hover {
            color: #ffffff; /* hover claro sobre fondo oscuro */
            transform: translateY(-2px);
        }
        .navbar .nav-link.nav-hover:hover::after {
            transform: translateX(-50%) scaleX(1);
        }
        /* Reduce underline space on small screens */
        @media (max-width: 576px) {
            .navbar .nav-link.nav-hover { padding-bottom: .45rem; }
            .navbar .nav-link.nav-hover::after { bottom: -4px; width: 70%; height: 2px; }
        }
        /* Global tooltip sizing: expand to fit longest line (capped) and avoid wrapping lines */
        .tooltip-inner {
            max-width: 90vw !important;
            white-space: nowrap !important;
            overflow: auto;
        }
        /* Make all table header cells bold globally and set color */
        table thead th, table th {
            font-weight: 700 !important;
            color: #65686a !important;
        }
        /* Highlight for matched terms in table filtering */
        mark.table-filter-highlight {
            background-color: #fff59d; /* pale yellow */
            color: inherit;
            padding: 0 .15rem;
            border-radius: 2px;
        }
        /* Tooltip variants for warning and danger */
        .tooltip-warning .tooltip-inner {
            background-color: var(--bs-warning) !important;
            color: var(--bs-dark, #000) !important;
            border: none;
            max-width: 90vw;
            white-space: nowrap;
        }
        .tooltip-danger .tooltip-inner {
            background-color: var(--bs-danger) !important;
            color: var(--bs-white, #fff) !important;
            border: none;
            max-width: 90vw;
            white-space: nowrap;
        }
    </style>
</head>

<body class="hold-transition" style="background-color: #f8f8fa;">


    <nav class="navbar navbar-dark fixed-top shadow-sm " style="position: fixed; width:100%; z-index:1030; background-color:#566cac; color: white; font-size: 18px">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <a href="{{ route('dashboard') }}" class="nav-link nav-hover px-3 {{ request()->is('/') ? 'active' : '' }}">
                    <i class="fas fa-home me-2"></i>Inicio
                </a>
                <a href="{{ route('webs.index') }}" class="nav-link nav-hover px-3 {{ request()->is('webs*') ? 'active' : '' }}">
                    <i class="fas fa-globe me-2"></i>Webs
                </a>
                <a href="{{ route('zones.index') }}" class="nav-link nav-hover px-3 {{ request()->is('zones*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group me-2"></i>Zonas
                </a>
                <a href="{{ route('banners.index') }}" class="nav-link nav-hover px-3 {{ request()->is('banners*') ? 'active' : '' }}">
                    <i class="fas fa-image me-2"></i>Banners
                </a>
                <a href="{{ route('assignments.index') }}" class="nav-link nav-hover px-3 {{ request()->is('assignments*') ? 'active' : '' }}">
                    <i class="fas fa-link me-2"></i>Código
                </a>
                <a href="{{ route('estadisticas.index') }}" class="nav-link nav-hover px-3 {{ request()->is('estadisticas*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar me-2"></i>Estadísticas
                </a>
            </div>

            <div class="d-flex align-items-center ms-auto">
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link nav-hover px-3 text-white" style="text-decoration:none;">
                        <i class="fas fa-sign-out-alt me-2"></i><b>Salir</b>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Contenido principal --}}
    <div class="content-wrapper" style="min-height: 85vh; margin-top: 20px;">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="mt-5 p-3">
            @yield('content')
        </div>
    </div>

    {{-- <div class="text-centert w-50">
        Aquí el banner
        <br>
        <div id="zone-10x10"></div>
        <script>
        (function(){
        var s=document.createElement('script');
        s.src='http://banner-system.test/js/banner.js?zone=10x10&site=banner-system.test';
        document.currentScript.parentNode.appendChild(s);
        })();
        </script>
    </div> --}}

    {{-- Footer --}}
    <footer class="main-footer text-center py-3border-top shadow-sm mt-5" style="background-color: whitesmoke;">
        <small>
            <span class="text-muted"> Copyright © <span id="year"></span>
                    by <a href="https://adclichosting.com/" target="_blank" data-bs-toggle="tooltip" title="{{ Route::currentRouteName() }}" data-bs-placement="top" data-bs-custom-class="tooltip-primary">
                    <span class="fw-medium text-primary">Adclic Hosting</span>
                </a> All rights reserved
            </span>
        </small>
    </footer>

    {{-- JS --}}
    <!-- jQuery (required by some plugins like bootstrap-multiselect) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('vendor/admin-lte/js/adminlte.min.js') }}"></script>

    <script>
    // Central tooltip initialization: enables HTML tooltips and zero show delay.
    document.addEventListener('DOMContentLoaded', () => {
        const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(el => {
            try { if (el._tooltip) el._tooltip.dispose(); } catch(e) {}
            el._tooltip = new bootstrap.Tooltip(el, { html: true, delay: { show: 0, hide: 100 } });
        });
    });
    </script>

    <script>
    // Global table filtering: any input with `.table-filter` will filter rows in the target table
    document.addEventListener('DOMContentLoaded', () => {
        const debounces = new WeakMap();

        function normalize(text) {
            return (text || '').toString().toLowerCase();
        }

        function escapeRegExp(string) {
            return String(string).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        function clearHighlights(el) {
            if (!el) return;
            const marks = Array.from(el.querySelectorAll('mark.table-filter-highlight'));
            marks.forEach(m => {
                const txt = document.createTextNode(m.textContent);
                m.replaceWith(txt);
            });
        }

        function highlightTextNode(textNode, regex) {
            const text = textNode.nodeValue;
            let lastIndex = 0;
            const frag = document.createDocumentFragment();
            regex.lastIndex = 0;
            let match;
            while ((match = regex.exec(text)) !== null) {
                if (match.index > lastIndex) {
                    frag.appendChild(document.createTextNode(text.slice(lastIndex, match.index)));
                }
                const mark = document.createElement('mark');
                mark.className = 'table-filter-highlight';
                mark.textContent = match[0];
                frag.appendChild(mark);
                lastIndex = regex.lastIndex;
            }
            if (lastIndex < text.length) {
                frag.appendChild(document.createTextNode(text.slice(lastIndex)));
            }
            textNode.replaceWith(frag);
        }

        function highlightElement(el, query) {
            if (!query) return;
            const regex = new RegExp(escapeRegExp(query), 'gi');

            // Process runs of consecutive text nodes under the same parent to allow single <mark>
            function processTextRun(parent, startIdx, endIdx) {
                // Combine text from the range
                const texts = [];
                for (let k = startIdx; k <= endIdx; k++) texts.push(parent.childNodes[k].nodeValue || '');
                const combined = texts.join('');

                // Find matches in combined text
                const matches = [];
                let m;
                while ((m = regex.exec(combined)) !== null) {
                    matches.push({ start: m.index, end: m.index + m[0].length });
                    if (m.index === regex.lastIndex) regex.lastIndex++;
                }
                if (!matches.length) return;

                // Build fragment by iterating through combined string and inserting marks
                const frag = document.createDocumentFragment();
                let pos = 0;
                matches.forEach(mm => {
                    if (mm.start > pos) frag.appendChild(document.createTextNode(combined.slice(pos, mm.start)));
                    // Extract raw matched substring and trim surrounding spaces
                    const raw = combined.slice(mm.start, mm.end);
                    const leadingMatch = raw.match(/^\s+/);
                    const trailingMatch = raw.match(/\s+$/);
                    const leading = leadingMatch ? leadingMatch[0] : '';
                    const trailing = trailingMatch ? trailingMatch[0] : '';
                    const core = raw.slice(leading.length, raw.length - trailing.length);
                    if (leading) frag.appendChild(document.createTextNode(leading));
                    const mark = document.createElement('mark');
                    mark.className = 'table-filter-highlight';
                    mark.textContent = core;
                    frag.appendChild(mark);
                    if (trailing) frag.appendChild(document.createTextNode(trailing));
                    pos = mm.end;
                });
                if (pos < combined.length) frag.appendChild(document.createTextNode(combined.slice(pos)));

                // Replace the original text nodes in parent with the fragment
                // Remove nodes from startIdx to endIdx
                for (let k = endIdx; k >= startIdx; k--) parent.removeChild(parent.childNodes[k]);
                // Insert fragment at position startIdx (now childNodes[startIdx] is next node)
                if (parent.childNodes.length > startIdx) parent.insertBefore(frag, parent.childNodes[startIdx]);
                else parent.appendChild(frag);
            }

            // Recursive traversal: find runs of consecutive text nodes
            function traverse(node) {
                if (!node || node.nodeType !== Node.ELEMENT_NODE) return;
                const children = node.childNodes;
                let i = 0;
                while (i < children.length) {
                    const child = children[i];
                    if (child.nodeType === Node.TEXT_NODE) {
                        // start of a run
                        let j = i;
                        while (j + 1 < children.length && children[j + 1].nodeType === Node.TEXT_NODE) j++;
                        // process run i..j
                        processTextRun(node, i, j);
                        // advance i to j+1 (note that childNodes changed after process)
                        i = j + 1;
                    } else if (child.nodeType === Node.ELEMENT_NODE) {
                        // recurse into element
                        traverse(child);
                        i++;
                    } else {
                        i++;
                    }
                }
            }

            traverse(el);
        }

        function filterTable(input) {
            const selector = input.dataset.target || input.getAttribute('data-target');
            if (!selector) return;
            const table = document.querySelector(selector);
            if (!table) return;
            const queryRaw = input.value || '';
            const query = normalize(queryRaw).trim();
            const tbody = table.querySelector('tbody');
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr'));

            rows.forEach(row => {
                const cells = Array.from(row.querySelectorAll('td, th'));
                let rowText = '';
                for (const c of cells) {
                    rowText += ' ' + c.textContent;
                }
                const found = query === '' || normalize(rowText).indexOf(query) !== -1;
                row.style.display = found ? '' : 'none';
                // Clear previous highlights
                cells.forEach(c => clearHighlights(c));
                // If found and query present, highlight matches inside visible cells
                if (found && query !== '') {
                    cells.forEach(c => highlightElement(c, queryRaw));
                }
            });
        }

        // Attach listeners to all existing and future .table-filter inputs
        function attachFilters(scope = document) {
            const inputs = Array.from(scope.querySelectorAll('.table-filter'));
            inputs.forEach(input => {
                if (input._filterAttached) return;
                input._filterAttached = true;
                input.addEventListener('input', (e) => {
                    const pending = debounces.get(input);
                    if (pending) clearTimeout(pending);
                    debounces.set(input, setTimeout(() => {
                        filterTable(input);
                    }, 150));
                });
                // trigger initial run in case input is pre-filled
                setTimeout(() => filterTable(input), 0);
            });
        }

        attachFilters();

        // Observe DOM additions to attach filters to later-inserted tables/inputs
        const observer = new MutationObserver((mutations) => {
            for (const m of mutations) {
                if (m.addedNodes && m.addedNodes.length) {
                    attachFilters(m.target || document);
                }
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });
    });
    </script>

    @stack('scripts')
</body>
</html>
