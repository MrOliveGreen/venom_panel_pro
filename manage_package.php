<?php 
include 'head.php';
$con = new db_connect();
$connection=$con->connect();

$get = new Select_DB($con->connect);

$flag = 0;
$alert = '';


if(isset($_GET['text']))
{
  $alert = base64_decode($_GET['text']);
  $flag = 1;
}

if(isset($_SESSION['user_info'])){
	//print_r( $_SESSION['user_info'] );exit;

if(isset($_POST['delete_id']))
{
  $id = base64_decode($_POST['delete_id']);
  $result = $get->delete_package($id);

  if($result)
  {
    $flag = 1;
    $alert = 'package Category Deleted Successfully!';
  }
  else
  {
    $flag = 2;
    $alert = 'Database Operation Failed!';
  }
}

if($connection==1){
 $package_obj = $get->get_packages();
}
?>

<!-- PAGE CONTENT -->
<?php
  if($_SESSION['user_info']['user_is_admin'] == 1 && !empty($_SESSION)){
  ?>
<div class="page-content"> 
  
  <!-- START X-NAVIGATION VERTICAL -->
  <ul class="x-navigation x-navigation-horizontal x-navigation-panel">
    <!-- TOGGLE NAVIGATION -->
    <li class="xn-icon-button"> <a href="#" class="x-navigation-minimize"><span class="fa fa-dedent"></span></a> </li>
    <!-- END TOGGLE NAVIGATION --> 
    <!-- SEARCH -->
    
    
    <!-- END SEARCH --> 
    <!-- SIGN OUT -->
    <li class="xn-icon-button" style = "position:relative; float:right;">
     <a href="<?php echo SITE_URL; ?>logout.php" class="mb-control" data-box="#mb-signout" style="margin-left:-50px; width:100px"><span class="fa fa-sign-out"></span> Logout</a> </li>
  </ul>
  <!-- END X-NAVIGATION VERTICAL --> 
  
  <!-- START BREADCRUMB -->
  <ul class="breadcrumb">
    <li><a href="#">Admin </a></li>
    <li><a href="#"> Package Management </a></li>
    <li><a href="#"> Manage Packages </a></li>
  </ul>
  <!-- END BREADCRUMB --> 
  
  <!-- PAGE CONTENT WRAPPER -->
  <div class="page-content-wrap"> 
    <div class="row">
    <?php if($flag==1){?>
          <div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
            <strong>Notice!</strong>
            <?php echo $alert; ?>
          </div>
          <?php }
          else if($flag == 2){?>
            <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
            <strong>Error!</strong>
            <?php echo $alert; ?>
          </div>
          <?php } ?>
    </div>
    <div class="row">
      <div class="col-md-12"> 
        
        <!-- START CHANNELS TABLE -->
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Manage Packages</h3>
          </div>
          <div class="panel-body">
            <table class="display" id = "table" style = "width:100%">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>PACKAGE NAME</th>
                  <th>PACKAGE DURATION</th>
                  <th>PACKAGE CREDIT</th>
                  <th>ACTION</th>
                </tr>
              </thead>
              <tbody>
                <?php 
				        while($package = mysqli_fetch_assoc($package_obj)){
				        ?>
                <tr>
                  <td style="cursor:pointer;"><?php echo $package['package_id']; ?></td>
                  <td style="cursor:pointer;"><?php echo $package['package_name']; ?></td>
                  <td style="cursor:pointer;"><?php 
                    if($package['package_duration_in'] == 0)
                      echo $package['package_duration'].' Hour';
                    else if($package['package_duration_in'] == 1)
                      echo $package['package_duration'].' Day';
                    else if($package['package_duration_in'] == 2)
                      echo $package['package_duration'].' Week';
                    else if($package['package_duration_in'] == 3)
                      echo $package['package_duration'].' Month';
                    else if($package['package_duration_in'] == 4)
                      echo $package['package_duration'].' Year';
                  ?></td>
                  <td style="cursor:pointer;"><?php echo $package['package_credit']; ?></td>
                  <td>
                    <button style = "float:left; position:relative;" class="btn btn-default btn-rounded btn-sm"><a href = "edit_package.php?package_id=<?php echo base64_encode($package['package_id']);?>"><span class="fa fa-pencil"></span></button>
                    <form style = "float:left; position:relative;" method = "post" enctype="multipart/form-data">
                      <input type = "text" name="delete_id" value="<?php echo base64_encode($package['package_id']);?>" hidden>
                      <button type = "submit" class="btn btn-danger btn-rounded btn-sm" onclick="return confirm('Are you sure want to delete?');"><span class="fa fa-times"></span></button>
                    </form>
                     </td>

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

<!-- <script type="text/javascript" src="js/general/jquery/dist/jquery.min.js"></script>  -->

<script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script> 
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script> 
<!-- END PLUGINS --> 

<!-- START THIS PAGE PLUGINS--> 
<!-- <script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>  -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script> 
<script type="text/javascript" src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script> 
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script> 

<script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script> 
<script type="text/javascript" src="js/plugins.js"></script> 
<script type="text/javascript" src="js/actions.js"></script> 
<!-- <script type="text/javascript" src="js/demo_dashboard.js"></script>  -->

<script>
  $(document).ready(function(){
$("#package_li").addClass("active");

  var table = $('#table').DataTable( {
        responsive: true
    } );
});

</script>

</body></html><?php }else{
	
	echo "You are not authorized to visit this page direclty,Sorry";
	} ?>
