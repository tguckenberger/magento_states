<?php
/**
 * Created by PhpStorm.
 * User: tristanguckenberger
 * Date: 1/19/18
 * Time: 10:19 AM
 */

namespace InteractOne\LimitStates\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use InteractOne\LimitStates\Api\Data\StateInterface;


interface StateRepositoryInterface
{
    /**
     * @param boolean $state_allowed
     * @return \InteractOne\LimitStates\Api\Data\StateInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByBool($state_allowed);
    /**
     * @param int $state_id
     * @return \InteractOne\LimitStates\Api\Data\StateInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($state_id);
    /**
     * @param \InteractOne\LimitStates\Api\Data\StateInterface $state
     * @return \InteractOne\LimitStates\Api\Data\StateInterface
     */
    public function save(StateInterface $state);
}