<?php

class Offshoreevolution_Contact_Block_Adminhtml_Synctable_Grid_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $this->_blockGroup = 'contact';
        $this->_controller = 'adminhtml_synctable_grid';
    }

    public function getHeaderText() {
        if (Mage::registry('syncTable') && Mage::registry('syncTable')->getId()) {
            return 'Edit Data';
        } else {
            return 'Add new Data';
        }
    }

}

?>