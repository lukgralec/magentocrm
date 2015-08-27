<?php

class Offshoreevolution_Contact_Model_Mysql4_Settings extends Mage_Core_Model_Mysql4_Abstract {

    protected function _construct() {
        $this->_init('contact/settings', 'pid');
    }

}

?>