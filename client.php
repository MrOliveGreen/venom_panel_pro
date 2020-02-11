<?php 
include 'head.php';
if(isset($_SESSION['user_info'])){
	//print_r( $_SESSION['user_info'] );exit;
  if(!isset($_GET['id']))
    $category_id = 1;
  else
    $category_id = base64_decode($_GET['id']);

$con = new db_connect();
$connection=$con->connect();

$get = new Select_DB($con->connect);

if($connection==1){
  $category = $get->get_category($category_id);
  $bouquets = $get->get_bouquets($category_id);
}
?>

<!-- PAGE CONTENT -->
<?php
  if($_SESSION['user_info']['user_is_admin'] == 0 && !empty($_SESSION)){
  ?>
<div class="page-content"> 
  <ul class="x-navigation x-navigation-horizontal x-navigation-panel">
    <!-- TOGGLE NAVIGATION -->
    <li class="xn-icon-button"> <a href="#" class="x-navigation-minimize"><span class="fa fa-dedent"></span></a> </li>
    <!-- END TOGGLE NAVIGATION --> 
    
    <!-- SIGN OUT -->
    <li class="xn-icon-button pull-right"> <a href="<?php echo SITE_URL; ?>logout.php" class="mb-control" data-box="#mb-signout" style="width:100px"><span class="fa fa-sign-out"></span> Logout</a> </li>
    <!-- END SIGN OUT -->
    
  </ul>
  <!-- END X-NAVIGATION VERTICAL --> 
  
  <!-- START BREADCRUMB -->
  <ul class="breadcrumb">
    <li><a href="#">Client</a></li>
    <li><a href="#"><?php echo $category['serie_category_name'];?></a></li>
  </ul>

  <div class="page-content-wrap">
    <div class="row">
      <div class="col-md-12"> 
        
        <!-- START CHANNELS TABLE -->
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><?php echo $category['serie_category_name'];?></h3>
          </div>
          <div class="panel-body">
            <table class="table datatable">
              <thead>
                <tr>
                  <th>Bouquet No</th>
                  <th>Bouquet Name</th>
                  <th>Total streams</th>
                  <th>Expiration Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $no = 1;
                foreach($bouquets as $bouquet){
                ?>
                <tr>
                  <td ><?php echo $no; $no++; ?></td>
                  <td style="cursor:pointer;"><?php echo $bouquet['bouquet_name']; ?></td>
                  <td style="cursor:pointer;"><?php
                    $count = count($bouquet['bouquet_streams']);
                    if($count)
                      echo $count;
                    else{
                      $count = count($bouquet['bouquet_movies']);
                    } ?></td>
                  <td style="cursor:pointer;"><?php  ?></td>
                  <td><a href="#">
                    <button class="btn btn-info btn-rounded btn-sm"><span class="fa fa-play"></span></button>
                    </a> <a href="#">
                    <button class="btn btn-success btn-rounded btn-sm"><span class="fa fa-address-card"></span></button>
                    </a> </td>
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
        <p>Press No if you want to continue work. Press Yes to logout current client.</p>
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
<script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script> 
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
<script type="text/javascript" src="js/demo_dashboard.js"></script> 
<script>
$(document).ready(function(){
$("#dashboard").addClass("active");
});
</script> 
<!-- END TEMPLATE --> 
<!-- END SCRIPTS -->
</body></html><?php }else{
	
	echo "You are not authorized to visit this page direclty,Sorry";
	} ?>
