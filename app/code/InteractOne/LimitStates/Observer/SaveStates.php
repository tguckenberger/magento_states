<?php

namespace InteractOne\LimitStates\Observer;

use InteractOne\LimitStates\Model\ResourceModel\Region\newCollection;
use Magento\Framework\Event\ObserverInterface;

class SaveStates extends newCollection implements ObserverInterface
{

    protected $stateFactory;
    protected  $countryFactory;
    protected  $scopeConfigInterface;
    public $selectedStates = array();
    protected $_regionNameTable;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \InteractOne\LimitStates\Model\StateFactory $stateFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    )
    {
        $this->stateFactory = $stateFactory;
        $this->countryFactory = $countryFactory;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->_init(\Magento\Directory\Model\Region::class, \Magento\Directory\Model\ResourceModel\Region::class);

        $this->_countryTable = $this->getTable('directory_country');
        $this->_regionNameTable = $this->getTable('directory_country_region_name_io');
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $stateArray = $this->countryFactory->create()->setId('US')->getLoadedRegionCollection()->toOptionArray();
        // Remove 'Please select state' option from array
        unset($stateArray[0]);
        $ID = array_column($stateArray, 'value');
        $stateIndex = explode(',',$this->scopeConfigInterface->getValue('general/region/limit_states'));


        // Sets all states to false
        foreach ($ID as $stateRef) {
            $state = $this->stateFactory->create();
            $state->load($stateArray[$stateRef]['value'])->addData(
                array(
                    'name' => $stateArray[$stateRef]['title'],
                    'state_allowed' => false
                ));
            $state->save();
        }

        // Sets all selected state_allowed values to true
        foreach ($stateIndex as $stateRef) {
            $state = $this->stateFactory->create();
            $state->load($stateArray[$stateRef]['value'])->addData(
                array(
                    'name' => $stateArray[$stateRef]['title'],
                    'state_allowed' => true
                ));
            $state->save();
        }

    }
    public function addCountryFilter($countryId)
    {
        $stateIndex = explode(',',$this->scopeConfigInterface->getValue('general/region/limit_states'));
        if (!empty($countryId)) {
            if (is_array($countryId)) {
                $this->addFieldToFilter('main_table.country_id', ['in' => $countryId]);
            } else {
                $this->addFieldToFilter('main_table.country_id', $countryId);
            }
        }

        if ($countryId == 'US') {

            $this->addUSRegionNameFilter($stateIndex);
        }




        return $this;
    }


}