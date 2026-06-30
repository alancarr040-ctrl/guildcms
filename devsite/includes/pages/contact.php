<?php
declare(strict_types=1);


require_once __DIR__ . '/../layout/framework-helpers.php';
global $user, $request;

$message = '';
$error = '';

$form_key = 'site_contact';
add_form_key($form_key);

$name = '';
$email = '';
$body = '';

if (!function_exists('site_form_token')) {
    function site_form_token(string $form_key): string
    {
        global $user;

        $creation_time = time();

        return build_hidden_fields([
            'creation_time' => $creation_time,
            'form_token' => sha1($creation_time . $user->data['user_form_salt'] . $form_key),
        ]);
    }
}

if (!empty($user->data['is_registered'])) {
    $name = $user->data['username'];
    $email = $user->data['user_email'];
}

if ($request->is_set_post('submit_contact')) {
    if (!check_form_key($form_key)) {
        $error = 'Invalid form submission. Please try again.';
    } else {
        $name = trim($request->variable('name', '', true));
        $email = trim($request->variable('email', '', true));
        $body = trim($request->variable('message', '', true));

        if ($name === '' || $email === '' || $body === '') {
            $error = 'Please complete all fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            $subject = 'The Regs Contact Form';
            $mail_body =
                "Contact request from The Regs\n\n" .
                "Name: {$name}\n" .
                "Email: {$email}\n\n" .
                $body;
            $headers =
                "From: noreply@theregs.org\r\n" .
                "Reply-To: {$email}\r\n";

            if (mail(
                'support@theregs.org',
                $subject,
                $mail_body,
                $headers
            )) {
                $message = 'Your message has been sent.';
                $body = '';
            } else {
                $error = 'Unable to send your message at this time.';
            }
        }
    }
}
?>

<?php
        /*
         * Mobile menu support.
         * Desktop keeps the normal left sidebar.
         * Mobile uses Bootstrap offcanvas so these pages match the article/home layout.
         */
        ?>
        <button
            class="btn btn-outline-light d-md-none mb-3 w-100"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#leftSidebar"
            aria-controls="leftSidebar"
        >
            ☰ Menu
        </button>

        <div
            class="offcanvas offcanvas-start text-bg-dark d-md-none"
            tabindex="-1"
            id="leftSidebar"
            aria-labelledby="leftSidebarLabel"
        >
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="leftSidebarLabel">The Regs</h5>
                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="offcanvas"
                    aria-label="Close"
                ></button>
            </div>
            <div class="offcanvas-body">
                <?php render_sidebar($section_key ?? null); ?>
            </div>
        </div>

        <aside class="col-md-2 d-none d-md-block sidebar-nav">
            <?php render_sidebar($section_key ?? null); ?>
        </aside>

<main
    class="col-md-8 text-light"
    style="background:url('//cdn.theregs.org/images/tsw/pixel.webp') repeat;"
>

<div class="card bg-dark border-secondary text-light my-4">
    <div class="card-header text-center">
        <h2 class="h4 mb-0">
            Contact Us
        </h2>
    </div>
    <div class="card-body">
        <p>
            Need to reach the site administrators?
            Send us a message below.
        </p>
        <?php if ($message): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label
                    for="name"
                    class="form-label"
                >
                    Your Name
                </label>
                <input
                    type="text"
                    class="form-control"
                    id="name"
                    name="name"
                    value="<?= htmlspecialchars($name) ?>"
                    required
                >
            </div>
            <div class="mb-3">
                <label
                    for="email"
                    class="form-label"
                >
                    Your Email
                </label>
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($email) ?>"
                    required
                >
            </div>
            <div class="mb-3">
                <label
                    for="message"
                    class="form-label"
                >
                    Message
                </label>
                <textarea
                    class="form-control"
                    id="message"
                    name="message"
                    rows="5"
                    required
                ><?= htmlspecialchars($body) ?></textarea>
            </div>
            <?= site_form_token($form_key) ?>
            <button
                type="submit"
                name="submit_contact"
                class="btn btn-primary"
            >
                Send Message
            </button>
        </form>
    </div>
</div>
</main>
<?php render_right_sidebar($section_key ?? null); ?>