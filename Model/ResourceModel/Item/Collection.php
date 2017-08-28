<?php
/**
 * Menu
 * 
 * @author Slava Yurthev
 */
namespace SY\Menu\Model\ResourceModel\Item;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection {
	protected function _construct() {
		$this->_init(
			'SY\Menu\Model\Item',
			'SY\Menu\Model\ResourceModel\Item'
		);
	}
}