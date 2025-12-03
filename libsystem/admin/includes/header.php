<!DOCTYPE html>
<html lang="en">
<head>
  	<meta charset="utf-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<title>Library System using PHP</title>

  	<!-- Responsive design meta tag -->
  	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  	<!-- Bootstrap 3.3.7 CSS -->
  	<link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css">

  	<!-- Font Awesome for icons -->
  	<link rel="stylesheet" href="../bower_components/font-awesome/css/font-awesome.min.css">

  	<!-- AdminLTE style for the admin panel -->
  	<link rel="stylesheet" href="../dist/css/AdminLTE.min.css">

  	<!-- DataTables CSS for tables -->
    <link rel="stylesheet" href="../bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

    <!-- Daterange picker CSS -->
    <link rel="stylesheet" href="../bower_components/bootstrap-daterangepicker/daterangepicker.css">

    <!-- Bootstrap time picker CSS -->
    <link rel="stylesheet" href="../plugins/timepicker/bootstrap-timepicker.min.css">

    <!-- Bootstrap datepicker CSS -->
    <link rel="stylesheet" href="../bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

    <!-- AdminLTE Skins (optional) -->
    <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">

  	<!-- HTML5 Shim and Respond.js for IE8 support -->
  	<!--[if lt IE 9]>
  	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  	<![endif]-->

  	<!-- Google Fonts (Source Sans Pro) -->
  	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  	<!-- Custom CSS for page -->
  	<style type="text/css">
  		.mt20 {
  			margin-top: 20px;
  		}

      /* Bold text style */
      .bold {
        font-weight: bold;
      }

      /* Chart legend styling */
      #legend ul {
        list-style: none;
      }

      #legend ul li {
        display: inline;
        padding-left: 30px;
        position: relative;
        margin-bottom: 4px;
        border-radius: 5px;
        padding: 2px 8px 2px 28px;
        font-size: 14px;
        cursor: default;
        transition: background-color 200ms ease-in-out;
      }

      #legend li span {
        display: block;
        position: absolute;
        left: 0;
        top: 0;
        width: 20px;
        height: 100%;
        border-radius: 5px;
      }

      /* Fixed navbar styling */
      .main-header {
        position: fixed !important;
        top: 0;
        right: 0;
        left: 0;
        z-index: 1030;
      }

      /* Fixed sidebar styling */
      .main-sidebar {
        position: fixed !important;
        height: 100vh !important;
        overflow-y: auto !important;
        overflow-x: hidden;
      }

      /* Ensure sidebar content is scrollable */
      .main-sidebar .sidebar {
        padding-bottom: 50px;
      }

      /* Adjust content wrapper to account for fixed sidebar and navbar */
      .content-wrapper {
        margin-top: 50px;
      }

      @media (min-width: 768px) {
        .content-wrapper,
        .main-footer {
          margin-left: 230px;
        }
      }

      /* Scrollbar styling for sidebar */
      .main-sidebar::-webkit-scrollbar {
        width: 8px;
      }

      .main-sidebar::-webkit-scrollbar-track {
        background: #1a1a1a;
      }

      .main-sidebar::-webkit-scrollbar-thumb {
        background: #006400;
        border-radius: 4px;
      }

      .main-sidebar::-webkit-scrollbar-thumb:hover {
        background: #228B22;
      }
  	</style>
</head>
