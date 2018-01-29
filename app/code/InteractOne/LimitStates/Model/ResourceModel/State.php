<?php

namespace InteractOne\LimitStates\Model\ResourceModel;

class State extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }
    protected function _construct()
    {
        $this->_init('interactone_states', 'state_id');
    }
}