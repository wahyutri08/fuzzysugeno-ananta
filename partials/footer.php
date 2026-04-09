  <?php
    if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
        header('HTTP/1.1 403 Forbidden');
        include("../errors/404.html");
        exit();
    }
    ?>

  <footer class="main-footer">
      <!-- To the right -->
      <div class="float-right d-none d-sm-inline">
          Fuzzy Sugeno
      </div>
      <!-- Default to the left -->
      <strong>Copyright &copy; <?= date('Y', strtotime('now')); ?> <a href="https://adminlte.io">Ananta Dicapriyo</a>.</strong> All rights reserved.
  </footer>