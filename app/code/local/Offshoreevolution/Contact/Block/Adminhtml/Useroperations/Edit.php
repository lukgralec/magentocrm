<?php

class Offshoreevolution_Contact_Block_Adminhtml_Useroperations_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $this->_blockGroup = 'contact';
        $this->_controller = 'adminhtml_useroperations';
        $this->_headerText = Mage::helper('contact')->__('User Operations');
    }

}

?>