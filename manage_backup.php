<?php 
include 'head.php';
$con = new db_connect();
$connection=$con->connect();

$get = new Select_DB($con->connect);

$flag = 0;
$alert = '';


if(isset($_GET['text']))
{
  $text = base64_decode($_GET['text']);
  if($text == 'success')
  {
    $alert = 'Restoring Database Succeeded. Please check...';
    $flag = 1;
  }
  else if($text == 'failed')
  {
    $alert = 'Restoring Database Failed';
    $flag = 2;
  }
  else
  {
    $alert = $text;
    $flag = 1;
  }
  
}

if(isset($_SESSION['user_info'])){
    //print_r( $_SESSION['user_info'] );exit;

if(isset($_POST['save']))
{
    $result = $get->backup();


    if($result == 'Success')
      {
        $flag = 1;
        $alert = 'SQL Backup File Created Successfully!';
      }
      else
      {
        $flag = 2;
        $alert = 'Backup Operation Failed!';
      }
}

if(isset($_POST['delete']))
{
  $name = base64_decode($_POST['delete']);
  $result = unlink(BACKUP_DIR.$name);

  if($result)
  {
    $flag = 1;
    $alert = 'SQL Backup File Deleted Successfully!';
  }
  else
  {
    $flag = 2;
    $alert = 'Delete Operation Failed!';
  }
}

    $files = array();
    $dir = BACKUP_DIR;
    $dh = @opendir($dir);
    //var_dump($dh);
    if ($dh) {
        while (($fname = readdir($dh)) !== false) {
            if($fname != '' && $fname != '.' && $fname != '..')
            {
                $stat = stat("$dir/$fname");
                $data = array();
                $data['ctime'] = date("m:d:Y h:i:s", $stat['ctime']);
                $data['name'] = $fname;
                $data['size'] = intval($stat['size'] / 1024) .' KB';
                $files[] = $data;
                //var_dump($stat);
            }
        }
        closedir($dh);   
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
    <li><a href="#"> Database Management </a></li>
    <li><a href="#"> Manage Backups </a></li>
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
            <h3 class="panel-title">Manage Database Backups</h3>
            <form class = "pull-right" method = "post" enctype="multipart/form-data">
              <input type = "text" name="save" value="current" hidden>
              <button type = "submit" class="btn btn-success" onclick="return confirm('Are you sure want to backup?');">Backup Current Database</button>
            </form>
          </div>
          <div class="panel-body">
            <table class="display" id = "table" style = "width:100%">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>DATE</th>
                  <th>FILESIZE</th>
                  <th>BACKUP NAME</th>
                  <th>ACTION</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                    $no = 1;
                    foreach($files as $file){
                    ?>
                <tr>
                  <td style="cursor:pointer;"><?php echo $no; $no ++; ?></td>
                  <td style="cursor:pointer;"><?php echo $file['ctime']; ?></td>
                  <td style="cursor:pointer;"><?php echo $file['size']; ?></td>
                  <td style="cursor:pointer;"><?php echo $file['name']; ?></td>
                  <td>
                    <button style = "float:left; position:relative;" class="btn btn-default btn-rounded btn-sm" onclick = "restore('<?php echo base64_encode($file['name']);?>')"><span class="fa fa-refresh"></span></button>
                    <form style = "float:left; position:relative;" method = "post" enctype="multipart/form-data">
                      <input type = "text" name="delete" value="<?php echo base64_encode($file['name']);?>" hidden>
                      <button type = "submit" class="btn btn-danger btn-rounded btn-sm" onclick="return confirm('Are you sure want to delete backup file?');"><span class="fa fa-times"></span></button>
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
<div id="loading" style="display:none;">
    Restoring Please Wait....
    <img src="assets/images/ajax-loader.gif" alt="Loading" />
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
$("#backup_li").addClass("active");

  var table = $('#table').DataTable( {
        responsive: true
    } );
});

function restore(name)
{
    if(confirm('Do you really want to restore database?'))
    {
         console.log('yup');
         $("#loading")[0].style = "display:show";

        $.ajax({
        type: "POST",
        url: "./restoreBackup.php",
        data: { name: name},
        dataType: "json",
        success: function(result) {
            $("#loading")[0].style = "display:none";
            console.log(result);
            //window.location.href = "manage_backup.php?text=" + result;
        }
        });
    }
}

</script>

</body></html><?php }else{
    
    echo "You are not authorized to visit this page direclty,Sorry";
    } ?>
