<?php
/**
 * Created by PhpStorm.
 * User: tristanguckenberger
 * Date: 1/26/18
 * Time: 4:52 PM
 */

namespace InteractOne\LimitStates\Model\ResourceModel\Region;

use InteractOne\LimitStates\Model\ResourceModel\State\Collection;
use Magento\Directory\Model\AllowedCountries;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;

class newCollectionAdmin extends Collection
{
    /**
     * Locale region name table name
     *
     * @var string
     */
    protected $_regionNameTableAdmin;

    /**
     * Country table name
     *
     * @var string
     */
    protected $_countryTableAdmin;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolverAdmin;
    protected $stateFactory;
    protected  $scopeConfigInterface;
    protected $scope;

    /**
     * @var AllowedCountries
     */
    private $allowedCountriesReaderAdmin;


    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param mixed $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactoryAdmin,
        \Psr\Log\LoggerInterface $loggerAdmin,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategyAdmin,
        \Magento\Framework\Event\ManagerInterface $eventManagerAdmin,
        \Magento\Framework\Locale\ResolverInterface $localeResolverAdmin,
        \InteractOne\LimitStates\Model\StateFactory $stateFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\Config\ScopeInterface $scope,
        \Magento\Framework\DB\Adapter\AdapterInterface $connectionAdmin = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resourceAdmin = null
    ) {
        $this->_localeResolverAdmin = $localeResolverAdmin;
        $this->_resource = $resourceAdmin;
        $this->stateFactory = $stateFactory;
        $this->scope = $scope;
        $this->scopeConfigInterface = $scopeConfigInterface;
        parent::__construct($entityFactoryAdmin, $loggerAdmin, $fetchStrategyAdmin, $eventManagerAdmin, $connectionAdmin, $resourceAdmin);
    }

    /**
     * Define main, country, locale region name tables
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magento\Directory\Model\Region::class, \Magento\Directory\Model\ResourceModel\Region::class);

        $this->_countryTableAdmin = $this->getTable('directory_country');
        $this->_regionNameTableAdmin = $this->getTable('directory_country_region_name_io');

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
        $localeAdmin = $this->_localeResolverAdmin->getLocale();

        $this->addBindParam(':region_locale', $localeAdmin);
        $this->getSelect()->joinLeft(
            ['rname' => $this->_regionNameTableAdmin],
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
        if (!$this->allowedCountriesReaderAdmin) {
            $this->allowedCountriesReaderAdmin = ObjectManager::getInstance()->get(AllowedCountries::class);
        }

        return $this->allowedCountriesReaderAdmin;
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
        $stateIndex = explode(',',$this->scopeConfigInterface->getValue('general/region/limit_states'));

                if ($countryId == 'US') {

                    $this->addUSRegionNameFilter($stateIndex);
                }


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
            ['country' => $this->_countryTable],
            'main_table.country_id = country.country_id'
        )->where(
            'country.iso3_code = ?',
            $countryCode
        );

        return $this;
    }

    public function addUSRegionNameFilter($regionName){

//        var_dump($regionName);
        if (!empty($regionName)) {
            print_r("Not Empty");
            if (is_array($regionName)) {
                $this->addFieldToFilter(array('main_table.default_name', 'main_table.country_id'), array(
                    array('in' => $regionName),
                    array('neq' => 'US')

                ));
                print_r("Not Empty first if inner");
            } else {
                $this->addFieldToFilter(array('main_table.default_name', 'main_table.country_id'), array(
                    array('eq' => $regionName),
                    array('neq' => 'US')
                ));
            }
        }
        print_r("End");
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