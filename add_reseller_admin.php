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

  $connection = $insert->add_reseller_admin($_POST['name'],$pass,$_POST['email'],isset($_POST['dns']) ? $_POST['dns'] : '', isset($_POST['note']) ? $_POST['note'] : '' ,$_POST['packages'], isset($_POST['custom_pkg']) ? $_POST['custom_pkg'] : '', $_POST['bouquets'], isset($_POST['credit']) ? $_POST['credit'] : 0, $_POST['user_type']);
	
	   $text = base64_encode('Add Subreseller Succeded. Please check with search box.');
     echo "<script>location.href='manage_reseller_admin.php?text=".$text."'</script>";
  
}
}
}
$packages = $insert->get_packages();
$bouquets = $insert->get_bouquets();
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
              <h3 class="panel-title"><strong>Add</strong> Reseller</h3>
              <ul class="panel-controls">
                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span></a></li>
              </ul>
            </div>
            <div class="panel-body"> </div>
            <div class="panel-body">

              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">RESELLER NAME</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group"> <span class="input-group-addon"><span class="fa fa-user"></span></span>
                    <input type="text" name="name" class="form-control" required/>
                  </div>
                  <span class="help-block">Enter Reseller Name</span> </div>
              </div>
              
              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">RESELLER PASS</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group"> <span class="input-group-addon"><span class="fa fa-key"></span></span>
                    <input type="text" name="pass" class="form-control" placeholder = "Leave it blank to generate it automatically"/>
                  </div>
                  <span class="help-block">Enter Reseller Password</span> </div>
              </div>
              
              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">RESELLER E-MAIL</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group"> <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
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
                  <span class="help-block">Enter Reseller Credit</span> </div>
              </div>

              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">RESELLER DNS</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group"> <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                    <input type="text" name="dns" class="form-control"/>
                  </div>
                  <span class="help-block">Enter Reseller DNS</span> </div>
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
                <label class="col-md-3 col-xs-12 control-label">RESELLER TYPE</label>
                <div class="col-md-6 col-xs-12">
                  
                    <select name = "user_type" style = "margin-right:20px; border-radius: 4px; height:30px; width:200px;">
                      
                      <option value = "1"> Reseller </option>
                      <option value = "0"> Admin </option>
                      <option value = "2"> SubReseller </option>
                      
                    </select>
                  </div>
              </div>

              <div class="form-group">
                  <label class="col-md-3 col-xs-12 control-label">RESELLER PACKAGE</label>
                  <div class="col-md-6 col-xs-12">                                            
                      <div class="input-group">
                          
                          <select name = "packages[ ]" class="multiselect" multiple='multiple'>
                            <?php
                              while($package = mysqli_fetch_assoc($packages))
                              {
                                    echo "<option value='".$package['package_id']."'>".$package['package_name']."</option>";
                              }
                            ?>
                          </select>
                      </div>                                            
                      <span class="help-block">Select Reseller Package</span>
                  </div>
              </div>

              <div class="form-group">
                  <label class="col-md-3 col-xs-12 control-label">RESELLER CUSTOM PACKAGE</label>
                  <div class="col-md-6 col-xs-12">            
                      <div class="input-group">
                          <div class = "row">
                            <div class = "col-md-3">
                              <input id = "pkg_name" class = "form-control" placeholder="Package Name" style = "border-radius: 4px;">
                            </div>
                            <div class = "col-md-2" class = "form-group">
                              <select id = "pkg_type" style = "margin-right:20px; border-radius: 4px; height:30px; width:100px;">
                                <option value = "0"> Hour </option>
                                <option value = "1"> Day </option>
                                <option value = "2"> Week </option>
                                <option value = "3"> Month </option>
                                <option value = "4"> Year </option>
                              </select>
                            </div>
                            <div class = "col-md-3">
                              <input id = "pkg_duration" class = "form-control" placeholder="Package Duration" style = "border-radius: 4px;">
                            </div>
                            <div class = "col-md-3">
                              <input id = "pkg_credit" class = "form-control" placeholder="Package Credit" style = "border-radius: 4px;">
                            </div>
                            <div class = "col-md-1">
                              <span class = "btn btn-warning" onclick = "addCustom()"> Add </span>
                            </div>
                          </div>
                          <div class = "row" style = "margin-top:20px;">
                            <div class = "col-md-5">
                              <select class="custom-select"  style = "width:250px;  height:180px; font-size: 16px;" id = "custom_box" multiple>
           
                              </select>
                          </div>
                            <div class = "col-md-5">
                              <span style = "margin-left:80%; margin-top:30px;"class = "btn btn-default fa fa-arrow-up" onclick = "toUp()"></span>
                              <span style = "margin-left:80%; margin-top:10px;"class = "btn btn-default fa fa-arrow-down" onclick = "toDown()"></span>
                              <span style = "margin-left:80%; margin-top:10px;"class = "btn btn-default fa fa-times" onclick = "toTrash()"></span>
                          </div>
                          <input type = "text" name = "custom_pkg" id = "custom_pkg_id" hidden>
                        </div>
                      </div>                                            
                      <span class="help-block">Enter Reseller Custom Package</span>
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
        <p>Press No if you want to continue work. Press Yes to logout current user.</p>
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
  var pkgname = [];
  var type = [];
  var duration = [];
  var credit = [];

  function prepareData()
  {
    var pkgs = [];
    for(var i = 0; i < pkgname.length; i ++)
    {
      var pkg = [];
      pkg.push(pkgname[i]);
      pkg.push(type[i]);
      pkg.push(duration[i]);
      pkg.push(credit[i]);
      pkgs.push(pkg);
    }
    $("#custom_pkg_id")[0].value = pkgs;
    console.log(pkgs);
  }

  function addCustom()
  {
    var pkg_name = $("#pkg_name")[0].value;
    var pkg_type = $("#pkg_type")[0].value;
    var pkg_duration = $("#pkg_duration")[0].value;
    console.log(pkg_duration);
    var pkg_credit = $("#pkg_credit")[0].value;
    var custom_box = $('#custom_box');
    if(pkg_name === '' || pkg_type === '' || pkg_duration === '' || isNaN(pkg_duration) || isNaN(pkg_credit))
      alert("Failed to add custom package. Please check your input.");
    else
    {
      custom_box.append('<option value="' + pkg_name + '" >' + pkg_name + '</option>');

      pkgname.push(pkg_name);
      type.push(pkg_type);
      duration.push(pkg_duration);
      credit.push(pkg_credit);
      prepareData();
    }
  }

  function changeSequence(i, j)
  {
    var temp;

    temp = pkgname[i]; pkgname[i] = pkgname[j]; pkgname[j] = temp;
    temp = type[i]; type[i] = type[j]; type[j] = temp;
    temp = duration[i]; duration[i] = duration[j]; duration[j] = temp;
    temp = credit[i]; credit[i] = credit[j]; credit[j] = temp;
  }

  function toUp() {
      
      var selectList = document.getElementById("custom_box");
      var selectOptions = selectList.getElementsByTagName('option');
      
      for (var i = 1; i < selectOptions.length; i++) {
        var opt = selectOptions[i];
        if (opt.selected) {
          selectList.removeChild(opt);
          selectList.insertBefore(opt, selectOptions[i - 1]);
          changeSequence(i, i - 1);
        }
      }

      prepareData();
    }

  function toDown() {
      selectList = document.getElementById("custom_box");
      selectOptions = selectList.getElementsByTagName('option');
      for (var i = selectOptions.length - 2; i >= 0; i--) {
        var opt = selectOptions[i];
        if (opt.selected) {
           var nextOpt = selectOptions[i + 1];
           changeSequence(i, i + 1);
           opt = selectList.removeChild(opt);
           nextOpt = selectList.replaceChild(opt, nextOpt);
           selectList.insertBefore(nextOpt, opt);
        }
      }

      prepareData();
    }

    function toTrash()
    {
      var i;
      var ids = [];
      for(i=custom_box.options.length-1;i>=0;i--)
      {
        if(custom_box.options[i].selected)
        {
          custom_box.remove(i);          
          console.log(i);
          ids.push(i);
        }
      }
      //console.log('ids:' + ids);
      for(i = 0; i < ids.length; i ++)
      {
        pkgname.splice(ids[i], 1);type.splice(ids[i], 1);duration.splice(ids[i], 1);credit.splice(ids[i], 1);
      }
      prepareData();
    }

	$(document).ready(function(){
		$("ul.reseller_li:nth-child(2)").addClass("active");
		$("#reseller_li").addClass("active");

    window.name = [];
    window.type = [];
    window.duration = [];
    window.credit = [];
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
