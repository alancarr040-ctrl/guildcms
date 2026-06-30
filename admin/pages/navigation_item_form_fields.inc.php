<?php
/*
 * Shared Navigation Item form fields.
 * Expects: $groups, $section, $item_types, $edit_item
 */

$current = is_array($edit_item ?? null) ? $edit_item : [];
$selected_group_id = (int) ($current['group_id'] ?? $request->variable('group_id', 0));
?>
<div class="mb-3">
    <label class="form-label">Group</label>
    <select class="form-select" name="group_id">
        <?php foreach ($groups as $group_id => $group): ?>
            <option value="<?= (int) $group_id ?>"<?= $selected_group_id === (int)$group_id ? ' selected' : '' ?>>
                <?= admin_nav_h($group['title'] ?: '(Untitled Group)') ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Type</label>
    <select class="form-select" name="item_type">
        <?php foreach ($item_types as $key => $label): ?>
            <option value="<?= admin_nav_h($key) ?>"<?= ($current['item_type'] ?? 'link') === $key ? ' selected' : '' ?>>
                <?= admin_nav_h($label) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Title</label>
    <input class="form-control" name="title" maxlength="150" required value="<?= admin_nav_h($current['title'] ?? '') ?>">
</div>

<div class="mb-3">
    <label class="form-label">URL</label>
    <input class="form-control" name="url" maxlength="500" value="<?= admin_nav_h($current['url'] ?? '') ?>">
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Article Section</label>
        <input class="form-control" name="article_section_key" maxlength="32" placeholder="<?= admin_nav_h($section) ?>" value="<?= admin_nav_h($current['article_section_key'] ?? '') ?>">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Category</label>
        <input class="form-control" name="article_category" maxlength="80" value="<?= admin_nav_h($current['article_category'] ?? '') ?>">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Slug</label>
        <input class="form-control" name="article_slug" maxlength="160" value="<?= admin_nav_h($current['article_slug'] ?? '') ?>">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">HTML Widget</label>
    <textarea class="form-control" name="html" rows="4"><?= admin_nav_h($current['html'] ?? '') ?></textarea>
</div>

<div class="form-check mb-3">
    <input class="form-check-input" name="target_blank" type="checkbox" value="1" id="target_blank"<?= (int)($current['target_blank'] ?? 0) === 1 ? ' checked' : '' ?>>
    <label class="form-check-label" for="target_blank">Open in new window</label>
</div>

<div class="mb-3">
    <label class="form-label">Sort Order</label>
    <input class="form-control" name="sort_order" type="number" value="<?= (int)($current['sort_order'] ?? 0) ?>">
</div>

<button class="btn btn-<?= $current ? 'primary' : 'success' ?>" type="submit">
    <?= $current ? 'Save Item' : 'Add Item' ?>
</button>

<?php if ($current): ?>
    <a class="btn btn-outline-light" href="?page=navigation&amp;section=<?= admin_nav_h($section) ?>">Cancel</a>
<?php endif; ?>
