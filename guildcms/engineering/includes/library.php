<?php
declare(strict_types=1);

function guildcms_engineering_publications(): array
{
    return [
        [
            'id' => 'GCMS-ENG-000',
            'publication' => 'Publication 0',
            'volume' => 'Foundational Note',
            'title' => "Founder's Note",
            'slug' => 'founders-note.php',
            'status' => 'Published',
            'version' => '1.0',
            'phase' => '4.3',
            'updated' => 'June 2026',
            'applies_to' => 'Guild CMS v0.9+',
            'category' => 'Foundation',
            'summary' => 'Introduces the purpose of the Engineering Library and its role in long-term Guild CMS stewardship.',
            'topics' => ['Project stewardship', 'Engineering transparency', 'Long-term maintainability'],
        ],
        [
            'id' => 'GCMS-ENG-001',
            'publication' => 'Publication 1',
            'volume' => 'Volume I',
            'title' => 'The Guild CMS Constitution',
            'slug' => 'constitution.php',
            'status' => 'Published',
            'version' => '1.0',
            'phase' => '4.3.0-4',
            'updated' => 'June 2026',
            'applies_to' => 'Guild CMS v0.9+',
            'category' => 'Foundation',
            'summary' => 'Establishes the enduring principles, governance commitments, release discipline, and stewardship obligations that guide Guild CMS.',
            'topics' => ['Project identity', 'Governance commitments', 'Stewardship principles'],
        ],
        [
            'id' => 'GCMS-ENG-002',
            'publication' => 'Publication 2',
            'volume' => 'Volume II',
            'title' => 'Vision & Mission',
            'slug' => 'vision-mission.php',
            'status' => 'Published',
            'version' => '1.0',
            'phase' => '4.3.0-4',
            'updated' => 'June 2026',
            'applies_to' => 'Guild CMS v0.9+',
            'category' => 'Foundation',
            'summary' => 'Defines the long-term product vision, mission, audience, and stewardship direction for Guild CMS.',
            'topics' => ['Product vision', 'Community mission', 'Long-term platform direction'],
        ],
        [
            'id' => 'GCMS-ENG-003',
            'publication' => 'Publication 3',
            'volume' => 'Volume III',
            'title' => 'Engineering Principles',
            'slug' => 'principles.php',
            'status' => 'Published',
            'version' => '1.0',
            'phase' => '4.3.0-5',
            'updated' => 'June 2026',
            'applies_to' => 'Guild CMS v0.9+',
            'category' => 'Foundation',
            'summary' => 'Defines the practical engineering principles that guide implementation, review, documentation, security, and long-term maintenance across Guild CMS.',
            'topics' => ['Security-first engineering', 'Maintainability', 'Documentation discipline', 'Reviewability', 'Incremental release quality'],
        ],
        [
            'id' => 'GCMS-ENG-004',
            'publication' => 'Publication 4',
            'volume' => 'Volume IV',
            'title' => 'Architecture Standards',
            'slug' => 'architecture-standards.php',
            'status' => 'Published',
            'version' => '1.0',
            'phase' => '4.3.0-6',
            'updated' => 'June 2026',
            'applies_to' => 'Guild CMS v0.9+',
            'category' => 'Architecture',
            'summary' => 'Defines the structural standards for Guild CMS routing, modules, providers, data access, themes, plugins, security boundaries, documentation, and architecture evolution.',
            'topics' => ['System architecture', 'Module boundaries', 'Provider model', 'Data layer standards', 'Plugin and theme architecture', 'Architecture decision records'],
        ],
        [
            'id' => 'GCMS-ENG-005',
            'publication' => 'Publication 5',
            'volume' => 'Volume V',
            'title' => 'Developer Handbook',
            'slug' => 'developer-handbook.php',
            'status' => 'Published',
            'version' => '1.0',
            'phase' => '4.3.0-7',
            'updated' => 'June 2026',
            'applies_to' => 'Guild CMS v0.9+',
            'category' => 'Development',
            'summary' => 'Provides the practical onboarding and daily workflow reference for developers working on Guild CMS core, public site, Development Center, release packages, and future extensions.',
            'topics' => ['Developer onboarding', 'Project structure', 'Package workflow', 'Security review', 'Release preparation', 'Documentation discipline'],
        ],
        [
            'id' => 'GCMS-ENG-006',
            'publication' => 'Publication 6',
            'volume' => 'Volume VI',
            'title' => 'Contribution Guide',
            'slug' => 'contribution-guide.php',
            'status' => 'Published',
            'version' => '1.0',
            'phase' => '4.3.0-8',
            'updated' => 'June 2026',
            'applies_to' => 'Guild CMS v0.9+',
            'category' => 'Development',
            'summary' => 'Defines how contributors propose, prepare, review, document, and deliver changes to Guild CMS while preserving the project\'s engineering standards.',
            'topics' => ['Contribution flow', 'Issue handling', 'Review expectations', 'Documentation requirements', 'Release package discipline', 'Stewardship'],
        ],
        [
            'id' => 'GCMS-ENG-007',
            'publication' => 'Publication 7',
            'volume' => 'Volume VII',
            'title' => 'Coding Standards',
            'slug' => 'coding-standards.php',
            'status' => 'Published',
            'version' => '1.0',
            'phase' => '4.3.0-8',
            'updated' => 'June 2026',
            'applies_to' => 'Guild CMS v0.9+',
            'category' => 'Development',
            'summary' => 'Defines the required coding conventions for PHP, SQL, HTML, CSS, JavaScript, naming, formatting, escaping, documentation, compatibility, and release quality across Guild CMS.',
            'topics' => ['PHP conventions', 'SQL conventions', 'Output escaping', 'File organization', 'Release QA'],
        ],
        [
            'id' => 'GCMS-ENG-008',
            'publication' => 'Publication 8',
            'volume' => 'Volume VIII',
            'title' => 'Security Standards',
            'slug' => 'security-standards.php',
            'status' => 'Published',
            'version' => '1.0',
            'phase' => '4.3.0-9',
            'updated' => 'June 2026',
            'applies_to' => 'Guild CMS v0.9+',
            'category' => 'Security',
            'summary' => 'Defines the required security posture for input handling, output escaping, authentication boundaries, authorization, CSRF, SQL safety, file uploads, sessions, cookies, security headers, logging, privacy, and release security review.',
            'topics' => ['Input validation', 'Output escaping', 'CSRF protection', 'Authorization', 'SQL safety', 'Upload security', 'Security headers', 'Release review'],
        ],
        [
            'id' => 'GCMS-ENG-009',
            'publication' => 'Publication 9',
            'volume' => 'Volume IX',
            'title' => 'Architecture Decision Records',
            'slug' => 'adr.php',
            'status' => 'Published',
            'version' => '1.0',
            'phase' => '4.3.0-10',
            'updated' => 'June 2026',
            'applies_to' => 'Guild CMS v0.9+',
            'category' => 'Architecture',
            'summary' => 'Defines the Guild CMS Architecture Decision Record process, lifecycle, format, numbering rules, review expectations, and first foundational architecture decisions.',
            'topics' => ['ADR format', 'Decision lifecycle', 'Tradeoff recording', 'Governance', 'Foundational decisions'],
        ],
        [
            'id' => 'GCMS-ENG-010',
            'publication' => 'Publication 10',
            'volume' => 'Volume X',
            'title' => 'Engineering Roadmap & Publication Framework',
            'slug' => 'future.php',
            'status' => 'Published',
            'version' => '1.0',
            'phase' => '4.3.0-11',
            'updated' => 'June 2026',
            'applies_to' => 'Guild CMS v0.9+',
            'category' => 'Governance',
            'summary' => 'Defines the Engineering Library publication lifecycle, numbering model, revision policy, future publication roadmap, and governance framework for long-term engineering documentation.',
            'topics' => ['Publication lifecycle', 'Numbering model', 'Revision policy', 'Future volumes', 'Documentation governance'],
        ],
        [
            'id' => 'GCMS-ENG-011',
            'publication' => 'Publication 11',
            'volume' => 'Volume XI',
            'title' => 'User Experience & Educational Design Principles',
            'slug' => 'user-experience.php',
            'status' => 'Published',
            'version' => '1.0',
            'phase' => '4.4.0-3',
            'updated' => 'June 2026',
            'applies_to' => 'Guild CMS v0.9+ and Phase 4.4 installer work',
            'category' => 'Product Experience',
            'summary' => 'Defines the Guild CMS product experience philosophy: explain before asking, teach instead of assuming, design for confidence, make recovery safe, and ensure the installer introduces the experience users should expect from the CMS.',
            'topics' => ['Educational design', 'Installer experience', 'Accessible workflows', 'Error guidance', 'Administrator confidence', 'Product identity'],
        ],
    ];
}

function guildcms_engineering_find(string $slug): ?array
{
    foreach (guildcms_engineering_publications() as $publication) {
        if ($publication['slug'] === $slug) {
            return $publication;
        }
    }

    return null;
}

function guildcms_engineering_status_class(string $status): string
{
    return match ($status) {
        'Published' => 'success',
        'In Development' => 'warning text-dark',
        'Under Development' => 'info text-dark',
        'Reserved' => 'secondary',
        default => 'secondary',
    };
}

function guildcms_engineering_badge(string $status): string
{
    return '<span class="badge bg-' . guildcms_engineering_status_class($status) . '">' . guildcms_h($status) . '</span>';
}

function guildcms_engineering_breadcrumb(array $publication): void
{
    ?>
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb guild-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="/" class="link-light">Home</a></li>
            <li class="breadcrumb-item"><a href="/engineering/" class="link-light">Engineering Library</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= guildcms_h($publication['title']) ?></li>
        </ol>
    </nav>
    <?php
}

function guildcms_engineering_sidebar(string $current_slug): void
{
    $groups = [];

    foreach (guildcms_engineering_publications() as $publication) {
        $groups[$publication['category']][] = $publication;
    }
    ?>
    <aside class="guild-card p-4 engineering-sidebar sticky-lg-top">
        <div class="guild-muted small text-uppercase mb-1">Guild CMS</div>
        <h2 class="h5 mb-3">Engineering Library</h2>
        <a class="d-block link-light text-decoration-none mb-3 <?= $current_slug === 'index' ? 'fw-bold' : '' ?>" href="/engineering/">Library Home</a>
        <?php foreach ($groups as $category => $items): ?>
            <div class="guild-muted small text-uppercase mt-3 mb-2"><?= guildcms_h($category) ?></div>
            <ul class="list-unstyled mb-0 engineering-nav-list">
                <?php foreach ($items as $item): ?>
                    <li>
                        <a class="<?= $current_slug === $item['slug'] ? 'active' : '' ?>" href="/engineering/<?= guildcms_h($item['slug']) ?>">
                            <span><?= guildcms_h($item['id']) ?></span>
                            <?= guildcms_h($item['title']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    </aside>
    <?php
}

function guildcms_engineering_publication_page(array $publication, ?string $body_html = null): void
{
    ?>
    <section class="py-5 engineering-publication">
        <div class="container">
            <?php guildcms_engineering_breadcrumb($publication); ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <article class="guild-card p-4 p-lg-5">
                        <div class="engineering-kicker">Guild CMS</div>
                        <h1 class="display-6 fw-bold mb-1">Engineering Library</h1>
                        <div class="h5 guild-muted mb-4">Knowledge • Architecture • Standards</div>
                        <hr class="border-secondary">
                        <div class="guild-muted small text-uppercase"><?= guildcms_h($publication['publication']) ?></div>
                        <div class="guild-muted small mb-2"><?= guildcms_h($publication['id']) ?> · <?= guildcms_h($publication['volume']) ?></div>
                        <h2 class="display-6 fw-bold mb-3"><?= guildcms_h($publication['title']) ?></h2>
                        <div class="mb-4"><?= guildcms_engineering_badge($publication['status']) ?></div>

                        <div class="engineering-meta guild-card-soft p-3 mb-4">
                            <div><strong>Publication</strong><span><?= guildcms_h($publication['publication']) ?></span></div>
                            <div><strong>Identifier</strong><span><?= guildcms_h($publication['id']) ?></span></div>
                            <div><strong>Version</strong><span><?= guildcms_h($publication['version']) ?></span></div>
                            <div><strong>Phase</strong><span><?= guildcms_h($publication['phase']) ?></span></div>
                            <div><strong>Last Updated</strong><span><?= guildcms_h($publication['updated']) ?></span></div>
                            <div><strong>Applies To</strong><span><?= guildcms_h($publication['applies_to']) ?></span></div>
                        </div>

                        <?php if ($body_html !== null): ?>
                            <?= $body_html ?>
                        <?php else: ?>
                            <p class="lead"><?= guildcms_h($publication['summary']) ?></p>
                            <h3 class="h5 mt-4">Publication Status</h3>
                            <p class="guild-muted">This publication shell is now part of the official Engineering Library framework. Full content will be expanded during Phase 4.3 and later phases as the related standards mature.</p>
                            <h3 class="h5 mt-4">Planned Topics</h3>
                            <ul class="mb-0">
                                <?php foreach ($publication['topics'] as $topic): ?>
                                    <li><?= guildcms_h($topic) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </article>
                </div>
                <div class="col-lg-4">
                    <?php guildcms_engineering_sidebar($publication['slug']); ?>
                </div>
            </div>
        </div>
    </section>
    <?php
}
