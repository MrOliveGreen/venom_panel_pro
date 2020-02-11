<?php 
include 'head.php';
if(isset($_SESSION['user_info']) && isset($_GET['bouquet_id'])){

$bouquet_id = base64_decode($_GET['bouquet_id']);
$error=NULL;
$case=1;

$con = new db_connect();

$connection=$con->connect();
if($connection==1){
 $insert = new Select_DB($con->connect);

if(isset($_POST['save']))
{
if(!isset($_POST['bouquet_name']) || $_POST['bouquet_name']=='' || $_POST['bouquet_name']==NULL)
{
	$case=0;
	$error="<li>Please Enter Category Name</li>";
}

if($case == 1)
{
     $insert->edit_bouquet($bouquet_id, $_POST['bouquet_name'], isset($_POST['streams_to']) ? $_POST['streams_to'] : '', isset($_POST['series_to']) ? $_POST['series_to'] : '', isset($_POST['movies_to'])? $_POST['movies_to'] : '' );
    
    $text = base64_encode('Edit Bouquet Succeded. Please check with search box.');
     echo "<script>location.href='manage_bouquet.php?text=".$text."'</script>";
}   
}

    $streams = $insert->get_streams_name();
    $movies = $insert->get_movies_name();
    $series = $insert->get_series();

    $bouquet = $insert->get_bouquet($bouquet_id);

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
                    <li><a href="#">Bouquet Management</a></li>
                    <li class="active">Edit bouquet</li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2><span class="fa fa-arrow-circle-o-left"></span> Edit Bouquet</h2>
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
                                        <label class="col-md-3 col-xs-12 control-label">BOUQUET NAME</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="bouquet_name" class="form-control" value = "<?php echo $bouquet['bouquet_name']; ?>"required/>
                                            </div>                                            
                                            <span class="help-block">Enter Bouquet Name</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">BOUQET STREAMS</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                  <div class="row">
                                                    <div class="col-xs-5">
                                                        <select id="stream" class="form-control" multiple="multiple" style = " height:250px; font-size: 16px;">
                                                            <?php
                                                                if($bouquet['bouquet_streams'] == '')
                                                                    $cur_streams = array();
                                                                else
                                                                    $cur_streams = json_decode($bouquet['bouquet_streams']);

                                                                $cur_str = array();

                                                                while($stream = mysqli_fetch_assoc($streams)){
                                                                    if(!in_array($stream['stream_id'], $cur_streams))
                                                                        echo "<option value='".$stream['stream_id']."'>".$stream['stream_name']."</option>";
                                                                    else
                                                                        $cur_str[] = $stream;
                                                                }
                                                              ?>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="col-xs-2" style = "top:20%;">
                                                        <button type="button" id="stream_rightAll" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
                                                         <button type="button" id="stream_move_up" class="btn btn-block"><i class="glyphicon glyphicon-arrow-up"></i></button>
                                                        <button type="button" id="stream_rightSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
                                                        <button type="button" id="stream_leftSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
                                                        <button type="button" id="stream_move_down" class="btn btn-block"><i class="glyphicon glyphicon-arrow-down"></i></button>
                                                        <button type="button" id="stream_leftAll" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
                                                    </div>
                                                    
                                                    <div class="col-xs-5">
                                                        <select name="streams_to[]" id="stream_to" class="form-control" multiple="multiple" style = " height:250px; font-size: 16px;">
                                                            <?php
                                                            foreach ($cur_str as $str)
                                                            {
                                                              echo "<option value='".$str['stream_id']."'>".$str['stream_name']."</option>";
                                                            }?>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                            </div>                                            
                                            <span class="help-block">Select bouquet streams</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">BOUQET SERIES</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">

                                                <div class="row">
                                                    <div class="col-xs-5">
                                                        <select id="serie" class="form-control" multiple="multiple" style = " height:250px; font-size: 16px;">
                                                            <?php
                                                                if($bouquet['bouquet_series'] == '')
                                                                    $cur_series = array();
                                                                else
                                                                    $cur_series = json_decode($bouquet['bouquet_series']);

                                                                $cur_ser = array();

                                                                while($serie = mysqli_fetch_assoc($series)){
                                                                    if(!in_array($serie['serie_id'], $cur_series))
                                                                        echo "<option value='".$serie['serie_id']."'>".$serie['serie_name']."</option>";
                                                                    else
                                                                        $cur_ser[] = $serie;
                                                                }
                                                              ?>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="col-xs-2" style = "top:20%;">
                                                        <button type="button" id="serie_rightAll" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
                                                         <button type="button" id="serie_move_up" class="btn btn-block"><i class="glyphicon glyphicon-arrow-up"></i></button>
                                                        <button type="button" id="serie_rightSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
                                                        <button type="button" id="serie_leftSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
                                                        <button type="button" id="serie_move_down" class="btn btn-block"><i class="glyphicon glyphicon-arrow-down"></i></button>
                                                        <button type="button" id="serie_leftAll" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
                                                    </div>
                                                    
                                                    <div class="col-xs-5">
                                                        <select name="series_to[]" id="serie_to" class="form-control" multiple="multiple" style = " height:250px; font-size: 16px;">
                                                            <?php
                                                            foreach ($cur_ser as $ser)
                                                            {
                                                              echo "<option value='".$ser['serie_id']."'>".$ser['serie_name']."</option>";
                                                            }?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>                                            
                                            <span class="help-block">Select bouquet series</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">BOUQET MOVIES</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">

                                                <div class="row">
                                                    <div class="col-xs-5">
                                                        <select id="movie" class="form-control" multiple="multiple" style = "height:250px; font-size: 16px;">
                                                            <?php
                                                                if($bouquet['bouquet_movies'] == '')
                                                                    $cur_movies = array();
                                                                else
                                                                    $cur_movies = json_decode($bouquet['bouquet_movies']);
                                                            $cur_mov = array();


                                                                while($movie = mysqli_fetch_assoc($movies)){
                                                                    if(!in_array($movie['movie_id'], $cur_movies))
                                                                        echo "<option value='".$movie['movie_id']."'>".$movie['movie_name']."</option>";
                                                                    else
                                                                        $cur_mov[] = $movie;
                                                                }
                                                              ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-xs-2" style = "top:20%;">
                                                        <button type="button" id="movie_rightAll" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
                                                         <button type="button" id="movie_move_up" class="btn btn-block"><i class="glyphicon glyphicon-arrow-up"></i></button>
                                                        <button type="button" id="movie_rightSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
                                                        <button type="button" id="movie_leftSelected" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
                                                        <button type="button" id="movie_move_down" class="btn btn-block"><i class="glyphicon glyphicon-arrow-down"></i></button>
                                                        <button type="button" id="movie_leftAll" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
                                                    </div>
                                                    
                                                    <div class="col-xs-5">
                                                        <select name="movies_to[]" id="movie_to" class="form-control" multiple="multiple" style = "width:100%; height:250px; font-size: 16px;">
                                                            <?php
                                                            foreach ($cur_mov as $mov)
                                                            {
                                                              echo "<option value='".$mov['movie_id']."'>".$mov['movie_name']."</option>";
                                                            }?>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>                                            
                                            <span class="help-block">Select bouquet movies</span>
                                        </div>
                                    </div>
                                <div class="panel-footer">
                                    <input type="reset" value="Reset" class="btn btn-default">                                   
                                    <input type="submit" name="save" value="Save" class="btn btn-primary pull-right" onclick="return confirm('Are you sure want to edit movie category?');">
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

        <script type="text/javascript" src="js/jquery.multiselect.js"></script>
        <script type="text/javascript" src="js/jquery.quicksearch.js"></script>
        <!-- END THIS PAGE PLUGINS -->       
        
        <!-- START TEMPLATE -->
        
        <script type="text/javascript" src="js/plugins.js"></script>        
        <script type="text/javascript" src="js/actions.js"></script>        
        <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->          
	<script>
	$(document).ready(function(){
		$("#bouquet_li").addClass("active");
	});

    $('#stream').multiselect({
        search: {
            left: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
            right: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
        },

    });

     $('#serie').multiselect({
        search: {
            left: '<input type="text" name="serieq" class="form-control" placeholder="Search..." />',
            right: '<input type="text" name="serieq" class="form-control" placeholder="Search..." />',
        },

    });

     $('#movie').multiselect({
        search: {
            left: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
            right: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
        },

    });

	</script>
    </body>
</html>
<?php }else{
	
	echo "You are not authorized to visit this page direclty,Sorry";
	} ?>




