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
if($_POST['streams']=='' || $_POST['streams']==NULL)
{
    $case=0;
    $error="<li>Please Select Streams</li>";
    
}

if($case==1){

if($connection==1){
         
    $connection=$insert->edit_mass_stream($_POST['streams'], $_POST['method'], $_POST['category'], isset($_POST['transcoding']) ? $_POST['transcoding'] : '', $_POST['native_frame'], isset($_POST['flag']) ? $_POST['flag'] : '', isset($_POST['proxy']) ? $_POST['proxy'] : '', isset($_POST['agent']) ? $_POST['agent'] : '', isset($_POST['auto_restart']) ? $_POST['auto_restart'] : '', isset($_POST['demand']) ? $_POST['demand'] : 'off', isset($_POST['restart']) ? $_POST['restart'] : 'off');

    $text = base64_encode('Mass Edit Stream Succeded. Please check with search box.');
    echo "<script>location.href='manage_stream.php?text=".$text."'</script>";
}
}
}

$id = 0;
if(isset($_GET['id']))
{
  $streams = array();

  $id = base64_decode($_GET['id']);
  $bouquet = $insert->get_bouquet($id);

  if($bouquet['bouquet_streams'] != '')
  {
    $bouquet_streams = json_decode($bouquet['bouquet_streams']);
    $streams = $insert->get_streams_name($bouquet_streams);
  }

}
else
  $streams = $insert->get_streams_name();

$categories = $insert->get_categories();
$servers = $insert->get_servers();
$transcodes = $insert->get_transcodes();
$epgs = $insert->get_epgs();
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
    <li><a href="#">Admin </a></li>
    <li><a href="#"> Stream Management </a></li>
    <li class="active"> Mass Edit Stream </li>
  </ul>
  <!-- END BREADCRUMB --> 
  
  <!-- PAGE CONTENT WRAPPER -->
  <div class="page-content-wrap">
    <?php if($case==0){?>
    <div class="alert alert-danger" role="alert">
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
      <strong>ERROR!</strong> <?php echo $error; ?> </div>
    <?php }else if($case == 2){ ?>
        <div class="alert alert-success" role="alert">
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
      <strong>NOTICE!</strong> <?php echo $notice; ?> </div>
    <?php }?>
    <div class="row">
      <div class="col-md-12">
        <form class="form-horizontal" enctype="multipart/form-data" method="post">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title"><strong>Mass Edit</strong> Stream</h3>
              <ul class="panel-controls">
                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span></a></li>
              </ul>
            </div>
            <div class="panel-body">

              <div class="form-group">
                  <label class="col-md-3 col-xs-12 control-label">CHOOSE BOUQUET</label>
                  <div class="col-md-6 col-xs-12">                                            
                      <div class="input-group">
                          <select id = "bouquet_id" style = "border-radius: 4px; height:30px; width:160px;" onchange="filter(this)">
                              <option value = "0">Show All</option>
                            <?php
                              while($bouquet = mysqli_fetch_assoc($bouquets))
                                  echo "<option value='".base64_encode($bouquet['bouquet_id'])."'".($bouquet['bouquet_id'] == $id ? "selected" : "").">".$bouquet['bouquet_name']."</option>";
                            ?>
                          </select>
                      </div>                                            
                      <span class="help-block">Enter Line Password</span>
                  </div>
              </div>

              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">CHOOSE YOUR STREAMS</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group">
                      <select name = "streams[ ]" class="streamselect" multiple='multiple'>
                        <?php while($stream = mysqli_fetch_assoc($streams)){
                            if($stream['stream_id'] != 1)
                                echo '<option value="'.$stream['stream_id'].'">'.$stream['stream_name'].'</option>';
                        }?> 
                      </select>
                  </div>
                  <span class="help-block">Choose Stream To Change</span> 
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">STREAM METHOD</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group">
                    <select name = "method" style = "margin-right:20px; border-radius: 4px; height:30px; width:160px;">
                        <option value = "0"> Keep Old Method </option>
                        <option value = "1"> Live Streaming </option>
                        <option value = "2"> Copy Streaming </option>
                        <option value = "3"> Local Streaming </option>
                        <option value = "4"> Loop Streaming </option>
                        <option value = "5"> Adaptive Streaming </option>
                    </select>
                  </div>
                  <span class="help-block">Choose Stream Method</span> </div>
              </div>

              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">STREAM CATEGORY</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group">
                    <select name = "category" style = "margin-right:20px; border-radius: 4px; height:30px; width:160px;">
                      <option value = "0"> Keep Old Category </option>
                     <?php while($category = mysqli_fetch_assoc($categories)){
                        echo '<option value="'.$category['stream_category_id'].'">'.$category['stream_category_name'].'</option>';
                     }?> 
                    </select>
                  </div>
                  <span class="help-block">Select Stream Category</span> </div>
              </div>

              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">STREAM TRANSCODING</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group">
                    <select name = "transcoding" style = "margin-right:20px; border-radius: 4px; height:30px; width:160px;">
                      <option value = "">Keep Old Transcoding</option>
                      <option value = "0">No Transcoding</option>
                        <?php while($transcode = mysqli_fetch_assoc($transcodes)){
                            echo '<option value="'.$transcode['transcoding_id'].'">'.$transcode['transcoding_name'].'</option>';
                        }?> 
                          
                    </select>
                  </div>
                  <span class="help-block">Choose Transcoding</span> </div>
                </div>

              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">STREAM NATIVE FRAMES</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group"> 
                    <select name = "native_frame" style = "margin-right:20px; border-radius: 4px; height:30px; width:160px;">
                        <option value = "2"> Keep Old Value </option>
                        <option value = "0"> No </option>
                        <option value = "1"> Yes </option>
                    </select>
                  </div>
                  <span class="help-block">Choose Native Frame</span> </div>
              </div>

              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">FORMAT FLAGS</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group"> <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                    <input type="text" name="flag" class="form-control"/>
                  </div>
                  <span class="help-block">Enter format flags</span> </div>
              </div>
              
              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">STREAM HTTP PROXY</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group"> <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                    <input type="text" name="proxy" class="form-control" placeholder=" Keep old proxy " />
                  </div>
                  <span class="help-block">Enter Stream Http Proxy</span> </div>
              </div>

              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">STREAM USER AGENT</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group"> <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                    <input type="text" name="agent" class="form-control" placeholder=" Keep old user agent " />
                  </div>
                  <span class="help-block">Enter Stream User Agent</span> </div>
              </div>

              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">AUTO RESTART STREAM</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group">
                    <div class="input-append date" id="datetimepicker" data-date-format="dd-mm-yyyy">
                        <input style = "height:32px; border-radius: 4px;"size="24" name = "auto_restart" type="text" readonly>
                        <span class="add-on"><i class="icon-th"></i></span>
                    </div>
                </div>
                <span class="help-block">Enter Auto Restart Stream</span> </div>
              </div>

              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">STREAM ON DEMAND</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group" style = "margin-top: 5px;"> 
                    <label>
                    <input type="checkbox" name = "demand" class="flat-green" >
                  </label>
                  </div>
                  <span class="help-block">Check if your stream is on demand</span> </div>
              </div>

              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">RESTART STREAM ON SUBMIT</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group"> 
                    <label>
                    <input type="checkbox" name = "restart" class="flat-green">
                  </label>
                  </div>
                  <span class="help-block">Check if you want to restart streams on submit.</span> </div>
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
<script type="text/javascript" src="js/bootstrap-datetimepicker.js"></script>

<!-- END SCRIPTS --> 
<script>

    $(document).ready(function(){
        $("ul.streams_li:nth-child(2)").addClass("active");
        $("#streams_li").addClass("active");
        collectSource();
    });

    $('#datetimepicker').datetimepicker({
    format: 'yyyy-mm-dd hh:ii'
    });
    $('#datetimepicker').datetimepicker('setStartDate', '2019-11-01 00:00');

  
  $('.streamselect').multiSelect({
      selectableHeader: "<label class='control-label' style = 'margin-top:-20px; margin-bottom:10px;'>AVAILABLE STREAMS</label>",
      selectionHeader: "<label class='control-label' style = 'margin-top:-20px; margin-bottom:10px;'>STREAMS TO CHANGE</label>",
      afterSelect: function(){
        this.qs1.cache();
        this.qs2.cache();
      },
      afterDeselect: function(){
        this.qs1.cache();
        this.qs2.cache();
      }
    });

    $('input[type="checkbox"].flat-green, input[type="radio"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    });

    function filter(sel)
    {
        selected = sel.options[sel.selectedIndex].value;

        if(selected == "0")
            window.location.href = "edit_stream_mass.php";
        else
            window.location.href = "edit_stream_mass.php?id=" + selected;
    }

    </script>
</body></html><?php }else{
    
    echo "You are not authorized to visit this page direclty,Sorry";
    } ?>
