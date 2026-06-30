<?php
declare(strict_types=1);

http_response_code(503);
header('Content-Type: text/html; charset=UTF-8');

function guildcms_setup_h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$installer_available = is_file(__DIR__ . '/../install/index.php');
$installer_url = 'install/';
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Guild CMS Setup Required</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; margin: 0; background: radial-gradient(circle at top, #16324f, #0f172a 45rem); color: #f8fafc; font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .setup-shell { max-width: 1080px; margin: 0 auto; padding: 3rem 1rem; }
        .setup-card { background: rgba(15, 23, 42, .88); border: 1px solid rgba(148, 163, 184, .28); border-radius: 1.5rem; box-shadow: 0 24px 80px rgba(0,0,0,.35); }
        .setup-muted { color: #cbd5e1; }
        .setup-kicker { color: #38bdf8; text-transform: uppercase; letter-spacing: .08em; font-weight: 700; font-size: .8rem; }
        .setup-list li { margin-bottom: .45rem; }
        .setup-code { background: rgba(2, 6, 23, .65); border: 1px solid rgba(148, 163, 184, .25); border-radius: .75rem; padding: .75rem 1rem; color: #bae6fd; }
        .btn-guild { background: #38bdf8; color: #082f49; border: 0; font-weight: 800; }
        .btn-guild:hover { background: #7dd3fc; color: #082f49; }
    </style>
</head>
<body>
<main class="setup-shell">
    <section class="setup-card p-4 p-lg-5">
        <div class="setup-kicker mb-2">Guild CMS Installation</div>
        <h1 class="display-5 fw-bold mb-3">Guild CMS is not installed yet.</h1>
        <p class="lead setup-muted">That is normal for a new Guild CMS download. This site is missing its completed configuration, so Guild CMS is pausing here instead of showing a technical error.</p>

        <div class="row g-4 mt-3">
            <div class="col-lg-7">
                <div class="setup-card p-4 h-100">
                    <h2 class="h4">What happened?</h2>
                    <p class="setup-muted">Guild CMS looked for <strong>includes/config.inc.php</strong>, the file that tells the site how to connect to its database and identify this installation.</p>
                    <p class="setup-muted mb-0">The installer will create that file for you later in the setup process. You should not need to write it by hand.</p>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="setup-card p-4 h-100">
                    <h2 class="h4">What happens next?</h2>
                    <ul class="setup-muted setup-list mb-0">
                        <li>Check your server readiness.</li>
                        <li>Review recommended features.</li>
                        <li>Connect to your database.</li>
                        <li>Create site configuration.</li>
                        <li>Prepare the administrator account.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="setup-card p-4 mt-4">
            <h2 class="h4">Ready to begin?</h2>
            <p class="setup-muted">The installer explains each step before asking for information. No permanent site changes are made until the install phase.</p>
            <?php if ($installer_available): ?>
                <a class="btn btn-guild btn-lg rounded-pill px-4" href="<?= guildcms_setup_h($installer_url) ?>">Begin Installation</a>
            <?php else: ?>
                <div class="alert alert-warning mb-0">The installer directory could not be found. Upload the Guild CMS <code>install/</code> directory, then refresh this page.</div>
            <?php endif; ?>
        </div>

        <p class="small setup-muted mt-4 mb-0">Guild CMS was designed to explain before it asks. This screen is part of that promise: the site tells you what is missing, why it matters, and how to continue.</p>
    </section>
</main>
</body>
</html>
