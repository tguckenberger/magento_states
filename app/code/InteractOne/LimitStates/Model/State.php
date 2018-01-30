<?php

namespace InteractOne\LimitStates\Model;

class State extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'interactone_limit_states';
    protected $_cacheTag = 'interactone_limit_states';
    protected $_eventPrefix = 'interactone_limit_states';
    protected function _construct()
    {
        $this->_init('InteractOne\LimitStates\Model\ResourceModel\State');
    }
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }
}