<?php 
include 'head.php';
if(isset($_SESSION['user_info'])){
?>
            <!-- PAGE CONTENT -->
            <div class="page-content">
                <?php
					$con = new db_connect();
					$auth = new Select_DB();
					$connection=$con->connect();
					if($connection==1){
					 $get = new Select_DB();
					 $result=$get->select_channel("tbl_content");
					 //$count=count($rows);
					 //print_r($rows);
					}
				?>
               <!-- START X-NAVIGATION VERTICAL -->
                <ul class="x-navigation x-navigation-horizontal x-navigation-panel">
                    <!-- TOGGLE NAVIGATION -->
                    <li class="xn-icon-button">
                        <a href="#" class="x-navigation-minimize"><span class="fa fa-dedent"></span></a>
                    </li>
                    <!-- END TOGGLE NAVIGATION -->
                    <!-- SIGN OUT -->
                    <li class="xn-icon-button pull-right">
                        <a href="<?php echo SITE_URL; ?>logout.php" class="mb-control" data-box="#mb-signout" style="width:100px"><span class="fa fa-sign-out"></span> Logout</a>
                    </li> 
                    <!-- END SIGN OUT -->
                   
                </ul>
                <!-- END X-NAVIGATION VERTICAL -->                     
                
                <!-- START BREADCRUMB -->
                <ul class="breadcrumb">
                    <li><a href="#">Dashboard</a></li>                    
                    <li><a href="#">Contents</a></li>
                    <li class="active">Content Management</li>
                </ul>
                <!-- END BREADCRUMB -->

                <!-- PAGE TITLE -->
                <div class="page-title">                    
                    <h2><span class="fa fa-arrow-circle-o-left"></span> About US Page Management</h2>
                </div>
                <!-- END PAGE TITLE -->                

                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">                
                
                    <div class="row">
                        <div class="col-md-12">
							<!-- START DEFAULT DATATABLE -->
                            <div class="panel panel-default">
                                <div class="panel-heading">                                
                                    <h3 class="panel-title">Content Management</h3>
                                    <ul class="panel-controls">
                                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
                                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                                        <li><a href="#" class="panel-remove"><span class="fa fa-times"></span></a></li>
                                    </ul>                                
                                </div>
                                <div class="panel-body">
                                    <table class="table datatable">
                                        <thead>
                                            <tr>
												<th>Content</th>
												<th>Page</th>
												<!--<th>Channel Logo</th>
												<th>Channel Stream</th>
												<th>Device Attached</th>-->
												<th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                       <?php 
											while($row=mysql_fetch_array($result)){ 
											?>
											<tr>
											<td>
											<?php
											echo $row['content'];
											/* $phrase = $row['content'];
											echo implode(' ', array_slice(str_word_count($phrase, 2), 0, 5)); */?></td>
											<td><?php echo  $row['page']?></td>
											<!--<td><?php
											//$phrase = $row['channel_description'];
											//echo implode(' ', array_slice(str_word_count($phrase, 2), 0, 5));?></td>
											<td align="center"><img width="92px;" height="75px;" src="channel_images/<?php //echo $row['channel_logo'];  ?>" style="border:solid 1px #CCC; margin-left: 75px; border-radius: 5px 5px 5px 5px;"></td>
											<td><?php //echo $row['channel_stream']; ?></td>
											<td><?php //if($row['device_id']==0){echo "No Device Attached";} else{ echo $row['deviced_attached'];} ?></td>-->
											<td><a href="about_us.php?id=<?php echo $row['content_id'];  ?>"><button class="btn btn-default btn-rounded btn-sm"><span class="fa fa-pencil"></span></button></a></td>
											</tr>
										<?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- END DEFAULT DATATABLE -->
                            </div>
                    </div>                                
                    
                </div>
                <!-- PAGE CONTENT WRAPPER -->                               
                                               
            </div>            
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

        <!-- START THIS PAGE PLUGINS-->        
        <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>        
        <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
        <script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>    
        <!-- END PAGE PLUGINS -->

        <!-- START TEMPLATE -->
        
        <script type="text/javascript" src="js/plugins.js"></script>        
        <script type="text/javascript" src="js/actions.js"></script>        
    <!-- END SCRIPTS -->         
	<script>
	$(document).ready(function(){
		$("ul.content li:first-child").addClass("active");
		$("#content_li").addClass("active");
	});
	</script>
    </body>
</html>
<?php }else{
	
	echo "You are not authorized to visit this page direclty,Sorry";
} ?>




