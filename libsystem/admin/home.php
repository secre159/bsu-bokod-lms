<?php
include 'includes/session.php';
include 'includes/timezone.php';
include 'includes/conn.php';

$today = date('Y-m-d');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
      <section class="content-header" style="background: linear-gradient(135deg, #006400 0%, #228B22 100%); color: #FFD700; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
      <h1 style="font-weight: 800; margin: 0; font-size: 28px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
        <i class="fa fa-dashboard" style="margin-right: 10px;"></i>Library Dashboard
      </h1>
      <ol class="breadcrumb" style="background-color: transparent; margin: 10px 0 0 0; padding: 0; font-weight: 600;">
        <li style="color: #FFD700;">HOME</li>
        <li style="color: #FFF;"><i class="fa fa-dashboard"></i> Dashboard</li>
      </ol>
    </section>

    <section class="content" style="background: linear-gradient(135deg, #f8fff0 0%, #e8f5e8 100%); padding: 20px; min-height: 80vh;">

      <!-- STATISTIC BOXES -->
      <div class="row" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 25px; margin-bottom: 40px;">

        <?php
        // Calculate total books + e-books separately
        $books_count = $conn->query("SELECT COUNT(*) AS total FROM books")->fetch_assoc()['total'];
        $ebooks_count = $conn->query("SELECT COUNT(*) AS total FROM calibre_books")->fetch_assoc()['total'];
        $total_books = $books_count + $ebooks_count;

        // Prepare other counts
        $total_students = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'];
        $total_faculty = $conn->query("SELECT COUNT(*) AS total FROM faculty")->fetch_assoc()['total'];
        $borrowed_today = $conn->query("SELECT COUNT(*) AS total FROM borrow_transactions WHERE DATE(borrow_date)=CURDATE()")->fetch_assoc()['total'];
        $returned_today = $conn->query("SELECT COUNT(*) AS total FROM borrow_transactions WHERE DATE(return_date)=CURDATE() AND status='Returned'")->fetch_assoc()['total'];
        $overdue_books = $conn->query("SELECT COUNT(*) AS total FROM borrow_transactions WHERE due_date < CURDATE() AND status!='Returned'")->fetch_assoc()['total'];

        // Box data array with colors
        $boxes = [
            ['link'=>'book.php', 'color'=>'#006400', 'count'=>$total_books, 'text'=>"Total Books and <br>e-Books Collection", 'icon'=>'fa-book'],
            ['link'=>'student.php', 'color'=>'#1E90FF', 'count'=>$total_students, 'text'=>"Registered Students", 'icon'=>'fa-users'],
            ['link'=>'faculty.php', 'color'=>'#8A2BE2', 'count'=>$total_faculty, 'text'=>"Registered Faculty", 'icon'=>'fa-user'],
            ['link'=>'transactions.php?filter=borrowed_today', 'color'=>'#FF6347', 'count'=>$borrowed_today, 'text'=>"Borrowed Today", 'icon'=>'fa-arrow-right'],
            ['link'=>'transactions.php?filter=returned_today', 'color'=>'#32CD32', 'count'=>$returned_today, 'text'=>"Returned Today", 'icon'=>'fa-arrow-left'],
            ['link'=>'transactions.php?filter=overdue', 'color'=>'#FFD700', 'count'=>$overdue_books, 'text'=>"Overdue Books", 'icon'=>'fa-exclamation-triangle']
        ];

        foreach($boxes as $box){
            echo '
            <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="flex:0 0 240px;">
              <a href="'.$box['link'].'" class="dashboard-link">
                <div class="small-box" style="background:#fff; border-left:4px solid '.$box['color'].'; color:'.$box['color'].'; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                  <div style="display:flex; align-items:center; justify-content:space-between; padding: 15px;">
                    <div class="text" style="line-height:1.2;">
                      <h3 style="color: '.$box['color'].';">'.$box['count'].'</h3>
                      <p style="color: '.$box['color'].';">'.$box['text'].'</p>
                    </div>
                    <div class="icon">
                      <i class="fa '.$box['icon'].'" style="font-size:40px; color:'.$box['color'].'75;"></i>
                    </div>
                  </div>
                </div>
              </a>
            </div>
            ';
        }
        ?>

      </div>

      <!-- Monthly Chart -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box" style="border-top:4px solid #006400;border-radius:10px;box-shadow:0 4px 12px rgba(0,100,0,0.15);overflow:hidden;">
            <div class="box-header with-border" style="background:#e0f7e0;padding:20px;">
              <h3 class="box-title" style="color:#006400;font-weight:700;"><i class="fa fa-bar-chart"></i> Monthly Transaction Report</h3>
              <div class="box-tools pull-right">
                <form class="form-inline">
                  <label style="color:#006400;font-weight:600;">Select Year:</label>
                  <select class="form-control input-sm" id="select_year" style="border-radius:6px;border:1px solid #006400;">
                    <?php
                      for ($i = 2015; $i <= 2065; $i++) {
                        $sel = ($i == $year) ? 'selected' : '';
                        echo "<option value='$i' $sel>$i</option>";
                      }
                    ?>
                  </select>
                </form>
              </div>
            </div>
            <div class="box-body">
              <canvas id="barChart" style="height:350px"></canvas>
            </div>
          </div>
        </div>
      </div>

    </section>
  </div>
</div>

<?php
$months = [];
$borrow = [];
$return = [];

$totalBorrow = 0;
$totalReturn = 0;

for ($m = 1; $m <= 12; $m++) {
  $month = date('M', mktime(0, 0, 0, $m, 1));
  $months[] = $month;

  $b = $conn->query("
      SELECT COUNT(*) AS total 
      FROM borrow_transactions 
      WHERE MONTH(borrow_date) = '$m' 
        AND YEAR(borrow_date) = '$year' 
        AND status IN ('borrowed', 'overdue')
    ")->fetch_assoc();

  $r = $conn->query("
      SELECT COUNT(*) AS total 
      FROM borrow_transactions 
      WHERE MONTH(return_date) = '$m' 
        AND YEAR(return_date) = '$year' 
        AND status = 'returned'
    ")->fetch_assoc();

  $borrow[] = (int)$b['total'];
  $return[] = (int)$r['total'];

  $totalBorrow += (int)$b['total'];
  $totalReturn += (int)$r['total'];
}

$hasData = ($totalBorrow + $totalReturn) > 0;
?>

<?php include 'includes/scripts.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const hasData = <?php echo $hasData ? 'true' : 'false'; ?>;

  if (hasData) {
    const ctx = document.getElementById('barChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [
          {
            label: '📤 Borrowed Books',
            backgroundColor: '#FF6347',
            borderColor: '#DC143C',
            borderWidth: 1,
            data: <?php echo json_encode($borrow); ?>,
            borderRadius: 6
          },
          {
            label: '📥 Returned Books',
            backgroundColor: '#32CD32',
            borderColor: '#228B22',
            borderWidth: 1,
            data: <?php echo json_encode($return); ?>,
            borderRadius: 6
          }
        ]
      },
      options: {
        responsive: true,
        aspectRatio: 2.2,
        plugins: {
          legend: {
            position: 'bottom',
            labels: { color: '#006400', font: { weight: '600' } }
          },
          title: {
            display: true,
            text: 'Monthly Borrow and Return Transactions (<?php echo $year; ?>)',
            color: '#006400',
            font: { size: 16, weight: 'bold' }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1,
              callback: value => Number.isInteger(value) ? value : ''
            },
            grid: { color: '#d3f0d3' }
          },
          x: { grid: { display: false } }
        }
      }
    });
  } else {
    const chartBox = document.getElementById('barChart').parentElement;
    chartBox.innerHTML = `
      <div style="text-align:center; color:#777; padding:40px;">
        <i class="fa fa-info-circle" style="font-size:40px; color:#999;"></i>
        <h4>No transaction data available for the year <?php echo $year; ?>.</h4>
      </div>
    `;
  }

  document.getElementById('select_year').addEventListener('change', function() {
    window.location = 'index.php?year=' + this.value;
  });
});
</script>

</body>
</html>
