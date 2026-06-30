    </div> <!-- end row -->
</div> <!-- end container-fluid -->
<footer class="bg-dark text-center text-light py-3 mt-4">
  <div class="container">
    &copy; <?php echo date("Y"); ?> TheRegs.org — Page Rendered in: <?= number_format(microtime(true) - APP_START, 3); ?> seconds.
  </div>
</footer>
<script src="//cdn.theregs.org/assets/js/main.bundle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Gallery Modal
    const galleryModal = document.getElementById('galleryModal');
    if (galleryModal) {
        const modalImg = document.getElementById('galleryModalImage');
        const modalTitle = document.getElementById('galleryModalLabel');
        
        galleryModal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            const imageUrl = trigger.getAttribute('data-img');
            const imageTitle = trigger.getAttribute('data-title');
            if (modalImg) { modalImg.src = imageUrl; modalImg.alt = imageTitle; }
            if (modalTitle) { modalTitle.textContent = imageTitle; }
        });
        galleryModal.addEventListener('hidden.bs.modal', function () {
            if (modalImg) { modalImg.src = ''; }
        });
    }

    // 2. Image Modal (Fixed lines 276+)
    const imgModal = document.getElementById('imgModal');
    if (imgModal) {
        const modalImage = document.getElementById('modalImage');
        const imgModalLabel = document.getElementById('imgModalLabel');
        
        imgModal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            const imageUrl = trigger.getAttribute('data-img');
            const imageTitle = trigger.getAttribute('title') || 'Image Preview';
            if (modalImage) { modalImage.src = imageUrl; }
            if (imgModalLabel) { imgModalLabel.textContent = imageTitle; }
        });
        imgModal.addEventListener('hidden.bs.modal', function () {
            if (modalImage) { modalImage.src = ''; }
        });
    }

    // 3. Video Modal (Fixed lines 293+)
    const videoModal = document.getElementById('videoModal');
    if (videoModal) {
        const videoFrame = document.getElementById('videoFrame');
        
        videoModal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            const videoUrl = trigger.getAttribute('data-video');
            if (videoFrame) { videoFrame.src = videoUrl; }
        });
        videoModal.addEventListener('hidden.bs.modal', function () {
            if (videoFrame) { videoFrame.src = ''; }
        });
    }
});
</script>
</body>
</html>
