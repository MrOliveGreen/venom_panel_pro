<?php 
include 'head.php';
if(isset($_SESSION['user_info']) && isset($_GET['server_id'])){

$server_id = base64_decode($_GET['server_id']);
$error=NULL;
$case=1;

$con = new db_connect();

$connection=$con->connect();
if($connection==1){
 $insert = new Select_DB($con->connect);

if(isset($_POST['save']))
{
if(!isset($_POST['server_name']) || $_POST['server_name']=='' || $_POST['server_name']==NULL)
{
	$case=0;
	$error="<li>Please Enter Server Name</li>";
}

if(!isset($_POST['server_ip']) || $_POST['server_ip']=='' || $_POST['server_ip']==NULL)
{
    $case=0;
    $error="<li>Please Enter Server IP</li>";
}

if(!isset($_POST['broadcast_port']) || $_POST['broadcast_port']=='' || $_POST['broadcast_port']==NULL)
{
    $case=0;
    $error="<li>Please Enter Broadcast Port</li>";
}

if(!isset($_POST['rtmp_port']) || $_POST['rtmp_port']=='' || $_POST['rtmp_port']==NULL)
{
    $case=0;
    $error="<li>Please Enter Rtmp Port</li>";
}

if($case == 1)
{
     $connection=$insert->edit_server($server_id, $_POST['server_name'], $_POST['server_ip'], $_POST['broadcast_port'], $_POST['rtmp_port'], $_POST['ssh_port'], isset($_POST['dns']) ? $_POST['dns'] : '', isset($_POST['ssh_password']) ? $_POST['ssh_password'] : '', isset($_POST['client_limit']) ? $_POST['client_limit'] : '', isset($_POST['band_limit']) ? $_POST['band_limit'] : '', isset($_POST['cpu_limit']) ? $_POST['cpu_limit'] : '', $_POST['iface'], isset($_POST['failover_ip']) ? $_POST['failover_ip'] : '');
    
    $text = base64_encode('Edit Server Succeded. Please check.');
     echo "<script>location.href='manage_server.php?text=".$text."'</script>";
}   
}

$server = $insert->get_server($server_id);
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
                    <li><a href="#">Server Management</a></li>
                    <li class="active">Edit Server</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2><span class="fa fa-arrow-circle-o-left"></span> Edit Server</h2>
                </div>
                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                <?php if($case==0){?>
					<div class="alert alert-danger" role="alert">
						<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
						<strong>ERROR!</strong>
						<?php echo $error; ?>
					</div>
					<?php } ?>
                     <div class="row">
                        <div class="col-md-12">
                            
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SERVER NAME</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="server_name" class="form-control" value = "<?php echo $server['server_name']; ?>" required/>
                                            </div>                                            
                                            <span class="help-block">Enter Server Name</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SERVER IP</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="server_ip" class="form-control" value = "<?php echo $server['server_ip']; ?>" required/>
                                            </div>                                            
                                            <span class="help-block">Enter Server IP</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">BROADCAST PORT</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="broadcast_port" class="form-control" value = "<?php echo $server['server_broadcast_port']; ?>" required/>
                                            </div>                                            
                                            <span class="help-block">Enter Broadcast Port</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">RTMP PORT</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="rtmp_port" class="form-control" value = "<?php echo $server['server_rtmp_port']; ?>" required/>
                                            </div>                                            
                                            <span class="help-block">Enter Rtmp Port</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SSH PORT</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="ssh_port" class="form-control" value = "<?php echo $server['server_ssh_port']; ?>" required/>
                                            </div>                                            
                                            <span class="help-block">Enter SSH Port</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">DNS</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="dns" class="form-control" value = "<?php echo $server['server_dns_name']; ?>" />
                                            </div>                                            
                                            <span class="help-block">Enter DNS</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SSH PASSWORD</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="ssh_password" class="form-control" placeholder = "Leave it blank to not change it"/>
                                            </div>                                            
                                            <span class="help-block">Enter SSH Password</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SERVER CLIENT LIMIT</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="client_limit" class="form-control" placeholder = "Leave it blank for no limit / eg. 1000(for 1000 connections)" value = "<?php echo $server['server_client_limit'];?>"/>
                                            </div>                                            
                                            <span class="help-block">Enter Server Client Limit</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SERVER BANDWIDTH LIMIT</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="band_limit" class="form-control" value = "650"/>
                                            </div>                                            
                                            <span class="help-block">Enter Server Bandwidth Limit</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SERVER CPU LIMIT</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="cpu_limit" class="form-control" placeholder="Leave it blank for no limit / eg. 80 (for 80%)" value = "<?php echo $server['server_cpu_limit'];?>"/>
                                            </div>                                            
                                            <span class="help-block">Enter Server CPU Limit</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                    <label class="col-md-3 col-xs-12 control-label">NETWORK INTERFACE</label>
                                    <div class="col-md-6 col-xs-12">
                                      <div class="input-group">
                                        <select name = "iface" style = "margin-right:20px; border-radius: 4px; height:30px; width:160px;">
                                            <option value = "storm1593" <?php echo ($server['server_iface'] == "storm1593" ? "selected" : "");?>> storm1593 </option>
                                            <option value = "gretap0" <?php echo ($server['server_iface'] == "gretap0" ? "selected" : "");?>> gretap0 </option>
                                            <option value = "erspan0" <?php echo ($server['server_iface'] == "erspan0" ? "selected" : "");?>> erspan0 </option>
                                            <option value = "lo" <?php echo ($server['server_iface'] == "lo" ? "selected" : "");?>> lo </option>
                                            <option value = "gre0" <?php echo ($server['server_iface'] == "gre0" ? "selected" : "");?>> gre0 </option>
                                            <option value = "eno1" <?php echo ($server['server_iface'] == "eno1" ? "selected" : "");?>> eno1 </option>
                                        </select>
                                      </div>
                                      <span class="help-block">Choose Stream Method</span> </div>
                                  </div>

                                  <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">FAILOVER IP</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="failover_ip" class="form-control" value = "<?php echo $server['failover_ip']; ?>"/>
                                            </div>                                            
                                            <span class="help-block">Enter Failover IP</span>
                                        </div>
                                    </div>
                                <div class="panel-footer">
                                    <input type="reset" value="Reset" class="btn btn-default">                                   
                                    <input type="submit" name="save" value="Save" class="btn btn-primary pull-right" onclick="return confirm('Are you sure want to save?');">
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
        
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-datepicker.js"></script>                
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-file-input.js"></script>
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-select.js"></script>
        <script type="text/javascript" src="js/plugins/tagsinput/jquery.tagsinput.min.js"></script>

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
		$("#server_li").addClass("active");

        $('input[type="checkbox"].flat-green, input[type="radio"].flat-green').iCheck({
          checkboxClass: 'icheckbox_flat-green',
          radioClass   : 'iradio_flat-green'
        });
	});

	</script>
    </body>
</html>
<?php }else{
	
	echo "You are not authorized to visit this page direclty,Sorry";
	} ?>




