<?php

namespace InteractOne\LimitStates\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

//    const XML_PATH_ENABLED = 'InteractOne/basic/enabled';
    const XML_PATH_ENABLED = 'general/region/limit_states';

    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
