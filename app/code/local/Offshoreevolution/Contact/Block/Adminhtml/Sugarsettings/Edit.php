<?php
class Offshoreevolution_Contact_Block_Adminhtml_Sugarsettings_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_blockGroup = 'contact';
		$this->_controller = 'adminhtml_sugarsettings';
		//$this->_mode = 'edit';
		
		$this->_updateButton('save', 'label','Save Login Details');
		$this->_addButton('button1', array(
            'label'     => Mage::helper('adminhtml')->__('Test Connection'),
            'class'     => 'testConnection',
        ),1 ,5);
		$this->_addButton('button2', array(
            'label'     => Mage::helper('adminhtml')->__('Sync SugarCRM Fields'),
            'class'     => 'syncfield',
        ),1 ,1);
		$this->_headerText = Mage::helper('contact')->__('SugarCRM settings');
	}
}
?>