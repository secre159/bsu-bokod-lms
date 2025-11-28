<?php
include 'includes/session.php';
include 'includes/timezone.php';
$today = date('Y-m-d');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-green sidebar-mini" style="background-color: #f0fff0; font-family: 'Source Sans Pro', Arial, sans-serif;">
<div class="wrapper">

  <?php include 'includes/menubar.php'; ?>
  <?php include 'includes/navbar.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper" style="background-color: #f0fff0;">
    <!-- Content Header -->
    <section class="content-header" style=" padding: 15px; border-radius: 5px 5px 0 0;">
      <h1 style="font-weight: bold;">Dashboard</h1>
      <ol class="breadcrumb" style="background-color: transparent; color: #000000ff; font-weight: bold;">
        <li class="active"><a href="#" style="color: white;"><i class="fa fa-dashboard"></i> Home</a></li>
        <li  style="color: #FFD700;">Dashboard</li>
      </ol>
    </section>

    <!-- Main Content -->
    <section class="content">

    </section>
  </div>
</div>

