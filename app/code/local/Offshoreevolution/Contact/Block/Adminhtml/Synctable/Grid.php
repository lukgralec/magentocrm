<?php

class Offshoreevolution_Contact_Block_Adminhtml_Synctable_Grid extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_synctable_grid';
        $this->_blockGroup = 'contact';
        //$this->_headerText = 'Table of Synchronization';
        parent::__construct();
        $this->_removeButton('add');
        /* $this->_addButton('button1', array(
          'label'     => Mage::helper('adminhtml')->__('Go Back'),
          'class'     => 'testConnection',
          ),1 ,5); */
    }

    public function getHeaderText() {
        $module = Mage::getSingleton('core/session')->getFiltermodule();
        /* if($module)
          {
          return 'Edit Contact information';
          }
          else
          {
          return 'Table of Synchrosnization';
          } */
        return 'Magento Customer To SugarCRM Contact Mapping';
    }

}

?>