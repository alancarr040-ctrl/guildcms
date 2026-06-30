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
                    <h2 class="h4 mb-0">So You Want to Join The Regs?</h2>
                </div>

                <div class="card-body lh-lg">
                    <p>
                        Due to issues that have arisen from an open recruitment policy, guild leadership has introduced
                        this policy effective immediately.
                    </p>

                    <p>
                        Anyone wishing to join the Regulators is free to post on our
                        <a target="_blank" rel="noopener" href="/forums/viewforum.php?f=206">Recruitment Forum</a>.
                        Possible recruits must be vouched for by two members in good standing or one guild leader, and
                        must be over level 50.
                    </p>

                    <p>
                        A screenshot of your login screen must be sent to
                        <a href="mailto:recruitment@theregs.org">recruitment@theregs.org</a>
                        before your application will be considered.
                    </p>

                    <hr>

                    <h3 class="h5">Good Standing</h3>

                    <p>
                        To be considered in good standing, guild members must not be on any form of probation. This
                        includes, but is not limited to, new members currently serving their 30-day probationary period.
                        The Recruitment Liaison has final say on whether someone is accepted into the guild.
                    </p>

                    <h3 class="h5">Probationary Period</h3>

                    <p>
                        After an individual is accepted into the guild, they will be placed on a 30-day probationary
                        period. During this time, the individual will not be allowed access to the Regs XP Chain,
                        the internal website, the guild or allied internal forums, and may not accept new vassals.
                    </p>

                    <p>
                        During the probationary period, the individual is allowed only one warning before removal from
                        the guild. They may not swear back into the guild until the Recruitment Liaison feels they may.
                    </p>

                    <p class="mb-0">
                        Once the individual has completed the 30-day probationary period, they will be instated as a full
                        member with all rights afforded to guild members.
                    </p>
                </div>
            </div>
        </main>

        <?php render_right_sidebar($section_key ?? null); ?>
    </div>
</div>