<!-- jQuery 3 -->
<script src="../bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Moment JS -->
<script src="../bower_components/moment/moment.js"></script>
<!-- DataTables -->
<script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- ChartJS -->
<script src="../bower_components/chart.js/Chart.js"></script>
<!-- daterangepicker -->
<script src="../bower_components/moment/min/moment.min.js"></script>
<script src="../bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="../bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- bootstrap time picker -->
<script src="../plugins/timepicker/bootstrap-timepicker.min.js"></script>
<!-- Slimscroll -->
<script src="../bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Active Script -->
<script>
$(function(){
	/** add active class and stay opened when selected */
	var url = window.location;

	// for sidebar menu entirely but not cover treeview
	$('ul.sidebar-menu a').filter(function() {
	    return this.href == url;
	}).parent().addClass('active');

	// for treeview
	$('ul.treeview-menu a').filter(function() {
	    return this.href == url;
	}).parentsUntil(".sidebar-menu > .treeview-menu").addClass('active');

});
</script>
<!-- Data Table Initialize -->


<!-- For the navbar-->
<script>
	
// Add responsive behavior
$(document).ready(function() {
  // Handle window resize
  $(window).resize(function() {
    adjustHeaderForMobile();
  });
  
  function adjustHeaderForMobile() {
    if ($(window).width() < 992) {
      $('.logo-lg .logo-text small').hide();
    } else {
      $('.logo-lg .logo-text small').show();
    }
  }
  
  // Initial adjustment
  adjustHeaderForMobile();
  
  // Smooth hover effects
  $('.dropdown').hover(function() {
    $(this).addClass('open');
  }, function() {
    $(this).removeClass('open');
  });
});
</script>