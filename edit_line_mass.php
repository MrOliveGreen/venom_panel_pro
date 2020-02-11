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

if(!isset($_POST['lines']) || $_POST['lines']=='' || $_POST['lines']==NULL)
{
    $case=0;
    $error="<li>Please Select Lines</li>";
}

if($case)
{
     $connection=$insert->edit_mass_line(isset($_POST['lines'])? $_POST['lines']: '', $_POST['user_id'], isset($_POST['connections'])? $_POST['connections']: "0", isset($_POST['ip'])? $_POST['ip']: NULL, isset($_POST['ua'])? $_POST['ua']: NULL, isset($_POST['isp'])? $_POST['isp']: NULL, isset($_POST['restreamer']) ? $_POST['restreamer'] : 'off', isset($_POST['bouquets'])? $_POST['bouquets']: '');
     $text = base64_encode('Mass Line Edit Succeded. Please check with search box.');
     echo "<script>location.href='manage_line_admin.php?text=".$text."'</script>";
}
  
}
    $id = 0;
    if(isset($_GET['id']))
    {
        $id = base64_decode($_GET['id']);
        //var_dump($id);
        $lines = $insert->get_lines_name($id);
        //var_dump($lines);
        //exit;
    }    
    else
        $lines = $insert->get_lines_name();

    $packages = $insert->get_packages();
    $bouquets = $insert->get_bouquets();
    $users = $insert->get_user_obj();
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
                    <li><a href="#">Admin</a></li>
                    <li><a href="#">Manage Lines</a></li>
                    <li class="active">Mass Line Edit</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2><span class="fa fa-arrow-circle-o-left"></span> Mass Line Edit</h2>
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
                                        <label class="col-md-3 col-xs-12 control-label">SELECT LINE RESELLER</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <select id = "reseller_id" style = "border-radius: 4px; height:30px; width:160px;" onchange="filter(this)">
                                                    <option value = "0">Show All</option>
                                                  <?php
                                                    while($user = mysqli_fetch_assoc($users))
                                                        echo "<option value='".base64_encode($user['user_id'])."'".($user['user_id'] == $id ? "selected" : "").">".$user['user_name']."</option>";
                                                  ?>
                                                </select>
                                            </div>                                            
                                            <span class="help-block">Enter Line Password</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">LINE USER</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <select name = "lines[ ]" class="multiselect" id = "linebox" multiple='multiple'>
                                                  <?php

                                                    while($line = mysqli_fetch_assoc($lines)){
                                                        echo "<option value='".$line['line_id']."' data-id = 'option_".$line['line_user_id']."'>".$line['line_user']."</option>";
                                                    }
                                                  ?>
                                                </select>
                                            </div>                                            
                                            <span class="help-block">Select bouquet streams</span>
                                        </div>
                                    </div>                    
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">ASSIGN THE LINE TO USER</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <select name = "user_id" style = "border-radius: 4px; height:30px; width:160px;">
                                                    <option value = "0">Current User</option>
                                                  <?php
                                                    while($user = mysqli_fetch_assoc($users))
                                                        echo "<option value='".$user['user_id']."'>".$user['user_name']."</option>";
                                                  ?>
                                                </select>
                                            </div>                                            
                                            <span class="help-block">Enter Line Password</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">ALLOWED CONNECTIONS</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="connection" class="form-control" />
                                            </div>                                            
                                            <span class="help-block">Enter Allowed Connections</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">LOCK TO IP</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="ip" class="form-control taginput" placeholder = "add ip and press TAB"/>
                                            </div>                                            
                                            <span class="help-block">Enter IP Address</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">LOCK TO USER AGENT</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="ua" class="form-control taginput" placeholder = "add ua and press TAB"/>
                                            </div>                                            
                                            <span class="help-block">Enter User Agent</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">LOCK TO ISP</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="isp" class="form-control taginput" placeholder = "add ip and press TAB"/>
                                            </div>                                            
                                            <span class="help-block">Enter ISP</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">LINE IS RESTREAMER</label>
                                        <div class="col-md-6 col-xs-12">
                                          <div class="input-group" style = "margin-top: 5px;"> 
                                            <label>
                                            <input type="checkbox" name = "restreamer" class="flat-green" >
                                          </label>
                                          </div>
                                          <span class="help-block">Check if line is restreamer</span> </div>
                                      </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">BOUQET STREAMS</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                
                                                <select name = "bouquets[ ]" class="multiselect" multiple='multiple'>
                                                  <?php
                                                    while($bouquet = mysqli_fetch_assoc($bouquets)){
                                                        echo "<option value='".$bouquet['bouquet_id']."'>".$bouquet['bouquet_name']."</option>";
                                                    }
                                                  ?>
                                                </select>
                                            </div>                                            
                                            <span class="help-block">Select bouquet streams</span>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                <div class="panel-footer">
                                    <input type="reset" value="Reset" class="btn btn-default">                                   
                                    <input type="submit" name="save" value="Save" class="btn btn-primary pull-right" onclick="return confirm('Are you sure want to add line?');">
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
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-tagsinput.js"></script>
        <script type="text/javascript" src="js/plugins/tagsinput/jquery.tagsinput.min.js"></script>

        <script type="text/javascript" src="js/jquery.multi-select.js"></script>
        <script type="text/javascript" src="js/jquery.quicksearch.js"></script>
        <!-- END THIS PAGE PLUGINS -->       
        
        <!-- START TEMPLATE -->
        
        <script type="text/javascript" src="js/plugins.js"></script>        
        <script type="text/javascript" src="js/actions.js"></script>
        <script type="text/javascript" src="js/bootstrap-datetimepicker.js"></script>
        <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->          
	<script>
	$(document).ready(function(){
		$("#line_li").addClass("active");

        $('#datetimepicker').datetimepicker({
        format: 'yyyy-mm-dd hh:ii'
        });
        $('#datetimepicker').datetimepicker('setStartDate', '2019-11-01 00:00');

        $('input[type="checkbox"].flat-green, input[type="radio"].flat-green').iCheck({
          checkboxClass: 'icheckbox_flat-green',
          radioClass   : 'iradio_flat-green'
        });
        //$('#datetimepicker').datetimepicker('setStartDate', '2019-11-01');

        $('.taginput').tagsinput({
          maxTags: 10
        });

        // $(window).keydown(function(event){
        //     if(event.keyCode == 13) {
        //       event.preventDefault();
        //       return false;
        //     }
        //   });
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

    function filter(sel)
    {
        selected = sel.options[sel.selectedIndex].value;

        if(selected == "0")
            window.location.href = "edit_line_mass.php";
        else
            window.location.href = "edit_line_mass.php?id=" + selected;
    }
	</script>
    </body>
</html>
<?php }else{
	
	echo "You are not authorized to visit this page direclty,Sorry";
	} ?>




