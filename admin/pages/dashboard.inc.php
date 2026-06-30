<?php
declare(strict_types=1);

if (!defined('IN_ADMIN')) {
    exit;
}

global $site_sections, $admin_module_labels;
?>

<div class="container-fluid py-4">

    <h1 class="h3 mb-3">Admin Dashboard</h1>

    <p class="text-secondary">
        Manage the editable sections of the Theregs website.
    </p>

    <div class="row g-4">
        <?php foreach ($site_sections as $section_key => $section): ?>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">
                        <strong><?= admin_h($section['name']) ?></strong>
                        <div class="small text-secondary">
                            <?= admin_h($section['path']) ?>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <?php foreach ($section['modules'] as $module): ?>
                                <?php
                                $label = $admin_module_labels[$module] ?? ucfirst($module);
                                $url = '/admin/?page=' . urlencode($module) . '&section=' . urlencode($section_key);
                                ?>
                                <a class="btn btn-outline-light text-start" href="<?= admin_h($url) ?>">
                                    Manage <?= admin_h($label) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="card-footer">
                        <a class="btn btn-sm btn-secondary" href="<?= admin_h($section['path']) ?>" target="_blank">
                            View Section
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>