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
        <li><a href="#" style="color: white;"><i class="fa fa-dashboard"></i> Homepage</a></li>
        <li class="active" style="color: #FFD700;">Dashboard</li>
      </ol>
    </section>

    <!-- Main Content -->
    <section class="content">
      <?php
        if (isset($_SESSION['error'])) {
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>

      <!-- Statistic Boxes -->
      <div class="row" style="display: flex; flex-wrap: wrap; justify-content: space-around; gap: 0px; margin-bottom: 30px;">

        <!-- Total Books -->
        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="flex: 1 1 180px; max-width: 200px;">
          <div class="small-box stat-box" style="background-color: #ADFF2F; color: #006400;">
            <?php
              $sql = "SELECT * FROM books";
              $query = $conn->query($sql);
              echo "<h3>".$query->num_rows."</h3>";
            ?>
            <p>Total Books</p>
            <div class="icon animated-icon">
              <i class="fa fa-book"></i>
            </div>
            <a href="book.php" class="small-box-footer" style="color: #006400;">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <!-- Total Students -->
        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="flex: 1 1 180px; max-width: 200px;">
          <div class="small-box stat-box" style="background-color: #32CD32; color: white;">
            <?php
              $sql = "SELECT * FROM students";
              $query = $conn->query($sql);
              echo "<h3>".$query->num_rows."</h3>";
            ?>
            <p>Total Students</p>
            <div class="icon animated-icon">
              <i class="fa fa-graduation-cap"></i>
            </div>
            <a href="student.php" class="small-box-footer" style="color: white;">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <!-- Returned Today -->
        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="flex: 1 1 180px; max-width: 200px;">
          <div class="small-box stat-box" style="background-color: #FFD700; color: #8B4513;">
            <?php
              $sql = "SELECT * FROM returns WHERE date_return = '$today'";
              $query = $conn->query($sql);
              echo "<h3>".$query->num_rows."</h3>";
            ?>
            <p>Returned Today</p>
            <div class="icon animated-icon">
              <i class="fa fa-mail-reply"></i>
            </div>
            <a href="return.php" class="small-box-footer" style="color: #8B4513;">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <!-- Borrowed Today -->
        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="flex: 1 1 180px; max-width: 200px;">
          <div class="small-box stat-box" style="background-color: #FF6347; color: white;">
            <?php
              $sql = "SELECT * FROM borrow WHERE date_borrow = '$today'";
              $query = $conn->query($sql);
              echo "<h3>".$query->num_rows."</h3>";
            ?>
            <p>Borrowed Today</p>
            <div class="icon animated-icon">
              <i class="fa fa-mail-forward"></i>
            </div>
            <a href="borrow.php" class="small-box-footer" style="color: white;">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <!-- Total PDF Books -->
        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="flex: 1 1 180px; max-width: 200px;">
          <div class="small-box stat-box" style="background-color: #20B2AA; color: white;">
            <?php
              $sql = "SELECT * FROM pdf_books";
              $query = $conn->query($sql);
              echo "<h3>".$query->num_rows."</h3>";
            ?>
            <p>Total PDF Books</p>
            <div class="icon animated-icon">
              <i class="fa fa-file-pdf-o"></i>
            </div>
            <a href="pdf_books.php" class="small-box-footer" style="color: white;">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>

      </div>

      <!-- Monthly Transaction Chart -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box" style="border: 2px solid #006400; border-radius: 12px;">
            <div class="box-header with-border" style="background-color: #006400; border-top-left-radius: 12px; border-top-right-radius: 12px;">
              <h3 class="box-title" style="color: white;">Monthly Transaction Report</h3>
              <div class="box-tools pull-right">
                <form class="form-inline">
                  <div class="form-group">
                    <label style="color: white;">Select Year: </label>
                    <select class="form-control input-sm" id="select_year" style="border: 1px solid #006400; background-color: #ffeb99;">
                      <?php
                        for ($i = 2015; $i <= 2065; $i++) {
                          $selected = ($i == $year) ? 'selected' : '';
                          echo "<option value='".$i."' ".$selected.">".$i."</option>";
                        }
                      ?>
                    </select>
                  </div>
                </form>
              </div>
            </div>
            <div class="box-body" style="background-color: #f0fff0;">
              <div class="chart">
                <canvas id="barChart" style="height:350px"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

    </section>
  </div>
</div>

<!-- PHP: Chart Data -->
<?php
$months = [];
$return = [];
$borrow = [];

for ($m = 1; $m <= 12; $m++) {
  $month = date('M', mktime(0, 0, 0, $m, 1));
  $months[] = $month;

  $b = $conn->query("SELECT COUNT(*) AS total FROM borrow WHERE MONTH(date_borrow) = '$m' AND YEAR(date_borrow) = '$year'")->fetch_assoc();
  $r = $conn->query("SELECT COUNT(*) AS total FROM returns WHERE MONTH(date_return) = '$m' AND YEAR(date_return) = '$year'")->fetch_assoc();

  $borrow[] = (int)$b['total'];
  $return[] = (int)$r['total'];
}
?>

<?php include 'includes/scripts.php'; ?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const ctx = document.getElementById('barChart').getContext('2d');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($months); ?>,
      datasets: [
        {
          label: 'Borrowed Books',
          backgroundColor: '#007bff', // 💙 Blue
          borderColor: '#0056b3',
          borderWidth: 1,
          data: <?php echo json_encode($borrow); ?>
        },
        {
          label: 'Returned Books',
          backgroundColor: '#28a745', // 💚 Green
          borderColor: '#1e7e34',
          borderWidth: 1,
          data: <?php echo json_encode($return); ?>
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: { color: '#000' }
        },
        x: {
          ticks: { color: '#000' }
        }
      },
      plugins: {
        legend: {
          position: 'top',
          labels: { color: '#000', font: { size: 14 } }
        },
        title: {
          display: true,
          text: 'Monthly Borrow and Return Transactions (<?php echo $year; ?>)',
          color: '#000',
          font: { size: 16 }
        }
      }
    }
  });

  // Change year reload
  document.getElementById('select_year').addEventListener('change', function() {
    const year = this.value;
    window.location = 'index.php?year=' + year;
  });
});
</script>

<style>
.stat-box {
  border-radius: 10px;
  height: 150px;
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  padding: 10px;
  transition: transform 0.2s ease-in-out;
}
.stat-box:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.3);
}
.animated-icon {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 70px;
  opacity: 0.5;
  transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
}
.stat-box:hover .animated-icon {
  transform: scale(1.2);
  opacity: 0.8;
}
.small-box-footer {
  position: absolute;
  bottom: 10px;
  font-weight: bold;
}
</style>

</body>
</html>
