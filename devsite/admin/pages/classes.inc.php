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

$section_name = $site_sections[$section_key]['name'] ?? 'World of Warcraft';

$form_key = 'admin_wow_classes';
add_form_key($form_key);

$message = '';
$error = '';

$action = $request->variable('action', '');
$id = $request->variable('id', 0);

if ($request->variable('added', 0) === 1) {
    $message = 'Class added.';
}

if ($request->variable('updated', 0) === 1) {
    $message = 'Class updated.';
}

function admin_wow_class_slug(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/i', '-', $value);
    $value = trim((string) $value, '-');

    return $value !== '' ? $value : 'class-entry';
}

function admin_wow_class_decode_html(string $value): string
{
    $previous = null;
    $decoded = $value;

    while ($decoded !== $previous) {
        $previous = $decoded;
        $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    return $decoded;
}

function admin_redirect_wow_classes(string $section_key): void
{
    redirect('/admin/?page=classes&section=' . rawurlencode($section_key));
}

function admin_wow_classes_form_url(string $section_key, string $action, int $id = 0): string
{
    $url = '/admin/?page=classes&section=' . rawurlencode($section_key) . '&action=' . rawurlencode($action);

    if ($id > 0) {
        $url .= '&id=' . $id;
    }

    return $url;
}

$edit_entry = [
    'id' => 0,
    'name' => '',
    'used1' => '',
    'used2' => '',
    'used3' => '',
    'class_back' => '',
    'char_image' => '',
    'title1' => '',
    'text1' => '',
    'class_info_title' => '',
    'class_info' => '',
    'spec_title' => '',
    'spec' => '',
    'features_title' => '',
];

for ($i = 1; $i <= 4; $i++) {
    $edit_entry['spec' . $i] = '';
    $edit_entry['spec' . $i . '_img'] = '';
    $edit_entry['feat' . $i] = '';
    $edit_entry['feat' . $i . '_img'] = '';
    $edit_entry['feat' . $i . '_txt'] = '';
}

if ($request->is_set_post('save')) {
    if (!check_form_key($form_key)) {
        $error = 'Invalid form submission. Please try again.';
    } else {
        $id = $request->variable('id', 0);

        $data = [
            'name' => trim($request->variable('name', '', true)),
            'used1' => trim($request->variable('used1', '', true)),
            'used2' => trim($request->variable('used2', '', true)),
            'used3' => trim($request->variable('used3', '', true)),
            'class_back' => trim($request->variable('class_back', '', true)),
            'char_image' => trim($request->variable('char_image', '', true)),
            'title1' => trim($request->variable('title1', '', true)),
            'text1' => admin_wow_class_decode_html(trim($request->variable('text1', '', true))),
            'class_info_title' => trim($request->variable('class_info_title', '', true)),
            'class_info' => admin_wow_class_decode_html(trim($request->variable('class_info', '', true))),
            'spec_title' => trim($request->variable('spec_title', '', true)),
            'spec' => admin_wow_class_decode_html(trim($request->variable('spec', '', true))),
            'features_title' => trim($request->variable('features_title', '', true)),
        ];

        for ($i = 1; $i <= 4; $i++) {
            $data['spec' . $i] = trim($request->variable('spec' . $i, '', true));
            $data['spec' . $i . '_img'] = trim($request->variable('spec' . $i . '_img', '', true));
            $data['feat' . $i] = trim($request->variable('feat' . $i, '', true));
            $data['feat' . $i . '_img'] = trim($request->variable('feat' . $i . '_img', '', true));
            $data['feat' . $i . '_txt'] = admin_wow_class_decode_html(trim($request->variable('feat' . $i . '_txt', '', true)));
        }

        $edit_entry = array_merge($edit_entry, ['id' => $id], $data);

        if ($data['name'] === '') {
            $error = 'Class name is required.';
        }

        $url_fields = [
            'Class background' => $data['class_back'],
            'Character/class image' => $data['char_image'],
        ];

        for ($i = 1; $i <= 4; $i++) {
            $url_fields['Spec ' . $i . ' image'] = $data['spec' . $i . '_img'];
            $url_fields['Feature ' . $i . ' image'] = $data['feat' . $i . '_img'];
        }

        foreach ($url_fields as $label => $value) {
            if ($value !== '' && !filter_var($value, FILTER_VALIDATE_URL) && str_starts_with($value, '/') === false) {
                $error = $label . ' must be a valid URL or local path beginning with /.';
                break;
            }
        }

        if ($error === '') {
            if ($id > 0) {
                $sql = 'UPDATE wow_class SET ' . $db->sql_build_array('UPDATE', $data) . '
                        WHERE id = ' . (int) $id;
                $db->sql_query($sql);
                redirect('/admin/?page=classes&section=' . rawurlencode($section_key) . '&updated=1');
            } else {
                $sql = 'INSERT INTO wow_class ' . $db->sql_build_array('INSERT', $data);
                $db->sql_query($sql);
                redirect('/admin/?page=classes&section=' . rawurlencode($section_key) . '&added=1');
            }
        }

        $action = ($id > 0) ? 'edit' : 'add';
    }
}

if ($action === 'delete' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_wow_classes_delete_' . $id)) {
        trigger_error('Invalid delete request.');
    }

    $sql = 'DELETE FROM wow_class WHERE id = ' . (int) $id;
    $db->sql_query($sql);
    admin_redirect_wow_classes($section_key);
}

if ($action === 'edit' && $id > 0 && !$request->is_set_post('save')) {
    $sql = 'SELECT * FROM wow_class WHERE id = ' . (int) $id;
    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    if ($row) {
        $edit_entry = array_merge($edit_entry, $row);
    } else {
        $error = 'Class entry was not found.';
        $action = '';
    }
}

$show_form = ($action === 'add' || ($action === 'edit' && (int) $edit_entry['id'] > 0));

$pagination = admin_get_pagination($request, $db, 'SELECT COUNT(*) AS total FROM wow_class');

$sql = 'SELECT * FROM wow_class ORDER BY name ASC';
$result = $db->sql_query_limit($sql, $pagination['per_page'], $pagination['offset']);

$entries = [];

while ($row = $db->sql_fetchrow($result)) {
    $entries[] = $row;
}

$db->sql_freeresult($result);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Manage WoW Classes</h1>
        <div class="text-secondary"><?= admin_h($section_name) ?></div>
    </div>

    <div class="d-flex gap-2">
        <?php if ($show_form): ?>
            <a class="btn btn-outline-secondary" href="/admin/?page=classes&amp;section=<?= admin_h($section_key) ?>">Back to Classes</a>
        <?php else: ?>
            <a class="btn btn-primary" href="/admin/?page=classes&amp;section=<?= admin_h($section_key) ?>&amp;action=add">Add Class</a>
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
        <div class="card-header"><?= ((int) $edit_entry['id'] > 0) ? 'Edit Class: ' . admin_h($edit_entry['name']) : 'Add Class' ?></div>

        <div class="card-body">
            <form method="post" action="<?= admin_h(admin_wow_classes_form_url($section_key, (int) $edit_entry['id'] > 0 ? 'edit' : 'add', (int) $edit_entry['id'])) ?>">
                <?= build_hidden_fields(['id' => (int) $edit_entry['id']]) ?>
                <?= admin_form_token($form_key) ?>

                <div class="accordion" id="classFormAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingBasic">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasic" aria-expanded="true" aria-controls="collapseBasic">Basic Information</button>
                        </h2>
                        <div id="collapseBasic" class="accordion-collapse collapse show" aria-labelledby="headingBasic" data-bs-parent="#classFormAccordion">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="name">Class Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?= admin_h($edit_entry['name']) ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="char_image">Class Icon / Image URL / Path</label>
                                        <input type="text" class="form-control" id="char_image" name="char_image" value="<?= admin_h($edit_entry['char_image']) ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="class_back">Background Image URL / Path</label>
                                    <input type="text" class="form-control" id="class_back" name="class_back" value="<?= admin_h($edit_entry['class_back']) ?>">
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3"><label class="form-label" for="used1">Used 1</label><input type="text" class="form-control" id="used1" name="used1" value="<?= admin_h($edit_entry['used1']) ?>"></div>
                                    <div class="col-md-4 mb-3"><label class="form-label" for="used2">Used 2</label><input type="text" class="form-control" id="used2" name="used2" value="<?= admin_h($edit_entry['used2']) ?>"></div>
                                    <div class="col-md-4 mb-3"><label class="form-label" for="used3">Used 3</label><input type="text" class="form-control" id="used3" name="used3" value="<?= admin_h($edit_entry['used3']) ?>"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingIntro"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseIntro">Intro / Class Info</button></h2>
                        <div id="collapseIntro" class="accordion-collapse collapse" data-bs-parent="#classFormAccordion">
                            <div class="accordion-body">
                                <div class="mb-3"><label class="form-label" for="title1">Intro Title</label><input type="text" class="form-control" id="title1" name="title1" value="<?= admin_h($edit_entry['title1']) ?>"></div>
                                <div class="mb-3"><label class="form-label" for="text1">Intro Text</label><textarea class="form-control" id="text1" name="text1" rows="8"><?= admin_h($edit_entry['text1']) ?></textarea></div>
                                <div class="mb-3"><label class="form-label" for="class_info_title">Class Info Title</label><input type="text" class="form-control" id="class_info_title" name="class_info_title" value="<?= admin_h($edit_entry['class_info_title']) ?>"></div>
                                <div class="mb-3"><label class="form-label" for="class_info">Class Info</label><textarea class="form-control" id="class_info" name="class_info" rows="8"><?= admin_h($edit_entry['class_info']) ?></textarea></div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSpecs"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSpecs">Specializations</button></h2>
                        <div id="collapseSpecs" class="accordion-collapse collapse" data-bs-parent="#classFormAccordion">
                            <div class="accordion-body">
                                <div class="mb-3"><label class="form-label" for="spec_title">Specialization Title</label><input type="text" class="form-control" id="spec_title" name="spec_title" value="<?= admin_h($edit_entry['spec_title']) ?>"></div>
                                <div class="mb-3"><label class="form-label" for="spec">Specialization Intro</label><textarea class="form-control" id="spec" name="spec" rows="5"><?= admin_h($edit_entry['spec']) ?></textarea></div>
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                    <div class="border rounded p-3 mb-3">
                                        <h6>Spec <?= $i ?></h6>
                                        <div class="mb-3"><label class="form-label" for="spec<?= $i ?>">Spec <?= $i ?> Name</label><input type="text" class="form-control" id="spec<?= $i ?>" name="spec<?= $i ?>" value="<?= admin_h($edit_entry['spec' . $i]) ?>"></div>
                                        <div class="mb-3"><label class="form-label" for="spec<?= $i ?>_img">Spec <?= $i ?> Image URL / Path</label><input type="text" class="form-control" id="spec<?= $i ?>_img" name="spec<?= $i ?>_img" value="<?= admin_h($edit_entry['spec' . $i . '_img']) ?>"></div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFeatures"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFeatures">Features</button></h2>
                        <div id="collapseFeatures" class="accordion-collapse collapse" data-bs-parent="#classFormAccordion">
                            <div class="accordion-body">
                                <div class="mb-3"><label class="form-label" for="features_title">Features Title</label><input type="text" class="form-control" id="features_title" name="features_title" value="<?= admin_h($edit_entry['features_title']) ?>"></div>
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                    <div class="border rounded p-3 mb-3">
                                        <h6>Feature <?= $i ?></h6>
                                        <div class="mb-3"><label class="form-label" for="feat<?= $i ?>">Feature <?= $i ?> Name</label><input type="text" class="form-control" id="feat<?= $i ?>" name="feat<?= $i ?>" value="<?= admin_h($edit_entry['feat' . $i]) ?>"></div>
                                        <div class="mb-3"><label class="form-label" for="feat<?= $i ?>_img">Feature <?= $i ?> Image URL / Path</label><input type="text" class="form-control" id="feat<?= $i ?>_img" name="feat<?= $i ?>_img" value="<?= admin_h($edit_entry['feat' . $i . '_img']) ?>"></div>
                                        <div class="mb-3"><label class="form-label" for="feat<?= $i ?>_txt">Feature <?= $i ?> Text</label><textarea class="form-control" id="feat<?= $i ?>_txt" name="feat<?= $i ?>_txt" rows="4"><?= admin_h($edit_entry['feat' . $i . '_txt']) ?></textarea></div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" name="save" value="1" class="btn btn-primary">Save Class</button>
                    <a class="btn btn-outline-secondary" href="/admin/?page=classes&amp;section=<?= admin_h($section_key) ?>">Cancel</a>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if (!$show_form): ?>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>Existing WoW Classes <span class="text-secondary small"><?= number_format($pagination['total']) ?> total</span></div>
            <div class="d-flex gap-2 align-items-center">
                <?php admin_per_page_selector('classes', $section_key, $pagination); ?>
                <a class="btn btn-sm btn-primary" href="/admin/?page=classes&amp;section=<?= admin_h($section_key) ?>&amp;action=add">Add Class</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead><tr><th style="width:90px;">Image</th><th>Name</th><th>Specs</th><th style="width:160px;">Actions</th></tr></thead>
                <tbody>
                    <?php if (!$entries): ?>
                        <tr><td colspan="4" class="text-center text-secondary py-4">No classes have been added yet.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($entries as $entry): ?>
                        <?php
                        $entry_id = (int) $entry['id'];
                        $delete_hash = generate_link_hash('admin_wow_classes_delete_' . $entry_id);
                        $specs = [];
                        for ($i = 1; $i <= 4; $i++) {
                            if (trim((string) $entry['spec' . $i]) !== '') {
                                $specs[] = trim((string) $entry['spec' . $i]);
                            }
                        }
                        ?>
                        <tr>
                            <td>
                                <?php if (!empty($entry['char_image'])): ?>
                                    <a href="<?= admin_h($entry['char_image']) ?>" target="_blank" rel="noopener"><img src="<?= admin_h($entry['char_image']) ?>" alt="" class="img-thumbnail" style="max-width:70px;height:auto;" loading="lazy"></a>
                                <?php else: ?>
                                    <span class="text-secondary small">No image</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= admin_h($entry['name']) ?></strong></td>
                            <td><?= admin_h(implode(', ', $specs)) ?></td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="/admin/?page=classes&amp;section=<?= admin_h($section_key) ?>&amp;action=edit&amp;id=<?= $entry_id ?>">Edit</a>
                                <a class="btn btn-sm btn-outline-danger" href="/admin/?page=classes&amp;section=<?= admin_h($section_key) ?>&amp;action=delete&amp;id=<?= $entry_id ?>&amp;hash=<?= admin_h($delete_hash) ?>" onclick="return confirm('Delete this class?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php admin_pagination_controls('classes', $section_key, $pagination); ?>
    </div>
<?php endif; ?>
