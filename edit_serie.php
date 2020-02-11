<?php 
include 'head.php';
if(isset($_SESSION['user_info']) && isset($_GET['serie_id'])){
$id = base64_decode($_GET['serie_id']);

$error=NULL;
$case=1;

$con = new db_connect();

$connection=$con->connect();
if($connection==1){
 $insert = new Select_DB($con->connect);

if(isset($_POST['save']))
{
if(!isset($_POST['panel_name']) || $_POST['panel_name']=='' || $_POST['panel_name']==NULL)
{
	$case=0;
	$error="<li>Please Enter Panel Name</li>";
}

if($case == 1)
{
     $connection=$insert->edit_serie($id, isset($_POST['tmdb_id']) ? $_POST['tmdb_id'] : '0', isset($_POST['imdb_name']) ? $_POST['imdb_name'] : '', $_POST['panel_name'],  $_POST['category'],isset($_POST['genre']) ? $_POST['genre'] : '', isset($_POST['director']) ? $_POST['director'] : '', isset($_POST['release']) ? $_POST['release'] : '', isset($_POST['description']) ? $_POST['description'] : '', isset($_POST['poster']) ? $_POST['poster'] : '', isset($_POST['lang']) ? $_POST['lang'] : 'en');
    
    $text = base64_encode('Edit Serie Succeded. Please check with search box.');
     echo "<script>location.href='manage_serie.php?text=".$text."'</script>";
}   
}
    $categories = $insert->get_serie_categories();
    $serie = $insert->get_serie($id);
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
                    <li><a href="#"> Admin </a></li>
                    <li><a href="#"> Serie management </a></li>
                    <li class="active"> Series </li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2><span class="fa fa-arrow-circle-o-left"></span> Edit Serie </h2>
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
                            
                            <form class="form-horizontal" method="post" enctype="multipart/form-data" >
                            <div class="panel panel-default">
                                <div class="panel-body">                    
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">LANGUAGE</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <select name = "lang" style = "/*margin-right:20px; */border-radius: 4px; height:30px; width:160px;" id = "langid" onchange = "setData()">
                                                    <option value = "en" <?php echo ($serie['serie_language'] == 'en' ? "selected" : "");?>>English</option>
                                                    <option value = "it" <?php echo ($serie['serie_language'] == 'it' ? "selected" : "");?>>Italian</option>
                                                    <option value = "de" <?php echo ($serie['serie_language'] == 'de' ? "selected" : "");?>>German</option>
                                                    <option value = "el" <?php echo ($serie['serie_language'] == 'el' ? "selected" : "");?>>Greek</option>
                                                    <option value = "es" <?php echo ($serie['serie_language'] == 'es' ? "selected" : "");?>>Spanish</option>
                                                    <option value = "tr" <?php echo ($serie['serie_language'] == 'tr' ? "selected" : "");?>>Turkish</option>
                                                    <option value = "fr" <?php echo ($serie['serie_language'] == 'fr' ? "selected" : "");?>>French</option>
                                                </select>
                                            </div>                                            
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">IMDB NAME</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="imdb_name" class="form-control" id = "imdb_title" value = "<?php echo $serie['serie_original_name'];?>"/>
                                            </div>                                            
                                            <span class="help-block">Enter Serie Name</span>
                                            <input type="text" name="tmdb_id" class="form-control" id = "tmdb_index" value = "<?php echo $serie['serie_tmdb_id'];?>" hidden/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">PANEL NAME</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="panel_name" class="form-control" value = "<?php echo $serie['serie_name'];?>"/>
                                            </div>                                            
                                            <span class="help-block">Enter Serie Name</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SERIE CATEGORY</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <select name = "category" style = "/*margin-right:20px; */border-radius: 4px; height:30px; width:160px;">
                                                <?php 
                                                    while($category = mysqli_fetch_assoc($categories))
                                                        echo '<option value = "'.$category['serie_category_id'].'"'.($category['serie_category_id'] == $serie['serie_category_id'] ? "selected" : "").'>'.$category['serie_category_name'].'</option>';
                                                ?>
                                                </select>
                                            </div>                                            
                                            <span class="help-block">Choose Movie Category</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SERIE GENRE</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="genre" class="form-control" id = "genreid" value = "<?php echo $serie['serie_genre'];?>"/>
                                            </div>                                            
                                            <span class="help-block">Enter Movie Genre</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SERIE DIRECTOR</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="director" class="form-control" id = "directorid" value = "<?php echo $serie['serie_director'];?>"/>
                                            </div>                                            
                                            <span class="help-block">Enter Serie Director</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SERIE RELEASE DATE</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="release" class="form-control" id = "releaseid" value = "<?php echo $serie['serie_release_date']; ?>"/>
                                            </div>                                            
                                            <span class="help-block">Enter Serie Release Date</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SERIE SHORT DESCRIPTION</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="description" class="form-control" id = "descriptionid" value = "<?php echo base64_decode($serie['serie_short_description']); ?>"/>
                                            </div>                                            
                                            <span class="help-block">Enter Serie Short Description</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SERIE POSTER</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <img src = "<?php echo $serie['serie_pic']; ?>" id = "srcid"/>
                                                <input type = "text" name = "poster" id = "posterid" hidden/>
                                            </div>                                            
                                            
                                        </div>
                                    </div>

                                </div>
                                <div class="panel-footer">
                                    <input type="reset" value="Reset" class="btn btn-default">                                   
                                    <input type="submit" name="save" value="Save" class="btn btn-primary pull-right" onclick="return confirm('Are you sure want to save serie?');">
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

    var delay = (function(){
          var timer = 0;
          return function(callback, ms){
          clearTimeout (timer);
          timer = setTimeout(callback, ms);
         };
    })();

    $('#imdb_title').keyup(function() {
      delay(function(){
        setData();
      }, 1500 );
    });

    function setData()
    {
        var title = $("#imdb_title")[0].value;
        var lang = $("#langid")[0].value;

        $.ajax({
                type: "POST",
                url: "./serie.php",
                data: { title: title,
                lang: lang },
                dataType: "json",
                success: function(result) {
                    if(result[0] == "true")
                    {
                        console.log("success");
                        $("#genreid")[0].value = result[1];
                        $("#directorid")[0].value = result[2];
                        $("#releaseid")[0].value = result[3];
                        $("#descriptionid")[0].value = result[4];
                        $("#srcid")[0].src = result[5];
                        $("#posterid")[0].value = result[5];
                        $("#tmdb_index")[0].value = result[6];
                    }
                    else
                        console.log("failed");
                }
                
            });
    }


	$(document).ready(function(){

        window.url = '';

		$("#series_li").addClass("active");

	});
	</script>
    </body>
</html>
<?php }else{
	
	echo "You are not authorized to visit this page direclty,Sorry";
	} ?>




