<?php

class Offshoreevolution_Contact_Adminhtml_FieldmappingController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->_setActiveMenu('sugarcrm/form');
        $this->_addBreadcrumb(Mage::helper('contact')->__('form'), Mage::helper('contact')->__('form'));
        $this->renderLayout();
        //Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());
    }

}

?>