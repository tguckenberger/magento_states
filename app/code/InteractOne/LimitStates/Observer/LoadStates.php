<?php

namespace InteractOne\LimitStates\Observer;

use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\Event\ObserverInterface;

class LoadStates extends AbstractAddress implements ObserverInterface {

    protected  $countryFactory;
    protected $stateFactory;
    protected  $scopeConfigInterface;




    public function __construct(

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





        $form = $observer->getEvent()->getForm();
//        $event = $observer->getEvent()->getData();
//        $event_data_array  =  ['country' => $event];
//        $this->_eventManager->dispatch('states_load', $event_data_array);
        //$event = $observer->getEvent()->getName();
        // TODO: Write foreach loops to compare core states db to custom states db
        //$allowedRegions = $this->scopeConfigInterface->getValue('general/region/limit_states', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        //var_dump($allowedRegions);
//        if ($event) {
//            print_r('howdy');
//        }else{
//            var_dump($event);
//        }

//        foreach ($event as $item => $value) {
//            if ($item = 'name'){
//                echo "$item=$value<br />";
//            }else{
//                echo'howdy';
//            }
//
//        }
        //print_r($event);






        //$customerObject = $observer->getData()->getCustomer()->getCustomerAddress();
        //$customer = $customerObject->getCountryId();

        //$selectedCountryName = $customerObject;
        //print_r($selectedCountryName);


        //echo $selectedCountryName;

//
//        $stateArray = $this->countryFactory->create()->setId('US')->getLoadedRegionCollection()->toOptionArray();
//        unset($stateArray[0]);
//        $ID = array_column($stateArray, 'value');
//        // Get list of saved states from interactone_states
//        foreach ($ID as $stateAllowed) {
//            $state = $this->stateFactory->create();
//            $val[] = $state->load($stateArray[$stateAllowed]['value'])->getData('state_allowed');
//        }
        // Filter only states that are true
//        $allowedStates=(array_filter($val));
        //print_r($allowedStates);

        // TODO: Intercept get and set methods for region


    }


}