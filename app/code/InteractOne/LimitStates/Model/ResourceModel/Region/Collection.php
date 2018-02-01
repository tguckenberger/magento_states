<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * @author InteractOne Devs
 * function - addUSRegionNameFilter()
 * @copyright Copyright (c) 2018 InteractOne
 * @package InteractOne\LimitStates\Model\ResourceModel\Region
 */


namespace InteractOne\LimitStates\Model\ResourceModel\Region;

use InteractOne\LimitStates\Model\State;
use InteractOne\LimitStates\Model\StateFactory;
use Magento\Directory\Model\AllowedCountries;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Regions collection
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @api
 * @since 100.0.2
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Locale region name table name
     *
     * @var string
     */
    protected $_regionNameTable;

    /**
     * Country table name
     *
     * @var string
     */
    protected $_countryTable;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var \Magento\Framework\Config\ScopeInterface
     */
    protected $scope;

    /**
     * @var CountryFactory
     */
    protected $countryFactory;

    /**
     * @var StateFactory
     */
    protected $stateFactory;

    /**
     * @var AllowedCountries
     */
    private $allowedCountriesReader;

    /**
     * Collection constructor.
     * @param EntityFactory $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param ResolverInterface $localeResolver
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param \Magento\Framework\Config\ScopeInterface $scope
     * @param CountryFactory $countryFactory
     * @param StateFactory $stateFactory
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        ResolverInterface $localeResolver,
        ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\Config\ScopeInterface $scope,
        CountryFactory $countryFactory,
        StateFactory $stateFactory,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) 
{
        $this->_localeResolver = $localeResolver;
        $this->_resource = $resource;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->scope = $scope;
        $this->countryFactory = $countryFactory;
        $this->stateFactory = $stateFactory;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Define main, country, locale region name tables
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magento\Directory\Model\Region::class, \Magento\Directory\Model\ResourceModel\Region::class);

        $this->_countryTable = $this->getTable('directory_country');
        $this->_regionNameTable = $this->getTable('directory_country_region_name');

        $this->addOrder('name', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
        $this->addOrder('default_name', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
    }

    /**
     * Initialize select object
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $locale = $this->_localeResolver->getLocale();

        $this->addBindParam(':region_locale', $locale);
        $this->getSelect()->joinLeft(
            array('rname' => $this->_regionNameTable),
            'main_table.region_id = rname.region_id AND rname.locale = :region_locale',
            array('name')
        );

        return $this;
    }

    /**
     * Return Allowed Countries reader
     *
     * @return \Magento\Directory\Model\AllowedCountries
     * @deprecated 100.1.4
     */
    private function getAllowedCountriesReader()
    {
        if (!$this->allowedCountriesReader) {
            $this->allowedCountriesReader = ObjectManager::getInstance()->get(AllowedCountries::class);
        }

        return $this->allowedCountriesReader;
    }

    /**
     * Set allowed countries filter based on the given store.
     * This is a convenience method for collection filtering based on store configuration settings.
     *
     * @param null|int|string|\Magento\Store\Model\Store $store
     * @return \Magento\Directory\Model\ResourceModel\Region\Collection
     * @since 100.1.4
     */
    public function addAllowedCountriesFilter($store = null)
    {
        $allowedCountries = $this->getAllowedCountriesReader()
            ->getAllowedCountries(ScopeInterface::SCOPE_STORE, $store);

        if (!empty($allowedCountries)) {
            $this->addFieldToFilter('main_table.country_id', array('in' => $allowedCountries));
        }

        $this->addUSRegionNameFilter();
        return $this;
    }

    /**
     * Filter by country_id
     *
     * @param string|array $countryId
     * @return $this
     */
    public function addCountryFilter($countryId)
    {
        if (!empty($countryId)) {
            if (is_array($countryId)) {
                $this->addFieldToFilter('main_table.country_id', array('in' => $countryId));
            } else {
                $this->addFieldToFilter('main_table.country_id', $countryId);
            }
        }

        $this->addUSRegionNameFilter();
        return $this;
    }

    /**
     * Filter by country code (ISO 3)
     *
     * @param string $countryCode
     * @return $this
     */
    public function addCountryCodeFilter($countryCode)
    {
        $this->getSelect()->joinLeft(
            array('country' => $this->_countryTable),
            'main_table.country_id = country.country_id'
        )->where(
            'country.iso3_code = ?',
            $countryCode
        );
        $this->addUSRegionNameFilter();
        return $this;
    }

    /**
     * Filter by Region code
     *
     * @param string|array $regionCode
     * @return $this
     */
    public function addRegionCodeFilter($regionCode)
    {
        if (!empty($regionCode)) {
            if (is_array($regionCode)) {
                $this->addFieldToFilter('main_table.code', array('in' => $regionCode));
            } else {
                $this->addFieldToFilter('main_table.code', $regionCode);
            }
        }

        return $this;
    }

    /**
     * Filter by region name
     *
     * @param string|array $regionName
     * @return $this
     */
    public function addRegionNameFilter($regionName)
    {
        if (!empty($regionName)) {
            if (is_array($regionName)) {
                $this->addFieldToFilter('main_table.default_name', array('in' => $regionName));
            } else {
                $this->addFieldToFilter('main_table.default_name', $regionName);
            }
        }

        return $this;
    }

    /**
     * Filter region by its code or name
     *
     * @param string|array $region
     * @return $this
     */
    public function addRegionCodeOrNameFilter($region)
    {
        if (!empty($region)) {
            $condition = is_array($region) ? array('in' => $region) : $region;
            $this->addFieldToFilter(
                array('main_table.code', 'main_table.default_name'),
                array($condition, $condition)
            );
        }

        return $this;
    }

    /**
     * Filter US Regions by allowed
     *
     * @return $this
     */
    public function addUSRegionNameFilter()
    {
        // Only filter if scope is frontend
        if ($this->scope->getCurrentScope() != \Magento\Framework\App\Area::AREA_ADMINHTML) {
            $state = $this->stateFactory->create();
            $stateCollection = $state->getCollection();
            $stateCollection->addFieldToFilter(
                'main_table.state_enabled',
                array('eq' => true)
            );
            $enabledStates = $stateCollection->getColumnValues('name');

            if (!empty($enabledStates)) {
                if (is_array($enabledStates)) {
                    $this->addFieldToFilter(
                        array('main_table.default_name', 'main_table.country_id'), array(
                        array('in' => $enabledStates),
                        array('neq' => 'US')
                        )
                    );
                } else {
                    $this->addFieldToFilter(
                        array('main_table.default_name', 'main_table.country_id'), array(
                        array('eq' => $enabledStates),
                        array('neq' => 'US')
                        )
                    );
                }
            }

            return $this;
        }
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $propertyMap = array(
            'value' => 'region_id',
            'title' => 'default_name',
            'country_id' => 'country_id',
        );

        foreach ($this as $item) {
            $option = array();
            foreach ($propertyMap as $code => $field) {
                $option[$code] = $item->getData($field);
            }

            $option['label'] = $item->getName();
            $options[] = $option;
        }

        if (count($options) > 0) {
            array_unshift(
                $options,
                array('title' => '', 'value' => '', 'label' => __('Please select a region, state or province.'))
            );
        }

        return $options;
    }
}
