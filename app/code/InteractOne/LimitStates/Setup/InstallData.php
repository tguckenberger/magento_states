<?php

namespace InteractOne\LimitStates\Setup;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Directory\Helper\Data;
class InstallData implements InstallDataInterface
{
    protected $stateFactory;
    protected $countryFactory;
    private $directoryData;

    public function __construct(
        \InteractOne\LimitStates\Model\State $stateFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Helper\Data $directoryData
    )
    {
        $this->stateFactory = $stateFactory;
        $this->countryFactory = $countryFactory;
        $this->directoryData = $directoryData;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $stateArray = $this->countryFactory->create()->setId('US')->getLoadedRegionCollection()->toOptionArray();
        unset($stateArray[0]);
        foreach ($stateArray as $stateRef) {
            $state = $this->stateFactory;
            $state->setData(array(
                'name' => $stateRef['title'],
                'state_enabled' => false
            ));
            try {
                $state->save();
            } catch (\Exception $e) {
                // handle exception
            }
        }
        }
}