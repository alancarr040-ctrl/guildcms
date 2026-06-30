<?php
declare(strict_types=1);

require_once __DIR__ . '/../layout/framework-helpers.php';
?>

<!-- Mobile Sidebar Button -->
<button
    class="btn btn-outline-light d-md-none mb-3"
    type="button"
    data-bs-toggle="offcanvas"
    data-bs-target="#leftSidebar"
    aria-controls="leftSidebar"
>
    ☰ Menu
</button>

<!-- Mobile Sidebar -->
<div
    class="offcanvas offcanvas-start d-md-none text-bg-dark"
    tabindex="-1"
    id="leftSidebar"
    aria-labelledby="leftSidebarLabel"
>
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="leftSidebarLabel">
            Navigation
        </h5>

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
            <h2 class="h4 mb-0">Privacy Policy</h2>
        </div>

        <div class="card-body lh-lg">
            <p>
                Your privacy is very important to us. We have developed this policy so you can understand
                how we collect, use, communicate, disclose, and make use of personal information.
            </p>

            <p>
                The following outlines our privacy policy:
            </p>

            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item bg-dark text-light border-secondary">
                    Before or at the time of collecting personal information, we will identify the purposes
                    for which information is being collected.
                </li>

                <li class="list-group-item bg-dark text-light border-secondary">
                    We will collect and use personal information solely for the purposes specified by us,
                    or for other compatible purposes, unless we obtain the consent of the individual concerned
                    or as required by law.
                </li>

                <li class="list-group-item bg-dark text-light border-secondary">
                    We will only retain personal information as long as necessary for the fulfillment of those purposes.
                </li>

                <li class="list-group-item bg-dark text-light border-secondary">
                    We will collect personal information by lawful and fair means and, where appropriate,
                    with the knowledge or consent of the individual concerned.
                </li>

                <li class="list-group-item bg-dark text-light border-secondary">
                    Personal data should be relevant to the purposes for which it is to be used, and to the extent
                    necessary for those purposes, should be accurate, complete, and up-to-date.
                </li>

                <li class="list-group-item bg-dark text-light border-secondary">
                    We will protect personal information by reasonable security safeguards against loss or theft,
                    as well as unauthorized access, disclosure, copying, use, or modification.
                </li>

                <li class="list-group-item bg-dark text-light border-secondary">
                    We will make readily available information about our policies and practices relating to the
                    management of personal information.
                </li>
            </ul>

            <p class="mb-0">
                We are committed to conducting our business in accordance with these principles in order to ensure
                that the confidentiality of personal information is protected and maintained.
            </p>
        </div>
    </div>
</main>

<?php render_right_sidebar($section_key ?? null); ?>