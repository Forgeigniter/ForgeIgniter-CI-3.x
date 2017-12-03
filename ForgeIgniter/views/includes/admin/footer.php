<?php if ($this->session->userdata('session_admin') && strpos(FULL_URL,'admin/login') == FALSE): ?>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer">

    <!-- Default to the left -->
    <div class="">
    <strong>Copyright &copy; <?= date("Y"); ?> <a href="http://www.forgeigniter.com">ForgeIgniter</a>.</strong> All rights reserved. | Page Executed In: {elapsed_time}

    <!-- To the right -->
    <div class="pull-right hidden-xs">
      <strong>Forged on:</strong> Codeigniter <?= CI_VERSION; ?> | ForgeIgniter v2.0 A1
    </div>
  </footer>


<?php

  /*

    Display:
       Selected Notifications (Set from backend, IE new comment etc...)
       Project Tasks
       Who's Online (Friends)

  */

?>

</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery UI -->
<script src="<?=PATH['theme']?>anvil/plugins/jQueryUI/jquery-ui.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?=PATH['theme']?>anvil/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- App -->
<script src="<?=PATH['theme']?>anvil/js/app.js"></script>

	<script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/default.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/admin.js"></script>

	<script language="JavaScript">
		$(function(){
			$('ul#menubar li').hover(
				function() { $('ul', this).css('display', 'block').parent().addClass('hover'); },
				function() { $('ul', this).css('display', 'none').parent().removeClass('hover'); }
			);
		});
	</script>

<!-- Optionally, add Slimscroll and FastClick plugins.
 -->

<?php endif; ?>
</body>
</html>