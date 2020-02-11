<?php 
include 'head.php';
$con = new db_connect();
$connection=$con->connect();

$get = new Select_DB($con->connect);

$alert = '';
$flag = 0;

if(isset($_SESSION['user_info'])){
	//print_r( $_SESSION['user_info'] );exit;

if(isset($_POST['delete_id']))
{
  $id = base64_decode($_POST['delete_id']);
  $result = $get->delete_server($id);
  if($result)
  {
    $flag = 1;
    $alert = 'Server Deleted Successfully!';
  }
  else
  {
    $flag = 2;
    $alert = 'Database Operation Failed!';
  }
}

if(isset($_POST['reinstall_id']))
{
  $id = base64_decode($_POST['reinstall_id']);
  $result = $get->set_server_status($id, 2);
  if($result)
  {
    $flag = 1;
    $alert = 'Server is reinstalling!';
  }
  else
  {
    $flag = 2;
    $alert = 'Database Operation Failed!';
  }
}

if(isset($_GET['text']))
{
  $alert = base64_decode($_GET['text']);
  $flag = 1;
}

if($connection==1){
 $server_obj = $get->get_servers();
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
    <li><a href="#">Admin</a></li>
    <li><a href="#">Server Management</a></li>
    <li><a href="#">Manage Servers</a></li>
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
            <h3 class="panel-title">Manage Servers</h3>
          </div>
          <div class="panel-body">
            <table class="display" id = "table" style = "width:100%">
              <thead>
                <tr>
                  <th>SERVER NAME</th>
                  <th>SERVER IP</th>
                  <th>SSH PORT</th>
                  <th>BROADCAST PORT</th>
                  <th>RTMP PORT</th>
                  <th>SERVER PROVIDER</th>
                  <th>SERVER STATUS</th>
                  <th>SERVER DNS</th>
                  <th style = "text-align: right;">ACTION</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $i = 0;
				        while($server = mysqli_fetch_assoc($server_obj)){
				        ?>
                <tr>
                  <td style="cursor:pointer;"><?php echo $server['server_name']; ?></td>
                  <td style="cursor:pointer;"><?php echo $server['server_ip']; ?></td>
                  <td style="cursor:pointer;"><?php echo $server['server_ssh_port']; ?></td>
                  <td style="cursor:pointer;"><?php echo $server['server_broadcast_port']; ?></td>
                  <td style="cursor:pointer;"><?php echo $server['server_rtmp_port']; ?></td>
                  <td style="cursor:pointer;" >
                    <input type = "text" style = "background: none; border: none;" id = "isp_<?php echo $i;?>" disabled/>
                  </td>
                   <td style="cursor:pointer;"><?php 
                   if($server['server_status'] == 0)
                    echo '<span class = "badge bg-light"> Offline </span>';
                  else if($server['server_status'] == 1)
                    echo '<span class = "badge bg-success"> Online </span>';
                  else if($server['server_status'] == 2)
                    echo '<span class = "badge bg-dark"> Installing </span>';
                  else if($server['server_status'] == 3)
                    echo '<span class = "badge bg-danger"> SSH Password False </span>';
                  else if($server['server_status'] == 4)
                    echo '<span class = "badge bg-danger"> Mysql False </span>';
                  else if($server['server_status'] == 5)
                    echo '<span class = "badge bg-danger"> Installation Not Found </span>';
                  else if($server['server_status'] == 6)
                    echo '<span class = "badge bg-alert"> Update </span>';
                  else if($server['server_status'] == 7)
                    echo '<span class = "badge bg-info"> Remake </span>';
                   ?></td>
                  <td style="cursor:pointer;"><span class = "badge bg-info"><?php echo $server['server_dns_name']; ?></span></td>
                  <td><a href="#">
                    <form class = "go-right" method = "post" enctype="multipart/form-data">
                      <input type = "text" name="delete_id" value="<?php echo base64_encode($server['server_id']);?>" hidden>
                      <button type = "submit" class="btn btn-danger btn-rounded btn-sm" onclick="return confirm('Are you sure want to delete server?');"><span class="fa fa-times"></span></button>
                    </form>
                    </a>
                    <form class = "go-right" method = "post" enctype="multipart/form-data">
                      <input type = "text" name="reinstall_id" value="<?php echo base64_encode($server['server_id']);?>" hidden>
                      <button class="go-right btn btn-default btn-rounded btn-sm" onclick="return confirm('Are you sure want to reinstall server?');"><span class="fa fa-refresh"></span></button>
                     </form>
                     <a href="#">
                    <button class="go-right btn btn-default btn-rounded btn-sm"><a href = "edit_server.php?server_id=<?php echo base64_encode($server['server_id']);?>"><span class="fa fa-pencil"></span></button>
                    </a>
                     </td>
                  <?php $i ++;} ?>
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
$("#server_li").addClass("active");

  var table = $('#table').DataTable( {
        responsive: true,
        paging: false,
        "aaSorting": []
    } );

  $.ajax({
        type: "POST",
        url: "./isp.php",
        data: { isp: 'isp'},
        dataType: "json",
        success: function(result) {
            for(var i = 0; i < result.length; i ++)
            {
                console.log("#isp_" + i);
                $("#isp_" + i)[0].value = result[i];
            }
        }
        
    });
});

</script>
<!-- END THIS PAGE PLUGINS--> 

<!-- <script type="text/javascript" src="js/general/bootstrap/js/src/util.js"></script>  -->

<!-- <script type="text/javascript" src="js/general/bootstrap/js/src/modal.js"></script>  -->

<!-- START TEMPLATE --> 

<!-- Remember to include jQuery :) -->


<!-- <script type="text/javascript" src="js/demo_dashboard.js"></script>  -->
<!-- <script>
$(document).ready(function(){
  $("#dashboard").addClass("active");
});
</script>  -->
<!-- <script>
  function clicked()
  {
      $('#myModal').modal('show');
  }
</script> -->
<!-- END TEMPLATE --> 
<!-- END SCRIPTS -->
</body></html><?php }else{
	
	echo "You are not authorized to visit this page direclty,Sorry";
	} ?>
