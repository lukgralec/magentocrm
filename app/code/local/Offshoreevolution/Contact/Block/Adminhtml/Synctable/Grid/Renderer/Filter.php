<?php
class Offshoreevolution_Contact_Block_Adminhtml_Synctable_Grid_Renderer_Filter extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
		$magfield = $row->getData('mag_field');
		$fieldType = $row->getData('mag_field_type');
		
			$CustomerDetail = Mage::helper('contact')->getContactsArray();
			$CustomerArray = array();
			$CustomerArray = array('label' => "Please Select");
			foreach($CustomerDetail as $key=>$val)
			{
				if($val && $val != '' && $key != 'default_billing' && $key != 'default_shipping')
				$CustomerArray[] = array('label' => $val,'value' => "Default#".$key);
			}
			$BillingAddressFields = Mage::helper('contact')->getAddressEntityList();
			$BillingArray = array();
			$ShippingArray = array();
			foreach($BillingAddressFields as $key=>$val)
			{
				if($val && $val != '')
				$BillingArray[] = array('label' => $val,'value' => "Billing#".$key);
				$ShippingArray[] = array('label' => $val,'value' => "Shipping#".$key);
			}
			
			$options = array(0=>array('label' => 'Contact Infromation Fields','value' => $CustomerArray),
							 1=>array('label' => 'Billing Address Fields','value' => $BillingArray),
							 2=>array('label' => 'Shipping Address Fields','value' => $ShippingArray)
						);
			$tmp = Mage::app()->getLayout()->createBlock('core/html_select')
						->setName('mag_field')
						->setId('mag_field')
						->setValue($fieldType."#".$magfield);
						
			$select =  $tmp->setOptions($options);
			if ($fieldType == 'Billing' OR $fieldType == 'Shipping'){
				$afterHtml = "<span class='fieldDesc' style='margin-left:20px;font-style: italic;'><strong>".$fieldType."</strong> Address Field<span>";
			} else {
				if($magfield && $magfield != ''){
					$afterHtml = "<span class='fieldDesc' style='margin-left:20px;font-style: italic;'><strong>Contact </strong> information field<span>";
				} else {
					$afterHtml = "<span class='fieldDesc' style='margin-left:20px;font-style: italic;'></span>";
				}
			}
			return $select->getHtml().$afterHtml; 		
	}
}
?>
