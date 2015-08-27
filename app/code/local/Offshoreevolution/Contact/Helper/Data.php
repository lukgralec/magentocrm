<?php

class Offshoreevolution_Contact_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getContactsArray() {
        $resultRs2 = Mage::getModel('customer/entity_attribute_collection')->getData();
        $CustomerDetail = array();
        foreach ($resultRs2 as $temp2) {
            $CustomerDetail[$temp2['attribute_code']] = $temp2['frontend_label'];
        }
        return $CustomerDetail;
    }

    public function getAddressEntityList() {
        $resultRs1 = Mage::getModel('customer/entity_address_attribute_collection')->getData();
        $AddressFields = array();
        foreach ($resultRs1 as $temp1) {
            $AddressFields[$temp1['attribute_code']] = $temp1['frontend_label'];
        }
        return $AddressFields;
    }

    public function getSugarObject() {
        $ObjectSugar = new Offshoreevolution_ClassOEPL();
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $resultRs = $read->fetchAll("SELECT * FROM oepl_sugar WHERE module='login'");
        $ObjectSugar->SugarURL = $resultRs[0]['meta_value'];
        $ObjectSugar->SugarUser = $resultRs[1]['meta_value'];
        $ObjectSugar->SugarPass = $resultRs[2]['meta_value'];
        return $ObjectSugar;
    }

}

?>