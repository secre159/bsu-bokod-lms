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
      
      <!-- HOME -->
      <li class="header" style="background: linear-gradient(135deg, #004d00 0%, #006400 100%); color: #FFD700; font-weight: 700; padding: 12px 15px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">HOME</li>
      
      <li>
        <a href="../index.php">
          <i class="fa fa-home" style="color: #fee900ff;"></i> 
          <span style="font-weight:800;">User Homepage</span>
        </a>
      </li>

      <li>
        <a href="home.php">
          <i class="fa fa-dashboard" style="color: #fee900ff;"></i> 
          <span style="font-weight:800;">Dashboard</span>
        </a>
      </li>

      <!-- MANAGE -->
      <li class="header" style="background: linear-gradient(135deg, #004d00 0%, #006400 100%); color: #FFD700; font-weight: 700; padding: 12px 15px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">MANAGE</li>

      <li>
        <a href="transactions.php">
          <i class="fa fa-refresh" style="color: #f6ff00ff;"></i> 
          <span style="font-weight: 500;">Transactions</span>
        </a>
      </li>

      <!-- Books -->
      <li class="treeview">
        <a href="#">
          <i class="fa fa-book" style="color: #f6ff00ff;"></i>
          <span style="font-weight: 500;">Books</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right" style="color: #f6ff00ff;"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="book.php"><i class="fa fa-cube" style="color: #f6ff00ff;"></i>Physical Book List</a></li>
          <li><a href="calibre_books.php"><i class="fa fa-tablet" style="color: #f6ff00ff;"></i>E-Book List</a></li>
          <li><a href="category.php"><i class="fa fa-tags" style="color: #f6ff00ff;"></i>Manage Category</a></li>
          <li><a href="subjects.php"><i class="fa fa-paperclip" style="color: #f6ff00ff;"></i>Manage Subject</a></li>
          <li><a href="new_books.php"><i class="fa fa-calendar" style="color: #f6ff00ff;"></i>Recent Books</a></li>
          <li><a href="book_count_monthly.php"><i class="fa fa-calendar" style="color: #f6ff00ff;"></i>Monthly Book Count</a></li>
        </ul>
      </li>

      <li>
        <a href="student.php">
          <i class="fa fa-graduation-cap" style="color: #f6ff00ff;"></i> 
          <span style="font-weight: 500;">Student List</span>
        </a>
      </li>

      <li>
        <a href="faculty.php">
          <i class="fa fa-users" style="color: #f6ff00ff;"></i> 
          <span style="font-weight: 500;">Employees</span>
        </a>
      </li>

      <li>
        <a href="post.php">
          <i class="fa fa-bullhorn" style="color: #f6ff00ff;"></i> 
          <span style="font-weight: 500;">Posts / News</span>
        </a>
      </li>

      <li>
        <a href="logbook.php">
          <i class="fa fa-history" style="color: #f6ff00ff;"></i> 
          <span style="font-weight: 500;">User Logbook</span>
        </a>
      </li>

      <!-- ⭐ NEW: SUGGESTED BOOKS -->
      <li class="header" style="background: linear-gradient(135deg, #004d00 0%, #006400 100%); color: #FFD700; font-weight: 700; padding: 12px 15px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">SUGGESTED BOOKS</li>

      <li>
        <a href="suggested_books.php">
          <i class="fa fa-lightbulb-o" style="color: #51ff00ff;"></i>
          <span style="font-weight: 500;">User Suggestions</span>
        </a>
      </li>

      <!-- ARCHIVE SECTION -->
      <li class="header" style="background: linear-gradient(135deg, #004d00 0%, #006400 100%); color: #FFD700; font-weight: 700; padding: 12px 15px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">ARCHIVE</li>

      <!-- Archived Books -->
      <li class="treeview">
        <a href="#">
          <i class="fa fa-archive" style="color: #51ff00ff;"></i>
          <span style="font-weight: 500;">Books</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right" style="color: #006400;"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="archived_book.php"><i class="fa fa-circle-o" style="color: #888;"></i> Book List</a></li>
          <li><a href="archived_category.php"><i class="fa fa-circle-o" style="color: #888;"></i> Category</a></li>
          <li><a href="archived_calibre_books.php"><i class="fa fa-circle-o" style="color: #888;"></i> E-Books</a></li>
          <li><a href="archived_subject.php"><i class="fa fa-circle-o" style="color: #888;"></i> Subjects</a></li>
        </ul>
      </li>

      <!-- Archived Students -->
      <li class="treeview">
        <a href="#">
          <i class="fa fa-users" style="color: #51ff00ff;"></i>
          <span style="font-weight: 500;">Students</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right" style="color: #006400;"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="archived_student.php"><i class="fa fa-circle-o" style="color: #888;"></i> Student List</a></li>
        </ul>
      </li>

      <!-- Archived Faculty -->
      <li class="treeview">
        <a href="#">
          <i class="fa fa-users" style="color: #51ff00ff;"></i>
          <span style="font-weight: 500;">Faculty / Employees</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right" style="color: #006400;"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="archived_faculty.php"><i class="fa fa-circle-o" style="color: #888;"></i> Faculty List</a></li>
        </ul>
      </li>

      <!-- SYSTEM SECTION -->
      <li class="header" style="background: linear-gradient(135deg, #004d00 0%, #006400 100%); color: #FFD700; font-weight: 700; padding: 12px 15px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">SYSTEM</li>

      <li>
        <a href="backup_manager.php">
          <i class="fa fa-hdd-o" style="color: #51ff00ff;"></i>
          <span style="font-weight: 500;">Backup Manager</span>
        </a>
      </li>

      <li>
        <a href="database_schema_fix.php">
          <i class="fa fa-wrench" style="color: #51ff00ff;"></i>
          <span style="font-weight: 500;">Schema Fix</span>
        </a>
      </li>

    </ul>

  </section>
  <!-- /.sidebar -->
</aside>
