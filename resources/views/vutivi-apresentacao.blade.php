<!doctype html>
<html lang="pt-MZ">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VUTIVI | Apresentação Interactiva</title>
    <style>
        /* ==========================================================
           VUTIVI slide deck: tokens, themes and motion foundations
           ========================================================== */
        :root {
            --orange: #FE6807;
            --amber: #ffb15f;
            --ink: #241b14;
            --ink-2: #2c1c13;
            --muted: #66594d;
            --paper: #fbfaf7;
            --surface: rgba(255, 255, 255, .82);
            --surface-strong: #ffffff;
            --surface-soft: #fffaf5;
            --surface-accent: #fff1e6;
            --line: #eadfce;
            --line-2: #decbb8;
            --good: #2f7d52;
            --bad: #a73522;
            --blue: #2f5f8f;
            --shadow: 0 26px 90px rgba(54, 39, 25, .16);
            --glass: linear-gradient(135deg, rgba(255,255,255,.92), rgba(255,244,236,.66));
            --glass-line: linear-gradient(135deg, rgba(254,104,7,.45), rgba(234,223,206,.8), rgba(255,255,255,.7));
            --bg:
                radial-gradient(circle at 12% 10%, rgba(254, 104, 7, .18), transparent 30%),
                radial-gradient(circle at 85% 20%, rgba(255, 177, 95, .18), transparent 28%),
                linear-gradient(135deg, #fff 0%, #fbfaf7 48%, #fff2e8 100%);
            --radius: 24px;
            --ease: cubic-bezier(.22, 1, .36, 1);
            --text-glow: 0 10px 40px rgba(254,104,7,.22);
        }

        body.dark {
            --ink: #fff7ef;
            --ink-2: #ffffff;
            --muted: #d8cec3;
            --paper: #050505;
            --surface: rgba(12, 10, 8, .82);
            --surface-strong: #0b0908;
            --surface-soft: #120d0a;
            --surface-accent: #1a110c;
            --line: #332820;
            --line-2: #4a3729;
            --good: #60d394;
            --bad: #ff7b68;
            --blue: #82b8ff;
            --shadow: 0 28px 110px rgba(0, 0, 0, .55);
            --glass: linear-gradient(135deg, rgba(18,13,10,.88), rgba(5,5,5,.76));
            --glass-line: linear-gradient(135deg, rgba(254,104,7,.58), rgba(74,55,41,.9), rgba(255,255,255,.08));
            --bg:
                radial-gradient(circle at 16% 12%, rgba(254,104,7,.24), transparent 26%),
                radial-gradient(circle at 82% 18%, rgba(255,177,95,.11), transparent 30%),
                radial-gradient(circle at 50% 90%, rgba(47,95,143,.12), transparent 30%),
                linear-gradient(135deg, #030201 0%, #0b0908 46%, #17120f 100%);
            --text-glow: 0 14px 52px rgba(254,104,7,.35);
        }

        * { box-sizing: border-box; }

        html { color-scheme: light; }
        body.dark { color-scheme: dark; }

        body {
            margin: 0;
            min-height: 100vh;
            overflow: hidden;
            background: var(--bg);
            color: var(--ink);
            cursor: none;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            transition: background .45s var(--ease), color .35s var(--ease);
        }

        body::before {
            content: "";
            position: fixed;
            inset: -20%;
            z-index: -3;
            background:
                conic-gradient(from 120deg at 48% 42%, rgba(254,104,7,.18), rgba(255,177,95,.08), transparent, rgba(254,104,7,.16));
            filter: blur(54px);
            opacity: .72;
            animation: aurora 16s linear infinite;
            pointer-events: none;
        }

        body.dark::before { opacity: .58; }
        button, a, input { font: inherit; cursor: none; }

        #particle-canvas {
            position: fixed;
            inset: 0;
            z-index: -2;
            opacity: .55;
            pointer-events: none;
        }

        .cursor, .cursor-trail {
            position: fixed;
            left: 0;
            top: 0;
            border-radius: 50%;
            pointer-events: none;
            transform: translate(-50%, -50%);
            z-index: 80;
            will-change: transform;
        }

        .cursor {
            width: 16px;
            height: 16px;
            background: var(--orange);
            box-shadow: 0 0 24px rgba(254,104,7,.8);
            mix-blend-mode: multiply;
        }

        body.dark .cursor { mix-blend-mode: screen; }

        .cursor-trail {
            width: 44px;
            height: 44px;
            border: 1px solid rgba(254,104,7,.48);
            background: rgba(254,104,7,.08);
        }

        /* ==========================================================
           Layout and reusable components
           ========================================================== */
        .deck {
            position: relative;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
        }

        .slide {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            padding: 6.2rem 5vw 5.3rem;
            opacity: 0;
            pointer-events: none;
            transform: translateX(8vw) scale(.96) rotateY(-5deg);
            filter: blur(14px) saturate(.7);
            transition: opacity .62s var(--ease), transform .8s var(--ease), filter .8s var(--ease);
            will-change: transform, opacity, filter;
        }

        .slide::after {
            content: "";
            position: absolute;
            inset: 0;
            opacity: 0;
            background: repeating-linear-gradient(90deg, transparent 0 12px, rgba(254,104,7,.08) 13px 14px);
            mix-blend-mode: multiply;
            pointer-events: none;
        }

        .slide.glitching::after { animation: glitchSweep .42s steps(2, end); }

        .slide.is-active {
            opacity: 1;
            pointer-events: auto;
            transform: translateX(0) scale(1) rotateY(0);
            filter: blur(0) saturate(1);
            z-index: 2;
        }

        .slide.is-leaving-left { transform: translateX(-8vw) scale(.96) rotateY(5deg); }
        .slide.is-leaving-right { transform: translateX(8vw) scale(.96) rotateY(-5deg); }

        .frame {
            width: min(1220px, 100%);
            min-height: min(710px, calc(100vh - 11.5rem));
            display: grid;
            gap: 1.35rem;
            align-content: center;
            position: relative;
        }

        .split {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, .92fr);
            gap: clamp(1.1rem, 4vw, 3.2rem);
            align-items: center;
        }

        .eyebrow {
            margin: 0 0 .85rem;
            color: var(--orange);
            font-size: clamp(.68rem, .82vw, .78rem);
            font-weight: 950;
            letter-spacing: .22em;
            text-transform: uppercase;
            text-shadow: 0 0 20px rgba(254,104,7,.18);
        }

        h1, h2 {
            margin: 0;
            color: var(--ink);
            font-family: Georgia, "Times New Roman", serif;
            line-height: .98;
            letter-spacing: 0;
            text-shadow: var(--text-glow);
        }

        h1 { font-size: clamp(3.3rem, 8vw, 7.8rem); }
        h2 { font-size: clamp(2rem, 5vw, 4.65rem); max-width: 980px; }

        .typewriter::after {
            content: "";
            display: inline-block;
            width: .08em;
            height: .82em;
            margin-left: .08em;
            background: var(--orange);
            animation: blink .8s steps(2, end) infinite;
            vertical-align: -.05em;
        }

        .subtitle {
            margin: 1rem 0 0;
            max-width: 780px;
            color: var(--muted);
            font-size: clamp(.98rem, 1.42vw, 1.28rem);
            line-height: 1.65;
            font-weight: 650;
        }

        .text {
            color: var(--muted);
            font-size: clamp(.92rem, 1vw, 1rem);
            line-height: 1.7;
        }

        .logo-light, .logo-dark { display: block; height: auto; }
        .logo-dark, body.dark .logo-light { display: none; }
        body.dark .logo-dark { display: block; }

        .hero-logo { width: min(330px, 72vw); margin-bottom: 1.25rem; view-transition-name: vutivi-logo; }
        .brand img { width: 112px; }

        .glass, .card, .flow-step, .demo-panel, .diagram, .modal-card {
            position: relative;
            border: 1px solid transparent;
            background:
                linear-gradient(var(--surface), var(--surface)) padding-box,
                var(--glass-line) border-box;
            box-shadow: var(--shadow);
            backdrop-filter: blur(22px);
        }

        .glass { border-radius: var(--radius); }

        .visual {
            min-height: 480px;
            overflow: hidden;
            padding: 1.2rem;
            transform-style: preserve-3d;
        }

        .visual img {
            width: 100%;
            height: 100%;
            min-height: 430px;
            object-fit: cover;
            border-radius: 18px;
            filter: saturate(1.08) contrast(1.03);
        }

        .floating-card {
            position: absolute;
            left: 1.8rem;
            right: 1.8rem;
            bottom: 1.8rem;
            padding: 1rem;
            border: 1px solid color-mix(in srgb, var(--line) 70%, transparent);
            border-radius: 18px;
            background: color-mix(in srgb, var(--surface-strong) 84%, transparent);
            box-shadow: 0 18px 48px rgba(54, 39, 25, .18);
            color: var(--ink);
        }

        body.dark .floating-card { box-shadow: 0 18px 54px rgba(0,0,0,.42); }

        .grid-2, .grid-3, .grid-4 { display: grid; gap: 1rem; }
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

        @supports (grid-template-rows: subgrid) {
            .grid-3 { align-items: stretch; }
            .card { display: grid; grid-template-rows: auto auto 1fr; }
        }

        .card {
            min-height: 148px;
            padding: 1.08rem;
            border-radius: 18px;
            overflow: hidden;
            transition: transform .45s var(--ease), box-shadow .45s var(--ease);
            will-change: transform;
        }

        .card::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: conic-gradient(from var(--scan-angle, 0deg), transparent, rgba(254,104,7,.95), transparent 35%);
            mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
            mask-composite: exclude;
            opacity: 0;
        }

        .card:hover {
            transform: translateY(-6px) scale(1.01);
            box-shadow: 0 26px 70px rgba(54, 39, 25, .17);
        }

        .card:hover::before { opacity: 1; animation: neonScan 1.2s linear infinite; }

        .slide.is-active .card, .slide.is-active .metric, .slide.is-active .flow-step {
            animation: rise .72s var(--ease) both;
            animation-delay: calc(var(--i, 0) * 70ms);
        }

        .card h3 {
            margin: .75rem 0 .35rem;
            color: var(--ink-2);
            font-size: clamp(1rem, 1.1vw, 1.12rem);
        }

        .card p { margin: 0; color: var(--muted); line-height: 1.58; font-size: .93rem; }

        .icon {
            width: 42px;
            height: 42px;
            display: inline-grid;
            place-items: center;
            border-radius: 14px;
            background: var(--surface-accent);
            color: var(--orange);
            box-shadow: inset 0 0 0 1px rgba(254,104,7,.12);
        }

        .icon svg {
            width: 22px;
            height: 22px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: transform .35s var(--ease), stroke-dashoffset .8s var(--ease);
        }

        .card:hover .icon svg { transform: rotate(-8deg) scale(1.12); }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border-radius: 999px;
            padding: .36rem .68rem;
            font-size: .76rem;
            font-weight: 900;
            color: var(--ink);
            background: var(--surface-accent);
            border: 1px solid var(--line);
            animation: pulseSoft 2.4s ease-in-out infinite;
        }

        .badge.good { color: var(--good); background: color-mix(in srgb, var(--good) 12%, var(--surface-strong)); }
        .badge.bad { color: var(--bad); background: color-mix(in srgb, var(--bad) 12%, var(--surface-strong)); }
        .badge.warn { color: var(--orange); background: var(--surface-accent); }

        .metric-row { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; }
        .metric {
            padding: 1rem;
            border-radius: 18px;
            color: var(--ink);
            min-height: 120px;
        }

        .metric strong {
            display: block;
            color: var(--orange);
            font-size: clamp(2rem, 3.2vw, 3rem);
            line-height: 1;
        }

        .metric span { display: block; margin-top: .55rem; color: var(--muted); font-size: .92rem; font-weight: 800; }

        /* ==========================================================
           Topbar, progress, dots, modal and keyboard UX
           ========================================================== */
        .topbar {
            position: fixed;
            left: 4vw;
            right: 4vw;
            top: 1.1rem;
            z-index: 30;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            pointer-events: none;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: .8rem;
            pointer-events: auto;
        }

        .brand span {
            color: var(--muted);
            font-size: .76rem;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .controls, .dots { pointer-events: auto; }

        .controls {
            display: flex;
            align-items: center;
            gap: .45rem;
            padding: .45rem;
            border: 1px solid transparent;
            border-radius: 999px;
            background:
                linear-gradient(var(--surface), var(--surface)) padding-box,
                var(--glass-line) border-box;
            box-shadow: 0 14px 38px rgba(54, 39, 25, .12);
            backdrop-filter: blur(18px);
        }

        .controls button, .theme-toggle {
            width: 42px;
            height: 42px;
            border: 0;
            border-radius: 50%;
            background: var(--surface-accent);
            color: var(--ink);
            transition: transform .25s var(--ease), background .25s var(--ease), color .25s var(--ease), box-shadow .25s var(--ease);
        }

        .controls button:hover, .theme-toggle:hover {
            transform: translateY(-2px);
            background: var(--orange);
            color: white;
            box-shadow: 0 0 24px rgba(254,104,7,.45);
        }

        .counter {
            min-width: 74px;
            text-align: center;
            color: var(--muted);
            font-size: .9rem;
            font-weight: 950;
            overflow: hidden;
        }

        .counter .roll { display: inline-block; animation: counterRoll .42s var(--ease); }

        .progress {
            position: fixed;
            left: 0;
            right: 0;
            top: 0;
            height: 5px;
            z-index: 50;
            background: color-mix(in srgb, var(--line) 60%, transparent);
        }

        .progress span {
            display: block;
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, var(--orange), var(--amber), var(--orange));
            box-shadow: 0 0 18px rgba(254,104,7,.85), 0 0 34px rgba(254,104,7,.45);
            transition: width .45s var(--ease);
        }

        .dots {
            position: fixed;
            left: 50%;
            bottom: 1.18rem;
            z-index: 25;
            display: flex;
            gap: .36rem;
            transform: translateX(-50%);
            padding: .55rem .72rem;
            border: 1px solid transparent;
            border-radius: 999px;
            background:
                linear-gradient(var(--surface), var(--surface)) padding-box,
                var(--glass-line) border-box;
            backdrop-filter: blur(18px);
        }

        .dot-wrap { position: relative; display: inline-flex; }
        .dot {
            width: .55rem;
            height: .55rem;
            border: 0;
            border-radius: 999px;
            background: color-mix(in srgb, var(--muted) 42%, transparent);
            transition: width .3s var(--ease), background .3s var(--ease), transform .3s var(--ease);
        }

        .dot:hover { transform: scale(1.25); }
        .dot.is-active { width: 1.8rem; background: var(--orange); box-shadow: 0 0 18px rgba(254,104,7,.55); }

        .dot-preview {
            position: absolute;
            left: 50%;
            bottom: 1.3rem;
            width: 168px;
            padding: .75rem;
            border-radius: 16px;
            background: var(--surface-strong);
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
            color: var(--ink);
            opacity: 0;
            transform: translateX(-50%) translateY(8px) scale(.94);
            transition: opacity .25s var(--ease), transform .25s var(--ease);
            pointer-events: none;
        }

        .dot-wrap:hover .dot-preview { opacity: 1; transform: translateX(-50%) translateY(0) scale(1); }
        .dot-preview strong { display: block; font-size: .78rem; }
        .dot-preview span { display: block; margin-top: .2rem; color: var(--muted); font-size: .72rem; line-height: 1.35; }

        .kbd {
            position: fixed;
            right: 4vw;
            bottom: 1.15rem;
            z-index: 24;
            color: var(--muted);
            font-size: .78rem;
            font-weight: 900;
            animation: hintIn 1s var(--ease) .8s both;
        }

        .modal {
            position: fixed;
            inset: 0;
            z-index: 70;
            display: none;
            place-items: center;
            padding: 1rem;
            background: rgba(0,0,0,.48);
            backdrop-filter: blur(12px);
        }

        .modal.is-open { display: grid; }
        .modal-card { width: min(880px, 100%); max-height: 84vh; overflow: auto; border-radius: 24px; padding: 1.2rem; }
        .goto-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: .8rem; margin-top: 1rem; }
        .goto-item {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--surface-soft);
            color: var(--ink);
            text-align: left;
            padding: .85rem;
            transition: transform .25s var(--ease), border-color .25s var(--ease);
        }
        .goto-item:hover { transform: translateY(-3px); border-color: var(--orange); }

        /* ==========================================================
           Diagrams, flow simulation and tooltips
           ========================================================== */
        .diagram {
            width: 100%;
            padding: 1.2rem;
            overflow: visible;
            border-radius: var(--radius);
        }

        .diagram svg { width: 100%; height: auto; display: block; overflow: visible; }
        .diagram text { font: 800 13px Inter, system-ui, sans-serif; fill: var(--ink); }
        .diagram .small { font-size: 11px; font-weight: 700; fill: var(--muted); }
        .diagram .box { fill: var(--surface-soft); stroke: var(--line-2); stroke-width: 1.6; }
        .diagram .accent { fill: var(--surface-accent); stroke: var(--orange); stroke-width: 1.8; }
        .diagram .line { stroke: color-mix(in srgb, var(--orange) 55%, var(--muted)); stroke-width: 1.8; fill: none; marker-end: url(#arrow); }
        .diagram .node { transform-box: fill-box; transform-origin: center; }
        .slide.is-active .diagram .node { animation: nodeIn .58s var(--ease) both; animation-delay: calc(var(--i, 0) * 100ms); }
        .slide.is-active .diagram .line { stroke-dasharray: 420; stroke-dashoffset: 420; animation: drawLine 1.1s var(--ease) both; animation-delay: calc(var(--i, 0) * 120ms); }
        .diagram .node:hover .box, .diagram .node:hover .accent, .diagram .flow-node:hover { filter: drop-shadow(0 0 14px rgba(254,104,7,.5)); }
        .diagram .is-current { animation: pulseNode 1.35s ease-in-out infinite; }

        .tooltip {
            position: fixed;
            z-index: 90;
            max-width: 260px;
            padding: .72rem .85rem;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--surface-strong);
            box-shadow: var(--shadow);
            color: var(--ink);
            opacity: 0;
            transform: translateY(8px);
            transition: opacity .2s var(--ease), transform .2s var(--ease);
            pointer-events: none;
            font-size: .86rem;
            line-height: 1.45;
        }

        .tooltip.is-visible { opacity: 1; transform: translateY(0); }
        .flow-controls { display: flex; flex-wrap: wrap; gap: .6rem; margin-top: .8rem; }
        .pill-btn, .primary-btn {
            border: 1px solid var(--line);
            border-radius: 999px;
            background: var(--surface-accent);
            color: var(--ink);
            padding: .72rem 1rem;
            font-weight: 900;
            transition: transform .25s var(--ease), background .25s var(--ease), color .25s var(--ease);
        }
        .pill-btn:hover, .primary-btn:hover { transform: translateY(-2px); background: var(--orange); color: #fff; }
        .primary-btn { position: relative; overflow: hidden; background: var(--orange); color: #fff; box-shadow: 0 14px 34px rgba(254,104,7,.28); }
        .ripple { position: absolute; border-radius: 50%; background: rgba(255,255,255,.42); transform: scale(0); animation: ripple .68s ease-out; pointer-events: none; }

        /* ==========================================================
           Demo slides
           ========================================================== */
        .demo-panel { border-radius: 22px; padding: 1rem; }
        .search-box { display: flex; gap: .7rem; align-items: center; border: 1px solid var(--line); border-radius: 16px; background: var(--surface-strong); padding: .8rem 1rem; }
        .search-box input { flex: 1; border: 0; outline: 0; background: transparent; color: var(--ink); font-weight: 800; min-width: 0; }
        .results-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .8rem; margin-top: 1rem; }
        .book-result { border: 1px solid var(--line); border-radius: 16px; padding: .85rem; background: var(--surface-soft); animation: rise .48s var(--ease) both; animation-delay: calc(var(--i, 0) * 80ms); }
        .book-result h3 { margin: 0 0 .25rem; font-size: 1rem; }
        .book-result p { margin: 0; color: var(--muted); font-size: .86rem; }

        .loan-demo { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
        .demo-col { border: 1px solid var(--line); border-radius: 18px; background: var(--surface-soft); padding: 1rem; min-height: 220px; }
        .status-orb { width: 88px; height: 88px; border-radius: 50%; display: grid; place-items: center; margin: 1rem auto; background: var(--surface-accent); color: var(--orange); box-shadow: inset 0 0 0 1px rgba(254,104,7,.22), 0 0 28px rgba(254,104,7,.12); font-weight: 950; text-align: center; }
        .demo-actions { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: 1rem; }
        .checkmark { display: none; color: var(--good); font-weight: 950; animation: pop .55s var(--ease); }
        .checkmark.is-visible { display: block; }

        .mock-navbar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; border: 1px solid var(--line); border-radius: 18px; background: var(--surface-strong); padding: .8rem 1rem; }
        .bell { position: relative; width: 46px; height: 46px; border: 0; border-radius: 14px; background: var(--surface-accent); color: var(--ink); }
        .notif-badge { position: absolute; right: -6px; top: -6px; min-width: 22px; height: 22px; border-radius: 999px; display: grid; place-items: center; background: var(--orange); color: white; font-size: .72rem; font-weight: 950; transform-origin: center; }
        .notif-badge.bump { animation: pop .38s var(--ease); }
        .notif-dropdown { display: none; margin-top: .8rem; border: 1px solid var(--line); border-radius: 18px; background: var(--surface-strong); overflow: hidden; }
        .notif-dropdown.is-open { display: block; animation: slideDown .35s var(--ease); }
        .notif-item { display: grid; grid-template-columns: 40px 1fr; gap: .7rem; padding: .85rem; border-bottom: 1px solid var(--line); animation: rise .4s var(--ease) both; }
        .notif-item:last-child { border-bottom: 0; }

        .roadmap { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .8rem; margin-top: 1rem; }
        .qr { width: 132px; height: 132px; display: grid; grid-template-columns: repeat(5, 1fr); gap: 5px; padding: 10px; border-radius: 18px; background: var(--surface-strong); border: 1px solid var(--line); }
        .qr span { background: var(--ink); border-radius: 3px; }
        .credits { display: flex; flex-wrap: wrap; gap: .6rem; margin-top: 1rem; }
        .credits span { opacity: 0; animation: rise .55s var(--ease) both; animation-delay: calc(var(--i, 0) * 110ms); }

        /* ==========================================================
           Animation keyframes
           ========================================================== */
        @keyframes aurora { to { transform: rotate(1turn) scale(1.08); } }
        @keyframes rise { from { opacity: 0; transform: translateY(18px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes blink { 50% { opacity: 0; } }
        @keyframes neonScan { to { --scan-angle: 360deg; } }
        @keyframes pulseSoft { 50% { transform: scale(1.025); box-shadow: 0 0 18px rgba(254,104,7,.16); } }
        @keyframes pulseNode { 50% { filter: drop-shadow(0 0 18px rgba(254,104,7,.75)); transform: scale(1.035); } }
        @keyframes nodeIn { from { opacity: 0; transform: scale(.84); } to { opacity: 1; transform: scale(1); } }
        @keyframes drawLine { to { stroke-dashoffset: 0; } }
        @keyframes glitchSweep { 0%,100% { opacity:0; transform: translateX(0); } 35% { opacity:.55; transform: translateX(-1.2%); } 70% { opacity:.35; transform: translateX(1%); } }
        @keyframes counterRoll { from { transform: translateY(80%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @keyframes hintIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes ripple { to { transform: scale(3.8); opacity: 0; } }
        @keyframes pop { 0% { transform: scale(.65); } 70% { transform: scale(1.18); } 100% { transform: scale(1); } }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes bounce { 50% { transform: translateY(8px); } }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; scroll-behavior: auto !important; }
            body { cursor: auto; }
            .cursor, .cursor-trail, #particle-canvas { display: none; }
        }

        /* ==========================================================
           Responsive behaviour: mobile scroll presentation
           ========================================================== */
        @media (max-width: 900px) {
            body { overflow: auto; cursor: auto; }
            .cursor, .cursor-trail { display: none; }
            .deck { height: auto; min-height: 100vh; overflow: visible; }
            .slide {
                position: relative;
                min-height: 100vh;
                display: grid;
                opacity: 1;
                pointer-events: auto;
                filter: none;
                transform: none;
                padding: 5.4rem 1rem 6rem;
            }
            .slide:not(.is-active) { opacity: .42; }
            .split, .grid-2, .grid-3, .grid-4, .metric-row, .flow, .loan-demo, .roadmap, .results-grid { grid-template-columns: 1fr; }
            .flow-step::after { display: none; }
            .visual { min-height: 310px; }
            .visual img { min-height: 270px; }
            .brand span, .kbd { display: none; }
            .brand img { width: 86px; }
            .topbar { left: .75rem; right: .75rem; top: .75rem; }
            .controls { gap: .18rem; padding: .32rem; }
            .controls button, .theme-toggle { width: 38px; height: 38px; }
            .controls .counter { display: none; }
            .dots { max-width: calc(100vw - 1.5rem); overflow-x: auto; bottom: .75rem; }
            h1 { font-size: clamp(2.8rem, 20vw, 5rem); }
            h2 { font-size: clamp(1.8rem, 10vw, 3.1rem); }
        }

        @media (min-width: 1800px) {
            .frame { width: min(1500px, 100%); }
            .subtitle { max-width: 980px; }
        }
    </style>
</head>
<body>
    <canvas id="particle-canvas" aria-hidden="true"></canvas>
    <div class="cursor" aria-hidden="true"></div>
    <div class="cursor-trail" aria-hidden="true"></div>
    <div class="progress" aria-hidden="true"><span id="progress"></span></div>
    <div class="tooltip" id="tooltip" role="status"></div>

    <header class="topbar">
        <div class="brand">
            <img class="logo-light" src="{{ asset('img/png/logo_bb.png') }}" alt="Logotipo VUTIVI">
            <img class="logo-dark" src="{{ asset('img/png/logo_bw.png') }}" alt="Logotipo VUTIVI">
            <span>Apresentação do projecto</span>
        </div>
        <div class="controls" aria-label="Controlos da apresentação">
            <button id="prev" type="button" aria-label="Slide anterior">‹</button>
            <span class="counter"><span id="current" class="roll">01</span>/<span id="total">20</span></span>
            <button id="next" type="button" aria-label="Slide seguinte">›</button>
            <button id="autoplay" type="button" aria-label="Apresentação automática">▶</button>
            <button id="theme" class="theme-toggle" type="button" aria-label="Alternar tema">◐</button>
        </div>
    </header>

    <main class="deck" id="deck">
        <section class="slide is-active hero-slide" data-title="Capa" data-note="Abertura do projecto VUTIVI">
            <div class="frame split">
                <div data-depth="22">
                    <picture>
                        <img class="hero-logo logo-light" src="{{ asset('img/png/logo_bb.png') }}" alt="VUTIVI">
                        <img class="hero-logo logo-dark" src="{{ asset('img/png/logo_bw.png') }}" alt="VUTIVI">
                    </picture>
                    <p class="eyebrow">Biblioteca física e digital</p>
                    <h1 data-type="VUTIVI">VUTIVI</h1>
                    <p class="subtitle" data-type="Plataforma de gestão de recursos, empréstimos, acessos digitais e circulação organizada do conhecimento.">Plataforma de gestão de recursos, empréstimos, acessos digitais e circulação organizada do conhecimento.</p>
                    <div style="display:flex;gap:.7rem;flex-wrap:wrap;margin-top:1.4rem">
                        <button class="primary-btn" data-start>Iniciar Apresentação</button>
                        <span class="badge warn">Laravel · Biblioteca · UX</span>
                    </div>
                    <p class="text" style="margin-top:1rem;animation:bounce 1.6s ease-in-out infinite">Use → ou toque para avançar</p>
                </div>
                <div class="visual glass" data-depth="-18">
                    <img src="{{ asset('img/png/book_background.png') }}" alt="Livros e ambiente de biblioteca">
                    <div class="floating-card">
                        <strong>Conhecimento acessível</strong>
                        <p class="text">Um sistema académico para pesquisar, publicar, emprestar, devolver e acompanhar recursos com clareza.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Introdução" data-note="Problema e contexto">
            <div class="frame">
                <p class="eyebrow">01. Introdução</p>
                <h2 data-type="Organizar conhecimento exige mais do que guardar ficheiros.">Organizar conhecimento exige mais do que guardar ficheiros.</h2>
                <p class="subtitle">Bibliotecas pequenas, centros de formação e equipas académicas lidam com dispersão de recursos, controlo manual de empréstimos e baixa visibilidade sobre prazos. A VUTIVI responde a esse cenário com uma experiência digital integrada.</p>
                <div class="grid-3">
                    <article class="card" style="--i:0"><span class="icon"><svg><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></svg></span><h3>Recursos dispersos</h3><p>Livros, documentos e materiais digitais passam a viver num catálogo pesquisável e partilhado.</p></article>
                    <article class="card" style="--i:1"><span class="icon"><svg><path d="M12 8v5l3 2"/><circle cx="12" cy="12" r="10"/></svg></span><h3>Prazos visíveis</h3><p>O utilizador acompanha devoluções, atrasos e pedidos de extensão sem depender de controlo informal.</p></article>
                    <article class="card" style="--i:2"><span class="icon"><svg><path d="M20 6 9 17l-5-5"/></svg></span><h3>Rastreabilidade</h3><p>Cada empréstimo mantém histórico, estado, dono, utilizador e sincronização de disponibilidade.</p></article>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Objectivos" data-note="Objectivo geral e metas">
            <div class="frame split">
                <div>
                    <p class="eyebrow">02. Objectivos</p>
                    <h2 data-type="Centralizar a gestão da biblioteca híbrida.">Centralizar a gestão da biblioteca híbrida.</h2>
                    <p class="subtitle">O objectivo central da VUTIVI é disponibilizar uma plataforma web que permita organizar recursos físicos e digitais, controlar empréstimos, apoiar a descoberta de materiais e melhorar a experiência de utilizadores e donos de recursos.</p>
                </div>
                <div class="grid-2">
                    <article class="card" style="--i:0"><h3>Utilizadores</h3><p>Cadastrar, autenticar e associar cada acção a uma identidade clara.</p></article>
                    <article class="card" style="--i:1"><h3>Recursos</h3><p>Registar físicos e digitais com capa, descrição, disponibilidade e dono.</p></article>
                    <article class="card" style="--i:2"><h3>Empréstimos</h3><p>Controlar pedido, aprovação, devolução, extensão e histórico.</p></article>
                    <article class="card" style="--i:3"><h3>Pesquisa</h3><p>Filtrar por título, tipo, formato, estado e popularidade.</p></article>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Visão Geral" data-note="Fluxo operacional">
            <div class="frame">
                <p class="eyebrow">03. Visão geral da solução</p>
                <h2 data-type="Um fluxo simples para uma operação completa.">Um fluxo simples para uma operação completa.</h2>
                <p class="subtitle">A plataforma liga catálogo, termos, empréstimos, notificações e disponibilidade num único percurso operacional.</p>
                <div class="flow">
                    <div class="flow-step" style="--i:0"><strong>Pesquisar</strong><span>O utilizador encontra recursos por filtros e palavras-chave.</span></div>
                    <div class="flow-step" style="--i:1"><strong>Abrir recurso</strong><span>Consulta descrição, dono, formato e cópias disponíveis.</span></div>
                    <div class="flow-step" style="--i:2"><strong>Solicitar</strong><span>Aceita termos e cria o pedido de empréstimo.</span></div>
                    <div class="flow-step" style="--i:3"><strong>Acompanhar</strong><span>Vê estado, prazo, alertas e extensão.</span></div>
                    <div class="flow-step" style="--i:4"><strong>Devolver</strong><span>O recurso volta a ficar disponível.</span></div>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Perfis" data-note="Actores do sistema">
            <div class="frame">
                <p class="eyebrow">04. Perfis de utilizador</p>
                <h2 data-type="Actores com responsabilidades distintas.">Actores com responsabilidades distintas.</h2>
                <div class="grid-3">
                    <article class="card" style="--i:0"><span class="icon"><svg><circle cx="12" cy="7" r="4"/><path d="M5.5 21a6.5 6.5 0 0 1 13 0"/></svg></span><h3>Cliente ou leitor</h3><p>Pesquisa recursos, consulta detalhes, pede empréstimos, devolve e solicita extensão.</p></article>
                    <article class="card" style="--i:1"><span class="icon"><svg><path d="M4 4h16v16H4z"/><path d="M8 8h8"/><path d="M8 12h8"/><path d="M8 16h5"/></svg></span><h3>Dono do recurso</h3><p>Publica materiais, acompanha pedidos, aprova extensões e controla disponibilidade.</p></article>
                    <article class="card" style="--i:2"><span class="icon"><svg><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-5"/></svg></span><h3>Gestor</h3><p>Supervisiona a consistência, valida registos e apoia a melhoria contínua.</p></article>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Funcionalidades" data-note="Módulos principais">
            <div class="frame">
                <p class="eyebrow">05. Funcionalidades principais</p>
                <h2 data-type="Uma biblioteca com operação, descoberta e memória.">Uma biblioteca com operação, descoberta e memória.</h2>
                <div class="grid-4">
                    <article class="card" style="--i:0"><h3>Biblioteca</h3><p>Listagem com filtros, pesquisa, paginação e cards ricos.</p></article>
                    <article class="card" style="--i:1"><h3>Recursos físicos</h3><p>Localização, condição, prazo máximo, quantidade e termos.</p></article>
                    <article class="card" style="--i:2"><h3>Recursos digitais</h3><p>Ficheiros com modo de visualização ou download.</p></article>
                    <article class="card" style="--i:3"><h3>Histórico</h3><p>Registo de empréstimos devolvidos, recusados e atrasados.</p></article>
                    <article class="card" style="--i:4"><h3>Alertas</h3><p>Prazos próximos, pedidos de extensão e decisões.</p></article>
                    <article class="card" style="--i:5"><h3>Favoritos</h3><p>Marcação rápida de recursos importantes.</p></article>
                    <article class="card" style="--i:6"><h3>Conta</h3><p>Resumo de recursos, empréstimos feitos e emprestados.</p></article>
                    <article class="card" style="--i:7"><h3>Toasts</h3><p>Feedback visual claro após acções relevantes.</p></article>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Recursos" data-note="Físico vs digital">
            <div class="frame split">
                <div>
                    <p class="eyebrow">06. Gestão de recursos</p>
                    <h2 data-type="Um recurso base, dois comportamentos especializados.">Um recurso base, dois comportamentos especializados.</h2>
                    <p class="subtitle">A VUTIVI separa dados comuns em `resources` e detalhes específicos em tabelas próprias. Isso evita duplicação e permite evoluir os tipos de recurso com menor impacto.</p>
                </div>
                <div class="grid-2">
                    <article class="card" style="--i:0"><span class="icon"><svg><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M20 22V2H6.5A2.5 2.5 0 0 0 4 4.5v15"/></svg></span><h3>Físico</h3><p>Localização, condição, prazo máximo, aprovação, extensão e quantidade disponível.</p></article>
                    <article class="card" style="--i:1"><span class="icon"><svg><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 15h6"/></svg></span><h3>Digital</h3><p>Ficheiro, tipo de acesso, janela de utilização, download ou visualização protegida.</p></article>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Fluxo" data-note="Diagrama animado">
            <div class="frame">
                <p class="eyebrow">07. Fluxo de empréstimo</p>
                <h2 data-type="Da solicitação à devolução com estado consistente.">Da solicitação à devolução com estado consistente.</h2>
                <div class="diagram glass flow-diagram">
                    <svg viewBox="0 0 1040 360" role="img" aria-label="Fluxo de empréstimo">
                        <defs><marker id="arrow" markerWidth="10" markerHeight="10" refX="8" refY="3" orient="auto"><path d="M0,0 L0,6 L9,3 z" fill="#FE6807"/></marker></defs>
                        <path class="line" style="--i:1" d="M150 180 H305"/><path class="line" style="--i:2" d="M410 180 H565"/><path class="line" style="--i:3" d="M670 180 H825"/><path class="line" style="--i:4" d="M625 240 C625 310 365 310 365 240"/>
                        <g class="node flow-node is-current" data-flow-node="0" data-tip="O utilizador aceita termos e cria o pedido." style="--i:0"><rect class="accent" x="30" y="120" width="130" height="120" rx="18"/><text x="95" y="170" text-anchor="middle">Pedido</text><text class="small" x="95" y="194" text-anchor="middle">Aceite</text></g>
                        <g class="node flow-node" data-flow-node="1" data-tip="O dono aprova ou o sistema aprova automaticamente." style="--i:1"><rect class="box" x="300" y="120" width="130" height="120" rx="18"/><text x="365" y="170" text-anchor="middle">Aprovação</text><text class="small" x="365" y="194" text-anchor="middle">Dono</text></g>
                        <g class="node flow-node" data-flow-node="2" data-tip="O recurso fica em uso e passa a ter prazo activo." style="--i:2"><rect class="box" x="560" y="120" width="130" height="120" rx="18"/><text x="625" y="170" text-anchor="middle">Em uso</text><text class="small" x="625" y="194" text-anchor="middle">Prazo</text></g>
                        <g class="node flow-node" data-flow-node="3" data-tip="A devolução encerra a reserva e repõe disponibilidade." style="--i:3"><rect class="box" x="820" y="120" width="160" height="120" rx="18"/><text x="900" y="170" text-anchor="middle">Devolução</text><text class="small" x="900" y="194" text-anchor="middle">Disponível</text></g>
                    </svg>
                    <div class="flow-controls">
                        <button class="pill-btn" data-flow-step="0">Pedido</button>
                        <button class="pill-btn" data-flow-step="1">Aprovação</button>
                        <button class="pill-btn" data-flow-step="2">Em uso</button>
                        <button class="pill-btn" data-flow-step="3">Devolução</button>
                    </div>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Extensão" data-note="Notificações e decisões">
            <div class="frame split">
                <div>
                    <p class="eyebrow">08. Extensão e notificações</p>
                    <h2 data-type="O pedido de mais tempo também tem governação.">O pedido de mais tempo também tem governação.</h2>
                    <p class="subtitle">O cliente solicita extensão, o dono decide e o sistema informa o resultado. A reserva mantém metadados de pedido, decisão, data e estado final.</p>
                </div>
                <div class="grid-2">
                    <article class="card" style="--i:0"><h3>Pedido</h3><p>O estado passa para extensão pendente e o dono visualiza a solicitação.</p></article>
                    <article class="card" style="--i:1"><h3>Decisão</h3><p>A aprovação aumenta o prazo; a recusa mantém o prazo original.</p></article>
                    <article class="card" style="--i:2"><h3>Cliente notificado</h3><p>A navbar e a área de alertas mostram aprovação ou recusa.</p></article>
                    <article class="card" style="--i:3"><h3>Consistência</h3><p>Após devolução, a disponibilidade volta a permitir novo empréstimo.</p></article>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Demo Pesquisa" data-note="Biblioteca interactiva">
            <div class="frame">
                <p class="eyebrow">09. Demo A · Pesquisa na biblioteca</p>
                <h2 data-type="A descoberta de recursos pode ser simulada ao vivo.">A descoberta de recursos pode ser simulada ao vivo.</h2>
                <div class="demo-panel">
                    <label class="search-box">
                        <span>⌕</span>
                        <input id="demo-search" type="search" value="" placeholder="Digite L, La, Lar ou Lara">
                    </label>
                    <div class="results-grid" id="search-results"></div>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Demo Empréstimo" data-note="Ciclo simulado">
            <div class="frame">
                <p class="eyebrow">10. Demo B · Fluxo de empréstimo</p>
                <h2 data-type="O estado do recurso muda conforme a acção.">O estado do recurso muda conforme a acção.</h2>
                <div class="demo-panel loan-demo">
                    <div class="demo-col"><h3>Recurso</h3><p>Laravel para Bibliotecas</p><span id="resource-badge" class="badge good">Disponível</span></div>
                    <div class="demo-col"><h3>Empréstimo</h3><p id="loan-copy">Nenhum pedido activo.</p><div class="demo-actions"><button class="pill-btn" data-loan-action="request">Solicitar</button><button class="pill-btn" data-loan-action="approve">Aprovar</button><button class="pill-btn" data-loan-action="extend">Pedir Extensão</button><button class="pill-btn" data-loan-action="approveExtend">Aprovar Extensão</button><button class="pill-btn" data-loan-action="return">Devolver</button></div></div>
                    <div class="demo-col"><h3>Estado</h3><div class="status-orb" id="loan-state">Livre</div><p id="loan-countdown" class="text">Prazo: --</p><strong id="loan-check" class="checkmark">✓ Recurso devolvido</strong></div>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Demo Notificações" data-note="Alertas simulados">
            <div class="frame">
                <p class="eyebrow">11. Demo C · Notificações em tempo real</p>
                <h2 data-type="A interface informa sem interromper o trabalho.">A interface informa sem interromper o trabalho.</h2>
                <div class="demo-panel">
                    <div class="mock-navbar"><strong>VUTIVI</strong><button class="bell" id="bell">🔔<span class="notif-badge" id="notif-count">0</span></button></div>
                    <div class="demo-actions"><button class="pill-btn" data-notif="approved">Empréstimo aprovado</button><button class="pill-btn" data-notif="due">Prazo a vencer</button><button class="pill-btn" data-notif="denied">Extensão recusada</button></div>
                    <div class="notif-dropdown" id="notif-list"></div>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Casos de Uso" data-note="Actores e operações">
            <div class="frame">
                <p class="eyebrow">12. Diagrama de casos de uso</p>
                <h2 data-type="Interacções principais do sistema.">Interacções principais do sistema.</h2>
                <div class="diagram glass">
                    <svg viewBox="0 0 1040 470" role="img" aria-label="Diagrama de casos de uso">
                        <defs><marker id="arrow2" markerWidth="10" markerHeight="10" refX="8" refY="3" orient="auto"><path d="M0,0 L0,6 L9,3 z" fill="#9f6a47"/></marker></defs>
                        <rect class="box node" data-tip="Fronteira lógica da aplicação Laravel." style="--i:0" x="250" y="40" width="540" height="390" rx="26"/><text x="520" y="75" text-anchor="middle">Sistema VUTIVI</text>
                        <g class="node" data-tip="Cliente que pesquisa, solicita e devolve recursos." style="--i:1"><circle cx="110" cy="115" r="28" fill="#fff1e6" stroke="#FE6807"/><path d="M110 143 v80 M70 175 h80 M110 223 l-42 62 M110 223 l42 62" stroke="#FE6807" fill="none" stroke-width="4"/><text x="110" y="330" text-anchor="middle">Utilizador</text></g>
                        <g class="node" data-tip="Pessoa responsável por gerir e aprovar recursos." style="--i:2"><circle cx="930" cy="115" r="28" fill="#fff1e6" stroke="#FE6807"/><path d="M930 143 v80 M890 175 h80 M930 223 l-42 62 M930 223 l42 62" stroke="#FE6807" fill="none" stroke-width="4"/><text x="930" y="330" text-anchor="middle">Dono</text></g>
                        <g class="node" data-tip="Operações que aproximam utilizador e recurso." style="--i:3"><ellipse class="accent" cx="410" cy="125" rx="110" ry="34"/><text x="410" y="130" text-anchor="middle">Pesquisar recurso</text><ellipse class="box" cx="410" cy="205" rx="120" ry="34"/><text x="410" y="210" text-anchor="middle">Solicitar empréstimo</text><ellipse class="box" cx="410" cy="285" rx="105" ry="34"/><text x="410" y="290" text-anchor="middle">Pedir extensão</text><ellipse class="box" cx="410" cy="365" rx="90" ry="34"/><text x="410" y="370" text-anchor="middle">Devolver</text></g>
                        <g class="node" data-tip="Operações executadas pelo dono do recurso." style="--i:4"><ellipse class="box" cx="650" cy="165" rx="100" ry="34"/><text x="650" y="170" text-anchor="middle">Gerir recurso</text><ellipse class="box" cx="650" cy="250" rx="125" ry="34"/><text x="650" y="255" text-anchor="middle">Aprovar extensão</text><ellipse class="box" cx="650" cy="335" rx="126" ry="34"/><text x="650" y="340" text-anchor="middle">Receber notificação</text></g>
                        <path class="line" style="--i:5" d="M145 155 C220 130 270 125 300 125"/><path class="line" style="--i:6" d="M145 190 C230 205 270 205 290 205"/><path class="line" style="--i:7" d="M145 235 C230 285 270 285 305 285"/><path class="line" style="--i:8" d="M895 220 C800 250 790 250 775 250"/>
                    </svg>
                </div>
            </div>
        </section>

        <section class="slide" data-title="ERD" data-note="Base de dados">
            <div class="frame">
                <p class="eyebrow">13. Diagrama de relacionamento</p>
                <h2 data-type="Modelo de dados principal.">Modelo de dados principal.</h2>
                <div class="diagram glass">
                    <svg viewBox="0 0 1040 500" role="img" aria-label="Diagrama entidade relacionamento">
                        <defs><marker id="arrow3" markerWidth="10" markerHeight="10" refX="8" refY="3" orient="auto"><path d="M0,0 L0,6 L9,3 z" fill="#9f6a47"/></marker></defs>
                        <g class="node" data-tip="Utilizadores autenticados, donos e clientes." style="--i:0"><rect class="accent" x="60" y="70" width="160" height="90" rx="16"/><text x="140" y="108" text-anchor="middle">users</text><text class="small" x="140" y="132" text-anchor="middle">id, name, email</text></g>
                        <g class="node" data-tip="Raiz comum dos recursos físicos e digitais." style="--i:1"><rect class="accent" x="440" y="70" width="170" height="90" rx="16"/><text x="525" y="108" text-anchor="middle">resources</text><text class="small" x="525" y="132" text-anchor="middle">title, type, owner</text></g>
                        <g class="node" data-tip="Detalhes próprios dos livros físicos." style="--i:2"><rect class="box" x="780" y="40" width="190" height="86" rx="16"/><text x="875" y="78" text-anchor="middle">physical_resources</text><text class="small" x="875" y="102" text-anchor="middle">location, max_days</text></g>
                        <g class="node" data-tip="Detalhes próprios dos ficheiros digitais." style="--i:3"><rect class="box" x="780" y="145" width="190" height="86" rx="16"/><text x="875" y="183" text-anchor="middle">digital_resources</text><text class="small" x="875" y="207" text-anchor="middle">file, access_type</text></g>
                        <g class="node" data-tip="Regista ciclo de empréstimo, devolução e extensão." style="--i:4"><rect class="box" x="430" y="300" width="190" height="100" rx="16"/><text x="525" y="340" text-anchor="middle">reservations</text><text class="small" x="525" y="364" text-anchor="middle">status, dates, return</text></g>
                        <g class="node" data-tip="Tabela pivot para favoritos dos utilizadores." style="--i:5"><rect class="box" x="70" y="310" width="180" height="90" rx="16"/><text x="160" y="348" text-anchor="middle">favorites</text><text class="small" x="160" y="372" text-anchor="middle">resource_user</text></g>
                        <g class="node" data-tip="Termos e aceitações ligadas às reservas." style="--i:6"><rect class="box" x="765" y="310" width="210" height="90" rx="16"/><text x="870" y="348" text-anchor="middle">terms</text><text class="small" x="870" y="372" text-anchor="middle">conditions, acceptances</text></g>
                        <path class="line" style="--i:7" d="M220 115 H440"/><path class="line" style="--i:8" d="M610 100 H780"/><path class="line" style="--i:9" d="M610 130 C690 130 710 188 780 188"/><path class="line" style="--i:10" d="M525 160 V300"/><path class="line" style="--i:11" d="M220 150 C300 260 390 325 430 340"/><path class="line" style="--i:12" d="M220 355 H430"/><path class="line" style="--i:13" d="M610 355 H765"/>
                    </svg>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Classes" data-note="Domínio">
            <div class="frame">
                <p class="eyebrow">14. Diagrama de classes</p>
                <h2 data-type="Classes de domínio e responsabilidades.">Classes de domínio e responsabilidades.</h2>
                <div class="diagram glass">
                    <svg viewBox="0 0 1040 520" role="img" aria-label="Diagrama de classes">
                        <defs><marker id="arrow4" markerWidth="10" markerHeight="10" refX="8" refY="3" orient="auto"><path d="M0,0 L0,6 L9,3 z" fill="#9f6a47"/></marker></defs>
                        <g class="node" data-tip="Utilizador autenticado e proprietário de recursos." style="--i:0"><rect class="accent" x="50" y="70" width="190" height="130" rx="14"/><text x="145" y="100" text-anchor="middle">User</text><text class="small" x="72" y="132">+ resources()</text><text class="small" x="72" y="154">+ reservations()</text><text class="small" x="72" y="176">+ favoriteResources()</text></g>
                        <g class="node" data-tip="Agregado principal do catálogo." style="--i:1"><rect class="accent" x="425" y="55" width="210" height="150" rx="14"/><text x="530" y="86" text-anchor="middle">Resource</text><text class="small" x="448" y="116">+ owner()</text><text class="small" x="448" y="138">+ physicalResource()</text><text class="small" x="448" y="160">+ digitalResource()</text><text class="small" x="448" y="182">+ reservations()</text></g>
                        <g class="node" data-tip="Subtipo para recursos físicos." style="--i:2"><rect class="box" x="765" y="55" width="220" height="110" rx="14"/><text x="875" y="86" text-anchor="middle">PhysicalResource</text><text class="small" x="790" y="118">location, condition</text><text class="small" x="790" y="140">max_loan_days</text></g>
                        <g class="node" data-tip="Subtipo para ficheiros digitais." style="--i:3"><rect class="box" x="765" y="205" width="220" height="110" rx="14"/><text x="875" y="236" text-anchor="middle">DigitalResource</text><text class="small" x="790" y="268">file_path</text><text class="small" x="790" y="290">access_type</text></g>
                        <g class="node" data-tip="Ciclo de empréstimo com estado e extensão." style="--i:4"><rect class="box" x="425" y="325" width="210" height="145" rx="14"/><text x="530" y="356" text-anchor="middle">Reservation</text><text class="small" x="448" y="388">status, start_date</text><text class="small" x="448" y="410">end_date, returned_at</text><text class="small" x="448" y="432">canExtend()</text></g>
                        <g class="node" data-tip="Termos e condições por recurso." style="--i:5"><rect class="box" x="70" y="325" width="210" height="110" rx="14"/><text x="175" y="356" text-anchor="middle">TermAndCondition</text><text class="small" x="95" y="388">scope, title, version</text><text class="small" x="95" y="410">activeByScope()</text></g>
                        <path class="line" style="--i:6" d="M240 125 H425"/><path class="line" style="--i:7" d="M635 115 H765"/><path class="line" style="--i:8" d="M635 145 C700 145 700 260 765 260"/><path class="line" style="--i:9" d="M530 205 V325"/><path class="line" style="--i:10" d="M240 180 C310 260 395 330 425 360"/><path class="line" style="--i:11" d="M280 380 H425"/>
                    </svg>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Arquitectura" data-note="Laravel MVC">
            <div class="frame">
                <p class="eyebrow">15. Arquitectura técnica</p>
                <h2 data-type="Laravel MVC com responsabilidades separadas.">Laravel MVC com responsabilidades separadas.</h2>
                <div class="grid-3">
                    <article class="card" style="--i:0"><h3>Controllers</h3><p>Recebem pedidos HTTP, coordenam actions e devolvem views, redirects ou JSON.</p></article>
                    <article class="card" style="--i:1"><h3>Models</h3><p>Representam entidades, relacionamentos e regras como `canExtend()` e scopes.</p></article>
                    <article class="card" style="--i:2"><h3>Actions</h3><p>Isolam operações de negócio como criação, devolução e sincronização.</p></article>
                    <article class="card" style="--i:3"><h3>Requests</h3><p>Validam dados de entrada e normalizam payloads antes da persistência.</p></article>
                    <article class="card" style="--i:4"><h3>Blade Views</h3><p>Renderizam interface, componentes, cards, navbar, sidebar e toasts.</p></article>
                    <article class="card" style="--i:5"><h3>Migrations e Tests</h3><p>Definem a base de dados e protegem regras críticas contra regressões.</p></article>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Interface" data-note="UX do sistema">
            <div class="frame split">
                <div>
                    <p class="eyebrow">16. Interface e experiência</p>
                    <h2 data-type="Uma interface orientada à leitura e à acção.">Uma interface orientada à leitura e à acção.</h2>
                    <p class="subtitle">A UI valoriza pesquisa, contexto, filtros, cards com métricas, feedback visual e estados compreensíveis. A experiência funciona em desktop e mobile.</p>
                </div>
                <div class="visual glass">
                    <img src="{{ asset('img/png/books.png') }}" alt="Livros usados como recurso visual">
                    <div class="floating-card"><strong>Biblioteca explorável</strong><p class="text">Cards, filtros, favoritos, notificações e navegação lateral reduzem esforço de utilização.</p></div>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Segurança" data-note="Validação e testes">
            <div class="frame">
                <p class="eyebrow">17. Segurança, validação e qualidade</p>
                <h2 data-type="O sistema protege dados e fluxos sensíveis.">O sistema protege dados e fluxos sensíveis.</h2>
                <div class="grid-3">
                    <article class="card" style="--i:0"><h3>Autenticação</h3><p>Rotas privadas usam middleware `auth`; login e registo usam sessão segura.</p></article>
                    <article class="card" style="--i:1"><h3>Autorização</h3><p>Somente o dono gere recursos e decide extensão. O utilizador não empresta o próprio recurso.</p></article>
                    <article class="card" style="--i:2"><h3>Form Requests</h3><p>Validações tratam uploads, datas, tipos, campos obrigatórios e disponibilidade.</p></article>
                    <div class="metric" style="--i:3"><strong data-count="16">0</strong><span>Testes automatizados</span></div>
                    <div class="metric" style="--i:4"><strong data-count="118">0</strong><span>Asserções executadas</span></div>
                    <article class="card" style="--i:5"><h3>Regressões</h3><p>Reservas, devolução, reempréstimo e logout têm cobertura automatizada.</p></article>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Roadmap" data-note="Evolução futura">
            <div class="frame">
                <p class="eyebrow">18. Roadmap</p>
                <h2 data-type="A base está pronta para novas camadas de valor.">A base está pronta para novas camadas de valor.</h2>
                <div class="roadmap">
                    <article class="card" style="--i:0"><h3>Notificações persistentes</h3><p>Guardar leituras, estado e histórico de alertas.</p></article>
                    <article class="card" style="--i:1"><h3>Dashboard</h3><p>Métricas de circulação, atrasos e popularidade.</p></article>
                    <article class="card" style="--i:2"><h3>API REST</h3><p>Integração com portais académicos e aplicações móveis.</p></article>
                    <article class="card" style="--i:3"><h3>Analytics</h3><p>Recomendações e relatórios de uso.</p></article>
                </div>
            </div>
        </section>

        <section class="slide" data-title="Conclusão" data-note="Call to action">
            <div class="frame split">
                <div>
                    <p class="eyebrow">19. Conclusão</p>
                    <h2 data-type="A VUTIVI transforma recursos em fluxo organizado de conhecimento.">A VUTIVI transforma recursos em fluxo organizado de conhecimento.</h2>
                    <p class="subtitle">A solução combina biblioteca física e digital, empréstimos, notificações, termos, favoritos, pesquisa e uma arquitectura Laravel extensível.</p>
                    <div class="credits"><span class="badge warn" style="--i:0">Laravel</span><span class="badge warn" style="--i:1">Blade</span><span class="badge warn" style="--i:2">UX</span><span class="badge warn" style="--i:3">Testes</span></div>
                </div>
                <div class="card" style="min-height:360px;display:grid;place-items:center;text-align:center">
                    <div>
                        <div class="qr" aria-label="QR code placeholder">
                            @for ($i = 0; $i < 25; $i++)
                                <span style="opacity: {{ in_array($i, [0,1,3,4,5,7,9,10,12,14,15,17,19,20,21,23,24]) ? 1 : .15 }}"></span>
                            @endfor
                        </div>
                        <h3>Link do projecto</h3>
                        <p class="text">http://seu-ip:8000/apresentacao</p>
                        <button class="primary-btn" data-confetti style="margin-top:1rem">Celebrar entrega</button>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <nav class="dots" id="dots" aria-label="Navegação por slides"></nav>
    <div class="kbd">← → navegar · F fullscreen · G ir para slide</div>

    <div class="modal" id="goto-modal" aria-hidden="true">
        <div class="modal-card">
            <div style="display:flex;justify-content:space-between;gap:1rem;align-items:center">
                <div><p class="eyebrow">Navegação rápida</p><h3 style="margin:0">Ir para slide</h3></div>
                <button class="pill-btn" id="close-goto">Fechar</button>
            </div>
            <div class="goto-grid" id="goto-grid"></div>
        </div>
    </div>

    <script>
        /* ==========================================================
           Core deck navigation and state
           ========================================================== */
        const slides = [...document.querySelectorAll('.slide')];
        const dots = document.getElementById('dots');
        const progress = document.getElementById('progress');
        const current = document.getElementById('current');
        const total = document.getElementById('total');
        const deck = document.getElementById('deck');
        let index = 0;
        let busy = false;
        let autoplayTimer = null;
        total.textContent = String(slides.length).padStart(2, '0');

        slides.forEach((slide, i) => {
            const wrap = document.createElement('span');
            wrap.className = 'dot-wrap';
            const dot = document.createElement('button');
            dot.className = 'dot' + (i === 0 ? ' is-active' : '');
            dot.type = 'button';
            dot.setAttribute('aria-label', `Ir para o slide ${i + 1}`);
            dot.addEventListener('click', () => goTo(i));
            const preview = document.createElement('span');
            preview.className = 'dot-preview';
            preview.innerHTML = `<strong>${String(i + 1).padStart(2, '0')} · ${slide.dataset.title}</strong><span>${slide.dataset.note || ''}</span>`;
            wrap.append(dot, preview);
            dots.appendChild(wrap);
        });

        function render(nextIndex, direction = 1) {
            if (busy || nextIndex === index || nextIndex < 0 || nextIndex >= slides.length) return;
            busy = true;
            const old = slides[index];
            const nextSlide = slides[nextIndex];
            old.classList.add('glitching');
            nextSlide.classList.add('glitching');

            const update = () => {
                old.classList.remove('is-active');
                old.classList.add(direction > 0 ? 'is-leaving-left' : 'is-leaving-right');
                index = nextIndex;
                nextSlide.classList.add('is-active');
                setTimeout(() => slides.forEach(s => s.classList.remove('is-leaving-left', 'is-leaving-right', 'glitching')), 720);
                syncUi();
                activateSlide(nextSlide);
            };

            if (document.startViewTransition) document.startViewTransition(update).finished.finally(() => busy = false);
            else { update(); setTimeout(() => busy = false, 760); }
        }

        function syncUi() {
            current.classList.remove('roll');
            void current.offsetWidth;
            current.textContent = String(index + 1).padStart(2, '0');
            current.classList.add('roll');
            progress.style.width = `${((index + 1) / slides.length) * 100}%`;
            [...dots.querySelectorAll('.dot')].forEach((dot, i) => dot.classList.toggle('is-active', i === index));
            if (index === slides.length - 1) fireConfetti();
        }

        function goTo(i) { render(i, i > index ? 1 : -1); }
        function next() { render(Math.min(index + 1, slides.length - 1), 1); }
        function prev() { render(Math.max(index - 1, 0), -1); }

        document.getElementById('next').addEventListener('click', next);
        document.getElementById('prev').addEventListener('click', prev);
        document.querySelector('[data-start]').addEventListener('click', next);
        document.getElementById('theme').addEventListener('click', () => document.body.classList.toggle('dark'));

        document.getElementById('autoplay').addEventListener('click', (event) => {
            if (autoplayTimer) {
                clearInterval(autoplayTimer);
                autoplayTimer = null;
                event.currentTarget.textContent = '▶';
                return;
            }
            event.currentTarget.textContent = 'Ⅱ';
            autoplayTimer = setInterval(() => index === slides.length - 1 ? goTo(0) : next(), 6500);
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'ArrowRight' || event.key === 'PageDown') next();
            if (event.key === 'ArrowLeft' || event.key === 'PageUp') prev();
            if (event.key === 'Home') goTo(0);
            if (event.key === 'End') goTo(slides.length - 1);
            if (event.key.toLowerCase() === 'f') document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen?.();
            if (event.key.toLowerCase() === 'g') openGoto();
            if (event.key === 'Escape') closeGoto();
        });

        let touchStart = 0;
        document.addEventListener('touchstart', e => touchStart = e.changedTouches[0].clientX, { passive: true });
        document.addEventListener('touchend', e => {
            const delta = e.changedTouches[0].clientX - touchStart;
            if (Math.abs(delta) > 46) delta < 0 ? next() : prev();
        }, { passive: true });

        /* ==========================================================
           Typewriter, parallax and counters
           ========================================================== */
        function activateSlide(slide) {
            slide.querySelectorAll('[data-type]').forEach(el => typeText(el, el.dataset.type));
            slide.querySelectorAll('[data-count]').forEach(el => animateCount(el, Number(el.dataset.count)));
        }

        function typeText(el, text) {
            if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            el.classList.add('typewriter');
            el.textContent = '';
            let i = 0;
            const tick = () => {
                el.textContent = text.slice(0, i++);
                if (i <= text.length) setTimeout(tick, Math.max(14, 520 / text.length));
                else setTimeout(() => el.classList.remove('typewriter'), 450);
            };
            tick();
        }

        deck.addEventListener('mousemove', event => {
            const active = slides[index];
            const rect = active.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - .5;
            const y = (event.clientY - rect.top) / rect.height - .5;
            active.querySelectorAll('[data-depth]').forEach(el => {
                const d = Number(el.dataset.depth);
                el.style.transform = `translate3d(${x * d}px, ${y * d}px, 0)`;
            });
        });

        function animateCount(el, target) {
            const start = performance.now();
            const duration = 900;
            function frame(now) {
                const t = Math.min(1, (now - start) / duration);
                el.textContent = String(Math.round(target * (1 - Math.pow(1 - t, 3))));
                if (t < 1) requestAnimationFrame(frame);
            }
            requestAnimationFrame(frame);
        }

        /* ==========================================================
           Cursor and particle background
           ========================================================== */
        const cursor = document.querySelector('.cursor');
        const trail = document.querySelector('.cursor-trail');
        let mouse = { x: innerWidth / 2, y: innerHeight / 2 };
        let trailPos = { ...mouse };
        document.addEventListener('mousemove', e => { mouse = { x: e.clientX, y: e.clientY }; cursor.style.transform = `translate(${mouse.x}px, ${mouse.y}px)`; });
        function animateCursor() {
            trailPos.x += (mouse.x - trailPos.x) * .14;
            trailPos.y += (mouse.y - trailPos.y) * .14;
            trail.style.transform = `translate(${trailPos.x}px, ${trailPos.y}px)`;
            requestAnimationFrame(animateCursor);
        }
        animateCursor();

        const canvas = document.getElementById('particle-canvas');
        const ctx = canvas.getContext('2d');
        let particles = [];
        function resizeCanvas() {
            canvas.width = innerWidth * devicePixelRatio;
            canvas.height = innerHeight * devicePixelRatio;
            ctx.setTransform(devicePixelRatio, 0, 0, devicePixelRatio, 0, 0);
            particles = Array.from({ length: Math.min(90, Math.floor(innerWidth / 18)) }, () => ({
                x: Math.random() * innerWidth, y: Math.random() * innerHeight,
                vx: (Math.random() - .5) * .28, vy: (Math.random() - .5) * .28
            }));
        }
        addEventListener('resize', resizeCanvas);
        resizeCanvas();
        function drawParticles() {
            ctx.clearRect(0, 0, innerWidth, innerHeight);
            ctx.fillStyle = 'rgba(254,104,7,.55)';
            ctx.strokeStyle = 'rgba(254,104,7,.12)';
            particles.forEach((p, i) => {
                p.x += p.vx; p.y += p.vy;
                if (p.x < 0 || p.x > innerWidth) p.vx *= -1;
                if (p.y < 0 || p.y > innerHeight) p.vy *= -1;
                ctx.beginPath(); ctx.arc(p.x, p.y, 2, 0, Math.PI * 2); ctx.fill();
                for (let j = i + 1; j < particles.length; j++) {
                    const q = particles[j], dist = Math.hypot(p.x - q.x, p.y - q.y);
                    if (dist < 120) { ctx.globalAlpha = 1 - dist / 120; ctx.beginPath(); ctx.moveTo(p.x, p.y); ctx.lineTo(q.x, q.y); ctx.stroke(); ctx.globalAlpha = 1; }
                }
            });
            requestAnimationFrame(drawParticles);
        }
        drawParticles();

        /* ==========================================================
           Tooltips and interactive diagrams
           ========================================================== */
        const tooltip = document.getElementById('tooltip');
        document.addEventListener('mouseover', e => {
            const node = e.target.closest('[data-tip]');
            if (!node) return;
            tooltip.textContent = node.dataset.tip;
            tooltip.classList.add('is-visible');
        });
        document.addEventListener('mousemove', e => {
            if (tooltip.classList.contains('is-visible')) {
                tooltip.style.left = `${Math.min(innerWidth - 280, e.clientX + 18)}px`;
                tooltip.style.top = `${e.clientY + 18}px`;
            }
        });
        document.addEventListener('mouseout', e => {
            if (e.target.closest('[data-tip]')) tooltip.classList.remove('is-visible');
        });
        document.querySelectorAll('[data-flow-step]').forEach(btn => btn.addEventListener('click', () => setFlowStep(Number(btn.dataset.flowStep))));
        function setFlowStep(step) {
            document.querySelectorAll('[data-flow-node]').forEach(node => node.classList.toggle('is-current', Number(node.dataset.flowNode) === step));
        }

        /* ==========================================================
           Demo A: search simulation
           ========================================================== */
        const books = [
            { title: 'Laravel para Bibliotecas', author: 'A. Mucavel', type: 'Físico', ok: true },
            { title: 'LaraDocs Académico', author: 'M. Bila', type: 'Digital', ok: true },
            { title: 'Lógica de Sistemas Web', author: 'S. Cossa', type: 'Físico', ok: false },
            { title: 'Laboratório de Dados', author: 'R. Matola', type: 'Digital', ok: true }
        ];
        const search = document.getElementById('demo-search');
        const results = document.getElementById('search-results');
        function renderSearch() {
            const q = search.value.trim().toLowerCase();
            const list = books.filter(b => !q || b.title.toLowerCase().includes(q));
            results.innerHTML = list.map((b, i) => `<article class="book-result" style="--i:${i}"><h3>${b.title}</h3><p>${b.author} · ${b.type}</p><span class="badge ${b.ok ? 'good' : 'bad'}">${b.ok ? 'Disponível' : 'Emprestado'}</span></article>`).join('');
        }
        search.addEventListener('input', renderSearch);
        renderSearch();

        /* ==========================================================
           Demo B: loan lifecycle simulation
           ========================================================== */
        const loanState = document.getElementById('loan-state');
        const loanCopy = document.getElementById('loan-copy');
        const loanBadge = document.getElementById('resource-badge');
        const loanCountdown = document.getElementById('loan-countdown');
        const loanCheck = document.getElementById('loan-check');
        let loanDays = 7;
        document.querySelectorAll('[data-loan-action]').forEach(btn => btn.addEventListener('click', () => {
            loanCheck.classList.remove('is-visible');
            const action = btn.dataset.loanAction;
            if (action === 'request') { loanState.textContent = 'Pendente'; loanCopy.textContent = 'Pedido criado e enviado ao dono.'; loanBadge.className = 'badge warn'; loanBadge.textContent = 'Reservado'; }
            if (action === 'approve') { loanState.textContent = 'Em uso'; loanCopy.textContent = 'Empréstimo aprovado.'; loanDays = 7; loanCountdown.textContent = `Prazo: ${loanDays} dias`; loanBadge.className = 'badge bad'; loanBadge.textContent = 'Emprestado'; }
            if (action === 'extend') { loanState.textContent = 'Extensão pendente'; loanCopy.textContent = 'Dono recebeu pedido de extensão.'; }
            if (action === 'approveExtend') { loanDays += 7; loanState.textContent = 'Estendido'; loanCopy.textContent = 'Novo prazo aplicado.'; loanCountdown.textContent = `Prazo: ${loanDays} dias`; }
            if (action === 'return') { loanState.textContent = 'Livre'; loanCopy.textContent = 'Recurso devolvido e disponível.'; loanCountdown.textContent = 'Prazo: --'; loanBadge.className = 'badge good'; loanBadge.textContent = 'Disponível'; loanCheck.classList.add('is-visible'); fireConfetti(70); }
        }));

        /* ==========================================================
           Demo C: notifications simulation
           ========================================================== */
        const notifList = document.getElementById('notif-list');
        const notifCount = document.getElementById('notif-count');
        const messages = {
            approved: ['✓', 'Empréstimo aprovado', 'O recurso está pronto para utilização.'],
            due: ['⏱', 'Prazo a vencer em 2 dias', 'Prepare a devolução ou solicite extensão.'],
            denied: ['×', 'Extensão recusada', 'O prazo original permanece activo.']
        };
        document.querySelectorAll('[data-notif]').forEach(btn => btn.addEventListener('click', () => {
            const [icon, title, body] = messages[btn.dataset.notif];
            notifList.insertAdjacentHTML('afterbegin', `<div class="notif-item"><span class="icon">${icon}</span><div><strong>${title}</strong><p class="text">${body}</p></div></div>`);
            notifCount.textContent = String(Number(notifCount.textContent) + 1);
            notifCount.classList.remove('bump'); void notifCount.offsetWidth; notifCount.classList.add('bump');
            notifList.classList.add('is-open');
        }));
        document.getElementById('bell').addEventListener('click', () => notifList.classList.toggle('is-open'));

        /* ==========================================================
           Goto modal, mobile observer and effects
           ========================================================== */
        const modal = document.getElementById('goto-modal');
        const grid = document.getElementById('goto-grid');
        slides.forEach((slide, i) => {
            const btn = document.createElement('button');
            btn.className = 'goto-item';
            btn.innerHTML = `<strong>${String(i + 1).padStart(2, '0')}</strong><br>${slide.dataset.title}<p class="text">${slide.dataset.note || ''}</p>`;
            btn.addEventListener('click', () => { closeGoto(); goTo(i); });
            grid.appendChild(btn);
        });
        function openGoto() { modal.classList.add('is-open'); modal.setAttribute('aria-hidden', 'false'); }
        function closeGoto() { modal.classList.remove('is-open'); modal.setAttribute('aria-hidden', 'true'); }
        document.getElementById('close-goto').addEventListener('click', closeGoto);
        modal.addEventListener('click', e => { if (e.target === modal) closeGoto(); });

        if (matchMedia('(max-width: 900px)').matches) {
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) { index = slides.indexOf(entry.target); syncUi(); activateSlide(entry.target); }
                });
            }, { threshold: .55 });
            slides.forEach(slide => observer.observe(slide));
        }

        function fireConfetti(amount = 120) {
            const colors = ['#FE6807', '#ffb15f', '#fff1e6', '#2f7d52'];
            for (let i = 0; i < amount; i++) {
                const piece = document.createElement('span');
                piece.style.cssText = `position:fixed;left:${Math.random()*100}vw;top:-20px;width:8px;height:14px;background:${colors[i%colors.length]};z-index:100;pointer-events:none;transform:rotate(${Math.random()*180}deg);border-radius:2px;`;
                document.body.appendChild(piece);
                piece.animate([{ transform: `translateY(0) rotate(0deg)`, opacity: 1 }, { transform: `translateY(${innerHeight + 80}px) rotate(${360 + Math.random()*360}deg)`, opacity: 0 }], { duration: 1600 + Math.random()*1200, easing: 'cubic-bezier(.22,1,.36,1)' }).finished.then(() => piece.remove());
            }
        }
        document.querySelector('[data-confetti]').addEventListener('click', () => fireConfetti());

        document.addEventListener('click', e => {
            const btn = e.target.closest('.primary-btn');
            if (!btn) return;
            const rect = btn.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.className = 'ripple';
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${e.clientX - rect.left - size / 2}px`;
            ripple.style.top = `${e.clientY - rect.top - size / 2}px`;
            btn.appendChild(ripple);
            setTimeout(() => ripple.remove(), 700);
        });

        syncUi();
        activateSlide(slides[0]);
    </script>
</body>
</html>
