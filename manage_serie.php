<?php 
include 'head.php';
$con = new db_connect();
$connection=$con->connect();

$get = new Select_DB($con->connect);

$alert = '';
$flag = 0;

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
  $result = $get->delete_serie($id);
  if($result)
  {
    $flag = 1;
    $alert = 'Serie Deleted Successfully!';
  }
  else
  {
    $flag = 2;
    $alert = 'Serie Operation Failed!';
  }
}

$series = $get->get_series();
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
    
    </li>
    <!-- END SEARCH --> 
    <!-- SIGN OUT -->
    <li class="xn-icon-button" style = "position:relative; float:right;">
     <a href="<?php echo SITE_URL; ?>logout.php" class="mb-control" data-box="#mb-signout" style="margin-left:-50px; width:100px"><span class="fa fa-sign-out"></span> Logout</a> </li>
    <!-- END SIGN OUT -->
    
  </ul>
  <!-- END X-NAVIGATION VERTICAL --> 
  
  <!-- START BREADCRUMB -->
  <ul class="breadcrumb">
  	<li><a href="#"> Admin </a></li>
    <li><a href="#"> Serie Management</a></li>
    <li><a href="#"> Series</a></li>
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
            <h3 class="panel-title">Manage Series</h3>
          </div>
          <div class="panel-body">
            <table class="display" id = "table" style = "width:100%">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>POSTER</th>
                  <th>NAME</th>
                  <th>GENRE</th>
                  <th>RELEASE DATE</th>
                  <th>SERIE DIRECTOR</th>
                  <th>ACTION</th>
                </tr>
              </thead>
              <tbody>
                <?php while($serie = mysqli_fetch_assoc($series)){?>
                <tr>
                  <td><?php echo $serie['serie_id']; ?></td>
                  <td><img style = "width:60px; height:100px;" src = "<?php echo $serie['serie_pic']; ?>" /></td>
                  <td><?php echo $serie['serie_name']; ?></td>
                  <td><?php echo $serie['serie_genre']; ?></td>
                  <td><?php echo $serie['serie_release_date']; ?></td>
                  <td><?php echo $serie['serie_director']; ?></td>
                  <td>
                    <form class = "go-right" method = "post" enctype="multipart/form-data">
                      <input type = "text" name="delete_id" value="<?php echo base64_encode($serie['serie_id']);?>" hidden>
                      <button type = "submit" class="btn btn-danger btn-rounded btn-sm" onclick="return confirm('Are you sure want to delete serie?');"><span class="fa fa-times"></span></button>
                    </form>
                    <a  href="#">
                    <button class="go-right btn btn-default btn-rounded btn-sm"><a href = "edit_serie.php?serie_id=<?php echo base64_encode($serie['serie_id']);?>"><span class="fa fa-pencil"></span></button>
                    </a>
                    <a href="#">
                    <button class="go-right btn btn-warning btn-rounded btn-sm"><a href = "add_episode.php?serie_id=<?php echo base64_encode($serie['serie_id']);?>"><span class="fa fa-plus"></span></button>
                    </a>
                    <a href="#">
                    <button class="go-right btn btn-info btn-rounded btn-sm"><a href = "add_multiple_episodes.php?serie_id=<?php echo base64_encode($serie['serie_id']);?>"><span class="fa fa-cloud"></span></button>
                    </a>
                    
                  
                  </td>
                </tr>
              <?php }?>
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
<script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script> 
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script> 
<!-- END PLUGINS --> 

<!-- START THIS PAGE PLUGINS--> 
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script> 
<script type="text/javascript" src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script> 
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script> 
<!-- START TEMPLATE --> 

<script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script> 
<script type="text/javascript" src="js/plugins.js"></script> 
<script type="text/javascript" src="js/actions.js"></script> 

<script>
$(document).ready(function(){
$("#series_li").addClass("active");

$(document).ready(function() {
  	$('#table').dataTable( {
  		responsive: true,
  	} );
} );
});
</script>
<!-- END TEMPLATE --> 
<!-- END SCRIPTS -->
</body></html><?php }else{
  
  echo "You are not authorized to visit this page direclty,Sorry";
  } ?>
