<?php 
include 'head.php';
$con = new db_connect();
$connection=$con->connect();

$get = new Select_DB($con->connect);

$alert = '';
$flag = 0;

if(isset($_SESSION['user_info'])){
  //print_r( $_SESSION['user_info'] );exit;

if(isset($_GET['text']))
{
  $alert = base64_decode($_GET['text']);
  $flag = 1;
}

if(isset($_POST['delete_id']))
{
  $id = base64_decode($_POST['delete_id']);
  $result = $get->delete_user($id);
  if($result)
  {
    $flag = 1;
    $alert = 'User Deleted Successfully!';
  }
  else
  {
    $flag = 2;
    $alert = 'Database Operation Failed!';
  }
}

if(isset($_POST['disable_id']))
{
  $id = base64_decode($_POST['disable_id']);
  $result = $get->change_subreseller_status($id, 0);
  if($result)
  {
    $flag = 1;
    $alert = 'User Disabled Successfully!';
  }
  else
  {
    $flag = 2;
    $alert = 'Database Operation Failed!';
  }
}

if(isset($_POST['enable_id']))
{
  $id = base64_decode($_POST['enable_id']);
  $result = $get->change_subreseller_status($id, 1);
  if($result)
  {
    $flag = 1;
    $alert = 'User Enabled Successfully!';
  }
  else
  {
    $flag = 2;
    $alert = 'Database Operation Failed!';
  }
}

if($connection==1){
 $users = $get->get_user_obj();
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
    <li><a href="#">Admin</a></li>
    <li><a href="#">Reseller Management</a></li>
    <li><a href="#">Manage Users</a></li>
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
            <h3 class="panel-title">Manage Users</h3>
          </div>
          <div class="panel-body">
            <table class="display" id = "table" style = "width:100%">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>RESELLER NAME</th>
                  <th>STATUS</th>
                  <th>CREDIT</th>
                  <th>IS ADMIN</th>
                  <th>LINES</th>
                  <th>OWNER</th>
                  <th>CREATOR</th>
                  <th>LAST LOGIN</th>
                  <th>NOTE</th>
                  <th>LOG</th>
                  <th>ACTION</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                while($user = mysqli_fetch_assoc($users)){
                ?>
                <tr>
                  <td style="cursor:pointer;"><?php echo $user['user_id']; ?></td>
                  <td style="cursor:pointer;"><?php echo $user['user_name']; ?></td>
                  <td style="cursor:pointer;"><?php 
                  if($user['user_status'] == '1')
                    echo '<span class = "badge bg-success"> Enabled </span>';
                  else
                    echo '<span class = "badge bg-dark"> Disabled </span>';
                   ?></td>
                  <td style="cursor:pointer;"><?php echo $user['user_credit']; ?></td>
                  <td style="cursor:pointer;"><?php 
                  if($user['user_is_admin'] == '1')
                    echo '<span class = "badge bg-light"> YES </span>';
                   ?></td>
                  <td style="cursor:pointer;"><?php echo $get->count_lines($user['user_id']); ?></td>
                  <td style="cursor:pointer;"><?php 
                    if($user['user_owner_id'] != 0)
                      {
                        $owner = $get->get_user($user['user_owner_id']); 
                        echo $owner['user_name'];
                      }
                      else
                        echo 'admin';
                      ?></td>
                  <td style="cursor:pointer;"><?php 
                  if($user['user_creator_id'] != 0)
                  {
                    $creator = $get->get_user($user['user_creator_id']); 
                    echo $creator['user_name'];
                  }
                  else
                    echo 'admin';
                    ?></td>
                  <td style="cursor:pointer;"><?php 
                    if($user['user_last_login'] == 0 || $user['user_last_login'] == '')
                      echo 'Not Logged Yet';
                    else
                      echo date ('d-m-Y h:i:s', $user['user_last_login']); ?></td>
                  <td style="cursor:pointer;" class = "ellipsis"><?php echo $user['user_note']; ?></td>
                  <td style="cursor:pointer;">
                    <button class="go-left btn btn-default btn-rounded btn-sm" onclick = "viewLog('<?php echo $user['user_id'];?>', '<?php echo $user['user_name'];?>')"><span class="fa fa-eye"></span></button>
                  </td>
                  <td>
                    <form class = "go-right" method = "post" enctype="multipart/form-data">
                      <input type = "text" name="delete_id" value="<?php echo base64_encode($user['user_id']);?>" hidden>
                      <button type = "submit" class="btn btn-danger btn-rounded btn-sm" onclick="return confirm('Are you sure want to delete user?');"><span class="fa fa-times"></span></button>
                    </form>
                    <a href="#">
                    <button class="go-right btn btn-default btn-rounded btn-sm"><a href = "edit_reseller_admin.php?user_id=<?php echo base64_encode($user['user_id']);?>"><span class="fa fa-pencil"></span></button>
                    </a>
                    <a href="#">
                    <form class = "go-right" method = "post" enctype="multipart/form-data">
                      <input type = "text" name="<?php echo ($user['user_status'] == '1' ? "disable_id" : "enable_id")?>" value="<?php echo base64_encode($user['user_id']);?>" hidden>
                      <button class="go-right btn btn-default btn-rounded btn-sm" onclick="return confirm('Are you sure want to <?php echo ($user['user_status'] == '1' ? "disable" : "enable")?> user?');"><span class="fa fa-<?php echo ($user['user_status'] == '1' ? "ban" : "check")?>"></span></button>
                     </form> 
                    </a>
                    
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

<div class="url-box animated fadeIn" data-sound="alert" id="mb-link">
  <div class="mb-container">
    <div class="mb-middle">
      <div class="mb-title">User <strong>LOG</strong> </div>
      <div class="mb-content">
          <div class = "row">
            <div class = "col-md-12">
              <table class="display" id = "logTbl" style = "width:100%">
              <thead>
                <tr style = "text-align: center;">
                  <th>LOG ID</th>
                  <th>RESELLER NAME</th>
                  <th>LOG DATE</th>
                  <th>CREDIT</th>
                </tr>
              </thead>
              <tbody>
                
              </tbody>
              </table>
            </div>
          </div>   
        <div class = "row" style = "margin-top:20px; margin-bottom:20px;">
          <div class = "col-md-7">
          </div>
          <div class = "col-md-5">
            <button class="btn btn-default  url-control-close pull-right" style = "width:80px; height:30px; font-size:16px;" >Exit</button>
          </div>
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

function viewLog(userid, username)
{
  console.log(userid);
  $.ajax({
        type: "POST",
        url: "./userLog.php",
        data: { userid: userid},
        dataType: "json",
        success: function(result) {
            console.log(result);
            $('#logTbl tbody').empty();
            for(var i = 0; i < result.length; i ++)
            {
                var append = '<tr class="child" style = "text-align:center;"><td>' + result[i]['user_log_id'] + '</td>' + '<td>' + username + '</td>' + '<td>' + result[i]['user_log_date'] + '</td>' + '<td>' + result[i]['user_log_credit'] + '</td>' + '</tr>';
                $('#logTbl tbody').append(append);
                console.log(append);
            }
            var box = $("#mb-link");
            box.addClass("open");
        }
        
    });
}

$(document).ready(function(){
$("#reseller_li").addClass("active");

var table = $('#table').DataTable( {
        responsive: true
    } );


});
</script>
<!-- END TEMPLATE --> 
<!-- END SCRIPTS -->
</body></html><?php }else{
  
  echo "You are not authorized to visit this page direclty,Sorry";
  } ?>
