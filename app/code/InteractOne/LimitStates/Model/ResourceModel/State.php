<?php
/**
 * @author InteractOne Devs
 * @copyright Copyright (c) 2018 InteractOne
 * @package InteractOne\LimitStates\Model\ResourceModel
 */
namespace InteractOne\LimitStates\Model\ResourceModel;

/**
 * Class State
 * @package InteractOne\LimitStates\Model\ResourceModel
 */
class State extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('interactone_limit_states', 'state_id');
    }
}
