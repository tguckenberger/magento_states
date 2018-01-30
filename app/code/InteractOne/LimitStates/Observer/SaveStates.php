<?php

namespace InteractOne\LimitStates\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveStates implements ObserverInterface {

    protected $countryFactory;
    protected $stateFactory;
    protected $scopeConfigInterface;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \InteractOne\LimitStates\Model\StateFactory $stateFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    )
    {
        $this->countryFactory = $countryFactory;
        $this->stateFactory = $stateFactory;
        $this->scopeConfigInterface = $scopeConfigInterface;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $stateArray = $this->countryFactory->create()->setId('US')->getLoadedRegionCollection()->toOptionArray();

        // Remove 'Please select state' option from array
        unset($stateArray[0]);
        $allStateIndices = array_column($stateArray, 'value');
        $enabledStateIndices = explode(',',$this->scopeConfigInterface->getValue('general/region/limit_states'));

        // Sets all states to false
        foreach ($allStateIndices as $stateIndex) {
            $state = $this->stateFactory->create();
            $state->load($stateArray[$stateIndex]['value'])->addData(
                array(
                    'name' => $stateArray[$stateIndex]['title'],
                    'state_enabled' => false
                ));
            try {
                $state->save();
            } catch (\Exception $e) {
                // TODO: handle exception
            }
        }

        // Sets all selected state_allowed values to true
        foreach ($enabledStateIndices as $stateIndex) {
            $state = $this->stateFactory->create();
            $state->load($stateArray[$stateIndex]['value'])->addData(
                array(
                    'name' => $stateArray[$stateIndex]['title'],
                    'state_enabled' => true
                ));
            try {
                $state->save();
            } catch (\Exception $e) {
                // TODO: handle exception
            }
        }
    }
}