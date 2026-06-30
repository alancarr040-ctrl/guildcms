<?php
declare(strict_types=1);

if (!defined('IN_ADMIN')) {
    exit;
}

global $db, $request, $user, $site_sections;

$section_key = $request->variable('section', 'wow');

if (!isset($site_sections[$section_key])) {
    $section_key = 'wow';
}

if (!in_array('races', $site_sections[$section_key]['modules'], true)) {
    trigger_error('Race management is not enabled for this section.');
}

$section_name = $site_sections[$section_key]['name'];

$form_key = 'admin_races';
add_form_key($form_key);

$message = '';
$error = '';

$action = $request->variable('action', '');
$id = $request->variable('id', 0);

if ($request->variable('added', 0) === 1) {
    $message = 'Race added.';
}

if ($request->variable('updated', 0) === 1) {
    $message = 'Race updated.';
}

function admin_race_slug(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/i', '-', $value);
    $value = trim((string) $value, '-');

    return $value !== '' ? $value : 'race-entry';
}

function admin_race_decode_html(string $value): string
{
    $previous = null;
    $decoded = $value;

    while ($decoded !== $previous) {
        $previous = $decoded;
        $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    return $decoded;
}

function admin_redirect_races(string $section_key): void
{
    redirect('/admin/?page=races&section=' . rawurlencode($section_key));
}

function admin_races_form_url(string $section_key, string $action, int $id = 0): string
{
    $url = '/admin/?page=races&section=' . rawurlencode($section_key) . '&action=' . rawurlencode($action);

    if ($id > 0) {
        $url .= '&id=' . $id;
    }

    return $url;
}

$edit_entry = [
    'id' => 0,
    'name' => '',
    'slug' => '',
    'side' => '',
    'background_url' => '',
    'character_image_url' => '',
    'leader_image_url' => '',
    'intro_title' => '',
    'intro_text' => '',
    'history_text' => '',
    'start_zone' => '',
    'start_zone_text' => '',
    'home_city' => '',
    'home_city_text' => '',
    'leader' => '',
    'leader_text' => '',
    'traits_title' => '',
    'classes_title' => '',
    'available_classes' => '',
    'racial_mount_title' => '',
    'racial_mount_image_url' => '',
    'racial_mount_text' => '',
    'heritage_armor_title' => '',
    'heritage_armor_image_url' => '',
    'heritage_armor_text' => '',
    'sort_order' => 0,
    'is_active' => 1,
];

for ($i = 1; $i <= 10; $i++) {
    $edit_entry['trait' . $i . '_name'] = '';
    $edit_entry['trait' . $i . '_image_url'] = '';
    $edit_entry['trait' . $i . '_text'] = '';
}

if ($request->is_set_post('save')) {
    if (!check_form_key($form_key)) {
        $error = 'Invalid form submission. Please try again.';
    } else {
        $id = $request->variable('id', 0);

        $name = trim($request->variable('name', '', true));
        $slug = trim($request->variable('slug', '', true));
        $side = trim($request->variable('side', '', true));

        $background_url = trim($request->variable('background_url', '', true));
        $character_image_url = trim($request->variable('character_image_url', '', true));
        $leader_image_url = trim($request->variable('leader_image_url', '', true));

        $intro_title = trim($request->variable('intro_title', '', true));
        $intro_text = admin_race_decode_html(trim($request->variable('intro_text', '', true)));
        $history_text = admin_race_decode_html(trim($request->variable('history_text', '', true)));

        $start_zone = trim($request->variable('start_zone', '', true));
        $start_zone_text = admin_race_decode_html(trim($request->variable('start_zone_text', '', true)));

        $home_city = trim($request->variable('home_city', '', true));
        $home_city_text = admin_race_decode_html(trim($request->variable('home_city_text', '', true)));

        $leader = trim($request->variable('leader', '', true));
        $leader_text = admin_race_decode_html(trim($request->variable('leader_text', '', true)));

        $traits_title = trim($request->variable('traits_title', '', true));
        $trait_data = [];

        for ($i = 1; $i <= 10; $i++) {
            $trait_data['trait' . $i . '_name'] = trim($request->variable('trait' . $i . '_name', '', true));
            $trait_data['trait' . $i . '_image_url'] = trim($request->variable('trait' . $i . '_image_url', '', true));
            $trait_data['trait' . $i . '_text'] = admin_race_decode_html(trim($request->variable('trait' . $i . '_text', '', true)));
        }


        $classes_title = trim($request->variable('classes_title', '', true));
        $available_classes = trim($request->variable('available_classes', '', true));

        $racial_mount_title = trim($request->variable('racial_mount_title', '', true));
        $racial_mount_image_url = trim($request->variable('racial_mount_image_url', '', true));
        $racial_mount_text = admin_race_decode_html(trim($request->variable('racial_mount_text', '', true)));

        $heritage_armor_title = trim($request->variable('heritage_armor_title', '', true));
        $heritage_armor_image_url = trim($request->variable('heritage_armor_image_url', '', true));
        $heritage_armor_text = admin_race_decode_html(trim($request->variable('heritage_armor_text', '', true)));

        $sort_order = $request->variable('sort_order', 0);
        $is_active = $request->variable('is_active', 1);

        $slug = ($slug === '') ? admin_race_slug($name) : admin_race_slug($slug);

        $edit_entry = array_merge($edit_entry, [
            'id' => $id,
            'name' => $name,
            'slug' => $slug,
            'side' => $side,
            'background_url' => $background_url,
            'character_image_url' => $character_image_url,
            'leader_image_url' => $leader_image_url,
            'intro_title' => $intro_title,
            'intro_text' => $intro_text,
            'history_text' => $history_text,
            'start_zone' => $start_zone,
            'start_zone_text' => $start_zone_text,
            'home_city' => $home_city,
            'home_city_text' => $home_city_text,
            'leader' => $leader,
            'leader_text' => $leader_text,
            'traits_title' => $traits_title,
            'trait1_name' => $trait1_name,
            'trait1_image_url' => $trait1_image_url,
            'trait1_text' => $trait1_text,
            'trait2_name' => $trait2_name,
            'trait2_image_url' => $trait2_image_url,
            'trait2_text' => $trait2_text,
            'trait3_name' => $trait3_name,
            'trait3_image_url' => $trait3_image_url,
            'trait3_text' => $trait3_text,
            'trait4_name' => $trait4_name,
            'trait4_image_url' => $trait4_image_url,
            'trait4_text' => $trait4_text,
            'classes_title' => $classes_title,
            'available_classes' => $available_classes,
            'racial_mount_title' => $racial_mount_title,
            'racial_mount_image_url' => $racial_mount_image_url,
            'racial_mount_text' => $racial_mount_text,
            'heritage_armor_title' => $heritage_armor_title,
            'heritage_armor_image_url' => $heritage_armor_image_url,
            'heritage_armor_text' => $heritage_armor_text,
            'sort_order' => $sort_order,
            'is_active' => $is_active ? 1 : 0,
        ], $trait_data);

        if ($name === '') {
            $error = 'Race name is required.';
        }

        $url_fields = [
            'Background image' => $background_url,
            'Character image' => $character_image_url,
            'Leader image' => $leader_image_url,
            'Racial mount image' => $racial_mount_image_url,
            'Heritage armor image' => $heritage_armor_image_url,
            'Trait 1 image' => $trait1_image_url,
            'Trait 2 image' => $trait2_image_url,
            'Trait 3 image' => $trait3_image_url,
            'Trait 4 image' => $trait4_image_url,
        ];

        for ($i = 1; $i <= 10; $i++) {
            $url_fields['Trait ' . $i . ' image'] = $trait_data['trait' . $i . '_image_url'];
        }

        foreach ($url_fields as $label => $value) {
            if ($value !== '' && !filter_var($value, FILTER_VALIDATE_URL) && str_starts_with($value, '/') === false) {
                $error = $label . ' must be a valid URL or local path beginning with /.';
                break;
            }
        }

        if ($error === '') {
            $sql = "SELECT id
                    FROM site_races
                    WHERE section_key = '" . $db->sql_escape($section_key) . "'
                    AND slug = '" . $db->sql_escape($slug) . "'
                    AND id <> " . (int) $id;

            $result = $db->sql_query($sql);
            $duplicate = $db->sql_fetchrow($result);
            $db->sql_freeresult($result);

            if ($duplicate) {
                $error = 'That slug is already used in this section.';
            }
        }

        if ($error === '') {
            $data = [
                'name' => $name,
                'slug' => $slug,
                'side' => $side,
                'background_url' => $background_url,
                'character_image_url' => $character_image_url,
                'leader_image_url' => $leader_image_url,
                'intro_title' => $intro_title,
                'intro_text' => $intro_text,
                'history_text' => $history_text,
                'start_zone' => $start_zone,
                'start_zone_text' => $start_zone_text,
                'home_city' => $home_city,
                'home_city_text' => $home_city_text,
                'leader' => $leader,
                'leader_text' => $leader_text,
                'traits_title' => $traits_title,
                'trait1_name' => $trait1_name,
                'trait1_image_url' => $trait1_image_url,
                'trait1_text' => $trait1_text,
                'trait2_name' => $trait2_name,
                'trait2_image_url' => $trait2_image_url,
                'trait2_text' => $trait2_text,
                'trait3_name' => $trait3_name,
                'trait3_image_url' => $trait3_image_url,
                'trait3_text' => $trait3_text,
                'trait4_name' => $trait4_name,
                'trait4_image_url' => $trait4_image_url,
                'trait4_text' => $trait4_text,
                'classes_title' => $classes_title,
                'available_classes' => $available_classes,
                'racial_mount_title' => $racial_mount_title,
                'racial_mount_image_url' => $racial_mount_image_url,
                'racial_mount_text' => $racial_mount_text,
                'heritage_armor_title' => $heritage_armor_title,
                'heritage_armor_image_url' => $heritage_armor_image_url,
                'heritage_armor_text' => $heritage_armor_text,
                'sort_order' => $sort_order,
                'is_active' => $is_active ? 1 : 0,
            ];

            $data = array_merge($data, $trait_data);

            if ($id > 0) {
                $data['updated_by_user_id'] = (int) $user->data['user_id'];
                $data['updated_by_name'] = (string) $user->data['username'];
                $data['updated_at'] = date('Y-m-d H:i:s');

                $sql = 'UPDATE site_races SET ' . $db->sql_build_array('UPDATE', $data) . '
                        WHERE id = ' . (int) $id . "
                        AND section_key = '" . $db->sql_escape($section_key) . "'";

                $db->sql_query($sql);

                redirect('/admin/?page=races&section=' . rawurlencode($section_key) . '&updated=1');
            } else {
                $data['section_key'] = $section_key;
                $data['created_by_user_id'] = (int) $user->data['user_id'];
                $data['created_by_name'] = (string) $user->data['username'];
                $data['created_at'] = date('Y-m-d H:i:s');

                $sql = 'INSERT INTO site_races ' . $db->sql_build_array('INSERT', $data);

                $db->sql_query($sql);

                redirect('/admin/?page=races&section=' . rawurlencode($section_key) . '&added=1');
            }
        }

        $action = ($id > 0) ? 'edit' : 'add';
    }
}

if ($action === 'delete' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_races_delete_' . $id)) {
        trigger_error('Invalid disable request.');
    }

    $sql = 'UPDATE site_races SET ' . $db->sql_build_array('UPDATE', [
        'is_active' => 0,
        'updated_by_user_id' => (int) $user->data['user_id'],
        'updated_by_name' => (string) $user->data['username'],
        'updated_at' => date('Y-m-d H:i:s'),
    ]) . '
    WHERE id = ' . (int) $id . "
    AND section_key = '" . $db->sql_escape($section_key) . "'";

    $db->sql_query($sql);
    admin_redirect_races($section_key);
}

if ($action === 'restore' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_races_restore_' . $id)) {
        trigger_error('Invalid restore request.');
    }

    $sql = 'UPDATE site_races SET ' . $db->sql_build_array('UPDATE', [
        'is_active' => 1,
        'updated_by_user_id' => (int) $user->data['user_id'],
        'updated_by_name' => (string) $user->data['username'],
        'updated_at' => date('Y-m-d H:i:s'),
    ]) . '
    WHERE id = ' . (int) $id . "
    AND section_key = '" . $db->sql_escape($section_key) . "'";

    $db->sql_query($sql);
    admin_redirect_races($section_key);
}

if ($action === 'edit' && $id > 0 && !$request->is_set_post('save')) {
    $sql = 'SELECT *
            FROM site_races
            WHERE id = ' . (int) $id . "
            AND section_key = '" . $db->sql_escape($section_key) . "'";

    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    if ($row) {
        $edit_entry = array_merge($edit_entry, $row);
    } else {
        $error = 'Race entry was not found.';
        $action = '';
    }
}

$show_form = ($action === 'add' || ($action === 'edit' && (int) $edit_entry['id'] > 0));

$pagination = admin_get_pagination(
    $request,
    $db,
    "SELECT COUNT(*) AS total
     FROM site_races
     WHERE section_key = '" . $db->sql_escape($section_key) . "'"
);

$sql = "SELECT *
        FROM site_races
        WHERE section_key = '" . $db->sql_escape($section_key) . "'
        ORDER BY is_active DESC, sort_order ASC, side ASC, name ASC";

$result = $db->sql_query_limit($sql, $pagination['per_page'], $pagination['offset']);

$entries = [];

while ($row = $db->sql_fetchrow($result)) {
    $entries[] = $row;
}

$db->sql_freeresult($result);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Manage Races</h1>
        <div class="text-secondary"><?= admin_h($section_name) ?></div>
    </div>

    <div class="d-flex gap-2">
        <?php if ($show_form): ?>
            <a class="btn btn-outline-secondary" href="/admin/?page=races&amp;section=<?= admin_h($section_key) ?>">
                Back to Races
            </a>
        <?php else: ?>
            <a class="btn btn-primary" href="/admin/?page=races&amp;section=<?= admin_h($section_key) ?>&amp;action=add">
                Add Race
            </a>
        <?php endif; ?>

        <a class="btn btn-secondary" href="/admin/">Back to Dashboard</a>
    </div>
</div>

<?php if ($message !== ''): ?>
    <div class="alert alert-success"><?= admin_h($message) ?></div>
<?php endif; ?>

<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?= admin_h($error) ?></div>
<?php endif; ?>

<?php if ($show_form): ?>
    <div class="card mb-4">
        <div class="card-header">
            <?= ((int) $edit_entry['id'] > 0) ? 'Edit Race: ' . admin_h($edit_entry['name']) : 'Add Race' ?>
        </div>

        <div class="card-body">
            <form method="post" action="<?= admin_h(admin_races_form_url($section_key, (int) $edit_entry['id'] > 0 ? 'edit' : 'add', (int) $edit_entry['id'])) ?>">
                <?= build_hidden_fields([
                    'id' => (int) $edit_entry['id'],
                ]) ?>

                <?= admin_form_token($form_key) ?>

                <div class="accordion" id="raceFormAccordion">

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingBasic">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasic" aria-expanded="true" aria-controls="collapseBasic">
                                Basic Information
                            </button>
                        </h2>

                        <div id="collapseBasic" class="accordion-collapse collapse show" aria-labelledby="headingBasic" data-bs-parent="#raceFormAccordion">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="name">Race Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?= admin_h($edit_entry['name']) ?>" maxlength="150" required>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="slug">Slug</label>
                                        <input type="text" class="form-control" id="slug" name="slug" value="<?= admin_h($edit_entry['slug']) ?>" maxlength="180" placeholder="auto-created from name if blank">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="side">Side</label>
                                        <select class="form-select" id="side" name="side">
                                            <?php foreach (['Alliance', 'Horde', 'Neutral'] as $side_option): ?>
                                                <option value="<?= admin_h($side_option) ?>" <?= $edit_entry['side'] === $side_option ? 'selected' : '' ?>>
                                                    <?= admin_h($side_option) ?>
                                                </option>
                                            <?php endforeach; ?>

                                            <?php if ($edit_entry['side'] !== '' && !in_array($edit_entry['side'], ['Alliance', 'Horde', 'Neutral'], true)): ?>
                                                <option value="<?= admin_h($edit_entry['side']) ?>" selected>
                                                    <?= admin_h($edit_entry['side']) ?>
                                                </option>
                                            <?php endif; ?>
                                        </select>
                                        <div class="form-text">Neutral displays publicly as Alliance / Horde.</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="sort_order">Sort Order</label>
                                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?= (int) $edit_entry['sort_order'] ?>">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="is_active">Status</label>
                                        <select class="form-select" id="is_active" name="is_active">
                                            <option value="1" <?= ((int) $edit_entry['is_active'] === 1) ? 'selected' : '' ?>>Active</option>
                                            <option value="0" <?= ((int) $edit_entry['is_active'] === 0) ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingImages">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseImages" aria-expanded="false" aria-controls="collapseImages">
                                Images
                            </button>
                        </h2>

                        <div id="collapseImages" class="accordion-collapse collapse" aria-labelledby="headingImages" data-bs-parent="#raceFormAccordion">
                            <div class="accordion-body">
                                <div class="mb-3">
                                    <label class="form-label" for="background_url">Background Image URL / Path</label>
                                    <input type="text" class="form-control" id="background_url" name="background_url" value="<?= admin_h($edit_entry['background_url']) ?>" maxlength="500">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="character_image_url">Character Image URL / Path</label>
                                    <input type="text" class="form-control" id="character_image_url" name="character_image_url" value="<?= admin_h($edit_entry['character_image_url']) ?>" maxlength="500">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="leader_image_url">Leader Image URL / Path</label>
                                    <input type="text" class="form-control" id="leader_image_url" name="leader_image_url" value="<?= admin_h($edit_entry['leader_image_url']) ?>" maxlength="500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingStory">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStory" aria-expanded="false" aria-controls="collapseStory">
                                Intro / History / Locations
                            </button>
                        </h2>

                        <div id="collapseStory" class="accordion-collapse collapse" aria-labelledby="headingStory" data-bs-parent="#raceFormAccordion">
                            <div class="accordion-body">
                                <div class="mb-3">
                                    <label class="form-label" for="intro_title">Intro Title</label>
                                    <input type="text" class="form-control" id="intro_title" name="intro_title" value="<?= admin_h($edit_entry['intro_title']) ?>" maxlength="255">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="intro_text">Intro Text</label>
                                    <textarea class="form-control" id="intro_text" name="intro_text" rows="8"><?= admin_h($edit_entry['intro_text']) ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="history_text">History Text</label>
                                    <textarea class="form-control" id="history_text" name="history_text" rows="8"><?= admin_h($edit_entry['history_text']) ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="start_zone">Start Zone</label>
                                        <input type="text" class="form-control" id="start_zone" name="start_zone" value="<?= admin_h($edit_entry['start_zone']) ?>" maxlength="150">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="home_city">Home City</label>
                                        <input type="text" class="form-control" id="home_city" name="home_city" value="<?= admin_h($edit_entry['home_city']) ?>" maxlength="150">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="start_zone_text">Start Zone Text</label>
                                    <textarea class="form-control" id="start_zone_text" name="start_zone_text" rows="6"><?= admin_h($edit_entry['start_zone_text']) ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="home_city_text">Home City Text</label>
                                    <textarea class="form-control" id="home_city_text" name="home_city_text" rows="6"><?= admin_h($edit_entry['home_city_text']) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingLeader">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLeader" aria-expanded="false" aria-controls="collapseLeader">
                                Leader
                            </button>
                        </h2>

                        <div id="collapseLeader" class="accordion-collapse collapse" aria-labelledby="headingLeader" data-bs-parent="#raceFormAccordion">
                            <div class="accordion-body">
                                <div class="mb-3">
                                    <label class="form-label" for="leader">Leader</label>
                                    <input type="text" class="form-control" id="leader" name="leader" value="<?= admin_h($edit_entry['leader']) ?>" maxlength="150">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="leader_text">Leader Text</label>
                                    <textarea class="form-control" id="leader_text" name="leader_text" rows="6"><?= admin_h($edit_entry['leader_text']) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTraits">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTraits" aria-expanded="false" aria-controls="collapseTraits">
                                Traits / Classes
                            </button>
                        </h2>

                        <div id="collapseTraits" class="accordion-collapse collapse" aria-labelledby="headingTraits" data-bs-parent="#raceFormAccordion">
                            <div class="accordion-body">
                                <div class="mb-3">
                                    <label class="form-label" for="traits_title">Traits Title</label>
                                    <input type="text" class="form-control" id="traits_title" name="traits_title" value="<?= admin_h($edit_entry['traits_title']) ?>" maxlength="255">
                                </div>

                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <div class="border rounded p-3 mb-3">
                                        <h6>Trait <?= $i ?></h6>

                                        <div class="mb-3">
                                            <label class="form-label" for="trait<?= $i ?>_name">Trait <?= $i ?> Name</label>
                                            <input type="text" class="form-control" id="trait<?= $i ?>_name" name="trait<?= $i ?>_name" value="<?= admin_h($edit_entry['trait' . $i . '_name']) ?>" maxlength="150">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="trait<?= $i ?>_image_url">Trait <?= $i ?> Image URL / Path</label>
                                            <input type="text" class="form-control" id="trait<?= $i ?>_image_url" name="trait<?= $i ?>_image_url" value="<?= admin_h($edit_entry['trait' . $i . '_image_url']) ?>" maxlength="500">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="trait<?= $i ?>_text">Trait <?= $i ?> Text</label>
                                            <textarea class="form-control" id="trait<?= $i ?>_text" name="trait<?= $i ?>_text" rows="4"><?= admin_h($edit_entry['trait' . $i . '_text']) ?></textarea>
                                        </div>
                                    </div>
                                <?php endfor; ?>

                                <div class="mb-3">
                                    <label class="form-label" for="classes_title">Classes Title</label>
                                    <input type="text" class="form-control" id="classes_title" name="classes_title" value="<?= admin_h($edit_entry['classes_title']) ?>" maxlength="255">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="available_classes">Available Classes</label>
                                    <input type="text" class="form-control" id="available_classes" name="available_classes" value="<?= admin_h($edit_entry['available_classes']) ?>" maxlength="1000" placeholder="warrior,hunter,mage">
                                    <div class="form-text">Comma-separated class slugs or names. These display as links to /wow/classes/{class}.</div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingMountArmor">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMountArmor" aria-expanded="false" aria-controls="collapseMountArmor">
                                Racial Mount / Heritage Armor
                            </button>
                        </h2>

                        <div id="collapseMountArmor" class="accordion-collapse collapse" aria-labelledby="headingMountArmor" data-bs-parent="#raceFormAccordion">
                            <div class="accordion-body">
                                <h5>Racial Mount</h5>

                                <div class="mb-3">
                                    <label class="form-label" for="racial_mount_title">Racial Mount Title</label>
                                    <input type="text" class="form-control" id="racial_mount_title" name="racial_mount_title" value="<?= admin_h($edit_entry['racial_mount_title']) ?>" maxlength="255">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="racial_mount_image_url">Racial Mount Image URL / Path</label>
                                    <input type="text" class="form-control" id="racial_mount_image_url" name="racial_mount_image_url" value="<?= admin_h($edit_entry['racial_mount_image_url']) ?>" maxlength="500">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="racial_mount_text">Racial Mount Text</label>
                                    <textarea class="form-control" id="racial_mount_text" name="racial_mount_text" rows="5"><?= admin_h($edit_entry['racial_mount_text']) ?></textarea>
                                </div>

                                <hr>

                                <h5>Heritage Armor</h5>

                                <div class="mb-3">
                                    <label class="form-label" for="heritage_armor_title">Heritage Armor Title</label>
                                    <input type="text" class="form-control" id="heritage_armor_title" name="heritage_armor_title" value="<?= admin_h($edit_entry['heritage_armor_title']) ?>" maxlength="255">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="heritage_armor_image_url">Heritage Armor Image URL / Path</label>
                                    <input type="text" class="form-control" id="heritage_armor_image_url" name="heritage_armor_image_url" value="<?= admin_h($edit_entry['heritage_armor_image_url']) ?>" maxlength="500">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="heritage_armor_text">Heritage Armor Text</label>
                                    <textarea class="form-control" id="heritage_armor_text" name="heritage_armor_text" rows="5"><?= admin_h($edit_entry['heritage_armor_text']) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="mt-4">
                    <button type="submit" name="save" value="1" class="btn btn-primary">Save Race</button>
                    <a class="btn btn-outline-secondary" href="/admin/?page=races&amp;section=<?= admin_h($section_key) ?>">Cancel</a>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if (!$show_form): ?>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                Existing Races
                <span class="text-secondary small">
                    <?= number_format($pagination['total']) ?> total
                </span>
            </div>

            <div class="d-flex gap-2 align-items-center">
                <?php admin_per_page_selector('races', $section_key, $pagination); ?>

                <a class="btn btn-sm btn-primary" href="/admin/?page=races&amp;section=<?= admin_h($section_key) ?>&amp;action=add">
                    Add Race
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 70px;">Sort</th>
                        <th style="width: 110px;">Image</th>
                        <th>Name</th>
                        <th>Side</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th style="width: 180px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!$entries): ?>
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-4">
                                No races have been added yet.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($entries as $entry): ?>
                        <?php
                        $entry_id = (int) $entry['id'];
                        $delete_hash = generate_link_hash('admin_races_delete_' . $entry_id);
                        $restore_hash = generate_link_hash('admin_races_restore_' . $entry_id);
                        ?>
                        <tr class="<?= ((int) $entry['is_active'] === 0) ? 'table-secondary opacity-75' : '' ?>">
                            <td><?= (int) $entry['sort_order'] ?></td>

                            <td>
                                <?php if (!empty($entry['character_image_url'])): ?>
                                    <a href="<?= admin_h($entry['character_image_url']) ?>" target="_blank" rel="noopener">
                                        <img src="<?= admin_h($entry['character_image_url']) ?>" alt="" class="img-thumbnail" style="max-width: 90px; height: auto;" loading="lazy">
                                    </a>
                                <?php else: ?>
                                    <span class="text-secondary small">No image</span>
                                <?php endif; ?>
                            </td>

                            <td><strong><?= admin_h($entry['name']) ?></strong></td>
                            <td><?= admin_h($entry['side']) ?></td>
                            <td><code><?= admin_h($entry['slug']) ?></code></td>

                            <td>
                                <?php if ((int) $entry['is_active'] === 1): ?>
                                    <span class="badge text-bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge text-bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="/admin/?page=races&amp;section=<?= admin_h($section_key) ?>&amp;action=edit&amp;id=<?= $entry_id ?>">Edit</a>

                                <?php if ((int) $entry['is_active'] === 1): ?>
                                    <a class="btn btn-sm btn-outline-danger" href="/admin/?page=races&amp;section=<?= admin_h($section_key) ?>&amp;action=delete&amp;id=<?= $entry_id ?>&amp;hash=<?= admin_h($delete_hash) ?>" onclick="return confirm('Disable this race?');">Disable</a>
                                <?php else: ?>
                                    <a class="btn btn-sm btn-outline-success" href="/admin/?page=races&amp;section=<?= admin_h($section_key) ?>&amp;action=restore&amp;id=<?= $entry_id ?>&amp;hash=<?= admin_h($restore_hash) ?>">Restore</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php admin_pagination_controls('races', $section_key, $pagination); ?>
    </div>
<?php endif; ?>
