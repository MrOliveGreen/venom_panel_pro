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
if($_POST['movies']=='' || $_POST['movies']==NULL)
{
    $case=0;
    $error="<li>Please Select Movies</li>";
    
}

if($case==1){

if($connection==1){
         
    $connection=$insert->edit_mass_movie($_POST['movies'], isset($_POST['new_category']) ? $_POST['new_category'] : '', isset($_POST['delete']) ? $_POST['delete'] : 'off');

    $text = base64_encode('Mass Edit Movie Succeded. Please check with search box.');
    echo "<script>location.href='manage_movie.php?text=".$text."'</script>";
}
}
}

$id = 0;
if(isset($_GET['id']))
{
  $streams = array();

  $id = base64_decode($_GET['id']);
  $movies = $insert->get_movies_name($id);
}
else
  $movies = $insert->get_movies_name();

$categories = $insert->get_movie_categories();
$new_categories = $insert->get_movie_categories();

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
                  <label class="col-md-3 col-xs-12 control-label">CHOOSE MOVIE CATEGORY</label>
                  <div class="col-md-6 col-xs-12">                                            
                      <div class="input-group">
                          <select id = "category_id" style = "border-radius: 4px; height:30px; width:160px;" onchange="filter(this)">
                              <option value = "0">Show All</option>
                            <?php
                              while($category = mysqli_fetch_assoc($categories))
                                  echo "<option value='".base64_encode($category['movie_category_id'])."'".($category['movie_category_id'] == $id ? "selected" : "").">".$category['movie_category_name']."</option>";
                            ?>
                          </select>
                      </div>                                            
                  </div>
              </div>

              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">CHOOSE YOUR MOVIES</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group">
                      <select name = "movies[ ]" class="movieselect" multiple='multiple'>
                        <?php while($movie = mysqli_fetch_assoc($movies)){
                            if($movie['movie_id'] != 1)
                                echo '<option value="'.$movie['movie_id'].'">'.$movie['movie_name'].'</option>';
                        }?> 
                      </select>
                  </div>
                  <span class="help-block">Choose Movie To Change</span> 
                </div>
              </div>

              <div class="form-group">
                  <label class="col-md-3 col-xs-12 control-label">ASSIGN NEW CATEGORY</label>
                  <div class="col-md-6 col-xs-12">                                            
                      <div class="input-group">
                          <select name = "new_category" style = "border-radius: 4px; height:30px; width:160px;" >
                            <option value = ""> Keep Current Category </option>
                            <?php
                              while($category = mysqli_fetch_assoc($new_categories))
                                  echo "<option value='".($category['movie_category_id'])."'".($category['movie_category_id'] == $id ? "selected" : "").">".$category['movie_category_name']."</option>";
                            ?>
                          </select>
                      </div>                                            
                  </div>
              </div>

              <div class="form-group">
                <label class="col-md-3 col-xs-12 control-label">DELETE SELECTED MOVIES</label>
                <div class="col-md-6 col-xs-12">
                  <div class="input-group" style = "margin-top: 5px;"> 
                    <label>
                    <input type="checkbox" name = "delete" class="flat-green" >
                  </label>
                  </div>
                  <span class="help-block">Check to delete selected movies</span> </div>
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
        $("ul.movies_li:nth-child(2)").addClass("active");
        $("#movies_li").addClass("active");
        collectSource();
    });

  $('.movieselect').multiSelect({
      selectableHeader: "<label class='control-label' style = 'margin-top:-20px; margin-bottom:10px;'>AVAILABLE MOVIES</label>",
      selectionHeader: "<label class='control-label' style = 'margin-top:-20px; margin-bottom:10px;'>MOVIES TO CHANGE</label>",
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
            window.location.href = "edit_movie_mass.php";
        else
            window.location.href = "edit_movie_mass.php?id=" + selected;
    }

    </script>
</body></html><?php }else{
    
    echo "You are not authorized to visit this page direclty,Sorry";
    } ?>
