<?php
/**
 * @author InteractOne Devs
 * @copyright Copyright (c) 2018 InteractOne
 * @package InteractOne\LimitStates\Model\Config\Source
 */
namespace InteractOne\LimitStates\Model\Config\Source;

use Magento\Directory\Model\CountryFactory;
use InteractOne\LimitStates\Model\StateFactory;

/**
 * Class Region
 * @package InteractOne\LimitStates\Model\Config\Source
 */
class Region implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var CountryFactory
     */
    protected $_country;

    /**
     * @var \InteractOne\LimitStates\Model\StateFactory
     */
    protected $stateFactory;

    /**
     * Region constructor.
     * @param StateFactory $stateFactory
     * @param CountryFactory $countryFactory
     */
    public function __construct(StateFactory $stateFactory, CountryFactory $countryFactory)
    {
        $this->_country = $countryFactory;
        $this->stateFactory = $stateFactory;
    }

    // Returns an array of states, via country code US.
    // This populates the admin Limit US States drop down in Config\General\StateOptions
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $stateArray = $this->_country->create()->setId('US')->getLoadedRegionCollection()->toOptionArray();
        $stateArray = array_slice($stateArray, 1, count($stateArray)-1);
        return $stateArray;
    }
}
