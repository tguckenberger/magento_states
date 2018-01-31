<?php

namespace InteractOne\LimitStates\Model\Config\Source;

use Magento\Directory\Model\CountryFactory;

class Region implements \Magento\Framework\Option\ArrayInterface {

    protected $_country;
    protected $stateFactory;

    public function __construct(\InteractOne\LimitStates\Model\StateFactory $stateFactory, CountryFactory $countryFactory) {
        $this->_country = $countryFactory;
        $this->stateFactory = $stateFactory;
    }
    // Returns an array of states, via country code US.
    // This populates the admin Limit US States drop down in Config\General\StateOptions
    public function toOptionArray() {
        $stateArray = $this->_country->create()->setId('US')->getLoadedRegionCollection()->toOptionArray();
        $stateArray = array_slice($stateArray, 1, count($stateArray)-1);
        return $stateArray;
    }

}