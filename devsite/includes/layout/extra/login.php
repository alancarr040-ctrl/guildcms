<?php
declare(strict_types=1);

global $DB_HOST, $DB_NAME, $DB_PASS, $DB_USER;
global $user, $phpEx, $request;

if (!function_exists('login_block_h')) {
    function login_block_h(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

/* phpBB form token helper */
if (!function_exists('login_block_token')) {
    function login_block_token(string $form_key): string
    {
        global $user;

        $time = time();

        return build_hidden_fields([
            'creation_time' => $time,
            'form_token' => sha1(
                $time .
                $user->data['user_form_salt'] .
                $form_key
            ),
        ]);
    }
}

$forums_path = '/forums/';
$current_url = '/';

if (isset($request)) {
    $current_url = $request->server('REQUEST_URI', '/');
}


/*
 * Site admin check
 * Same logic as /admin/includes/admin_auth.php
 */
$is_site_admin =
    !empty($user->data['is_registered'])
    &&
    in_array((int)$user->data['user_id'], [2], true);

$rank_title = '';
$rank_img = '';
$theme = 'Default';
/* Load extra phpBB info */
$link = mysqli_connect(
    $DB_HOST,
    $DB_USER,
    $DB_PASS,
    $DB_NAME
);

if ($link) {
    mysqli_set_charset($link, 'utf8mb4');
    /* Rank */
    if (!empty($user->data['user_rank'])) {

        $stmt = mysqli_prepare(
            $link,
            "SELECT rank_title, rank_image
             FROM phpbb3_ranks
             WHERE rank_id=?
             LIMIT 1"
        );

        if ($stmt) {

            $rank_id = (int)$user->data['user_rank'];

            mysqli_stmt_bind_param(
                $stmt,
                'i',
                $rank_id
            );

            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result(
                $stmt,
                $rank_title,
                $rank_img
            );

            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    /* Style */
    if (!empty($user->data['user_style'])) {
        $stmt = mysqli_prepare(
            $link,
            "SELECT style_name
             FROM phpbb3_styles
             WHERE style_id=?
             LIMIT 1"
        );

        if ($stmt) {
            $style_id = (int)$user->data['user_style'];
            mysqli_stmt_bind_param(
                $stmt,
                'i',
                $style_id
            );
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result(
                $stmt,
                $theme_name
            );

            if (mysqli_stmt_fetch($stmt)) {
                $theme = $theme_name;
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}
?>

<div class="card bg-dark text-light border-secondary">
    <div class="card-body small">
<?php if (!empty($user->data['is_registered'])): ?>
        <!-- USER INFO -->
        <div class="text-center mb-2">
            <strong>
                Welcome <?= login_block_h($user->data['username']) ?>
            </strong>
            <?php if (!empty($user->data['user_avatar'])): ?>
                <div class="mt-2">
                    <img
                        src="//cdn.theregs.org/forums/images/avatars/gallery/<?= login_block_h($user->data['user_avatar']) ?>"
                        class="img-fluid rounded"
                        style="max-width:90px;"
                        alt="Avatar"
                    >
                </div>
            <?php endif; ?>
            <?php if ($rank_title): ?>
                <div class="mt-2 text-warning">
                    <?= login_block_h($rank_title) ?>
                </div>
            <?php endif; ?>

            <?php if ($rank_img): ?>
                <img
                    src="//cdn.theregs.org/forums/images/ranks/<?= login_block_h($rank_img) ?>"
                    class="img-fluid"
                    alt="Rank"
                >
            <?php endif; ?>
        </div>
        <hr>
        <div>
            <strong>Joined:</strong><br>
            <?= date(
                'd F Y',
                (int)$user->data['user_regdate']
            ) ?>
        </div>
        <div class="mt-2">
            <strong>Posts:</strong>
            <?= number_format((int)$user->data['user_posts']) ?>
        </div>
        <hr>
        <div>
            <strong>Forum Style:</strong><br>
            <?= login_block_h($theme) ?>
        </div>
        <hr>
        <!-- phpBB LINKS -->
        <a class="d-block"
           href="<?= append_sid($forums_path . 'ucp.' . $phpEx) ?>">
            User CP
        </a>
        <a class="d-block"
           href="<?= append_sid($forums_path . 'ucp.' . $phpEx, 'i=pm&folder=inbox') ?>">
            Messages
            (<?= (int)$user->data['user_new_privmsg'] ?>)
        </a>
        <a class="d-block"
           href="<?= append_sid($forums_path . 'ucp.' . $phpEx, 'i=main&mode=bookmarks') ?>">
            Bookmarks
        </a>
        <a class="d-block"
           href="<?= append_sid($forums_path . 'ucp.' . $phpEx, 'i=main&mode=subscribed') ?>">
            Subscribed Posts
        </a>
        <hr>
        <a class="d-block"
           href="<?= append_sid($forums_path . 'search.' . $phpEx, 'search_id=newposts') ?>">
            View New Posts
        </a>
        <a class="d-block"
           href="<?= append_sid($forums_path . 'search.' . $phpEx, 'search_id=unreadposts') ?>">
            View Unread Posts
        </a>
        <a class="d-block"
           href="<?= append_sid($forums_path . 'search.' . $phpEx, 'search_id=egosearch') ?>">
            View Your Posts
        </a>

<?php if ($is_site_admin): ?>
        <hr>
        <a href="/admin/"
           class="btn btn-sm btn-outline-warning w-100">
            ⚙ Site Admin
        </a>
<?php endif; ?>
        <hr>
        <a class="btn btn-sm btn-outline-danger w-100"
           href="<?= append_sid($forums_path . 'ucp.' . $phpEx, 'mode=logout&sid=' . $user->session_id) ?>">
            Logout
        </a>

<?php else: ?>
        <!-- LOGIN FORM -->
        <form
            method="post"
            action="<?= $forums_path ?>ucp.php?mode=login"
        >
            <label class="form-label">
                Username
            </label>
            <input
                class="form-control form-control-sm"
                type="text"
                name="username"
            >
            <label class="form-label mt-2">
                Password
            </label>
            <input
                class="form-control form-control-sm"
                type="password"
                name="password"
            >
            <div class="form-check mt-2">
                <input
                    class="form-check-input"
                    type="checkbox"
                    name="autologin"
                    id="autologin"
                >
                <label class="form-check-label" for="autologin">
                    Remember Me
                </label>
            </div>
            <input
                type="hidden"
                name="redirect"
                value="<?= login_block_h($current_url) ?>"
            >
            <?= login_block_token('login') ?>
            <button
                class="btn btn-primary btn-sm w-100 mt-3"
                type="submit"
                name="login"
            >
                Login
            </button>
        </form>
<?php endif; ?>
    </div>
</div>