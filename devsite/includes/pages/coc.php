<?php
declare(strict_types=1);


require_once __DIR__ . '/../layout/framework-helpers.php';
?>

<div class="container-fluid">
    <div class="row">
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
                        Code of Honor
                    </h2>
                </div>
                <div class="card-body lh-lg">
                    <p class="lead">
                        We are a guild based on Honor.
                    </p>
                    <p>
                        It is our base, our foundation, our point of stability.
                        It defines who we are.
                    </p>
                    <hr>
                    <h3 class="h5 text-center">
                        What is Honor?
                    </h3>
                    <p>
                        Honor is a code of integrity, dignity, and pride.
                        It is a truth of character, expressed through one's
                        interactions with others.
                    </p>
                    <p>
                        Honor is something which rightfully attracts esteem,
                        respect, or consideration from others.
                    </p>
                    <p>
                        Honor gives oneself a feeling of self-respect, dignity,
                        courage, excellence of character, and nobleness.
                    </p>
                    <blockquote class="blockquote border-start ps-3 mt-4">
                        <p>
                            This is my definition, this is who we are,
                            what we strive for.
                        </p>
                        <footer class="blockquote-footer text-secondary">
                            If this does not hold true for you,
                            you will not fit in here.
                        </footer>
                    </blockquote>
                    <hr>
                    <h3 class="h5">
                        Guild Expectations
                    </h3>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-dark text-light border-secondary">
                            We do not tolerate whiners, beggars, thieves,
                            kill-stealers, grief players, or similar behavior.
                        </li>
                        <li class="list-group-item bg-dark text-light border-secondary">
                            We do not tolerate "sore losers" or "quitters".
                            Be gracious in victory as well as defeat.
                        </li>
                        <li class="list-group-item bg-dark text-light border-secondary">
                            Trash talk or disrespectful behavior
                            <strong>will never be acceptable.</strong>
                        </li>
                        <li class="list-group-item bg-dark text-light border-secondary">
                            Abnormal amounts of foul language or lewd behavior
                            will not be tolerated. Some of us have (or are) kids
                            and do not want to be exposed to that type of behavior.
                        </li>
                        <li class="list-group-item bg-dark text-light border-secondary">
                            We do not and will not tolerate sexism, racism,
                            or prejudice of any kind.
                            They do not belong in our clan.
                        </li>
                        <li class="list-group-item bg-dark text-light border-secondary">
                            Any member bad-mouthing another member publicly
                            may face disciplinary action.
                            Resolve issues privately or bring them to leadership
                            for mediation.
                        </li>
                        <li class="list-group-item bg-dark text-light border-secondary">
                            The clan comes before the individual.
                            Members should be willing to put clan needs ahead
                            of their own, while the clan works to support its members.
                        </li>
                    </ul>
                </div>
            </div>
        </main>
        <?php render_right_sidebar($section_key ?? null); ?>
    </div>
</div>