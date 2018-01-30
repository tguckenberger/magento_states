<?php

namespace InteractOne\LimitStates\Setup;


use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        try {

            $table = $installer->getConnection()->newTable(
                $installer->getTable('interactone_states')
            )->addColumn(
                'state_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                255,
                ['identity' => true, 'auto_increment' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'State ID'
            )->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'State name'
            )->addColumn(
                'state_allowed',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                [],
                'State allowed'
            )->setComment(
                'Allowed States Table'
            );

            $installer->getConnection()->createTable($table);

            /**
             * Create table 'directory_country_region_IO'
             * Modify Names -
             */
            $table2 = $installer->getConnection()->newTable(
                $installer->getTable('directory_country_region_io')
            )->addColumn(
                'region_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Region Id'
            )->addColumn(
                'country_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                4,
                ['nullable' => false, 'default' => '0'],
                'Country Id in ISO-2'
            )->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => true, 'default' => null],
                'Region code'
            )->addColumn(
                'default_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Region Name'
            )->addIndex(
                $installer->getIdxName('directory_country_region_io', ['country_id']),
                ['country_id']
            )->setComment(
                'Directory Country Region IO'
            );
            $installer->getConnection()->createTable($table2);

            /**
             * Create table 'directory_country_region_name_io'
             */
            $table3 = $installer->getConnection()->newTable(
                $installer->getTable('directory_country_region_name_io')
            )->addColumn(
                'locale',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                8,
                ['nullable' => false, 'primary' => true, 'default' => false],
                'Locale'
            )->addColumn(
                'region_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
                'Region Id'
            )->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => null],
                'Region Name'
            )->addIndex(
                $installer->getIdxName('directory_country_region_name_io', ['region_id']),
                ['region_id']
            )->addForeignKey(
                $installer->getFkName(
                    'directory_country_region_name_io',
                    'region_id',
                    'directory_country_region_io',
                    'region_id'
                ),
                'region_id',
                $installer->getTable('directory_country_region_io'),
                'region_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Directory Country Region Name IO'
            );
            $installer->getConnection()->createTable($table3);
        } catch (\Zend_Db_Exception $e) {
            // handle exception
        }
        $installer->endSetup();
    }
}