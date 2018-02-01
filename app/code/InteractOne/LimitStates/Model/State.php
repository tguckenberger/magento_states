<?php
/**
 * @author InteractOne Devs
 * @copyright Copyright (c) 2018 InteractOne
 * @package InteractOne\LimitStates\Model
 */
namespace InteractOne\LimitStates\Model;

/**
 * Class State
 * @package InteractOne\LimitStates\Model
 */
class State extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'interactone_limit_states';
    protected $_cacheTag = 'interactone_limit_states';
    protected $_eventPrefix = 'interactone_limit_states';

    /**
     *
     */
    protected function _construct()
    {
        //$this->_init('InteractOne\LimitStates\Model\ResourceModel\State');
        $this->_init(\InteractOne\LimitStates\Model\ResourceModel\State::class);
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return array(self::CACHE_TAG . '_' . $this->getId());
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        return [];
    }
}
