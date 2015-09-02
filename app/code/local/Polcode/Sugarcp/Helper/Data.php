<?php

/**
 * CustomersIntoSugarCrm Data Helper
 *
 * @category    Polcode
 * @package     Polcode_Sugarcp
 */
class Polcode_Sugarcp_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * paths to system.xml fields
     */
    const XML_PATH_ENABLED = 'sugarcp/extension/enabled';
    const XML_PATH_SUGAR_URL = 'sugarcp/sugarcrm/url';
    const XML_PATH_SUGAR_LOGIN = 'sugarcp/sugarcrm/login';
    const XML_PATH_SUGAR_PASSWD = 'sugarcp/sugarcrm/password';

    private $sessionId = '';

    /**
     * Synchronize customer with SugarCRM
     * @param  Mage_Customer_Model_Customer $customer
     *  @return Polcode_Sugarcp_Helper_Data
     */
    public function synchronizeCustomer() {
        $this->init();
        if (strlen($this->sessionId) > 0) {

            $sugarId = Mage::getModel('sugarcp/sugarcrm')->syncSugarcrm(
                    array(
                        'sessionId' => $this->sessionId,
                    )
            );
        }

        return $this;
    }

    /**
     * Synchronize product with SugarCRM
     * @param  Mage_Customer_Model_Customer $product
     *  @return Polcode_Sugarcp_Helper_Data
     */
    public function synchronizeProduct() {
        $this->init();
        if (strlen($this->sessionId) > 0) {

            $sugarId = Mage::getModel('sugarcp/sugarcrm')->syncSugarpro(
                    array(
                        'sessionId' => $this->sessionId,
                    )
            );
        }
        return $this;
    }

/////////////////////////////////////////////////////////////////////////////////
    /**
     * Delete customer from SugarCRM
     * @param  string $email
     * @return Polcode_Sugarcp_Helper_Data
     */
    public function deleteCustomer() {
        $this->init();

        if (strlen($this->sessionId) > 0) {

            $sugarId = Mage::getModel('sugarcp/sugarcrm')->deleteFromSugarcrm(
                    array(
                        'sessionId' => $this->sessionId,
                    )
            );
        }
        return $this;
    }

    /**
     * Delete product from SugarCRM
     * @param  string $email
     * @return Polcode_Sugarcp_Helper_Data
     */
    public function deleteProduct() {
        $this->init();

        if (strlen($this->sessionId) > 0) {
            $sugarId = Mage::getModel('sugarcp/sugarcrm')->deleteProductFromSugarcrm(
                    array(
                        'sessionId' => $this->sessionId,
                    )
            );
        }
        return $this;
    }

    /**
     * Send request to SugarCRM REST API
     * @param  string $method
     * @param  array $params
     * @return mixed
     */
    public function sendRequest($method, $params) {
        $result = false;

        $postParams = http_build_query(
                array(
                    'method' => $method,
                    'input_type' => 'JSON',
                    'response_type' => 'JSON',
                    'rest_data' => json_encode($params)
                )
        );

        $curl = curl_init($this->getSugarUrl());
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postParams);
        $response = curl_exec($curl);

        $jsonObj = json_decode($response);
        if (is_object($jsonObj) && get_class($jsonObj) == 'stdClass') {
            $result = $jsonObj;
        }

        return $result;
    }

    /**
     * Check if contact with this email exists in SugarCRM and return Contact id or false
     * @param  string $email
     * @return mixed
     */
    public function getContactID($email) {
        $result = false;

        $query = sprintf('contacts.id in (select eab.bean_id from email_addresses ea, email_addr_bean_rel eab where ea.email_address LIKE \'%s\' and eab.primary_address=1 and eab.email_address_id=ea.id and eab.bean_module=\'Contacts\' and ea.opt_out=0 and ea.deleted=0 and eab.deleted=0) and contacts.deleted=0', $email);
        $params = array($this->sessionId, 'Contacts', $query, '', '', '', '', '');
        $entries = $this->sendRequest('get_entry_list', $params);

//if contact exist - get Id
        if ($entries !== false && isset($entries->entry_list) && count($entries->entry_list) == 1 && isset($entries->entry_list[0])) {
            $result = $entries->entry_list[0]->id;
        }

        return $result;
    }

    public function getProductId($sku) {
        $result = false;

        $query = sprintf('svnumber = \'%s\'', $sku);
        $params = array($this->sessionId, 'oqc_Product', $query, '', '', '', '', '');
        $entries = $this->sendRequest('get_entry_list', $params);

//if product exist - get Id
        if ($entries !== false && isset($entries->entry_list) && count($entries->entry_list) == 1 && isset($entries->entry_list[0])) {
            $result = $entries->entry_list[0]->id;
        }


        return $result;
    }

    public function getContactsEmailToArray() {
        $result = false;

        $email = array();
        $query = '';
        $selectFields = array('email1');
        $params = array($this->sessionId, 'Contacts', $query, '', '', $selectFields, '', '');
        $entries = $this->sendRequest('get_entry_list', $params);
        $result_count = $entries->result_count;
        for ($i = 0; $i < $result_count; $i++) {
            $element = $entries->entry_list[$i]->name_value_list->email1->value;
            array_push($email, $element);
        }

        if ($entries !== false) {
            $result = $email;
        }
        return $result;
    }

    public function getCustomersEmailToArray() {
        $result = false;
        $emailArray = array();
        $collection = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect(array('email'));
        foreach ($collection as $value) {
            $email = $value['email'];
            array_push($emailArray, $email);
        }
        if (!empty($emailArray)) {
            $result = $emailArray;
        }

        return $result;
    }

    public function getProductsSvnToArray() {
        $result = false;

        $svnumber = array();
        $query = '';
        $selectFields = array('svnumber');
        $params = array($this->sessionId, 'oqc_Product', $query, '', '', $selectFields, '', '');
        $entries = $this->sendRequest('get_entry_list', $params);
        $result_count = $entries->result_count;

        for ($i = 0; $i < $result_count; $i++) {
            $element = $entries->entry_list[$i]->name_value_list->svnumber->value;
            array_push($svnumber, $element);
        }
        if ($entries !== false) {
            $result = $svnumber;
        }
        return $result;
    }

    public function getProductsSkuToArray() {
        $result = false;
        $skuArray = array();
        $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect(array('sku'));
        foreach ($collection as $value) {
            $sku = $value['sku'];
            array_push($skuArray, $sku);
        }
        if (!empty($skuArray)) {
            $result = $skuArray;
        }

        return $result;
    }

    public function getProductFromSugarCrmToArray($sku) {

        $result = false;

        $query = sprintf('svnumber = \'%s\'', $sku);
        $params = array($this->sessionId, 'oqc_Product', $query, '', '', '', '', '');
        $entries = $this->sendRequest('get_entry_list', $params);

//if product exist - get all magento
        if ($entries !== false && isset($entries->entry_list) && count($entries->entry_list) == 1 && isset($entries->entry_list[0])) {
            $result = array();
        //    $result[] = $entries->entry_list[0]->id;
            $result[] = $entries->entry_list[0]->name_value_list->name->value;
            $result[] = $entries->entry_list[0]->name_value_list->svnumber->value;
            $result[] = $entries->entry_list[0]->name_value_list->price->value;
            
//            $result[0] = $entries->entry_list[0]->id;
//            $result[1] = $entries->entry_list[0]->name;
//            $result[2] = $entries->entry_list[0]->price;
//            $result[3] = $entries->entry_list[0]->svnumber;
        }
        
        
        //id ---- name ---- price --- svnumber
//           var_dump($result);
//            die;

        return $result;
    }
    
    
        public function getCustomerFromSugarCrmToArray($email) {

        $result = false;

        $query = sprintf('contacts.id in (select eab.bean_id from email_addresses ea, email_addr_bean_rel eab where ea.email_address LIKE \'%s\' and eab.primary_address=1 and eab.email_address_id=ea.id and eab.bean_module=\'Contacts\' and ea.opt_out=0 and ea.deleted=0 and eab.deleted=0) and contacts.deleted=0', $email);
        $params = array($this->sessionId, 'Contacts', $query, '', '', '', '', '');
        $entries = $this->sendRequest('get_entry_list', $params);


//if product exist - get all magento
        if ($entries !== false && isset($entries->entry_list) && count($entries->entry_list) == 1 && isset($entries->entry_list[0])) {
            $result = array();
            $result[] = $entries->entry_list[0]->name_value_list->name->value;
            $result[] = $entries->entry_list[0]->name_value_list->email1->value;
//            $result[] = $entries->entry_list[0]->name_value_list->svnumber->value;
//            $result[] = $entries->entry_list[0]->name_value_list->price->value;
            
//            $result[0] = $entries->entry_list[0]->id;
//            $result[1] = $entries->entry_list[0]->name;
//            $result[2] = $entries->entry_list[0]->price;
//            $result[3] = $entries->entry_list[0]->svnumber;
        }
        
        
        //id ---- name ---- price --- svnumber
         

        return $result;
    }
    
    
    

    /**
     * Init function: login into Sugar, etc.
     * @return [type] [description]
     */
    private function init() {
        if (strlen($this->sessionId) == 0) {
            $params = array(
                'user_auth' => array(
                    'user_name' => $this->getSugarLogin(),
                    'password' => md5($this->getSugarPassword()),
                ),
            );

            $result = $this->sendRequest('login', $params);

            if ($result !== false && isset($result->id)) {
                $this->sessionId = $result->id;
            }
        }
    }

    private function getSugarUrl() {
        return Mage::getStoreConfig(self::XML_PATH_SUGAR_URL);
    }

    /**
     * Get login of SugarCRM user from config
     * @return string
     */
    private function getSugarLogin() {
        return Mage::getStoreConfig(self::XML_PATH_SUGAR_LOGIN);
    }

    /**
     * Get password of SugarCRM user from config
     * @return string
     */
    private function getSugarPassword() {
        return Mage::getStoreConfig(self::XML_PATH_SUGAR_PASSWD);
    }

}
