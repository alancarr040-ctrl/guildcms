<?php
/**
 * The Guild CMS - Development Center
 * Phase 4.2 Security Status
 *
 * Static v0.2 status page for tracking Security Hardening work.
 */

if (!defined('IN_PHPBB')) {
    exit;
}

$security_sections = [
    'Security Headers' => [
        ['label' => 'Shared security headers include', 'status' => 'complete', 'phase' => '4.2'],
        ['label' => 'X-Content-Type-Options: nosniff', 'status' => 'complete', 'phase' => '4.2'],
        ['label' => 'Referrer-Policy', 'status' => 'complete', 'phase' => '4.2'],
        ['label' => 'Permissions-Policy baseline', 'status' => 'complete', 'phase' => '4.2'],
        ['label' => 'X-Frame-Options / frame protection', 'status' => 'complete', 'phase' => '4.2'],
        ['label' => 'Remove X-Powered-By', 'status' => 'complete', 'phase' => '4.2'],
        ['label' => 'Content-Security-Policy report/testing mode', 'status' => 'planned', 'phase' => '4.2'],
        ['label' => 'Content-Security-Policy enforcement mode', 'status' => 'planned', 'phase' => '4.2'],
    ],
    'Sessions & Cookies' => [
        ['label' => 'Secure cookie review', 'status' => 'planned', 'phase' => '4.2'],
        ['label' => 'HttpOnly cookie review', 'status' => 'planned', 'phase' => '4.2'],
        ['label' => 'SameSite cookie review', 'status' => 'planned', 'phase' => '4.2'],
        ['label' => 'Session lifetime review', 'status' => 'planned', 'phase' => '4.2'],
        ['label' => 'phpBB session compatibility check', 'status' => 'planned', 'phase' => '4.2'],
    ],
    'Forms & CSRF' => [
        ['label' => 'Admin form CSRF audit', 'status' => 'planned', 'phase' => '4.2'],
        ['label' => 'Public form CSRF audit', 'status' => 'planned', 'phase' => '4.2'],
        ['label' => 'Token helper consistency review', 'status' => 'planned', 'phase' => '4.2'],
        ['label' => 'POST-only action review', 'status' => 'planned', 'phase' => '4.2'],
    ],
    'Input & Output Safety' => [
        ['label' => 'Direct superglobal usage removed from active codebase', 'status' => 'complete', 'phase' => '4.1'],
        ['label' => 'phpBB request class standard documented', 'status' => 'complete', 'phase' => '4.1'],
        ['label' => 'Output escaping audit', 'status' => 'planned', 'phase' => '4.2'],
        ['label' => 'Prepared statement review', 'status' => 'planned', 'phase' => '4.2'],
        ['label' => 'Allow-list validation review', 'status' => 'planned', 'phase' => '4.2'],
    ],
    'Uploads & Files' => [
        ['label' => 'Upload directory permissions review', 'status' => 'planned', 'phase' => '4.2'],
        ['label' => 'Allowed extension validation', 'status' => 'planned', 'phase' => '4.2'],
        ['label' => 'MIME/content validation', 'status' => 'planned', 'phase' => '4.2'],
        ['label' => 'Executable upload prevention', 'status' => 'planned', 'phase' => '4.2'],
        ['label' => 'Image processing safety review', 'status' => 'planned', 'phase' => '4.2'],
    ],
    'Documentation & Tooling' => [
        ['label' => 'Security Status page', 'status' => 'complete', 'phase' => '4.2'],
        ['label' => 'Security coding standards', 'status' => 'planned', 'phase' => '4.2'],
        ['label' => 'Security changelog entries', 'status' => 'planned', 'phase' => '4.2'],
        ['label' => 'Development Center security docs', 'status' => 'planned', 'phase' => '4.2'],
    ],
];

$total_items = 0;
$complete_items = 0;
$planned_items = 0;
$in_progress_items = 0;

foreach ($security_sections as $items) {
    foreach ($items as $item) {
        $total_items++;
        if ($item['status'] === 'complete') {
            $complete_items++;
        } elseif ($item['status'] === 'in_progress') {
            $in_progress_items++;
        } else {
            $planned_items++;
        }
    }
}

$progress_percent = $total_items > 0 ? (int) round(($complete_items / $total_items) * 100) : 0;

$status_badge = static function (string $status): string {
    switch ($status) {
        case 'complete':
            return '<span class="badge text-bg-success">Complete</span>';
        case 'in_progress':
            return '<span class="badge text-bg-warning text-dark">In Progress</span>';
        default:
            return '<span class="badge text-bg-secondary">Planned</span>';
    }
};

$status_icon = static function (string $status): string {
    switch ($status) {
        case 'complete':
            return '✓';
        case 'in_progress':
            return '▶';
        default:
            return '□';
    }
};
?>

<div class="container-fluid py-4 guildcms-devcenter guildcms-security-status">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Security Status</h1>
            <p class="text-muted mb-0">Phase 4.2 security hardening tracker for The Guild CMS.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge text-bg-primary">Phase 4.2</span>
            <span class="badge text-bg-info text-dark"><?= (int) $total_items ?> Security Items</span>
            <span class="badge text-bg-secondary">Static Page</span>
        </div>
    </div>

    <div class="alert alert-info border-0 shadow-sm mb-4">
        <strong>Purpose:</strong>
        This page tracks security-hardening progress for The Guild CMS while keeping TheRegs.org compatible with phpBB until phpBB becomes an optional integration/plugin in a later phase.
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Overall Progress</div>
                    <div class="display-6 fw-bold"><?= (int) $progress_percent ?>%</div>
                    <div class="progress mt-2" role="progressbar" aria-label="Security progress" aria-valuenow="<?= (int) $progress_percent ?>" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" style="width: <?= (int) $progress_percent ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Complete</div>
                    <div class="display-6 fw-bold text-success"><?= (int) $complete_items ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">In Progress</div>
                    <div class="display-6 fw-bold text-warning"><?= (int) $in_progress_items ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Planned</div>
                    <div class="display-6 fw-bold text-secondary"><?= (int) $planned_items ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($security_sections as $section_title => $items): ?>
            <?php
            $section_total = count($items);
            $section_complete = 0;
            foreach ($items as $item) {
                if ($item['status'] === 'complete') {
                    $section_complete++;
                }
            }
            ?>
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong><?= h($section_title) ?></strong>
                        <span class="badge text-bg-dark"><?= (int) $section_complete ?> / <?= (int) $section_total ?></span>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($items as $item): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <span class="me-2 fw-bold"><?= h($status_icon($item['status'])) ?></span>
                                    <span><?= h($item['label']) ?></span>
                                    <div class="small text-muted">Phase <?= h($item['phase']) ?></div>
                                </div>
                                <div class="text-nowrap">
                                    <?= $status_badge($item['status']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <strong>Security Standards Draft</strong>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">Initial standards for The Guild CMS security work.</p>
            <div class="row">
                <div class="col-md-6">
                    <ul class="mb-md-0">
                        <li>Never use PHP superglobals directly in the active phpBB-integrated codebase.</li>
                        <li>Use the phpBB request class or approved Guild CMS request helpers.</li>
                        <li>Escape output using the existing escaping helper.</li>
                        <li>Use prepared statements for database writes and variable reads.</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="mb-0">
                        <li>Require CSRF protection for POST actions.</li>
                        <li>Prefer allow-lists over deny-lists.</li>
                        <li>Centralize security headers.</li>
                        <li>Keep TheRegs.org compatible with phpBB until standalone mode is reached.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
