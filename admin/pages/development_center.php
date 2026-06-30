<?php
/**
 * The Guild CMS - Development Center Dashboard
 * Integrated v0.4 Phase 4.3 preview
 *
 * Adds Security Status and Backlog Summary directly to the main dashboard.
 * No direct superglobal usage.
 */

$devCenterData = __DIR__ . '/../data/development_center_data.php';
if (is_readable($devCenterData)) {
    require $devCenterData;
}

if (!function_exists('guildcms_dev_h')) {
    function guildcms_dev_h($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('guildcms_dev_count_status')) {
    function guildcms_dev_count_status(array $groups, string $wanted): int
    {
        $count = 0;
        foreach ($groups as $items) {
            foreach ($items as $item) {
                if (strtolower((string) ($item['status'] ?? '')) === strtolower($wanted) || ($wanted === 'complete' && !empty($item['done']))) {
                    $count++;
                }
            }
        }
        return $count;
    }
}

if (!function_exists('guildcms_dev_total_group_items')) {
    function guildcms_dev_total_group_items(array $groups): int
    {
        $count = 0;
        foreach ($groups as $items) {
            $count += count($items);
        }
        return $count;
    }
}

$guildcmsRoadmap = $guildcmsRoadmap ?? [];
$guildcmsSecurityStatus = $guildcmsSecurityStatus ?? [];
$guildcmsBacklog = $guildcmsBacklog ?? [];
$guildcmsEngineeringPublicationsBaseUrl = $guildcmsEngineeringPublicationsBaseUrl ?? 'https://guildcms.theregs.org/engineering/';
$guildcmsEngineeringPublications = $guildcmsEngineeringPublications ?? [];

$roadmapComplete = 0;
foreach ($guildcmsRoadmap as $phase) {
    if (strtolower((string) ($phase['status'] ?? '')) === 'complete') {
        $roadmapComplete++;
    }
}
$roadmapTotal = max(1, count($guildcmsRoadmap));
$roadmapPercent = (int) round(($roadmapComplete / $roadmapTotal) * 100);

$securityComplete = guildcms_dev_count_status($guildcmsSecurityStatus, 'complete');
$securityTotal = max(1, guildcms_dev_total_group_items($guildcmsSecurityStatus));
$securityPercent = (int) round(($securityComplete / $securityTotal) * 100);

$backlogTotal = guildcms_dev_total_group_items($guildcmsBacklog);
?>

<div class="container-fluid py-4 development-center-dashboard">
    <div class="p-4 mb-4 rounded border" style="background: linear-gradient(120deg, rgba(13,110,253,.18), rgba(25,135,84,.12)); border-color: rgba(255,255,255,.15) !important;">
        <div class="row align-items-center g-3">
            <div class="col-lg-7">
                <h1 class="mb-2">Development Center <span class="badge text-bg-info">v0.4 Phase 4.3</span></h1>
                <p class="text-secondary mb-3">The Guild CMS roadmap, changelog, backlog, security status, and architecture tracker.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="small text-secondary">Current Phase</div>
                        <div class="h5 mb-0">Phase 4.3 - Engineering Foundation & Governance</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-secondary">Next Phase</div>
                        <div class="h5 mb-0">Phase 4.4 - Installation & Bootstrap System</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="small text-secondary mb-1">Overall Roadmap Progress</div>
                <div class="progress mb-2" role="progressbar" aria-valuenow="<?= $roadmapPercent ?>" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar" style="width: <?= $roadmapPercent ?>%;"><?= $roadmapPercent ?>%</div>
                </div>
                <div class="small text-secondary"><?= (int) $roadmapComplete ?> of <?= (int) $roadmapTotal ?> tracked phases complete</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary small">Phases Complete</div>
                    <div class="display-6"><?= (int) $roadmapComplete ?>/<?= (int) $roadmapTotal ?></div>
                    <span class="badge text-bg-warning">In Progress</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary small">Security Progress</div>
                    <div class="display-6"><?= (int) $securityPercent ?>%</div>
                    <span class="badge text-bg-primary"><?= (int) $securityComplete ?> / <?= (int) $securityTotal ?> items</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary small">Backlog</div>
                    <div class="display-6"><?= (int) $backlogTotal ?></div>
                    <span class="badge text-bg-info">tracked ideas</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary small">Platform Direction</div>
                    <div class="h4 mb-1">CMS Core</div>
                    <span class="badge text-bg-success">Platform first</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Roadmap Snapshot</strong>
                    <span class="badge text-bg-secondary"><?= (int) count($guildcmsRoadmap) ?> phases</span>
                </div>
                <div class="card-body">
                    <?php foreach ($guildcmsRoadmap as $phase): ?>
                        <?php
                            $status = $phase['status'] ?? 'planned';
                            $badge = $status === 'complete' ? 'success' : ($status === 'in_progress' ? 'warning' : 'secondary');
                            $progress = (int) ($phase['progress'] ?? 0);
                        ?>
                        <div class="mb-3 ps-3 border-start border-3 <?= $status === 'complete' ? 'border-success' : ($status === 'in_progress' ? 'border-warning' : 'border-secondary') ?>">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div><strong><?= guildcms_dev_h($phase['phase'] ?? '') ?></strong> <span class="badge text-bg-<?= $badge ?>"><?= guildcms_dev_h(ucwords(str_replace('_', ' ', $status))) ?></span></div>
                                <div class="text-secondary small"><?= $progress ?>%</div>
                            </div>
                            <div class="progress" style="height: .7rem;">
                                <div class="progress-bar" style="width: <?= $progress ?>%;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Security Status</strong>
                    <span class="badge text-bg-primary">Phase 4.3</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-secondary">Hardening progress</span>
                            <span><?= (int) $securityPercent ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?= (int) $securityPercent ?>%;"><?= (int) $securityPercent ?>%</div>
                        </div>
                    </div>

                    <?php foreach ($guildcmsSecurityStatus as $group => $items): ?>
                        <div class="mb-3">
                            <div class="fw-semibold mb-2"><?= guildcms_dev_h($group) ?> <span class="badge text-bg-dark"><?= count($items) ?></span></div>
                            <ul class="list-unstyled mb-0 small">
                                <?php foreach ($items as $item): ?>
                                    <?php $done = strtolower((string) ($item['status'] ?? '')) === 'complete' || !empty($item['done']); ?>
                                    <li class="d-flex gap-2 py-1 border-bottom border-secondary-subtle">
                                        <span><?= $done ? '✔' : '□' ?></span>
                                        <span class="<?= $done ? 'text-success' : '' ?>"><?= guildcms_dev_h($item['label'] ?? '') ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Backlog Summary</strong>
                    <span class="badge text-bg-info"><?= (int) $backlogTotal ?> ideas</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($guildcmsBacklog as $category => $items): ?>
                            <div class="col-md-6 col-xxl-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong><?= guildcms_dev_h($category) ?></strong>
                                        <span class="badge text-bg-dark"><?= count($items) ?></span>
                                    </div>
                                    <ul class="list-unstyled small mb-0">
                                        <?php foreach (array_slice($items, 0, 4) as $item): ?>
                                            <li class="py-1">□ <?= guildcms_dev_h($item) ?></li>
                                        <?php endforeach; ?>
                                        <?php if (count($items) > 4): ?>
                                            <li class="text-secondary py-1">+ <?= count($items) - 4 ?> more</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card h-100 mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Engineering Publications</strong>
                    <span class="badge text-bg-info"><?= count($guildcmsEngineeringPublications) ?></span>
                </div>
                <div class="card-body">
                    <p class="small text-secondary">Development Center tracks publication metadata and links to the public Engineering Library.</p>
                    <a class="btn btn-sm btn-outline-info mb-3" href="<?= guildcms_dev_h($guildcmsEngineeringPublicationsBaseUrl) ?>" target="_blank" rel="noopener">Open Public Library</a>
                    <ul class="list-unstyled small mb-0">
                        <?php foreach (array_slice($guildcmsEngineeringPublications, 0, 5) as $publication): ?>
                            <li class="py-1 border-bottom border-secondary-subtle">
                                <code><?= guildcms_dev_h($publication['id'] ?? '') ?></code><br>
                                <a href="<?= guildcms_dev_h($publication['url'] ?? '#') ?>" target="_blank" rel="noopener"><?= guildcms_dev_h($publication['title'] ?? '') ?></a>
                                <span class="text-secondary">· <?= guildcms_dev_h($publication['status'] ?? '') ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="card h-100">
                <div class="card-header"><strong>Next Steps</strong></div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li>Publish Volume I: Guild CMS Constitution.</li>
                        <li>Use the public Engineering Library as the canonical document home.</li>
                        <li>Track publication metadata and release state in the Development Center.</li>
                        <li>Prepare future Phase 4.4 installation and bootstrap planning.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
