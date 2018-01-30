<?php

namespace InteractOne\LimitStates\Model\ResourceModel\State;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'state_id';
    protected $_eventPrefix = 'interactone_limit_states_state_collection';
    protected $_eventObject = 'state_collection';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('InteractOne\LimitStates\Model\State', 'InteractOne\LimitStates\Model\ResourceModel\State');
    }
}