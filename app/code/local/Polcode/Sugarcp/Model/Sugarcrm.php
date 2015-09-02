<?php

/**
 * Sugarcp Data Helper
 *
 * @category    Polcode
 * @package     Polcode_Sugarcp
 */
class Polcode_Sugarcp_Model_Sugarcrm extends Mage_Core_Model_Abstract {
    /**
     * Initialization of our custom model
     */
    protected function _construct() {
        $this->_init('sugarcp/sugarcrm');
        parent::_construct();
    }
    /**
     * Synchronize Magento Customer data with SugarCRM Contact object
     * @param  Mage_Customer_Model_Customer $customer
     * @param  array $params
     * @return Polcode_Sugarcp_Model_Sugarcrm
     */
    public function syncSugarcrm($params) {
        $contactId = $this->syncContact($params);

        return $contactId;
    }

    /**
     * Synchronize Magento Product data with SugarCRM Product object
     * @param  Mage_Customer_Model_Customer $customer
     * @param  array $params
     * @return Polcode_Sugarcp_Model_Sugarcrm
     */
    public function syncSugarpro($params) {
        $productId = $this->syncProduct($params);

        return $productId;
    }

    /**
     * Delete Contact object from SugarCRM
     * @param  array $params
     * @return Polcode_Sugarcp_Model_Sugarcrm
     */
    public function deleteFromSugarcrm($params) {
        $result = false;

        $emailContactsArray = Mage::helper('sugarcp')->getContactsEmailToArray();
        $emailCustomersArray = Mage::helper('sugarcp')->getCustomersEmailToArray();

        $idToDelete = array_diff($emailContactsArray, $emailCustomersArray);
        $nameValueList = array();
        
        if (!empty($idToDelete)) {
            foreach ($idToDelete as $email) {
                $id = Mage::helper('sugarcp')->getContactID($email);

                $arr = array(array(
                        array('name' => 'id', 'value' => $id),
                        array('name' => 'deleted', 'value' => '1')
                    )
                );
                $nameValueList = array_merge($nameValueList, $arr);
            }
            $contactsParams = array(
                'session' => $params['sessionId'],
                'module' => 'Contacts',
                'name_value_list' => $nameValueList,
            );
            $requestResult = Mage::helper('sugarcp')->sendRequest('set_entries', $contactsParams);

            if (isset($requestResult->ids) && isset($requestResult->ids[0])) {
                $result = $requestResult->ids[0];
            }
        }
        return $result;
    }
/**
     * Delete Product object from SugarCRM
     * @param  array $params
     * @return Polcode_Sugarcp_Model_Sugarcrm
     */
    public function deleteProductFromSugarcrm($params) {

        $result = false;
        $svnArray = Mage::helper('sugarcp')->getProductsSvnToArray();
        $skuArray = Mage::helper('sugarcp')->getProductsSkuToArray();

        $idToDelete = array_diff($svnArray, $skuArray);

        $nameValueList = array();
        if (!empty($idToDelete)) {
            foreach ($idToDelete as $sku) {
                $id = Mage::helper('sugarcp')->getProductId($sku);

                $arr = array(array(
                        array('name' => 'id', 'value' => $id),
                        array('name' => 'deleted', 'value' => '1')
                    )
                );
                $nameValueList = array_merge($nameValueList, $arr);
            }


            $productParams = array(
                'session' => $params['sessionId'],
                'module' => 'oqc_Product',
                'name_value_list' => $nameValueList,
            );
            $requestResult = Mage::helper('sugarcp')->sendRequest('set_entries', $productParams);

            if (isset($requestResult->ids) && isset($requestResult->ids[0])) {
                $result = $requestResult->ids[0];
            }
        }
        return $result;
    }

    /**
     * Update or create new Contact in SugarCRM
     * @param  Mage_Customer_Model_Customer $customer
     * @param  array $params
     * @return mixed
     */
    private function syncContact($params) {
        $result = false;

        $i = 0;
        $nameValueList = array();
        $collection = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect(array('firstname', 'lastname', 'email'));

        foreach ($collection as $value) {
            $email = $value['email'];
            $id = Mage::helper('sugarcp')->getContactID($email);
            if ($id !== false) {
                $arr = array($i => array(
                        array('name' => 'id', 'value' => $id),
                        array('name' => 'title', 'value' => ''),
                        array('name' => 'first_name', 'value' => $value['firstname']),
                        array('name' => 'last_name', 'value' => $value['lastname']),
                        array('name' => 'email1', 'value' => $value['email']),
                        array('name' => 'birthdate', 'value' => ''),
                        array('name' => 'lead_source', 'value' => 'Other'),
                        array('name' => 'primary_address_country', 'value' => ''),
                        array('name' => 'primary_address_postalcode', 'value' => ''),
                        array('name' => 'primary_address_state', 'value' => ''),
                        array('name' => 'primary_address_city', 'value' => ''),
                        array('name' => 'primary_address_street', 'value' => ''),
                        array('name' => 'alt_address_country', 'value' => ''),
                        array('name' => 'alt_address_postalcode', 'value' => ''),
                        array('name' => 'alt_address_state', 'value' => ''),
                        array('name' => 'alt_address_city', 'value' => ''),
                        array('name' => 'alt_address_street', 'value' => '')
                    )
                );
                $nameValueList = array_merge($nameValueList, $arr);
                $i++;
            } else {
                $arr = array($i => array(
                        array('name' => 'title', 'value' => ''),
                        array('name' => 'first_name', 'value' => $value['firstname']),
                        array('name' => 'last_name', 'value' => $value['lastname']),
                        array('name' => 'email1', 'value' => $value['email']),
                        array('name' => 'birthdate', 'value' => ''),
                        array('name' => 'lead_source', 'value' => 'Other'),
                        array('name' => 'primary_address_country', 'value' => ''),
                        array('name' => 'primary_address_postalcode', 'value' => ''),
                        array('name' => 'primary_address_state', 'value' => ''),
                        array('name' => 'primary_address_city', 'value' => ''),
                        array('name' => 'primary_address_street', 'value' => ''),
                        array('name' => 'alt_address_country', 'value' => ''),
                        array('name' => 'alt_address_postalcode', 'value' => ''),
                        array('name' => 'alt_address_state', 'value' => ''),
                        array('name' => 'alt_address_city', 'value' => ''),
                        array('name' => 'alt_address_street', 'value' => '')
                    )
                );

                $nameValueList = array_merge($nameValueList, $arr);

                $i++;
            }
        }
        //set Contact properties

        $contactParams = array(
            'session' => $params['sessionId'],
            'module' => 'Contacts',
            'name_value_list' => $nameValueList,
        );



        $requestResult = Mage::helper('sugarcp')->sendRequest('set_entries', $contactParams);

        if (isset($requestResult->ids) && isset($requestResult->ids[0])) {

            $result = $requestResult->ids[0];
        }

        return $result;
    }


////////////create new Product in SugarCRM/////////////////////////

    private function syncProduct($params) {
        $result = false;

        $i = 0;
        $nameValueList = array();
        $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect(array('name', 'sku'));

        foreach ($collection as $value) {
            $sku = $value['sku'];

            $id = Mage::helper('sugarcp')->getProductId($sku);

            if ($id !== false) {

                $arr = array($i => array(
                        array('name' => 'id', 'value' => $id),
                        array('name' => 'name', 'value' => $value['name']),
                        array('name' => 'svnumber', 'value' => $value['sku']),
                    )
                );
                $nameValueList = array_merge($nameValueList, $arr);
                $i++;
            } else {

                $arr = array($i => array(
                        array('name' => 'name', 'value' => $value['name']),
                        array('name' => 'svnumber', 'value' => $value['sku']),
                    )
                );
                $nameValueList = array_merge($nameValueList, $arr);
                $i++;
            }
        }

   
//set Product properties
        $productParams = array(
            'session' => $params['sessionId'],
            'module' => 'oqc_Product',
            'name_value_list' => $nameValueList,
        );

        $requestResult = Mage::helper('sugarcp')->sendRequest('set_entries', $productParams);

        if (isset($requestResult->ids) && isset($requestResult->ids[0])) {
            $result = $requestResult->ids[0];
        }

        return $result;
    }
}
