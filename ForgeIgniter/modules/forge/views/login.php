<body class="hold-transition skin-anvil-light login-page">
<div class="login-box">
  <div class="login-logo">
	<div class="header">
		<a href="#"><b>Admin - Login</b></a>
	</div>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
	<!--
    <p class="login-box-msg">Sign in to start your session</p>
	-->
	<?php if ($errors = validation_errors()): ?>
		<div class="alert alert-danger alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
			<h4><i class="icon fa fa-ban"></i> Error</h4>
			<?php echo $errors; ?>
        </div>
	<?php endif; ?>

    <form action="" method="post">
      <div class="form-group has-feedback">
        <input type="username" id="username" name="username" class="form-control" placeholder="Username">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" id="password" name="password" class="form-control" placeholder="Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">

        <!-- /.col -->
        <div class="col-xs-12" style="padding-top:10px">
          <button type="submit" id="login" name="login" class="btn btn-primary btn-block" >Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
