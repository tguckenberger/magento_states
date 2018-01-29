<?php

namespace InteractOne\LimitStates\Model\ResourceModel\Region;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\App\State;
use Magento\Directory\Model\ResourceModel\Region\Collection;

class newCollection extends Collection
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
     * @var AllowedCountries
     */
    protected  $scopeConfigInterface;
    protected $stateFactory;
    protected  $countryFactory;
    private $allowedCountriesReader;


    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolverAdmin,
        \InteractOne\LimitStates\Model\StateFactory $stateFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null

    ) {
        $this->_localeResolverAdmin = $localeResolverAdmin;
        $this->stateFactory = $stateFactory;
        $this->_resource = $resource;
        $this->countryFactory = $countryFactory;
        $this->scopeConfigInterface = $scopeConfigInterface;


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
        $this->_regionNameTable = $this->getTable('directory_country_region_name_io');

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
            ['rname' => $this->_regionNameTable],
            'main_table.region_id = rname.region_id AND rname.locale = :region_locale',
            ['name']
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
            $this->addFieldToFilter('main_table.country_id', ['in' => $allowedCountries]);
        }

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
                $this->addFieldToFilter('main_table.country_id', ['in' => $countryId]);
            } else {
                $this->addFieldToFilter('main_table.country_id', $countryId);
            }
        }


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
                $this->addFieldToFilter('main_table.code', ['in' => $regionCode]);
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
                $this->addFieldToFilter('main_table.default_name', ['in' => $regionName]);
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
            $condition = is_array($region) ? ['in' => $region] : $region;
            $this->addFieldToFilter(
                ['main_table.code', 'main_table.default_name'],
                [$condition, $condition]
            );
        }
        return $this;
    }

    public function addUSRegionNameFilter($regionName){

        if (!empty($regionName)) {
            if (is_array($regionName)) {
                $this->addFieldToFilter(array('main_table.default_name', 'main_table.country_id'), array(
                    array('in' => $regionName),
                    array('neq' => 'US')
                ));
            } else {
                $this->addFieldToFilter(array('main_table.default_name', 'main_table.country_id'), array(
                    array('eq' => $regionName),
                    array('neq' => 'US')
                ));
            }
        }
        return $this;



    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $propertyMap = [
            'value' => 'region_id',
            'title' => 'default_name',
            'country_id' => 'country_id',
        ];

        foreach ($this as $item) {
            $option = [];
            foreach ($propertyMap as $code => $field) {
                $option[$code] = $item->getData($field);
            }
            $option['label'] = $item->getName();
            $options[] = $option;
        }

        if (count($options) > 0) {
            array_unshift(
                $options,
                ['title' => '', 'value' => '', 'label' => __('Please select a region, state or province.')]
            );
        }
        return $options;
    }
}
