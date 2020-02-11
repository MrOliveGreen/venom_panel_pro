<?php 
include 'head.php';
if(isset($_SESSION['user_info'])){

$error=NULL;
$case=1;
$flag = 0;

$con = new db_connect();

$connection=$con->connect();
if($connection==1){
 $edit = new Select_DB($con->connect);

if(isset($_GET['text']))
{
  $return = base64_decode($_GET['text']);
  if($return == 'success')
  {
    $alert = 'Run tools Succeeded. Please check...';
    $flag = 1;
  }
  else
  {
    $alert = 'Run tools Failed. Please check...';
    $flag = 2;
  }
}

if(isset($_POST['save']))
{
    $old = isset($_POST['old_dns']) ? $_POST['old_dns'] : '';
    $new = isset($_POST['new_dns']) ? $_POST['new_dns'] : '';
    $from = isset($_POST['from_server']) ? $_POST['from_server'] : "0";
    $to = isset($_POST['to_server']) ? $_POST['to_server'] : "0";
    
    $return = $edit->run_tools($old, $new, $from, $to);
    
    if($return == false)
      $text = base64_encode('failed');
    else
      $text = base64_encode('success');
     echo "<script>location.href='tools.php?text=".$text."'</script>";
}

  $servers = $edit->get_servers_name();
}
?>
<?php
  if($_SESSION['user_info']['user_is_admin'] == 1 && !empty($_SESSION)){
  ?>
            <!-- PAGE CONTENT -->
            <div class="page-content">
                
                <!-- START X-NAVIGATION VERTICAL -->
                <ul class="x-navigation x-navigation-horizontal x-navigation-panel">
                    <!-- TOGGLE NAVIGATION -->
                    <li class="xn-icon-button">
                        <a href="#" class="x-navigation-minimize"><span class="fa fa-dedent"></span></a>
                    </li>
                    <!-- END TOGGLE NAVIGATION -->
                    <!-- SIGN OUT -->
                    <li class="xn-icon-button" style = "position:relative; float:right;">
                     <a href="<?php echo SITE_URL; ?>logout.php" class="mb-control" data-box="#mb-signout" style="margin-left:-50px; width:100px"><span class="fa fa-sign-out"></span> Logout</a> </li>
                    <!-- END SIGN OUT -->
                    
                </ul>
                <!-- END X-NAVIGATION VERTICAL -->                   
                
                <!-- START BREADCRUMB -->
                <ul class="breadcrumb">
                    <li><a href="#">Admin </a></li>
                    <li><a href="#">Tools</a></li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2><span class="fa fa-arrow-circle-o-left"></span> Tools</h2>
                </div>
                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
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
              
                     <div class="row">
                        <div class="col-md-12">
                            
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">CHANGE STREAM DNS</label>
                                        <div class="col-md-3 col-xs-6">
                                        <div class = "input-group">
                                          <input type="text" name = "old_dns" class="form-control" style = "border-radius: 4px;" placeholder = "Old DNS">
                                        </div>
                                        <span class="help-block">Input Old Stream DNS</span> 
                                          </div>
                                          <div class="col-md-3 col-xs-6">
                                        <div class = "input-group">
                                          <input type="text" name = "new_dns" class="form-control" style = "border-radius: 4px;" placeholder = "New DNS">
                                        </div>
                                        <span class="help-block">Input New Stream DNS</span> 
                                          </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label"> TRANSFER LIVE STREAMS </label>
                                        <div class="col-md-3 col-xs-3">
                                        <div class = "input-group">
                                          <select name = "from_server" style = "margin-right:20px; border-radius: 4px; height:30px; width:160px;">
                                            <option value = "0"> From Choose Server </option>
                                            <?php 
                                              foreach($servers as $server)
                                                echo '<option value = "'.$server['server_id'].'">'.$server['server_name'].'</option>';
                                            ?>
                                        </select>
                                        </div>
                                          <span class="help-block">Select Current Stream Server</span> </div>
                                          <div class="col-md-3 col-xs-3">
                                        <div class = "input-group">
                                          <select name = "to_server" style = "margin-right:20px; border-radius: 4px; height:30px; width:160px;">
                                            <option value = "0">To Choose Server </option>
                                            <?php 
                                              foreach($servers as $server)
                                                echo '<option value = "'.$server['server_id'].'">'.$server['server_name'].'</option>';
                                            ?>
                                        </select>
                                        </div>
                                          <span class="help-block">Select Stream Server To Transfer</span> </div>
                                      </div>

                                <div class="panel-footer">
                                    <input type="text" name = "id" value = "<?php echo $setting['setting_id']; ?>" hidden>
                                    <input type="reset" value="Reset" class="btn btn-default">                                   
                                    <input type="submit" name="save" value="Run" class="btn btn-primary pull-right" onclick="return confirm('Are you sure want to run tools?');">
                                </div>
                            </div>
                            </form>
                            
                        </div>
                    </div>                    
                    
                    </div>
                    
                </div>
                <!-- END PAGE CONTENT WRAPPER -->                                                
            </div>            
            <!-- END PAGE CONTENT -->
<?php
  }
  else { ?>
    echo "<script>location.href='index.php'</script>";
<?php }?>
        <!-- MESSAGE BOX-->
        <div class="message-box animated fadeIn" data-sound="alert" id="mb-signout">
            <div class="mb-container">
                <div class="mb-middle">
                    <div class="mb-title"><span class="fa fa-sign-out"></span> Log <strong>Out</strong> ?</div>
                    <div class="mb-content">
                        <p>Are you sure you want to log out?</p>                    
                        <p>Press No if youwant to continue work. Press Yes to logout current user.</p>
                    </div>
                    <div class="mb-footer">
                        <div class="pull-right">
                            <a href="logout.php" class="btn btn-success btn-lg">Yes</a>
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
        
        <!-- THIS PAGE PLUGINS -->
        <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
        <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>


        <script type="text/javascript" src="js/jquery.multi-select.js"></script>
        <script type="text/javascript" src="js/jquery.quicksearch.js"></script>
        <!-- END THIS PAGE PLUGINS -->       
        
        <!-- START TEMPLATE -->
        
        <script type="text/javascript" src="js/plugins.js"></script>        
        <script type="text/javascript" src="js/actions.js"></script>        
        <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->          
	<script>
    $(document).ready(function(){
        
        $("#tools_li").addClass("active");
    });
	</script>
    </body>
</html>
<?php }else{
	
	echo "You are not authorized to visit this page direclty,Sorry";
	} ?>




