<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

    <!-- Sidebar user panel -->
    <div class="user-panel" style="padding: 15px; border-bottom: 1px solid #e0e0e0;">
      <div class="pull-left image">
        <img src="<?php echo !empty($user['photo']) ? '../images/'.$user['photo'] : '../images/profile.jpg'; ?>" class="img-circle" alt="User Image" >
      </div>
      <div class="pull-left info" style="margin-left: 10px;">
        <p style="color: #ffffffff; font-weight: 600; margin-bottom: 5px;">Welcome, Admin</p>
        <a style="color: #32CD32; font-weight: 500;"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>

    <!-- Sidebar menu -->
    <ul class="sidebar-menu" data-widget="tree" style="margin-top: 0;">
      
      <!-- Reports Section -->
      <li class="header" style="background: linear-gradient(135deg, #004d00 0%, #006400 100%); color: #FFD700; font-weight: 700; padding: 12px 15px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">HOME</li>
      <li>
        <a href="../index.php" style="border-left: 3px solid transparent; transition: all 0.3s ease;">
          <i class="fa fa-home" style="color: #fee900ff;"></i> 
          <span style="font-weight:800;">User Homepage</span>
        </a>
      </li>
      <li>
        <a href="home.php" style="border-left: 3px solid transparent; transition: all 0.3s ease;">
          <i class="fa fa-dashboard" style="color: #fee900ff;"></i> 
          <span style="font-weight:800;">Dashboard</span>
        </a>
      </li>

      <!-- Manage Section -->
      <li class="header" style="background: linear-gradient(135deg, #004d00 0%, #006400 100%); color: #FFD700; font-weight: 700; padding: 12px 15px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">MANAGE</li>

      <li>
        <a href="transactions.php" style="border-left: 3px solid transparent; transition: all 0.3s ease;">
          <i class="fa fa-refresh" style="color: #f6ff00ff;"></i> 
          <span style="font-weight: 500;">Transactions</span>
        </a>
      </li>

      <li class="treeview">
        <a href="#" style="border-left: 3px solid transparent; transition: all 0.3s ease;">
          <i class="fa fa-book" style="color: #f6ff00ff;"></i>
          <span style="font-weight: 500;">Books</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right" style="color: #f6ff00ff;"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="book.php" style="margin-left: 0;"><i class="fa fa-cube" style="color: #f6ff00ff;"></i>Physical Book List</a></li>
          <li><a href="calibre_books.php" style="margin-left: 0;"><i class="fa fa-tablet" style="color: #f6ff00ff;"></i>E-Book List</a></li>
          <li><a href="category.php" style="margin-left: 0;"><i class="fa fa-tags" style="color: #f6ff00ff;"></i>Manage Category</a></li>
          <li><a href="subjects.php" style="margin-left: 0;"><i class="fa fa-paperclip" style="color: #f6ff00ff;"></i>Manage Subject</a></li>
          <li><a href="new_books.php" style="margin-left: 0;"><i class="fa fa-calendar" style="color: #f6ff00ff;"></i>Recent Books</a></li>
          <li><a href="book_count_monthly.php" style="margin-left: 0;"><i class="fa fa-calendar" style="color: #f6ff00ff;"></i>Monthly Book Count</a></li>
        </ul>
      </li>

      <li>
        <a href="student.php" style="border-left: 3px solid transparent; transition: all 0.3s ease;">
          <i class="fa fa-graduation-cap" style="color: #f6ff00ff;"></i> 
          <span style="font-weight: 500;">Student List</span>
        </a>
      </li>

      <li>
        <a href="faculty.php" style="border-left: 3px solid transparent; transition: all 0.3s ease;">
          <i class="fa fa-users" style="color: #f6ff00ff;"></i> 
          <span style="font-weight: 500;">Employees</span>
        </a>
      </li>

      <li>
        <a href="post.php" style="border-left: 3px solid transparent; transition: all 0.3s ease;">
          <i class="fa fa-bullhorn" style="color: #f6ff00ff;"></i> 
          <span style="font-weight: 500;">Posts / News</span>
        </a>
      </li>

      <li>
        <a href="logbook.php" style="border-left: 3px solid transparent; transition: all 0.3s ease;">
          <i class="fa fa-history" style="color: #f6ff00ff;"></i> 
          <span style="font-weight: 500;">User Logbook</span>
        </a>
      </li>

      <!-- Archive Section -->
      <li class="header" style="background: linear-gradient(135deg, #004d00 0%, #006400 100%); color: #FFD700; font-weight: 700; padding: 12px 15px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">ARCHIVE</li>

      <!-- Archived Books -->
      <li class="treeview">
        <a href="#" style="border-left: 3px solid transparent; transition: all 0.3s ease;">
          <i class="fa fa-archive" style="color: #51ff00ff;"></i>
          <span style="font-weight: 500;">Books</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right" style="color: #006400;"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="archived_book.php" style="margin-left: 0;"><i class="fa fa-circle-o" style="color: #888;"></i> Book List</a></li>
          <li><a href="archived_category.php" style="margin-left: 0;"><i class="fa fa-circle-o" style="color: #888;"></i> Category</a></li>
          <li><a href="archived_calibre_books.php" style="margin-left: 0;"><i class="fa fa-circle-o" style="color: #888;"></i> E-Books</a></li>
          <li><a href="archived_subject.php" style="margin-left: 0;"><i class="fa fa-circle-o" style="color: #888;"></i> Subjects</a></li>
        </ul>
      </li>

      <!-- Archived Students -->
      <li class="treeview">
        <a href="#" style="border-left: 3px solid transparent; transition: all 0.3s ease;">
          <i class="fa fa-users" style="color: #51ff00ff;"></i>
          <span style="font-weight: 500;">Students</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right" style="color: #006400;"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="archived_student.php" style="margin-left: 0;"><i class="fa fa-circle-o" style="color: #888;"></i> Student List</a></li>
        </ul>
      </li>


  <!-- /.sidebar -->
</aside>

<style>
.sidebar-menu > li > a {
  border-left: 3px solid transparent !important;
  transition: all 0.3s ease !important;
}

.sidebar-menu > li > a:hover {
  border-left: 3px solid #FFD700 !important;
  background-color: #6a6a6a6f !important;
  color: #ffffffff !important;
}

.sidebar-menu > li.active > a {
  border-left: 3px solid #229b00ff !important;
  background-color: #6a6a6a6f !important;
  color: #ffffffff !important;
  font-weight:1000 !important;
}

.treeview-menu > li > a {
  padding-left: 35px !important;
  transition: all 0.2s ease !important;
}

.treeview-menu > li > a:hover {
  background-color: #6a6a6a6f !important;
  color: #ffffffff !important;
  padding-left: 40px !important;
}

.treeview-menu > li.active > a {
  color: #ffffffff !important;
  font-weight: 1000 !important;
}
</style>
