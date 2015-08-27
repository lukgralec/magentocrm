<?php

class Offshoreevolution_Contact_Block_Adminhtml_Synctable_Grid_Renderer_Button extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $id = $row->getData('pid');
        $button = '<button class=syncAction pid=' . $id . '>Sync</button>';
        return $button;
    }

}

?>