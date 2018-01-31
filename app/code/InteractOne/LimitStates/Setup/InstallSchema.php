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
                $installer->getTable('interactone_limit_states')
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
                'state_enabled',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                [],
                'State enabled'
            );
            $installer->getConnection()->createTable($table);
        } catch (\Zend_Db_Exception $e) {
            \Monolog\Handler\error_log("Error in creating table, interactone_limit_states.");
        }
        $installer->endSetup();
    }
}