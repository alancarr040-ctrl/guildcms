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

        <main class="col-md-8 text-light" style="background: url('//cdn.theregs.org/images/tsw/pixel.webp') repeat;">
            <div class="card bg-dark border-secondary text-light my-4">
                <div class="card-header text-center">
                    <h2 class="h4 mb-0">Membership / Recruiting Policies</h2>
                </div>

                <div class="card-body lh-lg">
                    <p>
                        The Regs are a like-minded group of Honor players on the Asheron's Call world of Darktide.
                        We don't claim towns, we don't own people, and we don't Random Player Kill (RPK).
                    </p>

                    <p>
                        Each member of DR has the freedom to take on the responsibility of becoming a patron.
                        However, it is required by the guild that all possible new recruits post on the Guild
                        Recruitment Forum and be vouched for by 2 members in good standing or 1 guild leader.
                        Of our members, we have the following rules/guidelines:
                    </p>

                    <ol class="mb-4">
                        <li class="mb-3">
                            <strong>NO RPKing.</strong>
                            If you suddenly decide that you wish to become a Random Player Killer, make sure you stop
                            and think about it, and be sure that it's what you want. When you're certain, inform the
                            guild, and especially your patron. If possible, your patron will break allegiance with you.
                            But you do <strong>NOT</strong> start to PK until you remove the DR tag. If you later decide
                            to return to the side of good, then your conduct during your "days of evil" will determine
                            if we will still consider you a "friend." You will not, however, be a Reg.
                        </li>

                        <li class="mb-3">
                            <strong>No begging.</strong>
                            Just because you've joined a guild, this does not entitle you to every weapon and piece of
                            armor you've ever wanted. Your patron will do his/her best to equip you, and time-permitting,
                            will hunt with you. Just do your best not to demand all of your patron's in-game time.
                        </li>

                        <li class="mb-3">
                            <strong>Respect our enemies.</strong>
                            Despite being evil, our enemies are worthy of respect. When defeating them in battle, pay a
                            parting homage to them, thanking them for the fight. When you loot, if you see something that
                            you think might be special to the fallen, and they have returned your respect, consider giving
                            them the special item back. But never mock them. Conversely, when falling in battle, be
                            gracious. Again, thank them for the fight, and congratulate them. If they ignore you, or
                            attempt to make fun of you, don't reply; they don't deserve your time. Special note: if the
                            player is continuously spamming you with "OWNED" messages, feel free to squelch them for a
                            short time, but strive to keep your Squelch list empty. If you squelch someone, you won't
                            hear their spell-words, which is a tactical weakness.
                        </li>

                        <li class="mb-3">
                            <strong>Take care of your vassals.</strong>
                            You're under no obligation to spend all of your time with your vassals, or to give every item
                            you find to them. But make sure that they have what they need to find their way through the
                            world. If you give them an exceptionally good weapon, make sure that they're hunting somewhere
                            relatively safe, and that the weapon is covered by death-items. Make sure your vassals know
                            to visit this site often, and introduce themselves to the rest of the guild. Treat your
                            vassals with respect, and if they ever decide to leave the guild, release them, update the
                            Guilds Tree, and thank them for the time they've spent with us.
                        </li>

                        <li class="mb-3">
                            <strong>Use the Forum, Web Site Messaging, and IRC.</strong>
                            If you need something, let us all know, and we'll all do our best to help. The forums let us
                            all express our opinions, tell our stories, make our jokes, and make fun of each other. Most
                            of all, it keeps us all informed of guild policies, enemies, friends, and other important news.
                        </li>

                        <li class="mb-3">
                            <strong>NO GEAR. No Speedhack.</strong>
                            We will <strong><em>not</em></strong> cheat. We don't care if some level 13 jerk uses it to
                            get a kill on you; we do not use Gear. Other add-ons such as Sixth Sense, Mageminder, etc.,
                            are yours to use at your own risk. With Sixth Sense, though, I do ask that you avoid using it
                            to track down your PK enemies. In essence, any 3rd party utility that gives you an advantage
                            over a foe is <strong><em>not</em></strong> to be used in PvP situations.
                        </li>
                    </ol>

                    <hr>

                    <h3 class="h5">Trash-Talking</h3>

                    <p>
                        The Regs depend on our reputation in the world at large. While we don't give two squirts of piss
                        what a guild like Blood thinks of us, others, like COA, DW, etc., share our ideals and goals, and
                        are our brothers and sisters on the side of Good. The way we conduct ourselves, therefore, makes a
                        huge difference in how we're treated by our potential allies. There are those of us who tend to
                        lose our tempers and say things that we should not be saying; this leads to political troubles
                        that we can ill afford.
                    </p>

                    <p>
                        The Regs maintain a strict code in regards to the practice of trash-talking. As suggested above,
                        we simply do not do it. We cannot allow ourselves to snap, nor to take our frustrations out
                        verbally or otherwise on our friends, guildmates, or even enemies.
                    </p>

                    <p>
                        The first report of a trash-talking offense will result in a warning. The second will result in a
                        discussion involving the Regulators and the accused. All sides will be invited to speak their side
                        of the issue. Once the discussion is complete, a vote will decide if the accused is to be removed
                        from the Regulators. All subsequent offenses will be dealt with in the same way.
                    </p>

                    <p>
                        The goal here isn't to add to the "anti political BS" that people hate so much; it's to help us
                        maintain our sense of honor and dignity. Being removed for trash-talking doesn't make you a bad
                        person, nor does it mean you have to go and join Blood. It simply means that we're unable to agree
                        in terms of Code of Conduct, and ask you simply to wear a different tag.
                    </p>

                    <hr>

                    <p>
                        Our rules are simple, and are mostly based on common sense and respect. Darktide is a harsh realm,
                        and we're here with the hopes of making DT life better for all.
                    </p>

                    <p class="mb-0">
                        The main content of this charter was borrowed from the Darktide Wanderers. As our brothers in the
                        fight against evil, and our first allies on the harsh realm of Darktide, we hope they find our
                        blatant copying of their charter as an attempt to flatter them!
                    </p>
                </div>
            </div>
        </main>

        <?php render_right_sidebar($section_key ?? null); ?>
    </div>
</div>