<?php
include("connection.php");
 class Select_DB extends db_connect{
	
	public function autenticate($username,$password,$table){
		
		$query="Select user_name,password from ".$table." where user_name='".mysql_real_escape_string($username)."' and password='".mysql_real_escape_string($password)."'";
		$result=mysql_query($query);
		$num_rows=mysql_num_rows($result);
		if($num_rows>0)
		{
			$row=mysql_fetch_array($result);
			
			if($row['user_name'] == $username && $row['password']==$password)
			{
				$_SESSION['user_info']=$row;
				return true;
			}
		}
		return false;
		
	}
		
		public function logout()
		{
			unset($_SESSION['user_info']);
			return true;
			}
			public function insert_channel($channel_name,$channel_description,$channel_logo,$channel_stream,$category_id,$subcategory_id,$tbl_name)
			{
				
				//$to_date = date('Y-m-d', strtotime(str_replace('-', '/', $to_date)));
				//$end_date = date('Y-m-d', strtotime(str_replace('-', '/', $end_date)));
				$query_insert="Insert into ".$tbl_name." Set channel_name='".$channel_name."',
													channel_description='".$channel_description."',
													channel_logo='".$channel_logo."',
													channel_stream='".$channel_stream."',
													category_id='".$category_id."',
													sub_category_id='".$subcategory_id."'
													";
		        $result_inert=mysql_query($query_insert) or die(mysql_error());
				$result_inert;
				if(!$result_inert){
					return false;
					//echo "false";
				}
					else{
						return true;
						}
				}
				public function edit_channel1($channel_name,$channel_name_org,$channel_description,$channel_logo,$channel_stream,$category_id,$subcategory_id,$tbl_name,$id)
			{
				//$to_date = date('Y-m-d', strtotime(str_replace('-', '/', $to_date)));
				//$end_date = date('Y-m-d', strtotime(str_replace('-', '/', $end_date)));
				if($channel_logo!='default'){
				$query_insert="update ".$tbl_name." Set channel_name='".$channel_name."',
													channel_description='".$channel_description."',
													channel_logo='".$channel_logo."',
													channel_stream='".$channel_stream."',
													category_id='".$category_id."',
													sub_category_id='".$subcategory_id."'
													where channel_id='".$id."'";
				
				}
				else{
					$query_insert="Update ".$tbl_name." Set channel_name='".$channel_name."',
													channel_description='".$channel_description."',
													channel_stream='".$channel_stream."',
													category_id='".$category_id."',
													sub_category_id='".$subcategory_id."'
													where channel_id='".$id."'";
					
					}
					$query_update="update tbl_relationship Set channel_name='".$channel_name."'
													where channel_name='".$channel_name_org."'";
				$update=mysql_query($query_update) or die(mysql_error());
		        $result_inert=mysql_query($query_insert) or die(mysql_error());
				
				if(!$result_inert){
					return false;
					//echo "false";
				}
					else{
						return true;
						}
				}
				
				public function insert_device($mac_address,$d_name,$s_date ,$e_date,$tbl_name)
			{
			echo	$query_insert="Insert into ".$tbl_name." Set device_mac_address='".$mac_address."'
				,device_name='".$d_name."'
				,start_date='".$s_date."'
				,end_date='".$e_date."'";
		        $result_inert=mysql_query($query_insert) or die(mysql_error());
				$result_inert;
				if(!$result_inert){
					return false;
					//echo "false";
				}
					else{
						return true;
						}
				}
				
				public function edit_channel($tbl_name,$id)
			{
				$query="select * from ".$tbl_name." where channel_id='".$id."'";
		        $result=mysql_query($query);
			if(!$result){
			return false;
			}
			else{
				return $result;
				}
				}
				
				
			public function get_categories($tbl_name,$parent='')
			{
				$query="select * from ".$tbl_name." WHERE 1=1 ";
				if($parent!=="")
				{
					$query.=" AND parent_category_id='".$parent."' ";
				}
				$query.=" ORDER BY category_name";
		        $result=mysql_query($query);
			if(!$result){
			return false;
			}
			else{
				return $result;
				}
			}	
				
			public function get_category($tbl_name,$id)
			{
				$query="select * from ".$tbl_name." where  category_id='".$id."'";
		        $result=mysql_query($query);
			if(!$result){
			return false;
			}
			else{
				return $result;
				}
			}	
			public function select_category($tbl_name,$id)
			{
				$query="select * from ".$tbl_name." where  category_id='".$id."'";
		        $result=mysql_query($query);
			if(!$result){
			return false;
			}
			else{
				return $result;
				}
				}	
				
				public function insert_relationship($channel_name,$device_name,$tbl_name)
			{
				
				//echo $channel_name;exit;
				//print_r($channel_name);exit;
				//$names = implode(",", $channel_name);
				//echo $names;
				$names=explode(",",$channel_name);
				/*echo '<pre>';
				print_r($names);
				echo '</pre>';*/
				$count_channel=count($names);
				//echo $count_channel;exit;
				$i=0;
				while($i<$count_channel)
				{
				//echo $names[0];exit;
				//echo $i;
				$names[$i]=addslashes($names[$i]);
				$query_select="Select * from ".$tbl_name." where channel_name='".$names[$i]."' and device_mac_address='".$device_name."'";
				$result_select=mysql_query($query_select) or die(mysql_error());
				$rows=mysql_num_rows($result_select);
				if($rows>0)
				{
					$query="Delete from ".$tbl_name." where channel_name='".$names[$i]."' and device_mac_address='".$device_name."'";
					$result=mysql_query($query);
				}
		
				$query_insert="Insert into ".$tbl_name." Set channel_name='".$names[$i]."',device_mac_address='".$device_name."',category_id=".$names[$i+1]."";
		        $result_inert=mysql_query($query_insert) or die(mysql_error());
				$i=$i+2;
				}
				if(!$result_inert){
					return false;
					//echo "false";
				}
					else{
						return true;
						}
				}
				
				public function channel_list($device_name,$tbl_name)
				{
				$query_select="Select * from ".$tbl_name." where device_mac_address='".$device_name."'";
				$result_select=mysql_query($query_select) or die(mysql_error());
				if(!$result_select){
					return false;
					//echo "false";
				}
					else{
						return $result_select;
						}
					}

	public function select_channel($table){
		
		$query="Select * from ".$table."";
		$result=mysql_query($query);
		if(!$result){
			return false;
			}
			else{
				return $result;
				}
		
	}
		
	public function select_channel_by_id($table , $id){
		
		$query="Select * from ".$table." where  category_id='".$id."' ";
		$result=mysql_query($query);
		if(!$result){
			return false;
			}
			else{
				return $result;
				}
		
	}
		
		public function delete($table,$id,$column_name){
		
		$query="Delete from ".$table." where ".$column_name."=".$id."";
		$result=mysql_query($query)or die(mysql_error());
		if(!$result){
			return false;
			}
			else{
				return $result;
				}
		
		}
		function insert_category($parent_category_id=0,$category_name,$category_logo,$tbl_name)
		{
			$query="Insert into ".$tbl_name." set parent_category_id='".$parent_category_id."',
													category_name='".$category_name."',
													category_logo='".$category_logo."'";
			$result=mysql_query($query)or die(mysql_error());
		if(!$result){
			return false;
			}
			else{
				return $result;
				}
			
			}
		function update_category($parent_category_id=0,$category_name,$category_logo,$tbl_name,$id)
		{
			
			if($category_logo!='default')
			{
			$query="update ".$tbl_name." set parent_category_id='".$parent_category_id."',
													category_name='".$category_name."',
													category_logo='".$category_logo."'
													where  category_id=".$id."";
			}
			else{
				$query="update ".$tbl_name." set parent_category_id='".$parent_category_id."',
													category_name='".$category_name."'
													where  category_id=".$id."";
				
				}
			$result=mysql_query($query)or die(mysql_error());
		if(!$result){
			return false;
			}
			else{
				return $result;
				}
			
			}
		public function delete_channel_list($table,$channel_name,$mac_address){
		
		$query="Delete from ".$table." where channel_name='".$channel_name."' and device_mac_address='".$mac_address."'";
		$result=mysql_query($query)or die(mysql_error());
		if(!$result){
			return false;
			}
			else{
				return $result;
				}
		
		}
		
		
		public function insert_content($content,$page,$tbl_name)
		{
			$query="Insert into ".$tbl_name." set content='".$category_name."',
													page='".$page."'";
			$result=mysql_query($query)or die(mysql_error());
		if(!$result){
			return false;
			}
			else{
				return $result;
				}
			
			}
		
	
	public function edit_content($tbl_name,$id)
			{
				$query="select * from ".$tbl_name." where  content_id=".$id."";
		        $result=mysql_query($query);
			if(!$result){
			return false;
			}
			else{
				return $result;
				}
			}

public function update_content($content,$page,$tbl_name,$id)
		{
		
			$query="update ".$tbl_name." set content='".$content."',
													page='".$page."'
													where  content_id=".$id."";
		$result=mysql_query($query)or die(mysql_error());
		if(!$result){
			return false;
			}
			else{
				return $result;
				}
			
			}
			
			public function update_device($mac_address,$d_name, $s_date, $e_date, $tbl_name,$device_name,$tbl_name2,$id)
		{
		
			$query="update ".$tbl_name." set device_mac_address='".$mac_address."', device_name='".$d_name."', start_date='".$s_date."', end_date='".$e_date."' where  device_id=".$id."";
		$result=mysql_query($query)or die(mysql_error());
		//$query2="update ".$tbl_name2." Set device_mac_address='".$mac_address."' where  device_mac_address=".$device_name."";
		//$result2=mysql_query($query2)or die(mysql_error());
		if(!$result){
			return false;
			}
			else{
				return $result;
				}
			
			}
public function edit_device($tbl_name,$id)
			{
				$query="select * from ".$tbl_name." where  device_id='".$id."'";
		        $result=mysql_query($query);
			if(!$result){
			return false;
			}
			else{
				return $result;
				}
				}
				
				
				public function delete_device($tbl_name,$tbl_name2,$device_name,$id){
		
		$query="Delete from ".$tbl_name." where device_id=".$id."";
		$result=mysql_query($query)or die(mysql_error());
		$query="Delete from ".$tbl_name2." where device_mac_address='".$device_name."'";
		$result=mysql_query($query)or die(mysql_error());
		if(!$result){
			return false;
			}
			else{
				return $result;
				}
				}
				
				public function delete_channel($tbl_name,$tbl_name2,$channel_name,$id){
		
		$query="Delete from ".$tbl_name." where channel_id=".$id."";
		$result=mysql_query($query)or die(mysql_error());
		$query="Delete from ".$tbl_name2." where channel_name='".$channel_name."'";
		$result=mysql_query($query)or die(mysql_error());
		if(!$result){
			return false;
			}
			else{
				return $result;
				}
		
		}
		
		public function update_user($password,$tbl_name,$user_name)
		{
		
			$query="update ".$tbl_name." set password='".$password."' where  user_name='".$user_name."'";
			$result=mysql_query($query)or die(mysql_error());
			
			if(!$result){
				return false;
			}
			else{
				return $result;
			}
			
		}
		
		public function count_tbls($tblname)
		{
			
			$query = "SELECT count(*) as total from ".$tblname;
			$result=mysql_query($query) or die(mysql_error());
			$data=mysql_fetch_assoc($result);
			return $data['total'];
		}
}	
?>