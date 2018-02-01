<?php
/**
 * @author InteractOne Devs
 * @copyright Copyright (c) 2018 InteractOne
 * @package InteractOne\LimitStates\Observer
 */
namespace InteractOne\LimitStates\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Action\Context;
use InteractOne\LimitStates\Model\StateFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

/**
 * Class SaveStates
 * @package InteractOne\LimitStates\Observer
 */
class SaveStates implements ObserverInterface
{

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \InteractOne\LimitStates\Model\StateFactory
     */
    protected $stateFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $cacheFrontendPool;


    /**
     * SaveStates constructor.
     * @param Context $context
     * @param StateFactory $stateFactory
     * @param CountryFactory $countryFactory
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param TypeListInterface $cacheTypeList
     * @param Pool $cacheFrontendPool
     */
    public function __construct(
        Context $context,
        StateFactory $stateFactory,
        CountryFactory $countryFactory,
        ScopeConfigInterface $scopeConfigInterface,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool
    ) 
    {
        $this->countryFactory = $countryFactory;
        $this->stateFactory = $stateFactory;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
    }

    /**
     * execute() - Writes to interactone_limit_states table, cleans config and Full Page cache
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $stateArray = $this->countryFactory->create()->setId('US')->getLoadedRegionCollection()->toOptionArray();
        // Remove 'Please select state' option from array
        unset($stateArray[0]);
        $allStateIndices = array_column($stateArray, 'value');
        $enabledStateIndices = explode(',', $this->scopeConfigInterface->getValue('general/region/limit_states'));

        // Sets all states to false
        foreach ($allStateIndices as $stateIndex) {
            $state = $this->stateFactory->create();
            $state->load($stateArray[$stateIndex]['value'])->addData(
                array(
                    'name' => $stateArray[$stateIndex]['title'],
                    'state_enabled' => false
                )
            );
            try {
                $state->save();
            } catch (\Exception $e) {
                \Monolog\Handler\error_log("Unable to Save");
            }
        }

        // Sets all selected state_allowed values to true
        foreach ($enabledStateIndices as $stateIndex) {
            $state = $this->stateFactory->create();
            $state->load($stateArray[$stateIndex]['value'])->addData(
                array(
                    'name' => $stateArray[$stateIndex]['title'],
                    'state_enabled' => true
                )
            );
            try {
                $state->save();
            } catch (\Exception $e) {
                \Monolog\Handler\error_log("Unable to Save");
            }
        }

        // Get
        $types = array(
            'config',
            'full_page',
        );
        foreach ($types as $type) {
            $this->cacheTypeList->cleanType($type);
        }

        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }
}
