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

class Category extends \Magento\Ui\Component\Listing\Columns\Column
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
				if($item && isset($item['category']) && $item['category'] > 0) {
					$category = $this->_objectManager->get('Magento\Catalog\Model\Category')->load($item['category']);
					if($category->getId()){
						$item['category'] = $category->getData('name');
					}
					else{
						$item['category'] = __('Not Exists');
					}
				}
				else{
					$item['category'] = NULL;
				}
			}
		}
		return $dataSource;
	}
}