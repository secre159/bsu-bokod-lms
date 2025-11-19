<?php
include 'includes/session.php';
include 'includes/header.php';
?>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

<?php include 'includes/navbar.php'; ?>
<?php include 'includes/menubar.php'; ?>

<div class="content-wrapper">
  <section class="content-header" style=" padding: 15px; border-radius: 5px 5px 0 0;">
    <h1 style="font-weight: bold;">📚 New Books</h1>
    <ol class="breadcrumb" style="background-color: transparent; color: #FFD700; font-weight: bold;">
      <li><a href="home.php" style="color: #FFD700;"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active" style="color: #FFFFFF;">New Books</li>
    </ol>
  </section>

  <section class="content" style="background-color: #F8FFF0; padding: 15px; border-radius: 0 0 5px 5px;">
    <?php
      if(isset($_SESSION['error'])){
        echo "
          <div class='alert alert-danger alert-dismissible' style='background-color: #FF6347; color: white; font-weight: bold;'>
            <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
            <h4><i class='icon fa fa-warning'></i> Error!</h4>
            ".$_SESSION['error']."
          </div>
        ";
        unset($_SESSION['error']);
      }
      if(isset($_SESSION['success'])){
        echo "
          <div class='alert alert-success alert-dismissible' style='background-color: #32CD32; color: #006400; font-weight: bold;'>
            <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
            <h4><i class='icon fa fa-check'></i> Success!</h4>
            ".$_SESSION['success']."
          </div>
        ";
        unset($_SESSION['success']);
      }
    ?>

    <div class="box" style="border-top: 3px solid #006400; background-color: #FFFFFF; border-radius: 5px;">
      <div class="box-header with-border" style="background-color: #006400; color: #FFD700; font-weight: bold; padding: 10px; border-radius: 5px 5px 0 0;">
        <h3 class="box-title">Recently Added Books (This Year)</h3>
      </div>
      <div class="box-body">
        <table id="example1" class="table table-bordered table-striped">
          <thead style="background-color: #006400; color: #FFD700; font-weight: bold;">
            <tr>
              <th>Month Added</th>
              <th>ISBN</th>
              <th>Call No.</th>
              <th>Title</th>
              <th>Subject</th>
              <th>Author</th>
              <th>Publisher</th>
              <th>Publish Date</th>
              <th>Date Added</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
              // ✅ Fetch books added this year
              $sql = "
                SELECT 
                  b.id AS bookid,
                  b.isbn,
                  b.call_no,
                  b.subject,
                  b.title,
                  b.author,
                  b.publisher,
                  b.publish_date,
                  b.date_added,
                  b.status,
                  MONTHNAME(b.date_added) AS month_added,
                  GROUP_CONCAT(DISTINCT c.name ORDER BY c.name SEPARATOR ', ') AS categories
                FROM books b
                LEFT JOIN book_category_map m ON b.id = m.book_id
                LEFT JOIN category c ON m.category_id = c.id
                WHERE YEAR(b.date_added) = YEAR(CURDATE())
                GROUP BY b.id
                ORDER BY b.date_added DESC
              ";

              $query = $conn->query($sql);
              $current_month = '';

              while($row = $query->fetch_assoc()){
                $month = $row['month_added'] ? $row['month_added'] : 'Unknown Month';

                // 🟢 Add a header row for each new month
                if($month != $current_month){
                  $current_month = $month;
                  echo "
                    <tr style='background-color:#006400; color:#FFD700; font-weight:bold;'>
                      <td colspan='10'>📅 $month</td>
                    </tr>
                  ";
                }

                $status_label = $row['status'] == 0 
                  ? '<span style="color: #32CD32; font-weight: bold;">Available</span>'
                  : '<span style="color: #FF6347; font-weight: bold;">Borrowed</span>';

                echo "
                  <tr>
                    <td>".htmlspecialchars($month)."</td>
                    <td>".htmlspecialchars($row['isbn'])."</td>
                    <td>".htmlspecialchars($row['call_no'])."</td>
                    <td>".htmlspecialchars($row['title'])."</td>
                    <td>".htmlspecialchars($row['subject'])."</td>
                    <td>".htmlspecialchars($row['author'])."</td>
                    <td>".htmlspecialchars($row['publisher'])."</td>
                    <td>".htmlspecialchars($row['publish_date'])."</td>
                    <td>".htmlspecialchars(date('F d, Y', strtotime($row['date_added'])))."</td>
                    <td>".$status_label."</td>
                  </tr>
                ";
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<?php include 'includes/footer.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?>
</body>
</html>
