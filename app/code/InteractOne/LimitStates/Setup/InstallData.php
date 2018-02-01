<?php
/**
 * @author InteractOne Devs
 * @copyright Copyright (c) 2018 InteractOne
 * @package InteractOne\LimitStates\Setup
 */
namespace InteractOne\LimitStates\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Directory\Helper\Data;
use InteractOne\LimitStates\Model\State;
use Magento\Directory\Model\CountryFactory;


/**
 * Class InstallData
 * @package InteractOne\LimitStates\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \InteractOne\LimitStates\Model\State
     */
    protected $stateFactory;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var Data
     */
    private $directoryData;

    /**
     * InstallData constructor.
     * @param \InteractOne\LimitStates\Model\State $stateFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param Data $directoryData
     */
    public function __construct(
        State $stateFactory,
        CountryFactory $countryFactory,
        Data $directoryData
    ) 
    {
        $this->stateFactory = $stateFactory;
        $this->countryFactory = $countryFactory;
        $this->directoryData = $directoryData;
    }

    /**
     * install() - Installs Data into interactone_limit_states table
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $stateArray = $this->countryFactory->create()->setId('US')->getLoadedRegionCollection()->toOptionArray();
        unset($stateArray[0]);
        foreach ($stateArray as $stateRef) {
            $state = $this->stateFactory;
            $state->setData(
                array(
                'name' => $stateRef['title'],
                'state_enabled' => false
                )
            );
            try {
                $state->save();
            } catch (\Exception $e) {
                \Monolog\Handler\error_log("Unable to Save");
            }
        }
    }
}
