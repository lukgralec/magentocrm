<?php

/**
 * Sugarcp Data Helper
 *
 * @category    Polcode
 * @package     Polcode_Sugarcp
 */
class Polcode_Sugarcp_Model_Observer {

    const XML_PATH_ENABLED = 'sugarcp/extension/enabled';

    /**
     * Initialization of out custom model
     */
    protected function _construct() {
        $this->_init('sugarcp/observer');
        parent::__construct();
    }

 
    public function customerSaved() {
        if (Mage::getStoreConfigFlag(self::XML_PATH_ENABLED)) {
                Mage::helper('sugarcp')->synchronizeCustomer();
            }
        
        return $this;
    }

  
    public function productSaved() {
        if (Mage::getStoreConfigFlag(self::XML_PATH_ENABLED)) {
                Mage::helper('sugarcp')->synchronizeProduct();

        }
        return $this;
    }


    public function customerDeleted() {
        if (Mage::getStoreConfigFlag(self::XML_PATH_ENABLED)) {
            Mage::helper('sugarcp')->deleteCustomer();
        }

        return $this;
    }
    
   
     public function productDeleted() {
         
         Mage::log('productDeleted');
         
        if (Mage::getStoreConfigFlag(self::XML_PATH_ENABLED)) {
            Mage::helper('sugarcp')->deleteProduct();
        }

        return $this;
    }
    
    
    

}
