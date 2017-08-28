<?php
/**
 * Menu
 * 
 * @author Slava Yurthev
 */
namespace SY\Menu\Model;

use Magento\Framework\Model\AbstractModel;

class Item extends AbstractModel {
	private $childs;
	public function __construct(
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry
		){
		parent::__construct($context, $registry);
	}
	protected function _construct() {
		$this->_init('SY\Menu\Model\ResourceModel\Item');
	}
	public function getChilds(){
		if(!$this->childs){
			$this->childs = $this->getCollection()
				->addFieldToFilter('parent', $this->getData('id'))
				->addFieldToFilter('active', true)
				->setOrder('sort', 'asc');
		}
		return $this->childs;
	}
	public function getChildsCount(){
		return $this->getChilds()->count();
	}
}