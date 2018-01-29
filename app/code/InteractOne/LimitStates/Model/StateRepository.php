<?php
/**
 * Created by PhpStorm.
 * User: tristanguckenberger
 * Date: 1/19/18
 * Time: 11:00 AM
 */

namespace InteractOne\LimitStates\Model;



use Magento\Framework\Exception\NoSuchEntityException;
use InteractOne\LimitStates\Api\Data\StateInterface;
use InteractOne\LimitStates\Api\StateRepositoryInterface;
use InteractOne\LimitStates\Model\ResourceModel\State\Collection as StateCollectionFactory;
use InteractOne\LimitStates\Model\ResourceModel\State\Collection;

class StateRepository implements StateRepositoryInterface
{
    /**
     * @var State
     */
    private $stateFactory;

    /**
     * @var StateCollectionFactory
     */
    private $StateCollectionFactory;

    public function __construct( State $stateFactory, StateCollectionFactory $stateCollectionFactory) {
        $this->stateFactory = $stateFactory;
        $this->stateCollectionFactory = $stateCollectionFactory;
    }
    public function getByBool($state_allowed)
    {
        // TODO: Implement getByBool() method.
    }
    public function save(StateInterface $state)
    {
        // TODO: Implement save() method.
    }
    public function getById($state_id)
    {
        // TODO: Implement getById() method.
    }
}