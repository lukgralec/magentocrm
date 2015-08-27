<?php
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    exit('Please don\'t access this file directly.');
}


class Offshoreevolution_ClassOEPL
{
	var $HtaccessAdminUser 	= '';
	var $HtaccessAdminPass 	= '';
	var $ModuleList 		= array();
	var $ModuleListStr 		= '';
	
	function Offshoreevolution_ClassOEPL() {
		$this->DropSugarTblOnUninstall = false;

		$this->ModuleList = array( 'Contacts'); // 'Accounts', 'Notes'
		// $this->ModuleListName = array(	'Accounts' => 'Accounts', 
										// 'Notes' 		 => 'Notes', 
										// 'off_PortalUser' => 'Portal User',
										// 'Contacts' 		 => 'Conatcts',
										// 'tms_POE'		 => 'POE',
										// 'SUB_Subpoena'	 => 'Subpoena'
										// );
											
		$this->ModuleListStr = "'" . implode( "','", $this->ModuleList) . "'";
		$this->SugarURL  = '';
		$this->SugarUser = ''; 
		$this->SugarPass = ''; 
		$this->HtaccessAdminUser = ''; //OEPL_HTACCESS_ADMIN_USER; 
		$this->HtaccessAdminPass = ''; //OEPL_HTACCESS_ADMIN_PASS; 
		$this->SugarClient = '';
		$this->SugarSessID = '';
		$this->ExcludeFields = array('id', 'date_entered', 'date_modified', 'modified_user_id', 'modified_by_name', 'created_by', 'created_by_name', 'deleted', 'assigned_user_id', 'assigned_user_name', 'team_id', 'team_set_id', 'team_count', 'team_name',  'email_addresses_non_primary');
		$this->ExcludeFieldTypes = array('date', 'datetime');
	}
	
	function __destruct() {
		$this->LogoutToSugar();
	}
	
	function get_size($file, $type) {
		switch($type){  
			case "KB":  
				$filesize = filesize($file) * .0009765625; // bytes to KB  
			break;  
			case "MB":  
				$filesize = (filesize($file) * .0009765625) * .0009765625; // bytes to MB  
			break;  
			case "GB":  
				$filesize = ((filesize($file) * .0009765625) * .0009765625) * .0009765625; // bytes to GB  
			break;  
		}  
		if($filesize <= 0){  
			return $filesize = 0; /*'unknown file size'; */ }  
		else{return round($filesize, 2); /*.' '.$type;*/ }  
	}

	function ErrorLogWrite($text123) {
		$text = "\n\n\n";
		$text .= 'Log: ' . "\n" . $text123 . "\n" ;
		$myFile = OPEL_MODULE_PATH . 'Log.txt';
		if($this->get_size($myFile, 'MB') > 2 ) {
			@rename(OPEL_MODULE_PATH . 'Log.txt', OPEL_MODULE_PATH . 'Log_till_'.date('d-M-Y-H-i-s-u').'.txt');
		} 
		$myFile = OPEL_MODULE_PATH . 'Log.txt';
		$fh = fopen($myFile, 'a+') or die("can't open file");
		fwrite($fh, $text);
		fclose($fh);
		return NULL;
	}
	
	function isFieldAvailable($table, $field)
	{
		global $wpdb;
		$sql = "SHOW COLUMNS FROM " . $table;
		$FieldsRS = $wpdb->get_results($sql, ARRAY_A);
		$fcnt = count($FieldsRS);
		$fieldAr = array();
		for($i=0; $i<count($fcnt); $i++)
		{
			$fieldAr[$i] = $FieldsRS[$i]['Field'];
		}
		if(in_array($field, $fieldAr))
		{
			return true;
		} else {
			return false;
		}	
	}
	
	function getTableName($module){
		$module = strtolower(trim($module));
		if($module == '' ) return '';
		else return OEPL_METAKEY_EXT.'sugar_'.$module;
	}

	function LoginToSugar()
	{
		$login_parameters = array(	 "user_auth"=>array(  "user_name"	=> $this->SugarUser,
														  "password"	=> $this->SugarPass, 
														  "version"		=> "1"
													   ),
									 "application_name"	=>	"RestTest",
			 						 "name_value_list"	=>	array()
								);
		$this->SugarSessData = $this->SugarCall("login", $login_parameters, $this->SugarURL);
		$this->SugerSessID = $this->SugarSessData->id;
		return $this->SugerSessID;
	}
	
	function LogoutToSugar()
	{
		$login_parameters = array(	"user_auth"=>array(	"user_name"	=>	$this->SugarUser,
														"password"	=>	md5($this->SugarPass),
														"version"	=>	"1"
													   ),
									"application_name"	=>	"RestTest",
									"name_value_list"	=>	array(),
		);
		$this->SugarCall("logout", $login_parameters, $this->SugarURL);
	}

	function SugarCall($method, $parameters, $url = '')
	{
		if($url == '')
		{
			$url = $this->SugarURL;
		}
		$curl_request = curl_init();
		curl_setopt($curl_request, CURLOPT_URL, $url);
		/* HTaccess check
		if(IS_LIVE_SERVER)
		{
			curl_setopt($curl_request, CURLOPT_USERPWD, $this->HtaccessAdminUser.":".$this->HtaccessAdminPass);
		}
		 HTaccess check end*/
		curl_setopt($curl_request, CURLOPT_POST, 1);
		curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($curl_request, CURLOPT_HEADER, 1);
		curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);
	
		$jsonEncodedData = json_encode($parameters);
	
		$post = array(
			 "method" => $method,
			 "input_type" => "JSON",
			 "response_type" => "JSON",
			 "rest_data" => $jsonEncodedData
		);
	
		curl_setopt($curl_request, CURLOPT_POSTFIELDS, $post);
		$result = curl_exec($curl_request);
		curl_close($curl_request);
		$result = explode("\r\n\r\n", $result, 2);
		$response = json_decode($result[1]);
		return $response;
	}

	function getModuleRecordInfo($sid, $module)
	{
		$result = (object)array();
		if($this->SugerSessID == '')
		{
			$a = $this->LoginToSugar();
		}
		if($this->SugerSessID)
		{
			$set_entry_parameters = array(	 "session" 		=> $this->SugerSessID,
											 "module_name"	=> $module,
											 "id"  			=> $sid,
											 "select_fields" => array()
										 );
			$result = $this->SugarCall("get_entry", $set_entry_parameters, $this->SugarURL);
		}
		return $result;
	}

	function getRelatedModuleRecords($RecordID, $LinkFieldName, $parentModule, $childModule, $RelatedFields = array(), $RelatedModuleQuery='', $orderBy ='', $limit = 500 )
	{
		global $wpdb;

		$RecordID = trim($RecordID);
		if($RecordID == '' ) return NULL;

		if($this->SugerSessID == '')
		{
			$a = $this->LoginToSugar();
		}
		if($this->SugerSessID)
		{
			$get_relationships_parameters = array(
		
				 'session' => $this->SugerSessID,
		
				 //The name of the module from which to retrieve records.
				 'module_name' => $parentModule,
		
				 //The ID of the specified module bean.
				 'module_id' => $RecordID,
		
				 //The relationship name of the linked field from which to return records.
				 'link_field_name' => $LinkFieldName,
		
				 //The portion of the WHERE clause from the SQL statement used to find the related items.
				 'related_module_query' => $RelatedModuleQuery,
		
				 //The related fields to be returned.
				 'related_fields' => $RelatedFields,
				 
				 //For every related bean returned, specify link field names to field information.
				 'related_module_link_name_to_fields_array' => array(),
		
				 //To exclude deleted records
				 'deleted'=> '0',
		
				 //order by
				 'order_by' => $orderBy,
		
				 //offset
				 'offset' => 0,
		
				 //limit
				 'limit' => $limit,
			);
						
			$resultList = $this->SugarCall("get_relationships", $get_relationships_parameters, $this->SugarURL);
			return $resultList;
		}
		return false;
	}

	function getModuleFieldsList($moduleName)
	{
		$moduleName = trim($moduleName);
		if($moduleName == '' ) return NULL;
		$result = (object)array();
		if($this->SugerSessID == '')
		{
			$a = $this->LoginToSugar();
		}
		if($this->SugerSessID)
		{
			$set_entry_parameters = array(	 "session" 		=> $this->SugerSessID,
											 "module_name"	=> $moduleName
										 );
			$result = $this->SugarCall("get_module_fields", $set_entry_parameters, $this->SugarURL);
		}
		return $result;
	}
}
?>