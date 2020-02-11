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
if(!isset($_POST['panel_name']) || $_POST['panel_name']=='' || $_POST['panel_name']==NULL)
{
	$case=0;
	$error="<li>Please Enter Movie Name</li>";
}

if($case == 1)
{
     $method = $_POST['method'];
     if($method == '1')
        $source = isset($_POST['source_files']) ? $_POST['source_files'] : '';
     else
        $source = isset($_POST['source_url']) ? $_POST['source_url'] : '';

     $connection=$insert->add_movie($_POST['lang'], isset($_POST['imdb_name']) ? $_POST['imdb_name'] : '', $_POST['panel_name'], $method, $source, $_POST['server'], isset($_POST['copy']) ? $_POST['copy'] : 'off', isset($_POST['genre']) ? $_POST['genre'] : '', isset($_POST['director']) ? $_POST['director'] : '', isset($_POST['cast']) ? $_POST['cast'] : '', isset($_POST['release']) ? $_POST['release'] : '', isset($_POST['duration']) ? $_POST['duration'] : '', isset($_POST['description']) ? $_POST['description'] : '', $_POST['category'], $_POST['extension'], isset($_POST['transcoding']) ? $_POST['transcoding'] : '', isset($_POST['poster']) ? $_POST['poster'] : '');
    
    $text = base64_encode('Add Movie Succeded. Please check with search box.');
     echo "<script>location.href='manage_movie.php?text=".$text."'</script>";
}   
}
    $servers = $insert->get_servers();
    $categories = $insert->get_movie_categories();
    $transcodes = $insert->get_transcodes();
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
                    <li><a href="#"> Movie management </a></li>
                    <li class="active"> Movies </li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2><span class="fa fa-arrow-circle-o-left"></span> Add Movie </h2>
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
                                        <label class="col-md-3 col-xs-12 control-label">MOVIE IMDB NAME / MOVIE PANEL NAME</label>
                                        <div class="col-md-1 col-xs-12">                                            
                                            <div class="input-group">
                                                <select name = "lang" style = "/*margin-right:20px; */border-radius: 4px; height:30px; width:160px;" id = "langid" onchange = "setData()">
                                                    <option value = "en">English</option>
                                                    <option value = "it">Italian</option>
                                                    <option value = "de">German</option>
                                                    <option value = "el">Greek</option>
                                                    <option value = "es">Spanish</option>
                                                    <option value = "tr">Turkish</option>
                                                    <option value = "fr">French</option>
                                                </select>
                                            </div>                                            
                                        </div>
                                        <div class="col-md-3 col-xs-12">                                     
                                            <div class="input-group">
                                                <input type="text" name="imdb_name" class="form-control" id = "imdb_title" required/>
                                            </div>                                            
                                        </div>
                                        <div class="col-md-3 col-xs-12">                                            
                                            <div class="input-group">
                                                <input type="text" name="panel_name" class="form-control" required/>
                                            </div>                                            
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">MOVIE METHOD</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <div class="input-group">
                                                <select name = "method" style = "/*margin-right:20px; */border-radius: 4px; height:30px; width:160px;" onchange="changeSource()" id = "urltype">
                                                    <option value = "1">Local</option>
                                                    <option value = "0">Remote</option>
                                                </select>
                                            </div>           
                                            </div>                                            
                                            <span class="help-block">Enter Movie Method</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">MOVIE SERVER</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <select name = "server" style = "/*margin-right:20px; */border-radius: 4px; height:30px; width:160px;" id = "serverid">
                                                <?php 
                                                    while($server = mysqli_fetch_assoc($servers))
                                                        echo '<option value = "'.$server['server_id'].'">'.$server['server_name'].'</option>';
                                                ?>
                                            </select>
                                            </div>                                            
                                            <span class="help-block">Choose Movie Server</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">MOVIE SOURCE</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div id = "source1">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                    <input type="text" class="form-control" placeholder="Please Input Directory Here" id = "url"/>
                                                    <span class = "btn btn-success" style = "margin-left:20px;"onclick="addMovie()"> Show Movie Files </span>
                                                </div>
                                                <input type="text" name="source_files" id = "filepath" class="form-control" hidden/>
                                                <div id="jstree" style = "border: 1px; max-height:300px; overflow-y:auto;">
                                                    <ul>
                                                      <li id = "header"> FileList </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="input-group" id = "source2">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="source_url" class="form-control"/>
                                            </div>

                                            
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">MOVIE COPY(NOT DOWNLOADING)</label>
                                        <div class="col-md-6 col-xs-12">
                                          <div class="input-group"> 
                                            <label>
                                            <input type="checkbox" name = "copy" class="flat-green">
                                          </label>
                                          </div>
                                          <span class="help-block">Check if you want to copy movie</span> </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">MOVIE GENRE</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="genre" class="form-control" id = "genreid"/>
                                            </div>                                            
                                            <span class="help-block">Enter Movie Genre</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">MOVIE DIRECTOR</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="director" class="form-control" id = "directorid"/>
                                            </div>                                            
                                            <span class="help-block">Enter Movie Director</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">MOVIE CAST</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="cast" class="form-control" id = "castid"/>
                                            </div>                                            
                                            <span class="help-block">Enter Movie Cast</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">MOVIE RELEASE DATE</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="release" class="form-control" id = "releaseid"/>
                                            </div>                                            
                                            <span class="help-block">Enter Movie Release Date</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">MOVIE DURATION</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="duration" class="form-control" id = "durationid"/>
                                            </div>                                            
                                            <span class="help-block">Enter Movie Duration</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">MOVIE SHORT DESCRIPTION</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" name="description" class="form-control" id = "descriptionid"/>
                                            </div>                                            
                                            <span class="help-block">Enter Movie Short Description</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">MOVIE CATEGORY</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <select name = "category" style = "/*margin-right:20px; */border-radius: 4px; height:30px; width:160px;">
                                                <?php 
                                                    while($category = mysqli_fetch_assoc($categories))
                                                        echo '<option value = "'.$category['movie_category_id'].'">'.$category['movie_category_name'].'</option>';
                                                ?>
                                                </select>
                                            </div>                                            
                                            <span class="help-block">Choose Movie Category</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">MOVIE EXTENSION</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <div class="input-group">
                                                <select name = "extension" style = "/*margin-right:20px; */border-radius: 4px; height:30px; width:160px;">
                                                    <option value = "MKV">MKV</option>
                                                    <option value = "AVI">AVI</option>
                                                    <option value = "MP4">MP4</option>
                                                </select>
                                            </div>           
                                            </div>                                            
                                            <span class="help-block">Enter Movie Extension</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                    <label class="col-md-3 col-xs-12 control-label">STREAM TRANSCODING</label>
                                    <div class="col-md-6 col-xs-12">
                                      <div class="input-group">
                                        <select name = "transcoding" style = "margin-right:20px; border-radius: 4px; height:30px; width:160px;">
                                            <option value="">No Transcoding</option>;
                                            <?php while($transcode = mysqli_fetch_assoc($transcodes)){
                                                echo '<option value="'.$transcode['transcoding_id'].'">'.$transcode['transcoding_name'].'</option>';
                                            }?> 
                                              
                                        </select>
                                      </div>
                                      <span class="help-block">Choose Transcoding</span> </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">MOVIE POSTER</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <img src = "assets/images/default-movie.jpg" id = "srcid"/>
                                                <input type = "text" name = "poster" id = "posterid" hidden/>
                                            </div>                                            
                                            
                                        </div>
                                    </div>

                                </div>
                                <div class="panel-footer">
                                    <input type="reset" value="Reset" class="btn btn-default">                                   
                                    <input type="submit" name="save" value="Save" class="btn btn-primary pull-right" onclick="return confirm('Are you sure want to save movie?');">
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
        <script type="text/javascript" src="js/jstree/dist/jstree.min.js"></script>
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
                url: "./movie.php",
                data: { title: title,
                lang: lang },
                dataType: "json",
                success: function(result) {
                    if(result[0] == "true")
                    {
                        console.log("success");
                        $("#genreid")[0].value = result[1];
                        $("#directorid")[0].value = result[2];
                        $("#castid")[0].value = result[3];
                        $("#releaseid")[0].value = result[4];
                        $("#durationid")[0].value = result[5];
                        $("#descriptionid")[0].value = result[6];
                        $("#srcid")[0].src = result[7];
                        $("#posterid")[0].value = result[7];
                    }
                    else
                        console.log("failed");
                }
                
            });
    }

    function changeSource()
    {
        
        $method = $("#urltype")[0].value;
        console.log($method);
        if($method == 1)
        {
            $("#source1").removeClass('displayHide');
            $("#source1").addClass('displayShow');
            $("#source2").removeClass('displayShow');
            $("#source2").addClass('displayHide');
        }
        else
        {
            $("#source2").removeClass('displayHide');
            $("#source2").addClass('displayShow');
            $("#source1").removeClass('displayShow');
            $("#source1").addClass('displayHide');
        }
    }

    function addMovie()
    {
        if(window.url != $("#url")[0].value)
        {
            window.url = $("#url")[0].value;
            var server = $('#serverid').val();
            //$('#jstree').empty();
            //$("#jstree").jstree('create_node', null, {"text":"FileList","slug":"hhhhhhhhh","id":"header"}, 'last'); 
            
            //$("#tree").jstree("remove",data.rslt.obj.find('#header'));
            var words = url.split("/");
            var folder = words[words.length - 1];
            $.ajax({
                type: "POST",
                url: "./dir.php",
                data: { path: url,
                    server: server
                 },
                dataType: "json",
                success: function(result) {
                    console.log(result);
                    if(result.length == 0)
                        alert('No connection or No such directory... Please Input correct directory');
                    else
                    {
                        $("#jstree").jstree('create_node', '#header', {"text":url,"slug":"hhhhhhhhh","id":folder}, 'last'); 
                        for(var i = 0; i < result.length; i ++)
                            $("#jstree").jstree('create_node', '#' + folder, {"text":result[i],"slug":"hhhhhhhhh","id":folder + i}, 'last'); 
                    }
                }
                
            });
        }
            
        //$("#jstree").jstree('create_node', null , {"text":"GO somewhere","slug":"hhhhhhhhh","id":1}, 'last'); 
    }

	$(document).ready(function(){

        window.url = '';

		$("#movies_li").addClass("active");
        changeSource();

        $("#jstree").jstree({
            'plugins': ["wholerow", "checkbox", "types"],
     'core': {
          "check_callback": true,
          "themes" : {
              "responsive": true
           },
        },
        "types" : {
           "default" : {
               "icon" : "fa fa-film m--font-warning"
           },
           "file" : {
              "icon" : "fa fa-film   m--font-warning"
           }
            },  

          });

        $('#jstree').on("changed.jstree", function (e, data) {
            var path = $("#url")[0].value;
            if(path[path.length - 1] != '/')
                path = path + '/';

              var selectedNodes = $('#jstree').jstree("get_selected"); 

               var allText = [];

               // Go through all selected nodes to get text (jquery)
               var tree = $('#jstree').jstree();
               $.each(selectedNodes, function (i, nodeId) {
                   var node = $('#jstree').jstree("get_node", nodeId);
                   if(!tree.is_parent(node))
                    allText.push(path + node.text); // Add text to array
               });
               $("#filepath")[0].value = allText;
               console.log(allText.join());
               //return allText.join(); // This will join all entries with comma
               
            });
	});

    $('input[type="checkbox"].flat-green, input[type="radio"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    });
	</script>
    </body>
</html>
<?php }else{
	
	echo "You are not authorized to visit this page direclty,Sorry";
	} ?>




