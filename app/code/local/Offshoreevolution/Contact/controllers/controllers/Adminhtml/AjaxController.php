<?php

class Offshoreevolution_Contact_Adminhtml_AjaxController extends Mage_Adminhtml_Controller_Action {

    public function SyncnewfieldsAction() {
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $ObjectSugar = Mage::helper('contact')->getSugarObject();

        $prefix = Mage::getConfig()->getTablePrefix();
        $newFieldsArray = array();
        $flag = false;
        foreach ($ObjectSugar->ModuleList as $module) {
            $resultRS = $ObjectSugar->getModuleFieldsList($module);
            $skipArray = $ObjectSugar->ExcludeFields;
            foreach ($resultRS->module_fields as $k => $v) {
                if (!in_array($v->name, $skipArray)) {
                    if ($v->name == 'email') {
                        $v->name = 'email1';
                    }
                    $fieldname = $v->name;

                    $newFields = $read->fetchAll('SELECT * FROM ' . $prefix . 'oepl_map_fields WHERE field_name = "' . $fieldname . '" AND module = "' . $module . '"');
                    if (empty($newFields)) {
                        $flag = true;
                        $query = 'INSERT INTO ' . $prefix . 'oepl_map_fields SET module = "' . $module . '", 
									field_name = "' . $v->name . '",
									data_type = "' . $v->type . '",
									wp_meta_label = "' . trim($v->label, ":") . '",
									display_order = 1,
									is_show = "N",
									show_column = 1';
                        $no = $write->exec($query);
                    }
                    $newFieldsArray[] = $v->name;
                }
            }
            $preFieldsRs = $read->fetchAll('SELECT * FROM ' . $prefix . 'oepl_map_fields WHERE module = "' . $module . '"');
            $preFields = array();
            foreach ($preFieldsRs as $tmp) {
                $preFields[] = $tmp['field_name'];
            }
            $deleteFieldsName = array_diff($preFields, $newFieldsArray);
            if (!empty($deleteFieldsName)) {
                $flag = true;
                foreach ($deleteFieldsName as $tmpName) {
                    $where = "field_name = '" . $tmpName . "' AND module = '" . $module . "'";
                    $write->delete($prefix . "oepl_map_fields", $where);
                }
            }
        }
        if ($flag == true) {
            echo "updated";
        }
    }

    public function MapfieldAction() {
        $data = $this->getRequest()->getPost();
        $field = explode("#", $data['field']);
        //print_r($field); exit;
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $prefix = Mage::getConfig()->getTablePrefix();

        $write->query('UPDATE oepl_map_fields SET mag_field = "' . $field[1] . '", mag_field_type = "' . $field[0] . '" WHERE pid =' . trim($data['id']) . '');
        echo "true";
    }

}

?>