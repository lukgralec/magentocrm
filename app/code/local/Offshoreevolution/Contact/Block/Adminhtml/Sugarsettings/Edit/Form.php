<?php
class Offshoreevolution_Contact_Block_Adminhtml_Sugarsettings_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
/**
* Preparing form
*
* @return Mage_Adminhtml_Block_Widget_Form
*/
	protected function _prepareForm()
	{
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$resultRs = $read->fetchAll("SELECT * FROM oepl_sugar WHERE module='login'");
				
		$form = new Varien_Data_Form(
								  array(
								  	'id' => 'edit_form',
								  	'action' => $this->getUrl('*/*/save'),
								  	'method' => 'post',
								  ));
		
		$form->setUseContainer(true);
		$this->setForm($form);
		 
		$helper = Mage::helper('contact');
		$fieldset = $form->addFieldset('display', array(
														'legend' => $helper->__('Enter your SugarCRM details here'),
														//'class' => 'fieldset-wide'
													   ));
		 
		$fieldset->addField('url', 'text', array(
												  'name' => 'url',
												  'class' => 'required-entry',
												  'required' => true,
												  'label' => $helper->__('SugarCRM URL'),
												  'value' => $resultRs[0]['meta_value'],
												  'after_element_html' => '<a href="http://support.sugarcrm.com/02_Documentation/04_Sugar_Developer/Sugar_Developer_Guide_7.2/70_API/Web_Services/00_API_Versioning/" target="_blank" style="margin-left:15px"  >Click here</a> to refer REST API url for your SugarCRM version.'
 											  ));
		$fieldset->addField('username', 'text', array(
												  'name' => 'username',
												  'class' => 'required-entry',
												  'required' => true,
												  'label' => $helper->__('SugarCRM Admin User'),
												  'value' => $resultRs[1]['meta_value'],
											  ));
		$fieldset->addField('password', 'text', array(
												  'name' => 'password',
												  'class' => 'required-entry',
												  'required' => true,
												  'label' => $helper->__('SugarCRM Admin Password'),
												  //'after_element_html' => '<button type="button" onclick="ajaxCall()">Do not click</button>' 
											  ));
		
		 
		if (Mage::registry('contact'))
		{
			$form->setValues(Mage::registry('contact')->getData());
		}
		 
		return parent::_prepareForm();
	}
}
	
?>