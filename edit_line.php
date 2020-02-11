<?php 
include 'head.php';
if(isset($_SESSION['user_info']) && isset($_GET['line_id'])){
$line_id = base64_decode($_GET['line_id']);
$error=NULL;
$case=1;

$con = new db_connect();

$connection=$con->connect();
if($connection==1){
 $edit = new Select_DB($con->connect);

if(isset($_POST['save']))
{
if(!isset($_POST['line_name']) || $_POST['line_name']=='' || $_POST['line_name']==NULL)
{
	$case=0;
	$error="<li>Please Enter Line Name</li>";
}
if(!isset($_POST['line_pass']) || $_POST['line_pass']=='' || $_POST['line_pass']==NULL)
{
    $length = 10;
    $pass = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}
else
    $pass = $_POST['line_pass'];

if(($_POST['pkg_id']=='' || $_POST['pkg_id']==NULL) && $case != 0)
{
	$case=0;
	$error.="<li>Please Select Package</li>";
}
else {
    if(strpos($_POST['pkg_id'], "c") === false)
        $pkg = $edit->get_package($_POST['pkg_id']);
    else
    {
        $custom = json_decode($_SESSION['user_info']['user_custom_package']);
        $data = $custom[intval($_POST['pkg_id'][1])];
        //var_dump(intval($_POST['pkg_id'][1]));
        $pkg = array();
        $pkg['package_name'] = $data[0];
        $pkg['package_duration_in'] = $data[1];
        $pkg['package_duration'] = $data[2];
        $pkg['package_credit'] = $data[3];
    }
    $user_credit = $_SESSION['user_info']['user_credit'];
    // var_dump($pkg['package_credit']);
    // var_dump($user_credit);
    // exit();
    if($_SESSION['user_info']['user_is_admin'] != 1 && intval($user_credit) < intval($pkg['package_credit']))
    {
        $case=0;
        $error.="<li>You don't have enough credits! Please buy some credits. </li>";
    }
}

if(!isset($_POST['bouquets']) && $case !=0)
{
  $case=0;
  $error="<li>Please Choose Some Bouquets.</li>";  
}

if($case == 1)
{
     $connection=$edit->edit_line($line_id, $_POST['line_name'],$pass,$pkg,isset($_POST['line_mac'])? $_POST['line_mac']: NULL,isset($_POST['line_note'])? $_POST['line_note']: NULL, isset($_POST['bouquets'])? $_POST['bouquets']: '');
    
    $text = base64_encode('Edit Line Succeded. Please check with search box.');
     echo "<script>location.href='manage_line.php?text=".$text."'</script>";  
}   
}

    $packages = $edit->get_packages();
    $bouquets = $edit->get_bouquets();
    $line = $edit->get_line($line_id);

}
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
                    <li class="xn-icon-button" style = "position:relative; float:right;"> <h5 class = "top-reseller"> <?php echo $_SESSION['user_info']['user_name'].'('.$_SESSION['user_info']['user_credit'].' credit/s)'; ?></h5></li>
                    
                </ul>
                <!-- END X-NAVIGATION VERTICAL -->                   
                
                <!-- START BREADCRUMB -->
                <ul class="breadcrumb">
                    <li><a href="#">Dashboard></li>
                    <li><a href="#">Manage Lines</a></li>
                    <li class="active">Edit Line</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2><span class="fa fa-arrow-circle-o-left"></span> Edit Line</h2>
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
                            
                            <form class="form-horizontal" method="post" enctype="multipart/form-data" id = "addlineform">
                            <div class="panel panel-default">
                                <div class="panel-body">                    
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">LINE USER</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="line_name" class="form-control" value = "<?php echo $line['line_user']?>" required/>
                                            </div>                                            
                                            <span class="help-block">Enter Line Name</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">LINE PASS</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="line_pass" class="form-control" value = "<?php echo $line['line_pass']?>" placeholder="Leave it blank to generate it automatically" />
                                            </div>                                            
                                            <span class="help-block">Enter Line Password</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">LINE PACKAGE</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <select name = "pkg_id" form="addlineform" class = "form-control">
                                                <?php

                                                if($_SESSION['user_info']['user_package_id'] == '')
                                                    $user_pkg = array();
                                                else
                                                    $user_pkg = json_decode($_SESSION['user_info']['user_package_id']);

                                                 while($pkg = mysqli_fetch_assoc($packages)){
                                                    if(in_array($pkg['package_id'], $user_pkg))
                                                    {
                                                        if(strpos($pkg['package_name'], 'TEST') === false || strpos($line['line_status_reason'], 'TEST passed') === false)
                                                        echo '<option value="'.$pkg['package_id'].'">'.$pkg['package_name'].' - '.$pkg['package_credit'].' credit</option>';
                                                    }
                                                }
                                                $custom_pkg = json_decode($_SESSION['user_info']['user_custom_package']);
                                                for($i = 0; $i < count($custom_pkg); $i ++)
                                                {
                                                    if(strpos($pkg['package_name'], 'TEST') === false || strpos($line['line_status_reason'], 'TEST passed') === false)
                                                    echo '<option value="c'.$i.'">'.$custom_pkg[$i][0].' - '.$custom_pkg[$i][3].' credit</option>';
                                                }
                                                ?>
                                                </select>
                                            </div>                                            
                                            <span class="help-block">Select Package</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">LINE MAC ADDRESS</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="line_mac" class="form-control" />
                                            </div>                                            
                                            <span class="help-block">Enter Mac Address</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">NOTES</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="line_note" class="form-control" value = "<?php echo $line['line_note']?>"/>
                                            </div>                                            
                                            <span class="help-block">Enter Notes</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">BOUQET STREAMS</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <select name = "bouquets[ ]" class="multiselect" multiple='multiple'>
                                                  <?php

                                                  if($_SESSION['user_info']['user_bouquet_id'] == '')
                                                    $user_bouquet = array();
                                                  else
                                                    $user_bouquet = json_decode($_SESSION['user_info']['user_bouquet_id']);

                                                  if($line['line_bouquet_id'] == '')
                                                    $line_bouquet = array();
                                                  else
                                                    $line_bouquet = json_decode($line['line_bouquet_id']);

                                                    while($bouquet = mysqli_fetch_assoc($bouquets))
                                                    {
                                                        if(in_array($bouquet['bouquet_id'], $user_bouquet))
                                                        echo "<option value='".$bouquet['bouquet_id']."'".(in_array($bouquet['bouquet_id'], $line_bouquet)? "selected" : "").">".$bouquet['bouquet_name']."</option>";
                                                    }
                                                  ?>
                                                </select>
                                            </div>                                            
                                            <span class="help-block">Select bouquet streams</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <input type="reset" value="Reset" class="btn btn-default">                                   
                                    <input type="submit" name="save" value="Save" class="btn btn-primary pull-right" onclick="return confirm('Are you sure want to save line data? You can lose some credits. ');">
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
		$("#line_li").addClass("active");
	});

    $('.multiselect').multiSelect({
      selectableHeader: "<input type='text' class='form-control' autocomplete='off' placeholder='Search...'>",
      selectionHeader: "<input type='text' class='form-control' autocomplete='off' placeholder='Search...'>",
      afterInit: function(ms){
        var that = this,
            $selectableSearch = that.$selectableUl.prev(),
            $selectionSearch = that.$selectionUl.prev(),
            selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
            selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

        that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
        .on('keydown', function(e){
          if (e.which === 40){
            that.$selectableUl.focus();
            return false;
          }
        });

        that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
        .on('keydown', function(e){
          if (e.which == 40){
            that.$selectionUl.focus();
            return false;
          }
        });
      },
      afterSelect: function(){
        this.qs1.cache();
        this.qs2.cache();
      },
      afterDeselect: function(){
        this.qs1.cache();
        this.qs2.cache();
      }
    });
	</script>
    </body>
</html>
<?php }else{
	
	echo "You are not authorized to visit this page direclty,Sorry";
	} ?>




