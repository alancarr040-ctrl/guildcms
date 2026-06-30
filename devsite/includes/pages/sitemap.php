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
        <h2 class="h4 mb-0">
            Site Map
        </h2>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Main Site -->
            <div class="col-md-6 mb-4">
                <h4>Main Site</h4>
                <ul class="list-unstyled">
                    <li><a href="/">Home</a></li>
                    <li><a href="/?page=about">About Us</a></li>
                    <li><a href="/?page=history">History</a></li>
                    <li><a href="/?page=honor">Code of Honor</a></li>
                    <li><a href="/?page=charter">Guild Charter</a></li>
                    <li><a href="/?page=recruitment">Recruitment</a></li>
                    <li><a href="/?page=links">Links</a></li>
                    <li><a href="/?page=contact">Contact</a></li>
                </ul>
            </div>
            <!-- Community -->
            <div class="col-md-6 mb-4">
                <h4>Community</h4>
                <ul class="list-unstyled">
                    <li><a href="/forums/">Forums</a></li>
                    <li><a href="/?page=gallery">Gallery</a></li>
                    <li><a href="/?page=privacy">Privacy Policy</a></li>
                    <li><a href="/?page=copyright">Copyright</a></li>
                </ul>
            </div>
            <!-- Games -->
            <div class="col-md-6 mb-4">
                <h4>Game Sections</h4>
                <ul class="list-unstyled">
                    <li><a href="/ac/">Asheron's Call</a></li>
                    <li><a href="/ao/">Anarchy Online</a></li>
                    <li><a href="/tsw/">The Secret World</a></li>
                    <li><a href="/wow/">World of Warcraft</a></li>
                    <li><a href="/cod/">Call of Duty</a></li>
                    <li><a href="/coh/">City of Heroes</a></li>
                    <li><a href="/eo/">Eve Online</a></li>
                    <li><a href="/fo76/">Fallout 76</a></li>
                </ul>
            </div>
            <!-- Game Features -->
            <div class="col-md-6 mb-4">
                <h4>Game Features</h4>
                <ul class="list-unstyled">
                    <li>Videos</li>
                    <li>Gallery</li>
                    <li>World Information</li>
                    <li>Races / Factions</li>
                    <li>Diplomacy</li>
                    <li>KOS Lists</li>
                </ul>
            </div>
        </div>
    </div>
</div>
</main>

<?php render_right_sidebar($section_key ?? null); ?>