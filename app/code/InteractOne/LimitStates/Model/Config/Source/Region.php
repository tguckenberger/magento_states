<?php

namespace InteractOne\LimitStates\Model\Config\Source;

use InteractOne\LimitStates\Model\newCountryFactory;

class Region implements \Magento\Framework\Option\ArrayInterface {

    protected $_country;

    public function __construct(newCountryFactory $newCountryFactory) {
        $this->_country = $newCountryFactory;
    }

    // Returns an array of states, via country code US.
    public function toOptionArray() {
            $stateArray = $this->_country->create()->setID('US')->getLoadedRegionCollection()->toOptionArray();
            $stateArray = array_slice($stateArray, 1, count($stateArray)-1);
            return $stateArray;
    }

}
