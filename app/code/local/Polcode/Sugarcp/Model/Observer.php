<?php

/**
 * CustomersIntoSugarCrm Data Helper
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

    /**
     * Customer save handler
     * @param Varien_Object $observer
     * @return Polcode_Sugarcp_Model_Observer
     */
    public function customerSaved($observer) {
        if (Mage::getStoreConfigFlag(self::XML_PATH_ENABLED)){
            $customer = $observer->getEvent()->getCustomer();
            if (($customer instanceof Mage_Customer_Model_Customer)) {
                Mage::helper('sugarcp')->synchronizeCustomer($customer);
            }
        }
        return $this;
    }
    
     /**
     * Customer delete handler
     * @param Varien_Object $observer
     * @return Polcode_Sugarcp_Model_Observer
     */
     public function customerDeleted($observer){
         if (Mage::getStoreConfigFlag(self::XML_PATH_ENABLED)){
             Mage::helper('sugarcp')->deleteCustomer($observer->getEvent()->getCustomer()->getEmail());   
         }
         
         return $this;
     }
    
}
