<?php 
include 'head.php';
if(isset($_SESSION['user_info'])){
	//print_r( $_SESSION['user_info'] );exit;

$con = new db_connect();
$connection=$con->connect();

$get = new Select_DB($con->connect);

if($connection==1){
 $servers = $get->get_servers();
 // $alert_servers = $get->get_servers();
}
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
    <!-- SEARCH -->
    
    </li>
    <!-- END SEARCH --> 
    <!-- SIGN OUT -->
    <li class="xn-icon-button pull-right"> <a href="<?php echo SITE_URL; ?>logout.php" class="mb-control" data-box="#mb-signout" style="width:100px"><span class="fa fa-sign-out"></span> Logout</a> </li>
    <!-- END SIGN OUT -->
    
  </ul>
  <!-- END X-NAVIGATION VERTICAL --> 
  
  <!-- START BREADCRUMB -->
  <ul class="breadcrumb">
    <li><a href="#">Home</a></li>
  </ul>
  <!-- END BREADCRUMB --> 
  
  <!-- PAGE CONTENT WRAPPER -->
  <div class="page-content-wrap"> 
    
    <!-- START WIDGETS -->
    <div class="row">
      <div class="col-md-2"> 
        
        <!-- START WIDGET Channel Numers--> 
        <!--<div class="widget widget-default widget-item-icon" onclick="location.href='../channels.php';">-->
        <div class="widget widget-success widget-item-icon" style="cursor:pointer;">
          <div class="widget-item-left"> <span class="glyphicon glyphicon-volume-up"></span> </div>
          <div class="widget-data">
            <div class="widget-int num-count" id = "online_streams"><?php echo intval($get->get_online_stream_count()); ?></div>
            <div class="widget-title">Online Streams</div>
          </div>
          <div class="widget-controls"> <a href="#" class="widget-control-right widget-remove" data-toggle="tooltip" data-placement="top" title="Remove Widget"><span class="fa fa-times"></span></a> </div>
        </div>
        <!-- END WIDGET Channel Numers --> 
        
      </div>
      <div class="col-md-2"> 
        
        <!-- START WIDGET CLOCK -->
        <div class="widget widget-danger widget-item-icon" style="cursor:pointer;">
          <div class="widget-item-left"> <span class="glyphicon glyphicon-volume-off"></span> </div>
          <div class="widget-data">
            <div class="widget-int num-count" id = "offline_streams"><?php echo intval($get->get_offline_stream_count()); ?></div>
            <div class="widget-title">Offline Streams</div>
          </div>
          <div class="widget-controls"> <a href="#" class="widget-control-right widget-remove" data-toggle="tooltip" data-placement="top" title="Remove Widget"><span class="fa fa-times"></span></a> </div>
        </div>
        <!-- END WIDGET CLOCK --> 
        
      </div>
      <div class="col-md-2"> 
        
        <!-- START WIDGET USERS -->
        <div class="widget widget-default widget-item-icon" style="cursor:pointer;">
          <div class="widget-item-left"> <span class="glyphicon glyphicon-save"></span> </div>
          <div class="widget-data">
            <div class="widget-int num-count" id = "down_speed"><?php echo intval($get->get_servers_down_avg()); ?></div>
            <div class="widget-title">Down (mbit/s)</div>
          </div>
          <div class="widget-controls"> <a href="#" class="widget-control-right widget-remove" data-toggle="tooltip" data-placement="top" title="Remove Widget"><span class="fa fa-times"></span></a> </div>
        </div>
        <!-- END WIDGET USERS --> 
        
      </div>
      <div class="col-md-2"> 
        
        <!-- START WIDGET SLIDER -->
        <div class="widget widget-primary widget-item-icon" style="cursor:pointer;">
          <div class="widget-item-left"> <span class="glyphicon glyphicon-open"></span> </div>
          <div class="widget-data">
            <div class="widget-int num-count" id = "up_speed"><?php echo intval($get->get_servers_up_avg()); ?></div>
            <div class="widget-title">Up (mbit/s)</div>
          </div>
          <div class="widget-controls"> <a href="#" class="widget-control-right widget-remove" data-toggle="tooltip" data-placement="top" title="Remove Widget"><span class="fa fa-times"></span></a> </div>
        </div>
        <!-- END WIDGET SLIDER --> 
        
      </div>
      <div class="col-md-2"> 
        
        <!-- START WIDGET SLIDER -->
        <div class="widget widget-info widget-item-icon" style="cursor:pointer;">
          <div class="widget-item-left"> <span class="glyphicon glyphicon-flash"></span> </div>
          <div class="widget-data">
            <div class="widget-int num-count" id = "connections"><?php echo $get->get_connection_count(); ?></div>
            <div class="widget-title">Connections</div>
          </div>
          <div class="widget-controls"> <a href="#" class="widget-control-right widget-remove" data-toggle="tooltip" data-placement="top" title="Remove Widget"><span class="fa fa-times"></span></a> </div>
        </div>
        <!-- END WIDGET SLIDER --> 
        
      </div>
      <div class="col-md-2"> 
        
        <!-- START WIDGET SLIDER -->
        <div class="widget widget-warning widget-item-icon" style="cursor:pointer;">
          <div class="widget-item-left"> <span class="glyphicon glyphicon-hdd"></span> </div>
          <div class="widget-data">
            <div class="widget-int num-count" id = "server"><?php echo $get->get_server_count(); ?></div>
            <div class="widget-title">Server</div>
          </div>
          <div class="widget-controls"> <a href="#" class="widget-control-right widget-remove" data-toggle="tooltip" data-placement="top" title="Remove Widget"><span class="fa fa-times"></span></a> </div>
        </div>
        <!-- END WIDGET SLIDER --> 
        
      </div>
    </div>
    <!-- END WIDGETS -->
    
    <div class="row">
      <div class="col-md-12"> 
        
        <!-- START CHANNELS TABLE -->
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Dashboard</h3>
          </div>
          <div class="panel-body">
            <div class="row">
                <?php 
                $index = 0;
                while($server = mysqli_fetch_assoc($servers)){
                ?>
                <div class="card col-md-4" style = "margin-bottom : 60px; height: 220px;">
                  <div class="card-header p-0 bg-<?php echo ($server['server_main'] == 1 ? "success" : "server");?>">
                    <h3 class = "card-title p-3" style = "color:white;"> <?php echo $server['server_name']; ?> </h3>
                    <!-- <div class="ribbon-wrapper ribbon-lg">
                      <div class="ribbon bg-<?php echo ($server['server_main'] == 1 ? "success" : "info");?> text-lg">
                        <?php echo $server['server_name']; ?>
                      </div>
                    </div> -->
                  </div>
                  <div class = "card-body bg-dash-back">
                    <div class = "row">
                      <div class = "col-sm-6">
                        <div id="external-events">
                            <div class="external-event bg-dash-status1">Online <div id = "online_<?php echo $index; ?>" class = "pull-right"> <?php echo $get->server_activity_count($server['server_id']); ?></div> </div>
                            <div class="external-event bg-dash-status2">Streams <div id = "streams_<?php echo $index; ?>" class = "pull-right"> <?php echo $get->server_online_stream_count($server['server_id']); ?></div> </div>
                            <div class="external-event bg-dash-status1">Total <div id = "total_<?php echo $index; ?>" class = "pull-right"> <?php echo intval($server['server_down_speed']) + intval($server['server_down_speed']); ?> </div> </div>
                            <div class="external-event bg-dash-status2">Incoming <div id = "up_<?php echo $index; ?>" class = "pull-right"> <?php echo $server['server_up_speed']; ?> </div> </div>
                            <div class="external-event bg-dash-status1">Outgoing <div id = "down_<?php echo $index; ?>" class = "pull-right"><?php echo $server['server_down_speed']; ?> </div> </div>
                            <div class="external-event bg-dash-status2">Uptime <div id = "uptime_<?php echo $index; ?>"class = "pull-right"> <?php echo $server['server_uptime']; ?> </div> </div>
                        </div>
                      </div>
                      <!-- <div class = "col-sm-3 text-center" style = "margin-top : 40px;">
                          <input type="text" class="knob"  data-min = "0" data-max = "100" data-width="90" data-height="90" data-fgColor="#28a745" data-readOnly = "true" data-bgColor = "#b1b1b1" value="<?php echo $server['server_cpu_usage']; ?>">
                          <div style = "font-size: 18px; ">CPU</div>
                      </div> -->
                      <!-- <div class = "col-sm-3 text-center" style = "margin-top : 40px;">
                          <input type="text" class="knob" data-min = "0" data-max = "100" data-width="90" data-height="90" data-fgColor="#ffc107" data-readOnly = "true" data-bgColor = "#b1b1b1" value="<?php echo $server['server_ram_usage']; ?>">
                          <div style = "font-size: 18px; ">RAM</div>
                      </div> -->
                      <div class = "col-sm-6">
                        <div class = "row" style = "margin-top:10%; margin-left:3px;">
                          <img src = "assets/images/xx.gif" id = "flag_<?php echo $index; ?>" alt = "FLAG" style = "width:36px; height:20px;">
                          <input type = "text" style = "background: none; border: none;margin-left:10px;" id = "isp_<?php echo $index;?>" value = "ISP Server" disabled/>
                        </div>
                        <div class = "row" style = "margin-top:10%">
                          <div class = "col-sm-7">
                            <div class="progress">
                            <div class="progress-bar bg-primary progress-bar-striped" role="progressbar"
                                 aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $server['server_ram_usage']; ?>%;"
                                 id = "ram_progress_<?php echo $index; ?>">
                            </div>
                            </div>
                          </div>
                          <div class = "col-sm-5" id = "ram_<?php echo $index; ?>">
                            RAM  <?php echo $server['server_ram_usage']; ?>%
                          </div>
                        </div>
                        <div class = "row">
                          <div class = "col-sm-7">
                            <div class="progress">
                            <div class="progress-bar bg-success progress-bar-striped" role="progressbar"
                                 aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $server['server_cpu_usage']; ?>%;"
                                 id = "cpu_progress_<?php echo $index; ?>">
                            </div>
                            </div>
                          </div>
                          <div class = "col-sm-5" id = "cpu_<?php echo $index; ?>">
                            CPU  <?php echo $server['server_cpu_usage']; ?>%
                          </div>
                        </div>
                        <div class = "row">
                          <div class = "col-sm-7">
                            <div class="progress">
                            <div class="progress-bar bg-warning progress-bar-striped" role="progressbar"
                                 aria-valuemin="0" aria-valuemax="100" style="width: <?php 
                                 if($server['server_bandwidth_limit'] == '')
                                  echo '0';
                                 else
                                 {
                                  $speed = intval($server['server_down_speed']) + intval($server['server_down_speed']);
                                  $network = (floatval($speed) / intval($server['server_bandwidth_limit'])) * 100; 
                                  echo $network;
                                 }
                                 ?>%;" id = "net_progress_<?php echo $index; ?>">
                            </div>
                            </div>
                          </div>
                          <div class = "col-sm-5" id = "network_<?php echo $index; ?>">
                            Network  <?php echo intval($network); ?>%
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php 
                $index ++;
                }
                ?>
          </div>
        </div>
        <!-- END CHANNELS TABLE--> 
      </div>
<!--       <div class = "toast-container" class = "toast-top-right">
  <?php $index = 0;
    while($server = mysqli_fetch_assoc($alert_servers))
    {?>
      <div class="toast toast-error" aria-live="assertive" style="" id = "alert_<?php echo $index;?>">
        <button type="button" class="toast-close-button" role="button">×</button>
        <div class="toast-message"> <?php echo $server['server_name'];?> offline</div>
      </div>
    <?php 
    $index ++;
  }
  ?>
</div> -->
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

<!-- START THIS PAGE PLUGINS--> 
<script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script> 
<script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script> 
<script type="text/javascript" src="js/plugins/scrolltotop/scrolltopcontrol.js"></script> 
<script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script> 
<script type="text/javascript" src="js/plugins/morris/raphael-min.js"></script> 
<script type="text/javascript" src="js/plugins/morris/morris.min.js"></script> 
<script type="text/javascript" src="js/plugins/rickshaw/d3.v3.js"></script> 
<script type="text/javascript" src="js/plugins/rickshaw/rickshaw.min.js"></script> 
<script type='text/javascript' src='js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'></script> 
<script type='text/javascript' src='js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js'></script> 
<script type='text/javascript' src='js/plugins/bootstrap/bootstrap-datepicker.js'></script> 
<script type="text/javascript" src="js/plugins/owl/owl.carousel.min.js"></script> 
<script type="text/javascript" src="js/plugins/moment.min.js"></script> 
<script type="text/javascript" src="js/plugins/daterangepicker/daterangepicker.js"></script> 
<!-- END THIS PAGE PLUGINS--> 

<!-- START TEMPLATE --> 

<script type="text/javascript" src="js/plugins.js"></script> 
<script type="text/javascript" src="js/actions.js"></script> 
<script type="text/javascript" src="js/demo_dashboard.js"></script> 
<script type="text/javascript" src="js/toastr.min.js"></script>



<script>

var list = new Array;
var limit = new Array;
var flag = 0;
for(var i = 0; i < 100; i ++)
{
  list.push(0);
  limit.push(2);
}  

function setISP()
{
  $.ajax({
        type: "POST",
        url: "./isp.php",
        data: { isp: 'isp'},
        dataType: "json",
        success: function(result) {

          if(flag == 0)
          {
            flag = 1;
            toastr.options = {
                "closeButton": true,
                "timeOut": "0",
                "extendedTimeOut": "0"
            };
            for (var i = 0; i < result['sname'].length; i ++)
            {
              var t = toastr.error(result['sname'][i] + ' is offline!');
              console.log(result['sname'][i]);
              t.attr('id', 'alert_' + i);
              t.hide();
            }
          }

          //if(result['check'] == 1)
          
            console.log('status');
            //console.log(list);
            console.log(result);
            for(var i = 0; i < result['country'].length; i ++)
            {
                if(result['country'][i] != '' && result['name'][i] != '' && result['flag'][i] != '')
                {
                  
                    limit[i] += 10;
                  
                  $("#isp_" + i)[0].value = result['name'][i];
                  $("#flag_" + i)[0].src = result['flag'][i];
                  
                }
                else
                  list[i] ++;

                if(result['check'] == 1)
                {
                  if(list[i] >= limit[i])
                  $("#alert_" + i).show();
                  else
                  {

                    $("#alert_" + i).hide();
                  }  
                }
                
            }
          
        }
    });

}

function setServerData()
{
  console.log('setServerData');
  $.ajax({
        type: "POST",
        url: "./serverStatus.php",
        dataType: "json",
        success: function(result) {
            $("#online_streams")[0].innerHTML = result['online_stream'];
            $("#offline_streams")[0].innerHTML = result['offline_stream'];
            $("#down_speed")[0].innerHTML = result['down_avg'];
            $("#up_speed")[0].innerHTML = result['up_avg'];
            $("#connections")[0].innerHTML = result['connection'];
            $("#server")[0].innerHTML = result['server_count'];

            for(var i = 0; i < result['online'].length; i ++)
            {
              $("#online_" + i)[0].innerHTML = result['online'][i];
              $("#streams_" + i)[0].innerHTML = result['streams'][i];
              $("#total_" + i)[0].innerHTML = result['total'][i];
              $("#up_" + i)[0].innerHTML = result['incoming'][i];
              $("#down_" + i)[0].innerHTML = result['outgoing'][i];
              $("#uptime_" + i)[0].innerHTML = result['uptime'][i];

              $("#ram_" + i)[0].innerHTML = 'RAM ' + result['ram'][i] + '%';
              $("#ram_progress_" + i)[0].style = "width : " + result['ram'][i] + "%";
              $("#cpu_" + i)[0].innerHTML = 'CPU ' + result['cpu'][i] + '%';
              $("#cpu_progress_" + i)[0].style = "width : " + result['cpu'][i] + "%";
              $("#network_" + i)[0].innerHTML = 'Network ' + result['network'][i] + "%";
              $("#net_progress_" + i)[0].style = "width : " + result['network'][i] + "%";
            }
        }
        
    });
}

window.setInterval(function(){
  console.log('cron runned');
  setServerData();
  setISP();
}, 10000);

setISP();

</script> 
<!-- END TEMPLATE --> 
<!-- END SCRIPTS -->
</body></html><?php }else{
	
	echo "You are not authorized to visit this page direclty,Sorry";
	} ?>
