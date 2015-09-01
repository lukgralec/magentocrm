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
    public function synchronizeCustomer($customer) {
        $this->init();
        if (strlen($this->sessionId) > 0) {
            $contactId = $this->getContactID($customer->getData('email'));

            $sugarId = Mage::getModel('sugarcp/sugarcrm')->syncSugarcrm(
                    $customer, array(
                'sessionId' => $this->sessionId,
                'contactId' => $contactId
                    )
            );

//show messages only for admin
            if (Mage::getSingleton('admin/session')->isLoggedIn() && $sugarId !== false && strlen($sugarId) == 36) {
                if ($contactId == false) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                            $this->__('New Contact was created in SugarCRM')
                    );
                } else {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                            $this->__('The Contact info was updated in SugarCRM')
                    );
                }
            }
        }

        return $this;
    }

//////////////////////////////////sunchronizuj product//////////////////////////
    /**
     * Synchronize product with SugarCRM
     * @param  Mage_Customer_Model_Customer $product
     *  @return Polcode_Sugarcp_Helper_Data
     */
    public function synchronizeProduct() {
        $this->init();
        if (strlen($this->sessionId) > 0) {
       //     $productId = $this->getAllProductsSvnumbers();

            $sugarId = Mage::getModel('sugarcp/sugarcrm')->syncSugarpro(
                     array(
                'sessionId' => $this->sessionId,
                    //'productId' => $productId
                    )
            );

//show messages only for admin
        }


        return $this;
    }

/////////////////////////////////////////////////////////////////////////////////
    /**
     * Delete customer from SugarCRM
     * @param  string $email
     * @return Polcode_Sugarcp_Helper_Data
     */
    public function deleteCustomer($email) {
        $this->init();

        if (strlen($this->sessionId) > 0) {
            $contactId = $this->getContactID($email);

            if ($contactId !== false) {
                $sugarId = Mage::getModel('sugarcp/sugarcrm')->deleteFromSugarcrm(
                        array(
                            'sessionId' => $this->sessionId,
                            'contactId' => $contactId
                        )
                );

                if (Mage::getSingleton('admin/session')->isLoggedIn() && $sugarId !== false && strlen($sugarId) == 36) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                            $this->__('The Contact was deleted in SugarCRM')
                    );
                }
            } else if (Mage::getSingleton('admin/session')->isLoggedIn()) {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        $this->__('The Contact with this email not exists in SugarCRM')
                );
            }
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
    private function getContactID($email) {
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

        $query = sprintf('svnumber = \'%s\'',$sku);
        $params = array($this->sessionId, 'oqc_Product', $query, '', '', '', '', '');
        $entries = $this->sendRequest('get_entry_list', $params);

        if ($entries !== false && isset($entries->entry_list) && count($entries->entry_list) == 1 && isset($entries->entry_list[0])) {
            $result = $entries->entry_list[0]->id;
        }

        
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
