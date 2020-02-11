<?php 
include 'head.php';
$con = new db_connect();
$connection=$con->connect();

$get = new Select_DB($con->connect);

$alert = '';
$flag = 0;

if(!isset($_GET['id']))
  $id = base64_encode('0');
else
  $id = $_GET['id'];

if(!isset($_GET['status']))
  $status = base64_encode('all');
else
  $status = $_GET['status'];

if(isset($_SESSION['user_info'])){
  //print_r( $_SESSION['user_info'] );exit;


if(isset($_POST['action_all']))
{
  $action = base64_decode($_POST['action_all']);

  if($action == 'play')
  {
    $result = $get->set_stream_all(base64_decode($id), base64_decode($status), 3);
    $status = base64_encode('3');
  }  
  else if($action == 'stop')
  {
     $result = $get->set_stream_all(base64_decode($id), base64_decode($status), 5);
     $status = base64_encode('2');
  }  
  if($action == 'restart')
  {
     $result = $get->set_stream_all(base64_decode($id), base64_decode($status), 4);
     $status = base64_encode('4');
  }  

  if($result)
  {
    $flag = 1;
    $alert = 'All Stream Status Changed Successfully!';
  }
  else
  {
    $flag = 2;
    $alert = 'Database Operation Failed!';
  }
}

if(isset($_POST['delete_id']))
{
  $del_id = base64_decode($_POST['delete_id']);
  $result = $get->delete_stream($del_id);
  if($result)
  {
    $flag = 1;
    $alert = 'Stream Deleted Successfully!';
  }
  else
  {
    $flag = 2;
    $alert = 'Stream Operation Failed!';
  }
}

if(isset($_POST['pause_id']))
{
  $pause_id = base64_decode($_POST['pause_id']);
  $server_id = base64_decode($_POST['server_id']);
  $result = $get->change_stream_status($pause_id, $server_id, 5);
  if($result)
  {
    $flag = 1;
    $alert = 'Stream Paused Successfully!';
    $status = base64_encode('all');
  }
  else
  {
    $flag = 2;
    $alert = 'Database Operation Failed!';
  }
}

if(isset($_POST['play_id']))
{
  $play_id = base64_decode($_POST['play_id']);
  $server_id = base64_decode($_POST['server_id']);
  $result = $get->change_stream_status($play_id, $server_id, 3);
  echo "play_id".$play_id;
  if($result)
  {
    $flag = 1;
    $alert = 'Stream Allowed Successfully!';
    $status = base64_encode('all');
  }
  else
  {
    $flag = 2;
    $alert = 'Database Operation Failed!';
  }
}

if(isset($_POST['refresh_id']))
{
  $refresh_id = base64_decode($_POST['refresh_id']);
  $server_id = base64_decode($_POST['server_id']);
  $result = $get->change_stream_status($refresh_id, $server_id, 4);
  if($result)
  {
    $flag = 1;
    $alert = 'Stream Refreshing Now!';
    $status = base64_encode('all');
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

$servers = $get->get_servers();

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
    <li><a href="#"> Stream Management</a></li>
    <li><a href="#"> Streams</a></li>
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
            <h3 class="panel-title">Manage Streams</h3>
          </div>
          <div class="panel-body">

            <div class = "row">
              <div class = "col-md-2">
                <div class="form-group">

                  <div class="input-group">
                    <h5 class="help-block">Choose Server</h5> </div>
                    <select id = "server_id" style = "margin-top:10px;margin-bottom:20px; border-radius: 4px; height:30px; width:160px;" onchange="serverFilter(this)">
                      <option value = "<?php echo base64_encode('0'); ?>">All Server</option>
                        <?php while($server = mysqli_fetch_assoc($servers)){
                            echo '<option value="'.base64_encode($server['server_id']).'"'.(base64_encode($server['server_id']) == $id ? "selected" : "").'>'.$server['server_name'].'</option>';
                        }?> 
                    </select>
                  </div>
                </div>
                <div class = "col-md-2">
                <div class="form-group">

                  <div class="input-group">
                    <h5 class="help-block">Choose Stream Status</h5> </div>
                    <select id = "server_id" style = "margin-top:10px;margin-bottom:20px; border-radius: 4px; height:30px; width:160px;" onchange="statusFilter(this)">
                      <option value = "<?php echo base64_encode('all'); ?>" <?php echo base64_encode('all') == $status? "selected" : ""; ?>>All status</option>
                      <option value = "<?php echo base64_encode('0'); ?>" <?php echo base64_encode('0') == $status? "selected" : ""; ?>>Offline</option>
                      <option value = "<?php echo base64_encode('1'); ?>" <?php echo base64_encode('1') == $status? "selected" : ""; ?>>Online</option>
                      <option value = "<?php echo base64_encode('2'); ?>" <?php echo base64_encode('2') == $status? "selected" : ""; ?>>Paused</option>
                      <option value = "<?php echo base64_encode('3'); ?>" <?php echo base64_encode('3') == $status? "selected" : ""; ?>>Starting</option>
                      <option value = "<?php echo base64_encode('4'); ?>" <?php echo base64_encode('4') == $status? "selected" : ""; ?>>Restarting</option>
                      <option value = "<?php echo base64_encode('5'); ?>" <?php echo base64_encode('5') == $status? "selected" : ""; ?>>Stopping</option>
                      <option value = "<?php echo base64_encode('6'); ?>" <?php echo base64_encode('6') == $status? "selected" : ""; ?>>Stopped</option>
                      <option value = "<?php echo base64_encode('7'); ?>" <?php echo base64_encode('7') == $status? "selected" : ""; ?>>Deleted</option>
                    </select>
                  </div>
                </div>
                <div calss = "col-md-2">
                  <div class = "row">
                    <div class = "input-group">
                      <h5 class="help-block">Actions For All Streams </h5> </div>
                      <form  style = "margin-top:10px;" method = "post" action="manage_stream.php?id=<?php echo $id; ?>&status=<?php echo $status; ?>">
                      <input type = "text" name="action_all" value="<?php echo base64_encode('play'); ?>" hidden>
                      <button type = "submit" class="btn btn-info btn-rounded btn-sm" onclick="return confirm(\'Are you sure want to start all streams?\');"><span class="fa fa-play"></span></button>
                      </form>
                      <form  style = "margin-top:10px; margin-left:30px;" method = "post" action="manage_stream.php?id=<?php echo $id; ?>&status=<?php echo $status; ?>">
                      <input type = "text" name="action_all" value="<?php echo base64_encode('stop'); ?>" hidden>
                      <button type = "submit" class="btn btn-danger btn-rounded btn-sm" onclick="return confirm(\'Are you sure want to stop all streams?\');"><span class="fa fa-pause"></span></button>
                      </form>
                      <form  style = "margin-top:10px; margin-left:30px;" method = "post" action="manage_stream.php?id=<?php echo $id; ?>&status=<?php echo $status; ?>">
                      <input type = "text" name="action_all" value="<?php echo base64_encode('restart'); ?>" hidden>
                      <button type = "submit" class="btn btn-warning btn-rounded btn-sm" onclick="return confirm(\'Are you sure want to restart all streams?\');"><span class="fa fa-refresh"></span></button>
                      </form>
                    </div>
                </div>
                </div>
              </div>


            <table class="display" id = "table" style = "width:100%">
              <thead>
                <tr>
                  <th>TS</th>
                  <th>NAME</th>
                  <th>STATUS</th>
                  <th>UPTIME</th>
                  <th>SOURCE</th>
                  <th>ONLINE USER</th>
                  <th>BITRATE</th>
                  <th>ACTION ON SERVERS</th>
                  <th>STREAM ACTION</th>
                </tr>
              </thead>
              <tbody>
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
$("#streams_li").addClass("active");

$(document).ready(function() {
  var id = '<?php echo $id; ?>';
  var status = '<?php echo $status; ?>';
  

	$('#table').dataTable( {
		responsive: true,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "common//stream_processing.php?id=" + id + '&status=' + status,
		"aoColumns":[
        {"bSortable": true},
        {"bSortable": true},
        {"bSortable": true},
        {"bSortable": false},
        {"bSortable": false},
        {"bSortable": false},
        {"bSortable": false},
        {"bSortable": false},
        {"bSortable": false}
    ]
	} );
} );
});

function serverFilter(sel)
{
    selected = sel.options[sel.selectedIndex].value;
    var status = '<?php echo $status; ?>';
    //alert(selected);

    //if(status == "no")
      //  window.location.href = "manage_stream.php?id=" + selected;
    //else
        window.location.href = "manage_stream.php?id=" + selected + '&status=' + status;
}

function statusFilter(sel)
{
    selected = sel.options[sel.selectedIndex].value;
    var id = '<?php echo $id; ?>';
    //alert(selected);

    //if(status == "no")
      //  window.location.href = "manage_stream.php?id=" + selected;
    //else
        window.location.href = "manage_stream.php?id=" + id + '&status=' + selected;
}

</script>
<!-- END TEMPLATE --> 
<!-- END SCRIPTS -->
</body></html><?php }else{
  
  echo "You are not authorized to visit this page direclty,Sorry";
  } ?>
