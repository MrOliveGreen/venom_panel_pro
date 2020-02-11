<?php 
include 'head.php';
if(isset($_SESSION['user_info']) && isset($_GET['package_id'])){
$package_id = base64_decode($_GET['package_id']);

$error=NULL;
$case=1;

$con = new db_connect();

$connection=$con->connect();
if($connection==1){
 $insert = new Select_DB($con->connect);

if(isset($_POST['save']))
{
if(!isset($_POST['name']) || $_POST['name']=='' || $_POST['name']==NULL)
{
	$case=0;
	$error="<li>Please Enter package Name</li>";
}

if($case == 1)
{
    $connection=$insert->edit_package($package_id, $_POST['name'], $_POST['duration_in'], $_POST['duration'], $_POST['credit']);
    
    $text = base64_encode('Edit Package Succeded. Please check with search box.');
    echo "<script>location.href='manage_package.php?text=".$text."'</script>";
}   
}

$package = $insert->get_package($package_id);
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
                    <li><a href="#">Package Management</a></li>
                    <li class="active">Edit Package</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2><span class="fa fa-arrow-circle-o-left"></span> Edit Package</h2>
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
                                        <label class="col-md-3 col-xs-12 control-label">PACKAGE NAME</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="name" class="form-control" value = "<?php echo $package['package_name']; ?>" required/>
                                            </div>                                            
                                            <span class="help-block">Enter Package Name</span>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                    <label class="col-md-3 col-xs-12 control-label">PACKAGE DURATION IN</label>
                                    <div class="col-md-6 col-xs-12">
                                      <div class="input-group">
                                        <select name = "duration_in" style = "margin-right:20px; border-radius: 4px; height:30px; width:160px;">
                                            <option value = "0" <?php echo ($package["package_duration_in"] == 0 ? "selected" : "");?>> Hour </option>
                                            <option value = "1" <?php echo ($package["package_duration_in"] == 1 ? "selected" : "");?>> Day </option>
                                            <option value = "2" <?php echo ($package["package_duration_in"] == 2 ? "selected" : "");?>> Week </option>
                                            <option value = "3" <?php echo ($package["package_duration_in"] == 3 ? "selected" : "");?>> Month </option>
                                            <option value = "4" <?php echo ($package["package_duration_in"] == 4 ? "selected" : "");?>> Year </option>
                                        </select>
                                      </div>
                                      <span class="help-block">Choose Stream Method</span> </div>
                                  </div>

                                  <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">PACKAGE DURATION</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="duration" class="form-control" value = "<?php echo $package['package_duration']; ?>" required/>
                                            </div>                                            
                                            <span class="help-block">Enter Package Duration</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">PACKAGE CREDIT</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="credit" class="form-control" value = "<?php echo $package['package_credit']; ?>" required/>
                                            </div>                                            
                                            <span class="help-block">Enter Package Credit</span>
                                        </div>
                                    </div>

                                <div class="panel-footer">
                                    <input type="reset" value="Reset" class="btn btn-default">                                   
                                    <input type="submit" name="save" value="Save" class="btn btn-primary pull-right" onclick="return confirm('Are you sure want to add package?');">
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
		$("#package_li").addClass("active");
	});

	</script>
    </body>
</html>
<?php }else{
	
	echo "You are not authorized to visit this page direclty,Sorry";
	} ?>




