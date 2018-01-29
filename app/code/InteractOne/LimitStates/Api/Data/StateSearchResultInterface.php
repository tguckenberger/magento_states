<?php
/**
 * Created by PhpStorm.
 * User: tristanguckenberger
 * Date: 1/19/18
 * Time: 10:47 AM
 */

namespace InteractOne\LimitStates\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;


interface StateSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \InteractOne\LimitStates\Api\Data\StateInterface[]
     */
    public function getItems();

    /**
     * @param \InteractOne\LimitStates\Api\Data\StateInterface[] $items
     * @return void
     */
    public function setItems(array $items);

}