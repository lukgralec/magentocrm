<?php

class Offshoreevolution_Contact_Block_Adminhtml_Synctable_Grid_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('syncTable');
        $this->setDefaulfSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        /* if (Mage::registry('preparedFilter')) {
          $this->setDefaultFilter(Mage::registry('preparedFilter'));
          } */
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('contact/settings')->getCollection()->addFieldToFilter('module', 'Contacts');
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        /* $this->addColumn('some_column_id', array(
          'header' => Mage::helper('core')->__(''),
          'type' => 'checkbox',
          )); */

        /* $this->addColumn('module',
          array(
          'header' => 'SugarCRM Module Name',
          'align' =>'left',
          'index' => 'module',
          )); */

        $this->addColumn('fields', array(
            'header' => 'SugarCRM Field Name',
            'align' => 'left',
            'index' => 'wp_meta_label',
        ));

        /* $this->addColumn('fieldtype', array(
          'header' => 'Field Type',
          'align' =>'left',
          'index' => 'field_type',
          )); */

        $this->addColumn("magfield", array(
            "header" => Mage::helper("contact")->__("Magento Field Names"),
            'filter' => false,
            'sortable' => false,
            'align' => 'left',
            'width' => '45%',
            'renderer' => 'Offshoreevolution_Contact_Block_Adminhtml_Synctable_Grid_Renderer_Filter'
        ));

        $this->addColumn("actionButton", array(
            "header" => Mage::helper("contact")->__("Action"),
            'filter' => false,
            'sortable' => false,
            'align' => 'center',
            'width' => '80',
            'renderer' => 'Offshoreevolution_Contact_Block_Adminhtml_Synctable_Grid_Renderer_Button'
        ));
        //$this->addColumn('action',
//					array(
//					'header'    =>  Mage::helper('sugarcrm')->__('Action'),
//					'width'     => '100',
//					'type'      => 'action',
//					'getter'    => 'getId',
//					'index' => 'pid',
//					'actions'   => array(
//							array(
//									'caption'   => Mage::helper('sugarcrm')->__('Sync'),
//									'class'		=> 'syncAction',
//									//'url'       => array('base'=> '*/*/edit'),
//									
//									'field'     => 'id'
//							)
//					),
//					'filter'    => false,
//					'sortable'  => false,
//					'index'     => 'stores',
//					'is_system' => true,
//		));
//		$this->addColumn('id',
//             array(
//                    'header' => 'ID',
//                    'align' =>'right',
//                    'width' => '50px',
//                    'index' => 'pid',
//					'column_css_class'=>'no-display',
//   					'header_css_class'=>'no-display'
//        	       ));
        return parent::_prepareColumns();
    }

    //public function getRowUrl($row)
//	{
//		return $this->getUrl('*/*/edit',array('id'=>$row->getId()));
//	}
}

?>