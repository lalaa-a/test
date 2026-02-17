<?php
  // Minimal helpers (optional if your framework gives you these):
  $base = '/test/driver';
  // Determine active tab from the current path: /driver/{tab}
  $parts = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
  $activeTab = $parts[2] ?? 'dashboard';
  $pageTitle = ucfirst($activeTab);
  function currentAttr($tab, $active) { return $tab === $active ? 'aria-current="page"' : ''; }
?>
<!-- Sidebar + Content component (HTML + component-scoped CSS) -->
<style>
  /* Component scope */
  .st-sidebar-tabs {
    --st-active: #006A71; /* highlight color */
    --st-text: #000000;   /* normal text color */
    --st-muted: #6b7280;
    --st-hover: #f3f7f8;

  font-family: 'Geologica', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
    display: grid;
    grid-template-columns: 230px 1fr;
    gap: 36px;
    align-items: start;
    width: 100%;
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
    border-left: 3px solid transparent;
    text-decoration: none;
    transition: background .15s ease, color .15s ease, border-color .15s ease;
    outline: none;
  }
  .st-sidebar-tabs .st-item:hover,
  .st-sidebar-tabs .st-item:focus-visible { background: var(--st-hover); }

  /* Active link via aria-current */
  .st-sidebar-tabs .st-item[aria-current="page"] {
    color: var(--st-active);
    border-left-color: var(--st-active);
    background: rgba(0,106,113,0.12);
    background: color-mix(in srgb, var(--st-active) 16%, white);
  }

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
  .st-sidebar-tabs .st-title {
    margin: 0;
    font-weight: 700;
    color: var(--st-active);
    font-size: 20px;
    letter-spacing: .2px;
  }

  /* Responsive */
  @media (max-width: 640px) {
    .st-sidebar-tabs { grid-template-columns: 1fr; gap: 16px; }
    .st-sidebar-tabs .st-nav { flex-direction: row; flex-wrap: wrap; }
    .st-sidebar-tabs .st-item { border-left-width: 0; border-bottom: 3px solid transparent; }
    .st-sidebar-tabs .st-item[aria-current="page"] { border-bottom-color: var(--st-active); }
  }
</style>

<div class="st-sidebar-tabs">
  <!-- Sidebar: real links to MVC routes -->
  <aside class="st-nav" aria-label="Driver navigation">
    <a class="st-item" href="<?= $base ?>/dashboard" <?= currentAttr('dashboard', $activeTab) ?>>
      <svg viewBox="0 0 24 24" aria-hidden="true">
        <path d="M3 3v18h18M7 16l5-5 3 3 4-4"></path>
      </svg>
      <span>Dashboard</span>
    </a>

    <a class="st-item" href="<?= $base ?>/tours" <?= currentAttr('tours', $activeTab) ?>>
      <svg viewBox="0 0 24 24" aria-hidden="true">
        <path d="M12 3a9 9 0 0 0-9 9h18a9 9 0 0 0-9-9z"></path>
        <path d="M12 12v6a2 2 0 0 0 2 2"></path>
      </svg>
      <span>Tours</span>
    </a>

    <a class="st-item" href="<?= $base ?>/requests" <?= currentAttr('requests', $activeTab) ?>>
      <svg viewBox="0 0 24 24" aria-hidden="true">
        <rect x="3" y="4" width="18" height="14" rx="2" ry="2"></rect>
        <path d="M21 13h-4a5 5 0 0 1-10 0H3"></path>
      </svg>
      <span>Requests</span>
    </a>

    <a class="st-item" href="<?= $base ?>/earnings" <?= currentAttr('earnings', $activeTab) ?>>
      <svg viewBox="0 0 24 24" aria-hidden="true">
        <path d="M12 3v3M12 21v-3"></path>
        <path d="M8.5 16.5c.8.9 2.1 1.5 3.5 1.5 2.5 0 4-1.3 4-3s-1.5-3-4-3-4-1.3-4-3 1.5-3 4-3c1.5 0 2.8.7 3.5 1.5"></path>
      </svg>
      <span>Earnings</span>
    </a>

    <a class="st-item" href="<?= $base ?>/vehicles" <?= currentAttr('vehicles', $activeTab) ?>>
      <svg viewBox="0 0 24 24" aria-hidden="true">
        <path d="M3 15v-1a2 2 0 0 1 2-2h1l2-3h8l2 3h1a2 2 0 0 1 2 2v1"></path>
        <path d="M5 15h14"></path>
        <path d="M9 9.5h6"></path>
        <circle cx="7.5" cy="17.5" r="1.5"></circle>
        <circle cx="16.5" cy="17.5" r="1.5"></circle>
      </svg>
      <span>Vehicles</span>
    </a>

    <a class="st-item" href="<?= $base ?>/profile" <?= currentAttr('profile', $activeTab) ?>>
      <svg viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="12" cy="8" r="4"></circle>
        <path d="M4 20c2-3.5 5-5.5 8-5.5s6 2 8 5.5"></path>
      </svg>
      <span>Profile</span>
    </a>
  </aside>





  <!-- Content: render the page for the current route -->
  <main class="st-content">
    <h1 class="st-title"><?= htmlspecialchars($pageTitle) ?></h1>
    <!-- Your MVC view for this route outputs here -->

    
    <!-- e.g., in your controller action, include the specific view file -->
  </main>




  
</div>