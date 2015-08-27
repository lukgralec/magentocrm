<?php
class Offshoreevolution_Contact_Model_Observer extends Varien_Event_Observer
{
	private $sugarObj = '';
	public function __construct()
	{
		$OEPLSugarObj = Mage::helper('contact')->getSugarObject();
		if($OEPLSugarObj->SugerSessID == '')
		{
			$OEPLSugarObj->LoginToSugar();
			$this->sugarObj = $OEPLSugarObj;
		}
	}
	
	public function AddressSync($observer){
		$address = $observer->getCustomerAddress()->getData();
		$Customer = Mage::getSingleton('customer/session')->getCustomer();
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$write  = Mage::getSingleton('core/resource')->getConnection('core_write');
		if(is_object($Customer)){
			$Customer = $Customer->getData();
			$customerId = $Customer['entity_id'];
			if($Customer['default_billing'] && $Customer['default_billing'] == $address['entity_id']){
				$type = 'Billing';	
			} else if($Customer['default_shipping'] && $Customer['default_shipping'] == $address['entity_id'] ){
				$type = 'Shipping';
			}
			
			$fieldsToExport = $read->fetchAll("SELECT * FROM oepl_map_fields WHERE module = 'Contacts' AND mag_field_type = '".$type."' AND `mag_field` != ''");
			$permission 	= $read->fetchAll("SELECT * FROM oepl_sugar WHERE module = 'Contacts'");
			
			$Flag = true;
			$name_value_list = array();
			foreach ($fieldsToExport as $temp)
			{
				$name_value_list[$temp['field_name']] = $address[$temp['mag_field']] ;
			}
			$query2 = "SELECT sugar_id FROM oepl_sugar_map 
					   WHERE mag_id = '".$customerId."' AND module='Contact'";
			$sugarID = $read->fetchAll($query2);
			if(!empty($sugarID) && $sugarID != '')
			{
				$name_value_list['id'] = $sugarID[0]['sugar_id'];
			}
			$OEPLSugarObj = $this->sugarObj;
			
			if($OEPLSugarObj->SugerSessID != '')
			{
				if(!empty($sugarID)){
					if($permission[1]['meta_value'] == 'N'){
						$Flag = false;
					}
				} else {
					$Flag = false;
				}
				if($Flag){
					$set_entry_parameters = array("session" 			=> $OEPLSugarObj->SugerSessID,
												  "module_name"		=> "Contacts",
												  "name_value_list" 	=> $name_value_list
											     );
					$SugarResult = $OEPLSugarObj->SugarCall("set_entry", $set_entry_parameters);
				}
			}
		} 
	}
	
	public function userInsertUpdate($observer)
	{
		$event = $observer->getCustomer();
		$customerData = $event->getData();
		if(is_object($event->getDefaultBillingAddress())){
			$defaultBilling = $event->getDefaultBillingAddress()->getData();	
		} else {
			$defaultBilling = array();
		}
		
		if(is_object($event->getDefaultShippingAddress())){
			$defaultShipping = $event->getDefaultShippingAddress()->getData();
		} else {
			$defaultShipping = array();
		}
		
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$write  = Mage::getSingleton('core/resource')->getConnection('core_write');

		$fieldsToExport = $read->fetchAll("SELECT * FROM oepl_map_fields WHERE module = 'Contacts' AND `mag_field` != ''");
		$permission 	= $read->fetchAll("SELECT * FROM oepl_sugar WHERE module = 'Contacts'");
		
		$Flag = true;
		$name_value_list = array();
		foreach ($fieldsToExport as $temp)
		{
			if($temp['mag_field_type'] == "Billing"){
				$name_value_list[$temp['field_name']] = $defaultBilling[$temp['mag_field']];
			} else if ($temp['mag_field_type'] == "Shipping"){
				$name_value_list[$temp['field_name']] = $defaultShipping[$temp['mag_field']];
			} else {
				$name_value_list[$temp['field_name']] = $customerData[$temp['mag_field']] ;
			}
		}
		$query2 = "SELECT sugar_id FROM oepl_sugar_map 
				   WHERE mag_id = '".$customerData['entity_id']."' AND module='Contact'";
		$sugarID = $read->fetchAll($query2);
		if(!empty($sugarID) && $sugarID != '')
		{
			$name_value_list['id'] = $sugarID[0]['sugar_id'];
		}
		$OEPLSugarObj = $this->sugarObj;
		
		if($OEPLSugarObj->SugerSessID != '')
		{
			if(!empty($sugarID)){
				if($permission[1]['meta_value'] == 'N'){
					$Flag = false;
				}
			} else {
 				if($permission[0]['meta_value'] == 'N'){
					$Flag = false;
				}
			}
			if($Flag){
				$set_entry_parameters = array("session" 			=> $OEPLSugarObj->SugerSessID,
											  "module_name"		=> "Contacts",
											  "name_value_list" 	=> $name_value_list
										     );
				$SugarResult = $OEPLSugarObj->SugarCall("set_entry", $set_entry_parameters);
			}
			if(empty($sugarID)){
				if($Flag){
					$query = "INSERT INTO oepl_sugar_map 
					  SET mag_id='".$customerData['entity_id']."',sugar_id='".$SugarResult->id."',module='Contact'";
					$write->query($query);
				}
			}
		}
	}
	
	public function guest_user_sync($observer){
		if(!Mage::helper('customer')->isLoggedIn()){
			$read = Mage::getSingleton('core/resource')->getConnection('core_read');
			$write  = Mage::getSingleton('core/resource')->getConnection('core_write');
	
			$fieldsToExport = $read->fetchAll("SELECT * FROM oepl_map_fields WHERE module = 'Contacts' AND `mag_field` != ''");
			$permission 	= $read->fetchAll("SELECT * FROM oepl_sugar WHERE module = 'Contacts' AND meta_key = 'guest_order_sync'");
			$permission = $permission[0];
			if($permission['meta_value'] == 'Y')
			{
				$order = $observer->getEvent()->getOrder();
				$billingAddress = $order->getBillingAddress();
				$shippingAddress = $order->getShippingAddress();
				$name_value_list = array();
				foreach ($fieldsToExport as $temp)
				{
					if($temp['mag_field_type'] == "Billing"){
						$name_value_list[$temp['field_name']] = $billingAddress[$temp['mag_field']];
					} else if ($temp['mag_field_type'] == "Shipping"){
						$name_value_list[$temp['field_name']] = $shippingAddress[$temp['mag_field']];
					} else {
						$name_value_list[$temp['field_name']] = $billingAddress[$temp['mag_field']] ;
					}
				}
				$OEPLSugarObj = $this->sugarObj;
				$set_entry_parameters = array( "session" 			=> $OEPLSugarObj->SugerSessID,
											   "module_name"		=> "Contacts",
											   "name_value_list" 	=> $name_value_list
											 );
				$SugarResult = $OEPLSugarObj->SugarCall("set_entry", $set_entry_parameters);
			}
		}
	}
	
	public function userDelete($observer)
	{
		
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$write  = Mage::getSingleton('core/resource')->getConnection('core_write');
		$data = $observer->getEvent()->getData();
		$id = $data['object']->_data['entity_id'];
		if($id && $id != ''){
			$permission 	= $read->fetchAll("SELECT * FROM oepl_sugar WHERE module = 'Contacts' AND meta_key= 'Delete'");
			if($permission[0]['meta_value'] == 'Y')
			{
				$query = "SELECT sugar_id FROM oepl_sugar_map 
											WHERE mag_id = '".$id."'";
				
				$sugarIdRs = $read->fetchAll($query);
				$sugarID = $sugarIdRs[0]['sugar_id'];
				$_fields = array(
									'id' => $sugarID,
									'deleted' => 1
								);
				
				$OEPLSugarObj = $this->sugarObj;
				if($OEPLSugarObj->SugerSessID != '')
				{
					$set_entry_parameters = array(	 "session" 			=> $OEPLSugarObj->SugerSessID,
													 "module_name"		=> "Contacts",
													 "name_value_list" 	=> $_fields
												 );
					$SugarResult = $OEPLSugarObj->SugarCall("set_entry", $set_entry_parameters);
				}
				if($sugarID && $sugarID != '')
				{
					$where = "mag_id = ".$id." AND sugar_id = '".$sugarID."'";
					$write->delete('oepl_sugar_map', $where);
				}
			}
		}
	}	
}
?>