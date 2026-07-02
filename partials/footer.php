<?php // /partials/footer.php ?>
    </main>

    <footer class="mt-auto py-4 border-top bg-light">
      <div class="container d-flex flex-wrap justify-content-between align-items-center">
        <span class="text-muted small">&copy; <?= date('Y') ?> AuthCompare</span>
        <a class="small text-muted" href="<?= url('?p=about'); ?>">About</a>
      </div>
    </footer>

    <!-- Bootstrap JS (bundle includes Popper; required for navbar + tooltips) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Your app scripts -->
    <script src="<?= url('js/scripts.js') ?>"></script>

    <!-- Enable all [data-bs-toggle="tooltip"] elements -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
      var triggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      triggerList.forEach(function (el) { new bootstrap.Tooltip(el); });
    });
    </script>
  </body>
</html>
