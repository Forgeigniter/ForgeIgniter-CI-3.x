<?php if ($this->session->userdata('session_admin') && strpos(FULL_URL,'admin/login') == FALSE): ?>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  
  


  <!-- Main Footer -->
  <footer class="main-footer">

    <!-- Default to the left -->
    <div class="">
    <strong>Copyright &copy; 2017 <a href="http://www.forgeigniter.com">ForgeIgniter</a>.</strong> All rights reserved. | Page Executed In: {elapsed_time}

    <!-- To the right -->
    <div class="pull-right hidden-xs">
      <strong>Forged on:</strong> Codeigniter v3.1.5 | ForgeIgniter v2.0 
    </div>
  </footer>


<?php

  /*
    Just demo content for now

    Display:
       Selected Notifications (Set from backend, IE new comment etc...)
       Project Tasks
       Who's Online (Friends)

  */

?>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
      <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
      <!-- Home tab content -->
      <div class="tab-pane active" id="control-sidebar-home-tab">
        <h3 class="control-sidebar-heading">Recent Activity</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:;">
              <i class="menu-icon fa fa-birthday-cake bg-red"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                <p>Will be 23 on April 24th</p>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

        <h3 class="control-sidebar-heading">Tasks Progress</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:;">
              <h4 class="control-sidebar-subheading">
                Custom Template Design
                <span class="pull-right-container">
                    <span class="label label-danger pull-right">70%</span>
                  </span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

      </div>
      <!-- /.tab-pane -->
      <!-- Stats tab content -->
      <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
      <!-- /.tab-pane -->
      <!-- Settings tab content -->
      <div class="tab-pane" id="control-sidebar-settings-tab">
        <form method="post">
          <h3 class="control-sidebar-heading">General Settings</h3>

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Report panel usage
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Some information about this general settings option
            </p>
          </div>
          <!-- /.form-group -->
        </form>
      </div>
      <!-- /.tab-pane -->
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
  immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 3 -->
<script src="<?=PATH['theme']?>anvil/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?=PATH['theme']?>anvil/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=PATH['theme']?>anvil/js/app.js"></script>



<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->

<?php endif; ?>
</body>
</html>