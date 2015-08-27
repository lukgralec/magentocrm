<?php
class Offshoreevolution_Contact_Block_Adminhtml_Useroperations_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
				
		$form = new Varien_Data_Form(
								  array(
								  	'id' => 'edit_form',
								  	'action' => $this->getUrl('*/*/accesssave'),
									//'onsubmit' => 'ajaxCall()',
								  	'method' => 'post',
								  ));
		 
		$form->setUseContainer(true);
		$this->setForm($form);
		
		$test = Mage::helper('contact')->getSugarObject();
		$modules = $test->ModuleList;
		
		$helper = Mage::helper('contact');
		$fieldset = $form->addFieldset('display', array(
				'legend' => $helper->__('Access Control'),
		));
		foreach($modules as $key=>$val)
		{
			$resultRs = $read->fetchAll("SELECT * FROM oepl_sugar WHERE module='".$val."'");
			//echo "<pre>"; print_r($resultRs); echo "</pre>";
			
			$temp = $val;
			$temp =	$fieldset->addFieldset($val, array(
					'legend' => $helper->__($val." Module"),
					'name'	=> 'module',
					'value'	=> $val
			));
			
			$temp->addField($val.'Insert', 'select', array(
					'label'     => Mage::helper('contact')->__('Insert'),
					'name'      => $val.'Insert',
					'onclick' => "",
					'onchange' => "",
					'value'  => $resultRs[0]['meta_value'],
					'values' => array(
									  array('value'=>'N','label'=>'Disbled'),
									  array('value'=>'Y','label'=>'Enabled'),
								 ),
					'disabled' => false,
					'readonly' => false,
					'tabindex' => 1
			));
			$temp->addField($val.'Update', 'select', array(
					'label'     => Mage::helper('contact')->__('Update'),
					'name'      => $val.'Update',
					'onclick' => "",
					'onchange' => "",
					'value'  => $resultRs[1]['meta_value'],
					'values' => array(
									  array('value'=>'N','label'=>'Disbled'),
									  array('value'=>'Y','label'=>'Enabled'),
								 ),
					'disabled' => false,
					'readonly' => false,
					'tabindex' => 1
			));

			$temp->addField($val.'Delete', 'select', array(
					'label'     => Mage::helper('contact')->__('Delete'),
					'name'      => $val.'Delete',
					'onclick' => "",
					'onchange' => "",
					'value'  => $resultRs[2]['meta_value'],
					'values' => array(
					                  array('value'=>'N','label'=>'Disbled'),
									  array('value'=>'Y','label'=>'Enabled'),
								 ),
					'disabled' => false,
					'readonly' => false,
					'tabindex' => 1
			));
			
			if($val== 'Contacts'){
				$Guest_user = $fieldset->addFieldset('Guest_Order_Sync', array(
						'legend' => $helper->__("Insert Guest order customer information into SugarCRM Contact Module as a new Contact"),
				));
				$Guest_user->addField('guest_order_sync', 'select', array(
						'label'     => Mage::helper('contact')->__('Status'),
						'name'      => 'guest_order_sync',
						'onclick' => "",
						'onchange' => "",
						'value'  => $resultRs[3]['meta_value'],
						'values' => array(
										  array('value'=>'N','label'=>'Disbled'),
										  array('value'=>'Y','label'=>'Enabled'),
									 ),
						'disabled' => false,
						'readonly' => false,
						'after_element_html' => '<a class="OEPL_show_info" style="margin-left:15px;cursor:pointer">Click here</a> for help',
						'tabindex' => 1
				));
			}
		}
		
		/*if (Mage::registry('contact'))
		{
			$form->setValues(Mage::registry('contact')->getData());
		}*/
		 
		return parent::_prepareForm();
	}
}
?>