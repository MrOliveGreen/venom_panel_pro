<?php 
include 'head.php';
if(isset($_SESSION['user_info'])){
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

if(!isset($_POST['ssh_port']) || $_POST['ssh_port']=='' || $_POST['ssh_port']==NULL)
{
    $case=0;
    $error="<li>Please Enter SSH Port</li>";
}

if($case == 1)
{
     $connection=$insert->add_server($_POST['server_name'], $_POST['server_ip'], $_POST['broadcast_port'], $_POST['rtmp_port'], $_POST['ssh_port'], isset($_POST['dns']) ? $_POST['dns'] : '', isset($_POST['ssh_password']) ? $_POST['ssh_password'] : '', isset($_POST['db_password']) ? $_POST['db_password'] : '');
    
    $text = base64_encode('Add Server Succeded. Please check.');
     echo "<script>location.href='manage_server.php?text=".$text."'</script>";
}   
}
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
                    <li class="active">Add Server</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2><span class="fa fa-arrow-circle-o-left"></span> Add Server</h2>
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
                                                <input type="text" name="server_name" class="form-control" required/>
                                            </div>                                            
                                            <span class="help-block">Enter Server Name</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SERVER IP</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="server_ip" class="form-control" required/>
                                            </div>                                            
                                            <span class="help-block">Enter Server IP</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">BROADCAST PORT</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="broadcast_port" class="form-control" value = "25461" required/>
                                            </div>                                            
                                            <span class="help-block">Enter Broadcast Port</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">RTMP PORT</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="rtmp_port" class="form-control" value = "25462" required/>
                                            </div>                                            
                                            <span class="help-block">Enter Rtmp Port</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SSH PORT</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="ssh_port" class="form-control" value = "22" required/>
                                            </div>                                            
                                            <span class="help-block">Enter SSH Port</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">DNS</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="dns" class="form-control" />
                                            </div>                                            
                                            <span class="help-block">Enter DNS</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SSH PASSWORD</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="ssh_password" class="form-control" required/>
                                            </div>                                            
                                            <span class="help-block">Enter SSH Password</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">DB PASSWORD</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="db_password" class="form-control"/>
                                            </div>                                            
                                            <span class="help-block">Enter DB Password</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">INSTALL CUDA DRIVERS</label>
                                        <div class="col-md-6 col-xs-12">
                                          <div class="input-group" style = "margin-top: 5px;"> 
                                            <label>
                                            <input type="checkbox" name = "cuda" class="flat-green" >
                                          </label>
                                          </div>
                                          <span class="help-block">Check to install CUDA Drivers</span> </div>
                                      </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">INSTALL INTEL QUICKSYNC DRIVERS</label>
                                        <div class="col-md-6 col-xs-12">
                                          <div class="input-group" style = "margin-top: 5px;"> 
                                            <label>
                                            <input type="checkbox" name = "cuda" class="flat-green" >
                                          </label>
                                          </div>
                                          <span class="help-block">Check to install Quicksync Drivers</span> </div>
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




