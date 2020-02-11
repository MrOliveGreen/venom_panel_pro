<?php 
include 'head.php';
if(isset($_SESSION['user_info'])){
	//print_r( $_SESSION['user_info'] );exit;

$con = new db_connect();
$connection=$con->connect();

$get = new Select_DB($con->connect);

if($connection==1){
 $lines=$get->get_lines($_SESSION['user_info']['user_id']);
 $subreseller_count = $get->get_subreseller_count($_SESSION['user_info']['user_id']);
 $last_activities = $get->get_last_activity($lines, 10);
 
 // $and = ($_SESSION['user_info']['user_type'] != 1) ? " WHERE user_id_fk = " . $_SESSION['user_info']['user_id'] : "";
}
?>

<!-- PAGE CONTENT -->
<?php
  if($_SESSION['user_info']['user_is_admin'] == 0 && !empty($_SESSION)){
  ?>
<div class="page-content"> 
  
  <!-- START X-NAVIGATION VERTICAL -->
  <ul class="x-navigation x-navigation-horizontal x-navigation-panel">
    <!-- TOGGLE NAVIGATION -->
    <li class="xn-icon-button"> <a href="#" class="x-navigation-minimize"><span class="fa fa-dedent"></span></a> </li>
    <!-- END TOGGLE NAVIGATION --> 
    <!-- SEARCH -->
    
    </li>
    <!-- END SEARCH --> 
    <!-- SIGN OUT -->
    <li class="xn-icon-button" style = "position:relative; float:right;">
     <a href="<?php echo SITE_URL; ?>logout.php" class="mb-control" data-box="#mb-signout" style="margin-left:-50px; width:100px"><span class="fa fa-sign-out"></span> Logout</a> </li>
    <li class="xn-icon-button" style = "position:relative; float:right;"> <h5 class = "top-reseller"> <?php echo $_SESSION['user_info']['user_name'].'('.$_SESSION['user_info']['user_credit'].' credit/s)'; ?></h5></li>
    <!-- END SIGN OUT -->
    
  </ul>
  <!-- END X-NAVIGATION VERTICAL --> 
  
  <!-- START BREADCRUMB -->
  <ul class="breadcrumb">
    <li><a href="#">Home</a></li>
  </ul>
  <!-- END BREADCRUMB --> 
  
  <!-- PAGE CONTENT WRAPPER -->
  <div class="page-content-wrap"> 
    
    <!-- START WIDGETS -->
    <div class="row">
      <div class="col-md-3"> 
        
        <!-- START WIDGET Lines--> 
        <!--<div class="widget widget-default widget-item-icon" onclick="location.href='../channels.php';">-->
        <div class="widget widget-success widget-item-icon"  style="cursor:pointer;">
          <div class="widget-item-left"> <span class="glyphicon glyphicon-play-circle"></span> </div>
          <div class="widget-data">
            <div class="widget-int num-count"><?php echo count($lines); ?></div>
            <div class="widget-title">My Lines</div>
          </div>
          <div class="widget-controls"> <a href="#" class="widget-control-right widget-remove" data-toggle="tooltip" data-placement="top" title="Remove Widget"><span class="fa fa-times"></span></a> </div>
        </div>
        <!-- END WIDGET Channel Numers --> 
        
      </div>
      <div class="col-md-3"> 
        
        <!-- START WIDGET Subreseller -->
        <div class="widget widget-info widget-item-icon">
          <div class="widget-item-left"> <span class="fa fa-user"></span> </div>
          <div class="widget-data">
            <div class="widget-int num-count"><?php echo $subreseller_count; ?></div>
            <div class="widget-title">SUBRESELLER</div>
          </div>
          <div class="widget-controls"> <a href="#" class="widget-control-right widget-remove" data-toggle="tooltip" data-placement="top" title="Remove Widget"><span class="fa fa-times"></span></a> </div>
        </div>
        <!-- END WIDGET USERS --> 
        
      </div>
      <div class="col-md-3"> 
        
        <!-- START WIDGET Credit -->
        <div class="widget widget-warning widget-item-icon">
          <div class="widget-item-left"> <span class="fa fa-user"></span> </div>
          <div class="widget-data">
            <div class="widget-int num-count"><?php echo $_SESSION['user_info']['user_credit']; ?></div>
            <div class="widget-title">My Credits </div>
          </div>
          <div class="widget-controls"> <a href="#" class="widget-control-right widget-remove" data-toggle="tooltip" data-placement="top" title="Remove Widget"><span class="fa fa-times"></span></a> </div>
        </div>
        <!-- END WIDGET USERS --> 
        
      </div>
        <!-- END WIDGET SLIDER --> 
        <div class="col-md-3"> 
        
        <!-- START WIDGET CLOCK -->
        <div class="widget widget-danger widget-padding-sm">
          <div class="widget-big-int plugin-clock">00:00</div>
          <div class="widget-subtitle plugin-date">Loading...</div>
          <div class="widget-controls"> <a href="#" class="widget-control-right widget-remove" data-toggle="tooltip" data-placement="left" title="Remove Widget"><span class="fa fa-times"></span></a> </div>
          <div class="widget-buttons widget-c3">
            <div class="col"> <a href="#"><span class="fa fa-clock-o"></span></a> </div>
            <div class="col"> <a href="#"><span class="fa fa-bell"></span></a> </div>
            <div class="col"> <a href="#"><span class="fa fa-calendar"></span></a> </div>
          </div>
        </div>
        <!-- END WIDGET CLOCK --> 
        
      </div>
    </div>
    
    <!-- END WIDGETS -->
    
    <div class="row">
      <div class="col-md-12"> 
        
        <!-- START CHANNELS TABLE -->
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Last 10 Online Activities</h3>
          </div>
          <div class="panel-body">
            <table class="display" id = "table" style = "width:100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Line Name</th>
                  <th>Stream Name</th>
                  <th>IP Address</th>
                  <th>Player</th>
                  <th>Activity Time</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $no = 1;
				        foreach($last_activities as $activity){
				        ?>
                <tr>
                  <td ><?php echo $no; $no++; ?></td>
                  <td><?php 
                    $line = $get->get_line($activity[3]);
                    echo $line['line_user'];
                  ?>
                  <td style="cursor:pointer;"><?php echo $activity[12]; ?></td>
                  <td style="cursor:pointer;"><?php echo $activity[6]; ?></td>
                  <td class = "ellipsis" style="cursor:pointer;"><?php echo $activity[4]; ?></td>
                  <td style="cursor:pointer;"><?php echo date ('d-m-Y h:i:s', $activity[5]); ?></td>
                  <?php } ?>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <!-- END CHANNELS TABLE--> 
      </div>
    </div>
    </div>
  </div>
  <!-- END PAGE CONTENT WRAPPER -->
  <footer>
    <!-- <p>All Copy &copy; Reserved</p> -->
  </footer>
</div>
<?php
  }
  else { ?>
    echo "<script>location.href='index.php'</script>";
<?php }?>
<!-- END PAGE CONTENT -->
</div>
<!-- END PAGE CONTAINER --> 
<!-- MESSAGE BOX-->
<div class="message-box animated fadeIn" data-sound="alert" id="mb-signout">
  <div class="mb-container">
    <div class="mb-middle">
      <div class="mb-title"><span class="fa fa-sign-out"></span> Log <strong>Out</strong> ?</div>
      <div class="mb-content">
        <p>Are you sure you want to log out?</p>
        <p>Press No if you want to continue work. Press Yes to logout current administrator.</p>
      </div>
      <div class="mb-footer">
        <div class="pull-right"> <a href="logout.php" class="btn btn-success btn-lg">Yes</a>
          <button class="btn btn-default btn-lg mb-control-close">No</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- END MESSAGE BOX--> 

<!-- START PRELOADS -->
<audio id="audio-alert" src="audio/alert.mp3" preload="auto"></audio>
<audio id="audio-fail" src="audio/fail.mp3" preload="auto"></audio>
<!-- END PRELOADS --> 

<!-- START SCRIPTS --> 
<!-- START PLUGINS --> 
<script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script> 
<script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script> 
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script> 
<!-- END PLUGINS --> 

<!-- START THIS PAGE PLUGINS--> 
<script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script> 
<script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script> 
<script type="text/javascript" src="js/plugins/scrolltotop/scrolltopcontrol.js"></script> 

<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script> 
<script type="text/javascript" src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script> 
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script> 

<script type="text/javascript" src="js/plugins/morris/raphael-min.js"></script> 
<script type="text/javascript" src="js/plugins/morris/morris.min.js"></script> 
<script type="text/javascript" src="js/plugins/rickshaw/d3.v3.js"></script> 
<script type="text/javascript" src="js/plugins/rickshaw/rickshaw.min.js"></script> 
<script type='text/javascript' src='js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'></script> 
<script type='text/javascript' src='js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js'></script> 
<script type='text/javascript' src='js/plugins/bootstrap/bootstrap-datepicker.js'></script> 
<script type="text/javascript" src="js/plugins/owl/owl.carousel.min.js"></script> 
<script type="text/javascript" src="js/plugins/moment.min.js"></script> 
<script type="text/javascript" src="js/plugins/daterangepicker/daterangepicker.js"></script> 
<!-- END THIS PAGE PLUGINS--> 

<!-- START TEMPLATE --> 

<script type="text/javascript" src="js/plugins.js"></script> 
<script type="text/javascript" src="js/actions.js"></script> 
<!-- <script type="text/javascript" src="js/demo_dashboard.js"></script>  -->
<script>
  $(document).ready(function(){

var table = $('#table').DataTable( {
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true
    } );
});
</script> 
<!-- END TEMPLATE --> 
<!-- END SCRIPTS -->
</body></html><?php }else{
	
	echo "You are not authorized to visit this page direclty,Sorry";
	} ?>
