<?php 
include 'head.php';
if(isset($_SESSION['user_info'])){
$error=NULL;
$case=1;
$con = new db_connect();

$connection=$con->connect();
$insert = new Select_DB($con->connect);

if(isset($_POST['save']))
{ 
if($_POST['name']=='' || $_POST['name']==NULL)
{
	$case=0;
	$error="<li>Please Enter Reseller Name</li>";
	
}

if(($_POST['pass']=='' || $_POST['pass']==NULL) && $case !=0)
{
    $length = 10;
    $pass = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}
else
    $pass = $_POST['pass'];

if(($_POST['email']=='' || $_POST['email']==NULL) && $case !=0)
{
  $case=0;
  $error="<li>Please Enter Email</li>";  
}

if(!isset($_POST['packages']) && $case !=0)
{
  $case=0;
  $error="<li>Please Choose Some Packages.</li>";  
}

if(!isset($_POST['bouquets']) && $case !=0)
{
  $case=0;
  $error="<li>Please Choose Some Bouquets.</li>";  
}

if($case==1){

if($connection==1){
	
  $credit = $_POST['credit'];

  if(intval($_SESSION['user_info']['user_credit']) < intval($credit))
  {
    $case = 0;
    $error="<li>You don't have enough credits!</li>";
  }
  else
	 {
    $connection=$insert->add_subreseller($_POST['name'],$_POST['pass'],$_POST['email'],$_POST['note'],$_POST['packages'], $_POST['bouquets'], $credit);
	
	   $text = base64_encode('Add Subreseller Succeded. Please check with search box.');
     echo "<script>location.href='manage_reseller.php?text=".$text."'</script>";
  }
}
}
}
$packages = $insert->get_packages();
$bouquets = $insert->get_bouquets();
?>
<!-- PAGE CONTENT -->
<?php
  if($_SESSION['user_info']['user_owner_id'] == 0 && !empty($_SESSION)){
  ?>
<div class="page-content"> 
  
  <!-- START X-NAVIGATION VERTICAL -->
  <ul class="x-navigation x-navigation-horizontal x-navigation-panel">
    <!-- TOGGLE NAVIGATION -->
    <li class="xn-icon-button"> <a href="#" class="x-navigation-minimize"><span class="fa fa-dedent"></span></a> </li>
    <!-- END TOGGLE NAVIGATION --> 
    
    <!-- SIGN OUT -->
    <li class="xn-icon-button" style = "position:relative; float:right;">
     <a href="<?php echo SITE_URL; ?>logout.php" class="mb-control" data-box="#mb-signout" style="margin-left:-50px; width:100px"><span class="fa fa-sign-out"></span> Logout</a> </li>
    <li class="xn-icon-button" style = "position:relative; float:right;"> <h5 class = "top-reseller"> <?php echo $_SESSION['user_info']['user_name'].'('.$_SESSION['user_info']['user_credit'].' credit/s)'; ?></h5></li>
    <!-- END SIGN OUT -->
    
  </ul>
  <!-- END X-NAVIGATION VERTICAL --> 
  
  <!-- START BREADCRUMB -->
  <ul class="breadcrumb">
    <li><a href="#">Dashboard</a></li>
    <li><a href="#">Reseller Management</a></li>
    <li class="active">Add Reseller</li>
  </ul>
  <!-- END BREADCRUMB --> 
  
  <!-- PAGE CONTENT WRAPPER -->
  <div class="page-content-wrap">
    <?php if($case==0){?>
    <div class="alert alert-danger" role="alert">
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
      <strong>ERROR!</strong> <?php echo $error; ?> </div>
    <?php } ?>
    <div class="row">
      <div class="col-md-12">
        <form class="form-horizontal" enctype="multipart/form-data" method="post">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title"><strong>Add</strong> SubReseller</h3>
              <ul class="panel-controls">
                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span></a></li>
              </ul>
            </div>
            <div class="panel-body"> </div>
            <div class="panel-body">

              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">RESELLER NAME</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group"> <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                    <input type="text" name="name" class="form-control"/>
                  </div>
                  <span class="help-block">Enter Reseller Name</span> </div>
              </div>
              
              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">RESELLER PASS</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group"> <span class="input-group-addon"><span class="fa fa-user"></span></span>
                    <input type="text" name="pass" class="form-control" placeholder = "Leave it blank to generate it automatically"/>
                  </div>
                  <span class="help-block">Enter Reseller Password</span> </div>
              </div>
              
              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">RESELLER E-MAIL</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group"> <span class="input-group-addon"><span class="fa fa-key"></span></span>
                    <input type="text" name="email" class="form-control"/>
                  </div>
                  <span class="help-block">Enter Reseller Email</span> </div>
              </div>

              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">RESELLER CREDIT</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group"> <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                    <input type="text" name="credit" class="form-control" placeholder="0"/>
                  </div>
                  <span class="help-block">Enter Reseller Credit(ex:50: give 50 credit)</span> </div>
              </div>

              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">RESELLER NOTE</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group"> <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                    <input type="text" name="note" class="form-control"/>
                  </div>
                  <span class="help-block">Enter Reseller Note</span> </div>
              </div>

              <div class="form-group">
                  <label class="col-md-3 col-xs-12 control-label">RESELLER PACKAGE</label>
                  <div class="col-md-6 col-xs-12">                                            
                      <div class="input-group">
                          
                          <select name = "packages[ ]" class="multiselect" multiple='multiple'>
                            <?php
                              while($package = mysqli_fetch_assoc($packages))
                              {
                                  if(strstr($_SESSION['user_info']['user_package_id'], $package['package_id']))
                                    echo "<option value='".$package['package_id']."'>".$package['package_name']."</option>";
                              }
                            ?>
                          </select>
                      </div>                                            
                      <span class="help-block">Select Reseller Package</span>
                  </div>
              </div>

              <div class="form-group">
                  <label class="col-md-3 col-xs-12 control-label">BOUQET STREAMS</label>
                  <div class="col-md-6 col-xs-12">                                            
                      <div class="input-group">
                          
                          <select name = "bouquets[ ]" class="multiselect" multiple='multiple'>
                            <?php
                              while($bouquet = mysqli_fetch_assoc($bouquets))
                              {
                                  if(strstr($_SESSION['user_info']['user_bouquet_id'], $bouquet['bouquet_id']))
                                    echo "<option value='".$bouquet['bouquet_id']."'>".$bouquet['bouquet_name']."</option>";
                              }
                            ?>
                          </select>
                      </div>                                            
                      <span class="help-block">Select bouquet streams</span>
                  </div>
              </div>
              
            </div>
            <div class="panel-footer">
              <input type="reset" class="btn btn-default" value="Clear Form" />
              <input type="submit" name="save" class="btn btn-primary pull-right" value="Submit" />
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

<!-- THIS PAGE PLUGINS --> 
<script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script> 
<script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script> 
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-datepicker.js"></script> 
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-file-input.js"></script> 
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-select.js"></script> 
<script type="text/javascript" src="js/plugins/tagsinput/jquery.tagsinput.min.js"></script> 
<!-- END THIS PAGE PLUGINS --> 

<!-- START TEMPLATE --> 

<script type="text/javascript" src="js/plugins.js"></script> 
<script type="text/javascript" src="js/actions.js"></script> 
<!-- END TEMPLATE -->

<script type="text/javascript" src="js/jquery.multi-select.js"></script>
<script type="text/javascript" src="js/jquery.quicksearch.js"></script>

<!-- END SCRIPTS --> 
<script>
	$(document).ready(function(){
		$("ul.reseller_li:nth-child(2)").addClass("active");
		$("#reseller_li").addClass("active");
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
</body></html><?php }else{
	
	echo "You are not authorized to visit this page direclty,Sorry";
	} ?>
