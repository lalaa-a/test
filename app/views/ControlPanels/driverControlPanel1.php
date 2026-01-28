<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>

        body {
            padding: 0 150px; /* adds 40px left and right margin */
            border: 0;
        font-family: 'Geologica', Arial, sans-serif;
            background: #fff;
            color: #111;
        }
        
    /* Component scope */
    .st-sidebar-tabs {
        --st-active: #006A71; /* highlight color */
        --st-text: #000000;   /* normal text color */
        --st-muted: #6b7280;  /* subtle text if needed */
        --st-hover: #f3f7f8;  /* hover bg */
        font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
        display: grid;
        grid-template-columns: 230px 1fr;
        gap: 36px;
        align-items: start;
        width: 100%;
    }

    /* Hide radios but keep them in DOM for CSS control */
    .st-sidebar-tabs > input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    /* Sidebar */
    .st-sidebar-tabs .st-nav {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 16px 0;
    }
    .st-sidebar-tabs .st-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 10px;
        color: var(--st-text);
        cursor: pointer;
        user-select: none;
        border-left: 3px solid transparent;
        transition: background .15s ease, color .15s ease, border-color .15s ease;
    }
    .st-sidebar-tabs .st-item:hover { background: var(--st-hover); }
    .st-sidebar-tabs .st-item svg {
        width: 20px; height: 20px;
        stroke: currentColor;
        stroke-width: 1.6;
        fill: none;
        flex: 0 0 20px;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* Content area */
    .st-sidebar-tabs .st-content { padding-top: 22px; }
    .st-sidebar-tabs .st-panel { display: none; }
    .st-sidebar-tabs .st-title {
        margin: 0;
        font-weight: 700;
        color: var(--st-active);
        font-size: 20px;
        letter-spacing: .2px;
    }

    /* Active states (tab + content) */
    #st-dashboard:checked ~ .st-nav label[for="st-dashboard"],
    #st-tours:checked     ~ .st-nav label[for="st-tours"],
    #st-requests:checked  ~ .st-nav label[for="st-requests"],
    #st-earnings:checked  ~ .st-nav label[for="st-earnings"],
    #st-vehicles:checked  ~ .st-nav label[for="st-vehicles"],
    #st-profile:checked   ~ .st-nav label[for="st-profile"] {
        color: var(--st-active);
        border-left-color: var(--st-active);
        background: color-mix(in srgb, var(--st-active) 16%, white);
    }

    #st-dashboard:checked ~ .st-content #st-panel-dashboard { display: block; }
    #st-tours:checked     ~ .st-content #st-panel-tours     { display: block; }
    #st-requests:checked  ~ .st-content #st-panel-requests  { display: block; }
    #st-earnings:checked  ~ .st-content #st-panel-earnings  { display: block; }
    #st-vehicles:checked  ~ .st-content #st-panel-vehicles  { display: block; }
    #st-profile:checked   ~ .st-content #st-panel-profile   { display: block; }

    /* Responsive: stack on small screens */
    @media (max-width: 640px) {
        .st-sidebar-tabs { grid-template-columns: 1fr; gap: 16px; }
        .st-sidebar-tabs .st-nav { flex-direction: row; flex-wrap: wrap; }
        .st-sidebar-tabs .st-item { border-left-width: 0; border-bottom: 3px solid transparent; }
        #st-dashboard:checked ~ .st-nav label[for="st-dashboard"],
        #st-tours:checked     ~ .st-nav label[for="st-tours"],
        #st-requests:checked  ~ .st-nav label[for="st-requests"],
        #st-earnings:checked  ~ .st-nav label[for="st-earnings"],
        #st-vehicles:checked  ~ .st-nav label[for="st-vehicles"],
        #st-profile:checked   ~ .st-nav label[for="st-profile"] {
        border-bottom-color: var(--st-active);
        }
    }
    </style>

    <?php
        include APP_ROOT.'/libraries/Functions.php';
        addAssets('inc','navigation');
        addAssets('inc','footer');
        printAssets();
    ?>

</head>
<body>

    <!--navigation bar-->
    <?php renderComponent('inc','navigation',[]); ?>

    <div class="st-sidebar-tabs">
    <!-- Radios control which tab is active (CSS only) -->
    <input type="radio" name="st-tab" id="st-dashboard" checked>
    <input type="radio" name="st-tab" id="st-tours">
    <input type="radio" name="st-tab" id="st-requests">
    <input type="radio" name="st-tab" id="st-earnings">
    <input type="radio" name="st-tab" id="st-vehicles">
    <input type="radio" name="st-tab" id="st-profile">

    <!-- Sidebar -->
    <aside class="st-nav">
        <label class="st-item" for="st-dashboard" title="Dashboard">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M3 3v18h18M7 16l5-5 3 3 4-4"></path>
        </svg>
        <span>Dashboard</span>
        </label>

        <label class="st-item" for="st-tours" title="Tours">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 3a9 9 0 0 0-9 9h18a9 9 0 0 0-9-9z"></path>
            <path d="M12 12v6a2 2 0 0 0 2 2"></path>
        </svg>
        <span>Tours</span>
        </label>

        <label class="st-item" for="st-requests" title="Requests">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <rect x="3" y="4" width="18" height="14" rx="2" ry="2"></rect>
            <path d="M21 13h-4a5 5 0 0 1-10 0H3"></path>
        </svg>
        <span>Requests</span>
        </label>

        <label class="st-item" for="st-earnings" title="Earnings">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 3v3M12 21v-3"></path>
            <path d="M8.5 16.5c.8.9 2.1 1.5 3.5 1.5 2.5 0 4-1.3 4-3s-1.5-3-4-3-4-1.3-4-3 1.5-3 4-3c1.5 0 2.8.7 3.5 1.5"></path>
        </svg>
        <span>Earnings</span>
        </label>

        <!-- Vehicles (updated with a car icon) -->
        <label class="st-item" for="st-vehicles" title="Vehicles">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <!-- car body -->
            <path d="M3 15v-1a2 2 0 0 1 2-2h1l2-3h8l2 3h1a2 2 0 0 1 2 2v1"></path>
            <!-- ground line -->
            <path d="M5 15h14"></path>
            <!-- windows/roof hint -->
            <path d="M9 9.5h6"></path>
            <!-- wheels -->
            <circle cx="7.5" cy="17.5" r="1.5"></circle>
            <circle cx="16.5" cy="17.5" r="1.5"></circle>
        </svg>
        <span>Vehicles</span>
        </label>

        <label class="st-item" for="st-profile" title="Profile">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="12" cy="8" r="4"></circle>
            <path d="M4 20c2-3.5 5-5.5 8-5.5s6 2 8 5.5"></path>
        </svg>
        <span>Profile</span>
        </label>
    </aside>

    <!-- Content -->
    <main class="st-content">
        <section class="st-panel" id="st-panel-dashboard">
        <h1 class="st-title">Dashboard</h1>
        <!-- Your dashboard content -->
        </section>

        <section class="st-panel" id="st-panel-tours">
        <h1 class="st-title">Tours</h1>
        <!-- Your tours content -->
        </section>

        <section class="st-panel" id="st-panel-requests">
        <h1 class="st-title">Requests</h1>
        <!-- Your requests content -->
        </section>

        <section class="st-panel" id="st-panel-earnings">
        <h1 class="st-title">Earnings</h1>
        <!-- Your earnings content -->
        </section>

        <section class="st-panel" id="st-panel-vehicles">
        <h1 class="st-title">Vehicles</h1>
        <!-- Your vehicles content -->
        </section>

        <section class="st-panel" id="st-panel-profile">
        <h1 class="st-title">Profile</h1>
        <!-- Your profile content -->
        </section>
    </main>
    </div>

    <!--navigation bar-->
    <?php renderComponent('inc','footer',[]); ?> 

</body>
</html>