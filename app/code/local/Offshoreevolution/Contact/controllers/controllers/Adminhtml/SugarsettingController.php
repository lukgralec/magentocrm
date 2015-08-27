<?php
class Offshoreevolution_Contact_Adminhtml_SugarsettingController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
    	$this->loadLayout();
		$this->_setActiveMenu('sugarcrm/form');
		$this->_addBreadcrumb(Mage::helper('contact')->__('form'), Mage::helper('contact')->__('form'));
		$this->renderLayout();
	}
	
	public function testAction()
	{
		if($this->getRequest()->getPost())
		{
			$data = $this->getRequest()->getPost();
			$objSugar = new Offshoreevolution_ClassOEPL();
					
			$objSugar->SugarURL = $data['url'];
			$objSugar->SugarUser = $data['username'];
			$objSugar->SugarPass = md5($data['password']);
			$login = $objSugar->LoginToSugar();
			if($login && $login != '')
			{
				echo "true";
			} else {
				echo "false";
			}
		}
	}
	
	public function saveAction(){
		echo "<pre>";
		$data = $this->getRequest()->getPost();
		
		$objSugar = new Offshoreevolution_ClassOEPL();
		$objSugar->SugarURL 	= $data['url'];
		$objSugar->SugarUser 	= $data['username'];
		$objSugar->SugarPass 	= md5($data['password']);
		
		$login = $objSugar->LoginToSugar();		
		if($login && $login != ''){
			$read = Mage::getSingleton('core/resource')->getConnection('core_read');
			$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
			$skiparray = array('form_key','password');
			$connection->query('DELETE FROM oepl_sugar WHERE module = "login"');
			foreach($data as $key=>$value)
			{
				$flag = false;
				if(!in_array($key,$skiparray))
				{
					$__fields['module'] = 'login';
					$__fields['meta_key'] = $key;
					$__fields['meta_value'] = $value;
					$flag = true;		
				}
				if($key == 'password')
				{
					$flag = true;
					$__fields['module'] = 'login';
					$__fields['meta_key'] = $key;
					$__fields['meta_value'] = md5($value);			
				}
				if($flag == true)
				{
					$insert = $connection->insert('oepl_sugar', $__fields);
				}
			}
			Mage::getSingleton('core/session')->addSuccess('Configuration saved successfully.');
			$this->_redirect('*/*/');
		} else {
			Mage::getSingleton('core/session')->addError('Invalid login details. Please try again');
			$this->_redirect('*/*/');
		}
	}
	
	public function UseroperationsAction()
	{
		$this->loadLayout();
		$this->_setActiveMenu('sugarcrm/form');
		$this->_addBreadcrumb(Mage::helper('contact')->__('form'), Mage::helper('contact')->__('form'));
		$this->renderLayout();
	}
	
	public function AccesssaveAction(){
		$data = $this->getRequest()->getPost();
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$test = Mage::helper('contact')->getSugarObject();
		$modules = $test->ModuleList;
		$operations = array('Insert','Update','Delete');
		foreach($modules as $module){
			$write->query('DELETE FROM oepl_sugar WHERE module = "'.$module.'"');
			foreach ($operations as $op){
				$fields['module'] = $module;
				$fields['meta_key'] = $op;
				$fields['meta_value'] = $data[$module.$op];
				$write->insert('oepl_sugar', $fields);	
			}
		}
		$fields['module'] = 'Contacts';
		$fields['meta_key'] = 'guest_order_sync';
		$fields['meta_value'] = $data['guest_order_sync'];
		$write->insert('oepl_sugar', $fields);
		Mage::getSingleton('core/session')->addSuccess('Operations saved successfully');
		$this->_redirect('*/*/Useroperations');
	}
}