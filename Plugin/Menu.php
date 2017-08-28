<?php
/**
 * Menu
 * 
 * @author Slava Yurthev
 */
namespace SY\Menu\Plugin;

use Magento\Framework\Data\Tree\NodeFactory;
use SY\Menu\Model\Item;

class Menu {
	protected $nodeFactory;
	protected $beforeCollection;
	protected $afterCollection;
	protected $aroundBeforeCollection;
	protected $aroundAfterCollection;
	protected $scopeConfig;
	public function __construct(
			NodeFactory $nodeFactory,
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			Item $model
		){
		$this->nodeFactory = $nodeFactory;
		$this->scopeConfig = $scopeConfig;
		if($this->scopeConfig->getValue(
			'sy_menu/general/active', 
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
			) == "1"){
			$this->beforeCollection = $model->getCollection()
				->addFieldToFilter('active', true)
				->addFieldToFilter('position', 0)
				->addFieldToFilter('category', 0)
				->addFieldToFilter('parent', 0)
				->setOrder('sort', 'asc');
			$this->afterCollection = $model->getCollection()
				->addFieldToFilter('active', true)
				->addFieldToFilter('position', 1)
				->addFieldToFilter('category', 0)
				->addFieldToFilter('parent', 0)
				->setOrder('sort', 'asc');
			$this->aroundBeforeCollection = $model->getCollection()
				->addFieldToFilter('active', true)
				->addFieldToFilter('position', 0)
				->addFieldToFilter('category', ['gt'=>0])
				->addFieldToFilter('parent', false)
				->setOrder('sort', 'asc');
			$this->aroundAfterCollection = $model->getCollection()
				->addFieldToFilter('active', true)
				->addFieldToFilter('position', 1)
				->addFieldToFilter('category', ['gt'=>0])
				->addFieldToFilter('parent', false)
				->setOrder('sort', 'asc');
		}
	}
	public function beforeGetHtml(
			\Magento\Theme\Block\Html\Topmenu $subject,
			$outermostClass = '', 
			$childrenWrapClass = '', 
			$limit = 0
		){
		if($this->scopeConfig->getValue(
			'sy_menu/general/active', 
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
			) == "1"){
			if($this->beforeCollection->count()>0){
				$childNodes = $subject->getMenu()->getAllChildNodes();
				if(count($childNodes)>0){
					foreach ($childNodes as $childNode) {
						$subject->getMenu()->removeChild($childNode);
					}
				}
				foreach ($this->beforeCollection as $item) {
					$node = $this->nodeFactory->create(
						[
							'data' => [
								'name' => $item->getData('label'),
								'id' => 'sy-menu-'.$item->getData('id'),
								'url' => $item->getData('url'),
								'has_active' => false,
								'is_active' => false
							],
							'idField' => 'id',
							'tree' => $subject->getMenu()->getTree()
						]
					);
					$this->appendChild($subject, $item, $node);
					$subject->getMenu()->addChild($node);
				}
				if(count($childNodes)>0){
					foreach ($childNodes as $childNode) {
						$subject->getMenu()->addChild($childNode);
					}
				}
			}
			if($this->aroundBeforeCollection->count()>0 || $this->aroundAfterCollection->count()>0){
				$childNodes = $subject->getMenu()->getAllChildNodes();
				if(count($childNodes)>0){
					foreach ($childNodes as $childNode) {
						$subject->getMenu()->removeChild($childNode);
					}
					foreach ($childNodes as $childNode) {
						if($this->aroundBeforeCollection->count()>0){
							foreach ($this->aroundBeforeCollection as $item) {
								if('category-node-'.$item->getData('category') == $childNode->getData('id')){
									$node = $this->nodeFactory->create(
										[
											'data' => [
												'name' => $item->getData('label'),
												'id' => 'sy-menu-'.$item->getData('id'),
												'url' => $item->getData('url'),
												'has_active' => false,
												'is_active' => false
											],
											'idField' => 'id',
											'tree' => $subject->getMenu()->getTree()
										]
									);
									$this->appendChild($subject, $item, $node);
									$subject->getMenu()->addChild($node);
								}
							}
						}
						$subject->getMenu()->addChild($childNode);
						if($this->aroundAfterCollection->count()>0){
							foreach ($this->aroundAfterCollection as $item) {
								if('category-node-'.$item->getData('category') == $childNode->getData('id')){
									$node = $this->nodeFactory->create(
										[
											'data' => [
												'name' => $item->getData('label'),
												'id' => 'sy-menu-'.$item->getData('id'),
												'url' => $item->getData('url'),
												'has_active' => false,
												'is_active' => false
											],
											'idField' => 'id',
											'tree' => $subject->getMenu()->getTree()
										]
									);
									$this->appendChild($subject, $item, $node);
									$subject->getMenu()->addChild($node);
								}
							}
						}
					}
				}
			}
			if($this->afterCollection->count()>0){
				foreach ($this->afterCollection as $item) {
					$node = $this->nodeFactory->create(
						[
							'data' => [
								'name' => $item->getData('label'),
								'id' => 'sy-menu-'.$item->getData('id'),
								'url' => $item->getData('url'),
								'has_active' => false,
								'is_active' => false
							],
							'idField' => 'id',
							'tree' => $subject->getMenu()->getTree()
						]
					);
					$this->appendChild($subject, $item, $node);
					$subject->getMenu()->addChild($node);
				}
			}
		}
	}
	public function appendChild($subject, $item, $node){
		if($item->getChildsCount()>0){
			foreach ($item->getChilds() as $childItem) {
				$childNode = $this->nodeFactory->create(
					[
						'data' => [
							'name' => $childItem->getData('label'),
							'id' => $node->getData('id').'-'.$childItem->getData('id'),
							'url' => $childItem->getData('url'),
							'has_active' => false,
							'is_active' => false
						],
						'idField' => 'id',
						'tree' => $subject->getMenu()->getTree()
					]
				);
				$this->appendChild($subject, $childItem, $childNode);
				$node->addChild($childNode);
			}
		}
	}
}