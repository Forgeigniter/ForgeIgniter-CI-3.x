<?php if ($this->session->userdata('session_admin') && strpos(FULL_URL,'admin/login') == FALSE): ?>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer">

    <!-- Default to the left -->
    <div class="">
    <strong>Copyright &copy; <?= date("Y");?> <a href="http://www.forgeigniter.com">ForgeIgniter</a>.</strong> All rights reserved. | Page Executed In: {elapsed_time}

    <!-- To the right -->
    <div class="pull-right hidden-xs">
      <strong>Forged on:</strong> Codeigniter <?= CI_VERSION; ?> | ForgeIgniter v2.0 A2.2
    </div>
  </footer>

</div>
<!-- ./wrapper -->

<?php endif; ?>
</body>
</html>
