<?php

/**
 * CustomersIntoSugarCrm Data Helper
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
    public function syncSugarcrm($customer, $params) {
        $contactId = $this->syncContact($customer, $params);

        return $contactId;
    }

    /**
     * Synchronize Magento Product data with SugarCRM Product object
     * @param  Mage_Customer_Model_Customer $customer
     * @param  array $params
     * @return Polcode_Sugarcp_Model_Sugarcrm
     */
    public function syncSugarpro($product, $params) {
        $productId = $this->syncProduct($product, $params);

        return $productId;
    }

    /**
     * Delete Contact object from SugarCRM
     * @param  array $params
     * @return Polcode_Sugarcp_Model_Sugarcrm
     */
    public function deleteFromSugarcrm($params) {
        $result = false;

        if ($params['contactId'] !== false) {
            //set Contact properties
            $contactParams = array(
                'session' => $params['sessionId'],
                'module' => 'Contacts',
                'name_value_list' => array(
                    0 => array(
                        array('name' => 'id', 'value' => $params['contactId']),
                        array('name' => 'deleted', 'value' => '1')
                    )
                ),
            );

            $requestResult = Mage::helper('sugarcp')->sendRequest('set_entries', $contactParams);

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
    private function syncContact($customer, $params) {
        $result = false;

        //set Contact properties
        $contactParams = array(
            'session' => $params['sessionId'],
            'module' => 'Contacts',
            'name_value_list' => array(
                0 => array(
                    array('name' => 'title', 'value' => $customer->getData('prefix')),
                    array('name' => 'first_name', 'value' => $customer->getData('firstname')),
                    array('name' => 'last_name', 'value' => $customer->getData('lastname')),
                    array('name' => 'email1', 'value' => $customer->getData('email')),
                    array('name' => 'birthdate', 'value' => $customer->getData('dob')),
                    array('name' => 'lead_source', 'value' => 'Other')
                )
            ),
        );

        //get all addresses of current customer
        $addressList = $customer->getAddresses();

        //add Primary Address of Contact
        if (($address = $customer->getPrimaryAddress('default_billing')) != false || count($addressList) > 0) {
            //we do not have default billing BUT we have "some" address in address book - we use it
            if ($address == false) {
                $address = $addressList[0];
            }

            $contactParams['name_value_list'][0] = array_merge(
                    $contactParams['name_value_list'][0], $this->prepareAddressArray($address)
            );
        }
        //set address as empty
        else {
            $contactParams['name_value_list'][0][] = array('name' => 'primary_address_country', 'value' => '');
            $contactParams['name_value_list'][0][] = array('name' => 'primary_address_postalcode', 'value' => '');
            $contactParams['name_value_list'][0][] = array('name' => 'primary_address_state', 'value' => '');
            $contactParams['name_value_list'][0][] = array('name' => 'primary_address_city', 'value' => '');
            $contactParams['name_value_list'][0][] = array('name' => 'primary_address_street', 'value' => '');
        }

        //add Other Address of Contact
        if (($address = $customer->getPrimaryAddress('default_shipping')) != false) {
            $contactParams['name_value_list'][0] = array_merge(
                    $contactParams['name_value_list'][0], $this->prepareAddressArray($address, 'alt')
            );
        }
        //set address as empty
        else {
            $contactParams['name_value_list'][0][] = array('name' => 'alt_address_country', 'value' => '');
            $contactParams['name_value_list'][0][] = array('name' => 'alt_address_postalcode', 'value' => '');
            $contactParams['name_value_list'][0][] = array('name' => 'alt_address_state', 'value' => '');
            $contactParams['name_value_list'][0][] = array('name' => 'alt_address_city', 'value' => '');
            $contactParams['name_value_list'][0][] = array('name' => 'alt_address_street', 'value' => '');
        }

        //if Contact exists - we add Id into params to update info instead of creating new contact
        if ($params['contactId'] !== false) {
            $contactParams['name_value_list'][0][] = array('name' => 'id', 'value' => $params['contactId']);
        }

        $requestResult = Mage::helper('sugarcp')->sendRequest('set_entries', $contactParams);

        if (isset($requestResult->ids) && isset($requestResult->ids[0])) {
            $result = $requestResult->ids[0];
        }

        return $result;
    }

    ////////////create new Product in SugarCRM/////////////////////////

    private function syncProduct($product, $params) {
        $result = false;

//////////////////////////getAllProductnamestoArray//////////////////////////////
        $i = 0;
        $dupa = array();
        $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect(array('name', 'sku'));
        foreach ($collection as $value) {
            $arr = array($i => array(

            array('name' => 'name', 'value' => $value['name']),
            // array('name' => 'price', 'value' => $product->getData('price')),
            array('name' => 'svnumber', 'value' => $value['sku']),
            )
            );
            $i++;
            $dupa = array_merge($dupa,$arr);
        }

        //////////////////////////////////////////////////////////      
        //set Product properties
        $productParams = array(
            'session' => $params['sessionId'],
            'module' => 'oqc_Product',
            'name_value_list' => $dupa,
        );
        //if Product exists - we add Id into params to update info instead of creating new contact
        if ($params['productId'] !== false) {
            $productParams['name_value_list'][0][] = array('name' => 'id', 'value' => $params['productId']);
        }

        $requestResult = Mage::helper('sugarcp')->sendRequest('set_entries', $productParams);

        if (isset($requestResult->ids) && isset($requestResult->ids[0])) {
            $result = $requestResult->ids[0];
        }

        return $result;
    }

    ////////////////////////////////////////////////////////////////////

    /**
     * Prepare array of Contact Address properties
     * @param  Mage_Customer_Model_Address $address
     * @param  string $prefix
     * @return array
     */
    private function prepareAddressArray($address, $prefix = 'primary') {
        $result = array();

        //Country
        $result[] = array(
            'name' => $prefix . '_address_country',
            'value' => Mage::app()->getLocale()->getCountryTranslation($address->getData('country_id'))
        );
        //Postal Code
        $result[] = array(
            'name' => $prefix . '_address_postalcode',
            'value' => $address->getData('postcode')
        );
        //State/Region
        $result[] = array(
            'name' => $prefix . '_address_state',
            'value' => (
            $address->getData('region_id') > 0 ?
                    Mage::getModel('directory/region')->load($address->getData('region_id'))->getName() :
                    $address->getData('region')
            )
        );
        //City
        $result[] = array(
            'name' => $prefix . '_address_city',
            'value' => $address->getData('city')
        );
        //Address
        $result[] = array(
            'name' => $prefix . '_address_street',
            'value' => $address->getData('street')
        );

        return $result;
    }

}
