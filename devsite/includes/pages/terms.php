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
>
    <div class="offcanvas-header">

        <h5 class="offcanvas-title">
            Navigation
        </h5>

        <button
            type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="offcanvas"
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
    Web Site Terms and Conditions of Use
</h2>

</div>


<div class="card-body lh-lg">
<h3>
	1. Terms
</h3>
<p>
	By accessing this web site, you are agreeing to be bound by these 
	web site Terms and Conditions of Use, all applicable laws and regulations, 
	and agree that you are responsible for compliance with any applicable local 
	laws. If you do not agree with any of these terms, you are prohibited from 
	using or accessing this site. The materials contained in this web site are 
	protected by applicable copyright and trade mark law.
</p>
<h3>
	2. Use License
</h3>
<ol type="a">
	<li>
		Permission is granted to temporarily download one copy of the materials 
		(information or software) on The Regs's web site for personal, 
		non-commercial transitory viewing only. This is the grant of a license, 
		not a transfer of title, and under this license you may not:
		<ol type="i">
			<li>modify or copy the materials;</li>
			<li>use the materials for any commercial purpose, or for any public display (commercial or non-commercial);</li>
			<li>attempt to decompile or reverse engineer any software contained on The Regs's web site;</li>
			<li>remove any copyright or other proprietary notations from the materials; or</li>
			<li>transfer the materials to another person or "mirror" the materials on any other server.</li>
		</ol>
	</li>
	<li>
		This license shall automatically terminate if you violate any of these restrictions and may be terminated by The Regs at any time. Upon terminating your viewing of these materials or upon the termination of this license, you must destroy any downloaded materials in your possession whether in electronic or printed format.
	</li>
</ol>
<h3>
	3. Disclaimer
</h3>
<ol type="a">
	<li>
		The materials on The Regs's web site are provided "as is". The Regs makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties, including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights. Further, The Regs does not warrant or make any representations concerning the accuracy, likely results, or reliability of the use of the materials on its Internet web site or otherwise relating to such materials or on any sites linked to this site.
	</li>
</ol>
<h3>
	4. Limitations
</h3>
<p>
	In no event shall The Regs or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption,) arising out of the use or inability to use the materials on The Regs's Internet site, even if The Regs or a The Regs authorized representative has been notified orally or in writing of the possibility of such damage. Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.
</p>
<h3>
	5. Revisions and Errata
</h3>
<p>
	The materials appearing on The Regs's web site could include technical, typographical, or photographic errors. The Regs does not warrant that any of the materials on its web site are accurate, complete, or current. The Regs may make changes to the materials contained on its web site at any time without notice. The Regs does not, however, make any commitment to update the materials.
</p>
<h3>
	6. Links
</h3>
<p>
	The Regs has not reviewed all of the sites linked to its Internet web site and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by The Regs of the site. Use of any such linked web site is at the user's own risk.
</p>
<h3>
	7. Site Terms of Use Modifications
</h3>
<p>
	The Regs may revise these terms of use for its web site at any time without notice. By using this web site you are agreeing to be bound by the then current version of these Terms and Conditions of Use.
</p>
<h3>8. Governing Law</h3>
<p>Any claim relating to The Regs's web site shall be governed by the laws of the State of Michigan without regard to its conflict of law provisions.</p>
<p>General Terms and Conditions applicable to Use of a Web Site.</p>
</div>


</div>


</main>


<?php render_right_sidebar($section_key ?? null); ?>