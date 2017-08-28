<?php
/**
 * Menu
 * 
 * @author Slava Yurthev
 */
namespace SY\Menu\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;

class ParentItem extends \Magento\Ui\Component\Listing\Columns\Column
{
	protected $storeManager;
	private $_objectManager;
	public function __construct(
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		StoreManagerInterface $storeManager,
		\Magento\Framework\ObjectManagerInterface $objectmanager,
		array $components = [],
		array $data = []
	) {
		$this->_objectManager = $objectmanager;
		$this->storeManager = $storeManager;
		parent::__construct($context, $uiComponentFactory, $components, $data);
	}
	public function prepareDataSource(array $dataSource) {
		if(isset($dataSource['data']['items'])) {
			foreach($dataSource['data']['items'] as & $item) {
				if($item && isset($item['parent']) && $item['parent'] > 0) {
					$_item = $this->_objectManager->get('SY\Menu\Model\Item')->load($item['parent']);
					if($_item->getId()){
						$path = $_item->getData('label');
						$parentId = $_item->getData('parent');
						if($parentId>0){
							do {
								$_parent = $this->_objectManager->get('SY\Menu\Model\Item')->load($parentId);
								$path = $_parent->getData('label').' / '.$path;
								$parentId = $_parent->getData('parent');
							} while ($parentId>0);
						}
						$item['parent'] = $path;
					}
					else{
						$item['parent'] = __('Not Exists');
					}
				}
				else{
					$item['parent'] = NULL;
				}
			}
		}
		return $dataSource;
	}
}