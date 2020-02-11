<?php 
include 'head.php';
if(isset($_SESSION['user_info'])){

$error=NULL;
$case=1;
$flag = 0;

$con = new db_connect();

$connection=$con->connect();
if($connection==1){
 $edit = new Select_DB($con->connect);

if(isset($_GET['text']))
{
  $alert = base64_decode($_GET['text']);
  $flag = 1;
}

if(isset($_POST['save']))
{
    $ua = isset($_POST['ua']) ? $_POST['ua'] : 'off';
    $flood = isset($_POST['flood']) ? $_POST['flood'] : 'off';
    $captcha = isset($_POST['captcha']) ? $_POST['captcha'] : 'off';
    $bann = isset($_POST['bann']) ? $_POST['bann'] : 'off';
    $episode = isset($_POST['episode']) ? $_POST['episode'] : 'off';
    $category = isset($_POST['category']) ? $_POST['category'] : 'off';

    $apitoken = isset($_POST['apitoken']) ? $_POST['apitoken'] : '';
    $panel_name = isset($_POST['panel_name']) ? $_POST['panel_name'] : 'Venom';
    $cur_pass = isset($_POST['cur_pass']) ? $_POST['cur_pass'] : '';
    $new_pass = isset($_POST['new_pass']) ? $_POST['new_pass'] : '';
    $probesize = isset($_POST['probesize']) ? $_POST['probesize'] : '';
    $analyze = isset($_POST['analyze']) ? $_POST['analyze'] : '';
    $buffersize = isset($_POST['buffersize']) ? $_POST['buffersize'] : '';
    $prebuffer = isset($_POST['prebuffer']) ? $_POST['prebuffer'] : '0';
    $delimiter = isset($_POST['delimiter']) ? $_POST['delimiter'] : '1';
    $balance = isset($_POST['balance']) ? $_POST['balance'] : '0';
    $stb_type = isset($_POST['stb_type']) ? $_POST['stb_type'] : '0';

    $uploadOk = 1;

    if(isset($_FILES["favIconUpload"]) && $_FILES["favIconUpload"]["tmp_name"] != '')
    {
      //var_dump($_FILES["favIconUpload"]);
      // $length = 10;
      // $name = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);

      $target_dir = UPLOAD_DIR;
      $target_file = $target_dir.'favicon.ico';
      $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

      $check = getimagesize($_FILES["favIconUpload"]["tmp_name"]);
      if($check === false)
      {
          $flag = 2;
          $alert = 'UPloaded Favicon is not an image';
          $uploadOk = 0;
      }

      if ($_FILES["favIconUpload"]["size"] > 500000) {
          $flag = 2;
          $alert = "Sorry, your file is too large.";
          $uploadOk = 0;
      }

      if($imageFileType != "ico" && $imageFileType != "bmp" && $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
          echo "Sorry, only ICO, BMP, JPG, JPEG, PNG & GIF files are allowed.";
          $uploadOk = 0;
      }

      if($uploadOk)
      {
        if(!move_uploaded_file($_FILES["favIconUpload"]["tmp_name"], $target_file))
        {
          $flag = 2;
          $alert = "Sorry, there was an error moving your file.";
          $uploadOk = 0;
        }
      }
    }

    $logo_url = '';
    if($uploadOk && isset($_FILES["logoUpload"]) && $_FILES["logoUpload"]["tmp_name"] != '')
    {
      //var_dump($_FILES["logoUpload"]);
      // $length = 10;
      // $name = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);

      $target_dir = UPLOAD_DIR;
      $target_file = $target_dir.basename($_FILES["logoUpload"]["name"]);
      $target_url = basename($_FILES["logoUpload"]["name"]);
      $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

      $check = getimagesize($_FILES["logoUpload"]["tmp_name"]);
      if($check === false)
      {
          $flag = 2;
          $alert = 'UPloaded Logo is not an image';
          $uploadOk = 0;
      }

      if ($_FILES["logoUpload"]["size"] > 500000) {
          $flag = 2;
          $alert = "Sorry, your file is too large.";
          $uploadOk = 0;
      }

      if($imageFileType != "ico" && $imageFileType != "bmp" && $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
          echo "Sorry, only ICO, BMP, JPG, JPEG, PNG & GIF files are allowed.";
          $uploadOk = 0;
      }

      if($uploadOk)
      {
        if(!move_uploaded_file($_FILES["logoUpload"]["tmp_name"], $target_file))
        {
          $flag = 2;
          $alert = "Sorry, there was an error moving your file.";
          $uploadOk = 0;
        }
        else
          $logo_url = $target_url;
      }
    }
    
    if($uploadOk)
    {
      $connection=$edit->save_setting($_POST['id'], $ua, $flood, $captcha, $bann, $episode, $category, $apitoken, $panel_name, $cur_pass, $new_pass, $probesize, $analyze, $buffersize, $prebuffer, $delimiter, $balance, $stb_type, $logo_url);
    
      $text = base64_encode('Save Option Succeded. Please check.');
      echo "<script>location.href='options.php?text=".$text."'</script>";
    }
}

    $setting = $edit->get_setting();
    $iconSize = getimagesize(UPLOAD_DIR.'favicon.ico');
    $logoSize = getimagesize(UPLOAD_DIR.$setting['setting_panel_logo']);
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
                    <li><a href="#">Options</a></li>
                </ul>
                <!-- END BREADCRUMB -->
                
                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2><span class="fa fa-arrow-circle-o-left"></span> Options</h2>
                </div>
                <!-- END PAGE TITLE -->                
                
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                <?php if($flag == 1){?>
                      <div class="alert alert-success" role="alert">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                        <strong>Notice!</strong>
                        <?php echo $alert; ?>
                      </div>
                      <?php }else if ($flag == 2){?>
                        <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                        <strong>Error!</strong>
                        <?php echo $alert; ?>
                      </div>
                      <?php }?>
                
                     <div class="row">
                        <div class="col-md-12">
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">DISALLOW EMPTY UA</label>
                                        <div class="col-md-6 col-xs-12">
                                          <div class="input-group"> 
                                            <label>
                                            <input type="checkbox" name = "ua" class="flat-green" <?php echo $setting['setting_disallowua'] ? "checked" : ""?> >
                                          </label>
                                          </div>
                                          <span class="help-block">Check if you don't want to allow empty ua.</span> </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SHOW CAPTCHA ON LOGIN</label>
                                        <div class="col-md-6 col-xs-12">
                                          <div class="input-group"> 
                                            <label>
                                            <input type="checkbox" name = "captcha" class="flat-green" <?php echo $setting['setting_show_captcha'] ? "checked" : ""?> >
                                          </label>
                                          </div>
                                          <span class="help-block">Check if you want to show captcha on login</span> </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">FLOOD PROTECTION</label>
                                        <div class="col-md-6 col-xs-12">
                                          <div class="input-group"> 
                                            <label>
                                            <input type="checkbox" name = "flood" class="flat-green" <?php echo $setting['setting_flood_protection'] ? "checked" : ""?> >
                                          </label>
                                          </div>
                                          <span class="help-block">Check if you want Flood Protection</span> </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">BANN CONNECTED LINE IP IF IT EXPIRED</label>
                                        <div class="col-md-6 col-xs-12">
                                          <div class="input-group"> 
                                            <label>
                                            <input type="checkbox" name = "bann" class="flat-green" <?php echo $setting['setting_bann_expire_date'] ? "checked" : ""?> >
                                          </label>
                                          </div>
                                          <span class="help-block">Check if you want bann connected line ip when it expires</span> </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SHOW ALL EPISODES ON PLAYLIST</label>
                                        <div class="col-md-6 col-xs-12">
                                          <div class="input-group"> 
                                            <label>
                                            <input type="checkbox" name = "episode" class="flat-green" <?php echo $setting['setting_show_all_episodes'] ? "checked" : ""?> >
                                          </label>
                                          </div>
                                          <span class="help-block">Check if you want to show all episodes on playlist</span> </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">SHOW CATEGORY LABEL ON STREAMS</label>
                                        <div class="col-md-6 col-xs-12">
                                          <div class="input-group"> 
                                            <label>
                                            <input type="checkbox" name = "category" class="flat-green" <?php echo $setting['setting_show_all_category_mag'] ? "checked" : ""?> >
                                          </label>
                                          </div>
                                          <span class="help-block">Check if you want to show category label on streams</span> </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">API TOKEN</label>
                                        <div class="col-md-6 col-xs-12">
                                          <input type="text" name = "apitoken" id = "apibox" class="form-control" style = "border-radius: 4px;" value = "<?php echo $setting['setting_security_token']; ?>">
                                            
                                          <span class="help-block">API Token(Press the green button to generate)</span> </div>
                                          <a class = "btn btn-success" style = "margin-left:15px; border-radius: 4px; color:white" onclick = "genAPI()"> Generate </a>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">PANEL NAME</label>
                                        <div class="col-md-6 col-xs-12">
                                        <div class = "input-group">
                                          <input type="text" name = "panel_name" class="form-control" style = "border-radius: 4px;" value = "<?php echo $setting['setting_panel_name']; ?>">
                                        </div>
                                          <span class="help-block">Panel Name</span> </div>
                                      </div>

                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">FAVICON</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                              
                                                <img src = "<?php echo UPLOAD_URL; ?>favicon.ico" id = "iconImg"/>
                                              
                                                <input type="file" name="favIconUpload" id="iconUpload" hidden>

                                                <!-- //<input type = "text" name = "favicon" id = "posterid" hidden/> -->
                                            </div>
                                            <span class="help-block" id = "iconHelp">Click on the icon to edit! Favicon size <?php echo $iconSize[0];?>X<?php echo $iconSize[1];?>px </span></div>
                                        </div>
                                      

                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">LOGO</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                              
                                                <img src = "<?php echo UPLOAD_URL.$setting['setting_panel_logo']; ?>" id = "logoImg" style = "background-color:grey"/>
                                              
                                                <input type="file" name="logoUpload" id="picUpload" hidden>

                                                <!-- //<input type = "text" name = "favicon" id = "posterid" hidden/> -->
                                            </div>                                            
                                            <span class="help-block" id = "logoHelp">Click on the logo to edit! Logo size <?php echo $logoSize[0];?>X<?php echo $logoSize[1];?>px</span>
                                        </div>
                                      </div>

                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">PANEL ROOT PASSWORD</label>
                                        <div class="col-md-3 col-xs-6">
                                        <div class = "input-group">
                                          <input type="text" name = "cur_pass" class="form-control" style = "border-radius: 4px;" placeholder = "Current Root Password">
                                        </div>
                                        <span class="help-block">Input your Current Admin Password</span> 
                                          </div>
                                          <div class="col-md-3 col-xs-6">
                                        <div class = "input-group">
                                          <input type="text" name = "new_pass" class="form-control" style = "border-radius: 4px;" placeholder = "Your New Root Password">
                                        </div>
                                        <span class="help-block">Input your New Admin Password</span> 
                                          </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">PROBESIZE STREAM</label>
                                        <div class="col-md-6 col-xs-12">
                                        <div class = "input-group">
                                          <input type="text" name = "probesize" class="form-control" style = "border-radius: 4px;" value = "<?php echo $setting['setting_stream_probesize']; ?>">
                                        </div>
                                          <span class="help-block">Input Probesize Stream</span> </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">ANALYZE STREAM</label>
                                        <div class="col-md-6 col-xs-12">
                                        <div class = "input-group">
                                          <input type="text" name = "analyze" class="form-control" style = "border-radius: 4px;" value = "<?php echo $setting['setting_stream_analyze']; ?>">
                                        </div>
                                          <span class="help-block">Input Analyze Stream</span> </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">BUFFERSIZE FOR READING</label>
                                        <div class="col-md-6 col-xs-12">
                                        <div class = "input-group">
                                          <input type="text" name = "buffersize" class="form-control" style = "border-radius: 4px;"value = "<?php echo $setting['setting_buffersize_reading']; ?>">
                                        </div>
                                          <span class="help-block">Input Buffersize</span> </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">PREBUFFER IN SECONDS</label>
                                        <div class="col-md-6 col-xs-12">
                                        <div class = "input-group">
                                          <select name = "prebuffer" style = "margin-right:20px; border-radius: 4px; height:30px; width:160px;">
                                            <option value = "0" <?php echo $setting['setting_prebuffer_sec'] == "0" ? "selected" : ""; ?>> Live </option>
                                            <option value = "10" <?php echo $setting['setting_prebuffer_sec'] == "10" ? "selected" : ""; ?>> 10 </option>
                                            <option value = "20" <?php echo $setting['setting_prebuffer_sec'] == "20" ? "selected" : ""; ?>> 20 </option>
                                            <option value = "30" <?php echo $setting['setting_prebuffer_sec'] == "30" ? "selected" : ""; ?>> 30 </option>
                                            <option value = "40" <?php echo $setting['setting_prebuffer_sec'] == "40" ? "selected" : ""; ?>> 40 </option>
                                            <option value = "50" <?php echo $setting['setting_prebuffer_sec'] == "50" ? "selected" : ""; ?>> 50 </option>
                                            <option value = "60" <?php echo $setting['setting_prebuffer_sec'] == "60" ? "selected" : ""; ?>> 60 </option>
                                        </select>
                                        </div>
                                          <span class="help-block">Select Prebuffer in Seconds</span> </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">STREAM COUNTRY DELIMITER</label>
                                        <div class="col-md-6 col-xs-12">
                                        <div class = "input-group">
                                          <select name = "prebuffer" style = "margin-right:20px; border-radius: 4px; height:30px; width:160px;">
                                            <option value = "1" <?php echo $setting['setting_delimiter'] == "1" ? "selected" : ""; ?>> : </option>
                                            <option value = "2" <?php echo $setting['setting_delimiter'] == "2" ? "selected" : ""; ?>> | </option>
                                            <option value = "3" <?php echo $setting['setting_delimiter'] == "3" ? "selected" : ""; ?>> - </option>
                                            <option value = "4" <?php echo $setting['setting_delimiter'] == "4" ? "selected" : ""; ?>> () </option>
                                            <option value = "5" <?php echo $setting['setting_delimiter'] == "5" ? "selected" : ""; ?>> ()- </option>
                                            <option value = "6" <?php echo $setting['setting_delimiter'] == "6" ? "selected" : ""; ?>> (): </option>
                                            <option value = "7" <?php echo $setting['setting_delimiter'] == "7" ? "selected" : ""; ?>> {} </option>
                                            <option value = "8" <?php echo $setting['setting_delimiter'] == "8" ? "selected" : ""; ?>> {}- </option>
                                            <option value = "9" <?php echo $setting['setting_delimiter'] == "9" ? "selected" : ""; ?>> {}: </option>
                                            <option value = "10" <?php echo $setting['setting_delimiter'] == "10" ? "selected" : ""; ?>> [] </option>
                                            <option value = "11" <?php echo $setting['setting_delimiter'] == "11" ? "selected" : ""; ?>> []- </option>
                                            <option value = "12" <?php echo $setting['setting_delimiter'] == "12" ? "selected" : ""; ?>> []: </option>
                                        </select>
                                        </div>
                                          <span class="help-block">Select Stream Country Delimiter</span> </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">LOAD BALANCE LIMITING</label>
                                        <div class="col-md-6 col-xs-12">
                                        <div class = "input-group">
                                          <select name = "balance" style = "margin-right:20px; border-radius: 4px; height:30px; width:160px;">
                                            <option value = "0" <?php echo $setting['setting_lb_limit'] == "0" ? "selected" : ""; ?>> No Limiting </option>
                                            <option value = "1" <?php echo $setting['setting_lb_limit'] == "1" ? "selected" : ""; ?>> Bandwidth Limiting </option>
                                            <option value = "2" <?php echo $setting['setting_lb_limit'] == "2" ? "selected" : ""; ?>> Client Limiting </option>
                                            <option value = "3" <?php echo $setting['setting_lb_limit'] == "3" ? "selected" : ""; ?>> CPU Limiting </option>
                                        </select>
                                        </div>
                                          <span class="help-block">Select Stream Country Delimiter</span> </div>
                                      </div>
                                      <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">ALLOWED STB TYPES</label>
                                        <div class="col-md-6 col-xs-12">                                            
                                            <div class="input-group">
                                                <input type="text" name="stb_type" class="form-control taginput" placeholder = "add type and press TAB" style = "border-radius: 4px; " value = "<?php echo implode (",", json_decode($setting['setting_stb_types']));?>"/>
                                            </div>                                            
                                            <span class="help-block">Enter STB Types</span>
                                        </div>
                                      </div>
                                  </div>
                                <div class="panel-footer">
                                    <input type="text" name = "id" value = "<?php echo $setting['setting_id']; ?>" hidden>
                                    <input type="reset" value="Reset" class="btn btn-default">                                   
                                    <input type="submit" name="save" value="Save" class="btn btn-primary pull-right" onclick="return confirm('Are you sure want to save settings?');">
                                </div>
                            </div>
                            </form>
                            
                        </div> 
                        </div>                    
                    
                    </div>
                </div>
            </div>      

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
        <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->          
	<script>

    $('#iconImg').click(function(){ $('#iconUpload').trigger('click'); });

    $('#iconUpload').change(function(){
      //console.log('here');
      var input = this;
      var url = $(this).val();
      var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
      if (input.files && input.files[0]&& (ext == "ico" || ext == "bmp" || ext == "jpg" || ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) 
       {
          var reader = new FileReader();

          reader.onload = function (e) {
             $('#iconImg').attr('src', e.target.result).load(function(){
                
                $('#iconHelp')[0].innerHTML = 'Click on the icon to edit! Favicon size ' + this.width + 'X' + this.height + 'px';
                //console.log('Click on the icon to edit! Logo size ' + this.width + 'X' + this.height + 'px');
            });
          }
         reader.readAsDataURL(input.files[0]);
      }
      else
        alert('Please select correct Image');
    });

    $('#logoImg').click(function(){ $('#picUpload').trigger('click'); });

    $('#picUpload').change(function(){
      //console.log('here');
      var input = this;
      var url = $(this).val();
      var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
      if (input.files && input.files[0]&& (ext == "ico" || ext == "bmp" || ext == "jpg" || ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) 
       {
          var reader = new FileReader();

          reader.onload = function (e) {
             $('#logoImg').attr('src', e.target.result).load(function(){
                
                $('#logoHelp')[0].innerHTML = 'Click on the logo to edit! Logo size ' + this.width + 'X' + this.height + 'px';
                //console.log('Click on the logo to edit! Logo size ' + this.width + 'X' + this.height + 'px');
            });
          }
         reader.readAsDataURL(input.files[0]);
      }
      else
        alert('Please select correct Image');
    });

    $(document).ready(function(){
        
        $("#options_li").addClass("active");
    });

    function genAPI() {
       var result           = '';
       var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
       var charactersLength = characters.length;
       for ( var i = 0; i < 30; i++ ) {
          result += characters.charAt(Math.floor(Math.random() * charactersLength));
       }
       $("#apibox")[0].value = result;
    }

    $('input[type="checkbox"].flat-green, input[type="radio"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    });

    $('.taginput').tagsinput({
          confirmKeys: [13, 188]
        });

    $('.taginput').on('keypress', function(e){
        console.log('here');
    if (e.keyCode == 13){
      e.keyCode = 188;
      e.preventDefault();
    };
  });

	</script>
    </body>
</html>
<?php }else{
	
	echo "You are not authorized to visit this page direclty,Sorry";
	} ?>




