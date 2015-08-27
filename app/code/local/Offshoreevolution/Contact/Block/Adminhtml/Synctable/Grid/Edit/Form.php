<?php
class Offshoreevolution_Sugarcrm_Block_Adminhtml_Synctable_Grid_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(
								  array(
								  	'id' => 'edit_form',
								  	'action' => $this->getUrl('*/*/save'),
									//'onsubmit' => 'ajaxCall()',
								  	'method' => 'post',
								  ));
		 
		$form->setUseContainer(true);
		$this->setForm($form);
		 
		$helper = Mage::helper('sugarcrm');
		$fieldset = $form->addFieldset('display', array(
									  'legend' => $helper->__('Module Information'),
									  //'class' => 'fieldset-wide'
													   ));
		
		$fieldset->addField('module', 'text', array(
												  'name' => 'module',
												  'class' => 'required-entry',
												  'required' => true,
												  'label' => $helper->__('Module'),
 											  ));
		$fieldset->addField('wp_meta_label', 'text', array(
												  'name' => 'fields',
												  'class' => 'required-entry',
												  'required' => true,
												  'label' => $helper->__('Field Name'),
											  ));
											  
		$fieldset->addField('field_type', 'text', array(
												  'name' => 'fieldtype',
												  'class' => 'required-entry',
												  'required' => true,
												  'label' => $helper->__('Field Type'),
											  ));
		
		$fieldset->addField('data_type', 'text', array(
												  'name' => 'datatype',
												  'class' => 'required-entry',
												  'required' => true,
												  'label' => $helper->__('Data Type'),
											  ));
		 
		if (Mage::registry('syncTable'))
		{
			$form->setValues(Mage::registry('syncTable')->getData());
		}
		 
		return parent::_prepareForm();
	}
}
?>