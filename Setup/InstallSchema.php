<?php
/**
 * Menu
 * 
 * @author Slava Yurthev
 */
namespace SY\Menu\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface {
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();

		$table = $setup->getConnection()->newTable($setup->getTable('sy_menu'))
			->addColumn(
				'id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				[
					'identity' => true, 
					'unsigned' => true, 
					'nullable' => false, 
					'primary' => true
				],
				'Id'
			)->addColumn(
				'url',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				[
					'nullable' => true
				],
				'Url'
			)->addColumn(
				'label',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				[
					'nullable' => true
				],
				'Label'
			)->addColumn(
				'active',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				1,
				[
					'nullable' => true,
					'default' => '1'
				],
				'Active'
			)->addColumn(
				'sort',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				1,
				[
					'nullable' => true,
					'default' => '0'
				],
				'Sort'
			)->addColumn(
				'position',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				1,
				[
					'nullable' => true,
					'default' => '0'
				],
				'Position'
			)->addColumn(
				'category',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				[
					'nullable' => true
				],
				'Category'
			)->addColumn(
				'parent',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				[
					'nullable' => true
				],
				'Parent'
			)->setComment(
				'Menu Table'
			);
		$setup->getConnection()->createTable($table);

		$setup->endSetup();
	}
}