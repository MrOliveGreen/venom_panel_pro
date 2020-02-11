<?php 
include 'head.php';
$con = new db_connect();
$connection=$con->connect();

$get = new Select_DB($con->connect);

$alert = '';
$flag = 0;

if(isset($_SESSION['user_info'])){
	//print_r( $_SESSION['user_info'] );exit;

if(isset($_POST['delete_id']))
{
  $id = base64_decode($_POST['delete_id']);
  $result = $get->delete_line($id);
  if($result)
  {
    $flag = 1;
    $alert = 'Line Deleted Successfully!';
  }
  else
  {
    $flag = 2;
    $alert = 'Database Operation Failed!';
  }
}

if(isset($_POST['bann_id']))
{
  $id = base64_decode($_POST['bann_id']);
  $result = $get->bann_line($id);
  if($result)
  {
    $flag = 1;
    $alert = 'Line Banned Successfully!';
  }
  else
  {
    $flag = 2;
    $alert = 'Database Operation Failed!';
  }
}

if(isset($_GET['text']))
{
  $alert = base64_decode($_GET['text']);
  $flag = 1;
}

if($connection==1){
 $line_obj = $get->get_lines_obj($_SESSION['user_info']['user_id']);
}
?>

<!-- PAGE CONTENT -->
<?php
  if($_SESSION['user_info']['user_is_admin'] == 0 && !empty($_SESSION)){
  ?>
<div class="page-content"> 
  
  <!-- START X-NAVIGATION VERTICAL -->
  <ul class="x-navigation x-navigation-horizontal x-navigation-panel">
    <!-- TOGGLE NAVIGATION -->
    <li class="xn-icon-button"> <a href="#" class="x-navigation-minimize"><span class="fa fa-dedent"></span></a> </li>
    <!-- END TOGGLE NAVIGATION --> 
    <!-- SEARCH -->
    
    
    <!-- END SEARCH --> 
    <!-- SIGN OUT -->
    <li class="xn-icon-button" style = "position:relative; float:right;">
     <a href="<?php echo SITE_URL; ?>logout.php" class="mb-control" data-box="#mb-signout" style="margin-left:-50px; width:100px"><span class="fa fa-sign-out"></span> Logout</a> </li>
    <li class="xn-icon-button" style = "position:relative; float:right;"> <h5 class = "top-reseller"> <?php echo $_SESSION['user_info']['user_name'].'('.$_SESSION['user_info']['user_credit'].' credit/s)'; ?></h5></li>
  </ul>
  <!-- END X-NAVIGATION VERTICAL --> 
  
  <!-- START BREADCRUMB -->
  <ul class="breadcrumb">
    <li><a href="#">Line Management</a></li>
    <li><a href="#">Manage Lines</a></li>
  </ul>
  <!-- END BREADCRUMB --> 
  
  <!-- PAGE CONTENT WRAPPER -->
  <div class="page-content-wrap"> 
    <div class="row">
    <?php if($flag==1){?>
          <div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
            <strong>Notice!</strong>
            <?php echo $alert; ?>
          </div>
          <?php }
          else if($flag == 2){?>
            <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
            <strong>Error!</strong>
            <?php echo $alert; ?>
          </div>
          <?php } ?>
    </div>
    <div class="row">
      <div class="col-md-12"> 
        
        <!-- START CHANNELS TABLE -->
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Manage Lines</h3>
          </div>
          <div class="panel-body">
            <table class="display" id = "table" style = "width:100%">
              <thead>
                <tr>
                  <th>LINE NAME</th>
                  <th>LINE PASS</th>
                  <th>LINE USER</th>
                  <th>FINGERPRINT</th>
                  <th>LINE STATUS</th>
                  <th>EXPIRE DATE</th>
                  <th>CONNECTIONS</th>
                  <th>DOWNLOAD</th>
                  <th style = "text-align: right;">ACTION</th>
                </tr>
              </thead>
              <tbody>
                <?php 
				        while($line = mysqli_fetch_assoc($line_obj)){
				        ?>
                <tr>
                  <td style="cursor:pointer;"><?php echo $line['line_user']; ?></td>
                  <td style="cursor:pointer;"><?php echo $line['line_pass']; ?></td>
                  <td style="cursor:pointer;"><?php echo $_SESSION['user_info']['user_name']; ?></td>
                  <td style="cursor:pointer;"><?php 
                  if($line['line_fingerprint'] == '')
                    echo 'Not Available';
                  else
                    echo $line['line_fingerprint'];
                   ?></td>
                   <td style="cursor:pointer;"><?php 
                   if($line['line_status'] == 0)
                    echo '<span class = "badge bg-light"> Offline </span>';
                  else if($line['line_status'] == 1)
                    echo '<span class = "badge bg-success"> Online </span>';
                  else if($line['line_status'] == 2)
                    echo '<span class = "badge bg-dark"> Expired </span>';
                  else if($line['line_status'] == 3)
                    echo '<span class = "badge bg-danger"> Banned </span>';
                  else if($line['line_status'] == 4)
                    echo '<span class = "badge bg-warning"> Kicked </span>';
                   ?></td>
                  <td style="cursor:pointer;"><?php echo date('d.m.Y h:i', $line['line_expire_date']);?></td>
                  <td style="cursor:pointer;"><?php
                    $count = ($line['line_status'] == 1 ? 1 : 0);
                   echo '<span class = "badge bg-light">'.$count.'/'.$line['line_connection'].'</span>';?></td>
                  <td style="cursor:pointer;">
                    <?php echo '<span class = "badge bg-dark" data-box="#mb-url" onclick = setURLData("'.$line['line_user'].'","'.$line['line_pass'].'"'.')> Download </span>';?>
                    </td>
                  <td><a href="#">
                    <form class = "go-right" method = "post" enctype="multipart/form-data">
                      <input type = "text" name="delete_id" value="<?php echo base64_encode($line['line_id']);?>" hidden>
                      <button type = "submit" class="btn btn-danger btn-rounded btn-sm" onclick="return confirm('Are you sure want to delete?');"><span class="fa fa-times"></span></button>
                    </form>
                    </a> <a href="#">
                    <button class="go-right btn btn-default btn-rounded btn-sm"><a href = "edit_line.php?line_id=<?php echo base64_encode($line['line_id']);?>"><span class="fa fa-pencil"></span></button>
                    </a>
                    <form class = "go-right" method = "post" enctype="multipart/form-data">
                      <input type = "text" name="bann_id" value="<?php echo base64_encode($line['line_id']);?>" hidden>
                      <button class="go-right btn btn-default btn-rounded btn-sm" onclick="return confirm('Are you sure want to bann line?');"><span class="fa fa-ban"></span></button>
                     </form> 
                    
                     </td>

                  <?php }
                   ?>
                </tr>
              </tbody>
            </table>
            <input type = "text" id = "user_id" value = "<?php echo $_SESSION['user_info']['user_id'];?>"hidden>
          </div>

        </div>
        <!-- END CHANNELS TABLE--> 
      </div>
    </div>

  </div>
  <!-- END PAGE CONTENT WRAPPER -->

  <footer>
    <!-- <p>All Copy &copy; Reserved</p> -->
  </footer>
</div>
<?php
  }
  else { ?>
    echo "<script>location.href='index.php'</script>";
<?php }?>
<!-- END PAGE CONTENT -->
</div>
<!-- END PAGE CONTAINER --> 

<!-- MESSAGE BOX-->
<div class="message-box animated fadeIn" data-sound="alert" id="mb-signout">
  <div class="mb-container">
    <div class="mb-middle">
      <div class="mb-title"><span class="fa fa-sign-out"></span> Log <strong>Out</strong> ?</div>
      <div class="mb-content">
        <p>Are you sure you want to log out?</p>
        <p>Press No if you want to continue work. Press Yes to logout current administrator.</p>
      </div>
      <div class="mb-footer">
        <div class="pull-right"> <a href="logout.php" class="btn btn-success btn-lg">Yes</a>
          <button class="btn btn-default btn-lg mb-control-close">No</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="url-box animated fadeIn" data-sound="alert" id="mb-url">
  <div class="mb-container">
    <div class="mb-middle">
      <div class="mb-title"><strong>URL</strong> </div>
      <div class="mb-content">
          <div class = "row">
            <div class = "col-md-8">
              <input class="form-control" type="text" id="url" placeholder="URL Will be generated here..." >
            </div>
            <div class = "col-md-4">
              <span class="input-group-append">
                  <button type="button" class="btn btn-info btn-flat" onclick = "generate()">Generate</button>
              </span>
            </div>
          </div>   
        <div class = "row">
        <div class = "col-md-8">
          <select class = "form-control" id = "urltype" style = "width:100px; margin-top:5px;">
            <optgroup label="M3U Plus">
              <option value="29">M3U Plus - HLS</option>
              <option value="30">M3U Plus - MPEGTS</option>
            </optgroup>
            <optgroup label="M3U STANDARD">
              <option value="31">M3U STANDARD - HLS</option>
              <option value="32">M3U STANDARD - MPEGTS</option>
            </optgroup>
            <optgroup label="ENIGMA 2 OE 1.6">
              <option value="33">ENIGMA 2 OE 1.6 - HLS</option>
              <option value="34">ENIGMA 2 OE 1.6 - MPEGTS</option>
            </optgroup>
            <optgroup label="ENIGMA 2 OE 2.0 AUTOSCRIPT">
              <option value="35">ENIGMA 2 OE 2.0 AUTOSCRIPT</option>
            </optgroup>
            <optgroup label="DREAMBOX">
              <option value="1">DREAMBOX - HLS</option>
              <option value="2">DREAMBOX - MPEGTS</option>
            </optgroup>
            <optgroup label="GIGABLUE">
              <option value="3">GIGABLUE - HLS</option>
              <option value="4">GIGABLUE - MPEGTS</option>
            </optgroup>
            <optgroup label="SIMPLE LIST">
              <option value="5">SIMPLE LIST - HLS</option>
              <option value="6">SIMPLE LIST - MPEGTS</option>
            </optgroup>
            <optgroup label="OCTAGON">
              <option value="7">OCTAGON - HLS</option>
              <option value="8">OCTAGON - MPEGTS</option>
            </optgroup>
            <optgroup label="STARLIVE V3/STARSATHD6060/AZCLASS">
              <option value="9">STARLIVE V3/STARSATHD6060/AZCLASS - HLS</option>
              <option value="10">STARLIVE V3/STARSATHD6060/AZCLASS - MPEGTS</option>
            </optgroup>
            <optgroup label="STARLIVE V5">
              <option value="11">STARLIVE V5 - HLS</option>
              <option value="12">STARLIVE V5 - MPEGTS</option>
            </optgroup>
            <optgroup label="MEDIASTAR/STARLIVE/GEANT/TIGER">
              <option value="13">MEDIASTAR/STARLIVE/GEANT/TIGER - HLS</option>
              <option value="14">MEDIASTAR/STARLIVE/GEANT/TIGER - MPEGTS</option>
            </optgroup>
            <optgroup label="WEB TV LIST">
              <option value="15">WEB TV LIST - HLS</option>
              <option value="16">WEB TV LIST - MPEGTS</option>
            </optgroup>
            <optgroup label="ARIVA">
              <option value="17">ARIVA - HLS</option>
              <option value="18">ARIVA - MPEGTS</option>
            </optgroup>
            <optgroup label="SPARK">
              <option value="19">SPARK V5 - HLS</option>
              <option value="20">SPARK V5 - MPEGTS</option>
            </optgroup>
            <optgroup label="GEANT/STARSAT/TIGER/QMAX/HYPER/ROYAL(OLD)">
              <option value="21">GEANT/STARSAT/TIGER/QMAX/HYPER/ROYAL(OLD) - HLS</option>
              <option value="22">GEANT/STARSAT/TIGER/QMAX/HYPER/ROYAL(OLD) - MPEGTS</option>
            </optgroup>
            <optgroup label="FORTEC999/PRIFIX9400/STARTPORT">
              <option value="23">FORTEC999/PRIFIX9400/STARTPORT - HLS</option>
              <option value="24">FORTEC999/PRIFIX9400/STARTPORT - MPEGTS</option>
            </optgroup>
            <optgroup label="REVOLUTION 60/60 SUNPLUS">
              <option value="25">REVOLUTION 60/60 SUNPLUS - HLS</option>
              <option value="26">REVOLUTION 60/60 SUNPLUS - MPEGTS</option>
            </optgroup>
            <optgroup label="ZORRO">
              <option value="27">ZORRO - HLS</option>
              <option value="28">ZORRO - MPEGTS</option>
            </optgroup>
          </select>
        </div>
        
        <div class = "col-md-4">
         <button class="btn btn-success " style = "margin-top:5px;" onclick = "copyToClipboard()">Copy URL</button>
          <button class="btn btn-default  url-control-close" style = "margin-top:5px;" >Exit</button>
        </div>
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

<!-- <script type="text/javascript" src="js/general/jquery/dist/jquery.min.js"></script>  -->

<script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script> 
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script> 
<!-- END PLUGINS --> 

<!-- START THIS PAGE PLUGINS--> 
<!-- <script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>  -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script> 
<script type="text/javascript" src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script> 
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script> 

<script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script> 
<script type="text/javascript" src="js/plugins.js"></script> 
<script type="text/javascript" src="js/actions.js"></script> 
<!-- <script type="text/javascript" src="js/demo_dashboard.js"></script>  -->

<script>
  var dns = '';

  $(document).ready(function(){
$("#line_li").addClass("active");

  var table = $('#table').DataTable( {
        responsive: true
    } );

  var id = $("#user_id")[0].value;

  $.ajax({
        type: "POST",
        url: "./dns.php",
        data: { user_id: id},
        dataType: "json",
        success: function(result) {
            dns = 'http://' + result;
            console.log(dns);
        }
    });

});

  function setURLData(name, pass)
  {
    window.line_name = name;
    window.line_pass = pass; 

    var box = $("#mb-url");
    $("#url")[0].value = '';
    box.addClass("open");
  }

  function generate()
  {
    var type = $("#urltype")[0].value;
    var url = '';
    var selfDNS ="<?php echo SITE_URL; ?>";
    switch (parseInt(type))
    {
      case 1:
        //url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=dreambox&output=hls";
          url = dns + "/get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=dreambox&output=hls";
          break;
      case 2:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=dreambox&dreambox=mpegts";
        break;
      case 3:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=gigablue&output=hls";
        break;
      case 4:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=gigablue&output=mpegts";
        break;
      case 5:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=simple&output=hls";
        break;
      case 6:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=simple&output=mpegts";
        break;
      case 7:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=octagon&output=hls";
        break;
      case 8:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=octagon&output=mpegts";
        break;
      case 9:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=starlivev3&output=hls";
        break;
      case 10:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=starlivev3&output=mpegts";
        break;
      case 11:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=starlivev5&output=hls";
        break;
      case 12:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=starlivev5&output=mpegts";
        break;
      case 13:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=mediastar&output=hls";
        break;
      case 14:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=mediastar&output=mpegts";
        break;
      case 15:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=webtvlist&output=hls";
        break;
      case 16:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=webtvlist&output=mpegts";
        break;
      case 17:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=ariva&output=hls";
        break;
      case 18:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=ariva&output=mpegts";
        break;
      case 19:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=spark&output=hls";
        break;
      case 20:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=spark&output=mpegts";
        break;
      case 21:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=gst&output=hls";
        break;
      case 22:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=gst&output=mpegts";
        break;
      case 23:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=fps&output=hls";
        break;
      case 24:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=fps&output=mpegts";
        break;
      case 25:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=revosun&output=hls";
        break;
      case 26:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=revosun&output=mpegts";
        break;
      case 27:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=zorro&output=hls";
        break;
      case 28:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=zorro&output=mpegts";
        break;
      case 29:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=m3u_plus&output=hls";
        break;
      case 30:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=m3u_plus&output=mpegts";
        break;
      case 31:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=m3u&output=hls";
        break;
      case 32:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=m3u&output=mpegts";
        break;
      case 33:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=enigma16&output=hls";
        break;
      case 34:
        url = dns + "get.php?username=" + window.line_name + "&password=" + window.line_pass + "&type=enigma16&output=mpegts";
        break;
      case 35:
        url = 'wget -O /etc/enigma2/iptv.sh"' + dns + 'get.php?username='+ window.line_name + "&password=" + window.line_pass + '&type=enigma216_script" && chmod 777 /etc/enigma2/iptv.sh && /etc/enigma2/iptv.sh';
        break;
      default:
        break;
    }
    $("#url")[0].value = url;
  }

  function copyToClipboard()
  {
    var dummy = document.createElement("textarea");
    // to avoid breaking orgain page when copying more words
    // cant copy when adding below this code
    // dummy.style.display = 'none'
    document.body.appendChild(dummy);
    //Be careful if you use texarea. setAttribute('value', value), which works with "input" does not work with "textarea". – Eduard
    dummy.value = $("#url")[0].value;
    dummy.select();
    document.execCommand("copy");
    document.body.removeChild(dummy);
  }

</script>
<!-- END THIS PAGE PLUGINS--> 

<!-- <script type="text/javascript" src="js/general/bootstrap/js/src/util.js"></script>  -->

<!-- <script type="text/javascript" src="js/general/bootstrap/js/src/modal.js"></script>  -->

<!-- START TEMPLATE --> 

<!-- Remember to include jQuery :) -->


<!-- <script type="text/javascript" src="js/demo_dashboard.js"></script>  -->
<!-- <script>
$(document).ready(function(){
  $("#dashboard").addClass("active");
});
</script>  -->
<!-- <script>
  function clicked()
  {
      $('#myModal').modal('show');
  }
</script> -->
<!-- END TEMPLATE --> 
<!-- END SCRIPTS -->
</body></html><?php }else{
	
	echo "You are not authorized to visit this page direclty,Sorry";
	} ?>
