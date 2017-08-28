<?php
/**
 * Menu
 * 
 * @author Slava Yurthev
 */
namespace SY\Menu\Block\Adminhtml\Items\Edit\Tab;
 
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
 
class General extends Generic implements TabInterface {
	protected $_wysiwygConfig;
	protected $_newsStatus;
	private $_objectManager;
	public function __construct(
		Context $context,
		Registry $registry,
		FormFactory $formFactory,
		Config $wysiwygConfig,
		\Magento\Framework\ObjectManagerInterface $objectmanager,
		array $data = []
	) {
		$this->_wysiwygConfig = $wysiwygConfig;
		$this->_objectManager = $objectmanager;
		parent::__construct($context, $registry, $formFactory, $data);
	}
	protected function _prepareForm(){
		$model = $this->_coreRegistry->registry('sy_menu_item');
		$form = $this->_formFactory->create();
 
		$fieldset = $form->addFieldset(
			'base_fieldset',
			['legend' => __('General')]
		);
 
		if ($model->getId()) {
			$fieldset->addField(
				'id',
				'hidden',
				['name' => 'id']
			);
		}
		$fieldset->addField(
			'label',
			'text',
			[
				'name' => 'label',
				'label'	=> __('Label'),
				'required' => true
			]
		);
		$fieldset->addField(
			'url',
			'text',
			[
				'name' => 'url',
				'label'	=> __('Url'),
				'required' => true
			]
		);
		$fieldset->addField(
			'active',
			'select',
			[
				'name' => 'active',
				'label'	=> __('Active'),
				'required' => true,
				'values' => [
					['value'=>"1",'label'=>__('Yes')],
					['value'=>"0",'label'=>__('No')]
				]
			]
		);
		if(!$model->getData('sort')){
			$model->setData('sort', "0");
		}
		$fieldset->addField(
			'sort',
			'text',
			[
				'name' => 'sort',
				'label'	=> __('Sort'),
				'required' => true
			]
		);
		$fieldset->addField(
			'position',
			'select',
			[
				'name' => 'position',
				'label'	=> __('Position'),
				'required' => true,
				'values' => [
					['value'=>"0",'label'=>__('Before')],
					['value'=>"1",'label'=>__('After')]
				],
				'note' => __('Relative By Categories')
			]
		);
		$categories = [['value'=>false,'label'=>__('None')]];
		$collection = $this->_objectManager->get('Magento\Catalog\Model\Category')->getCollection()
			->addFieldToSelect(['name', 'url_key'])
			->addFieldToFilter('level', 2);
		if($collection->count()>0){
			foreach ($collection as $category) {
				$categories[] = [
					'value'=>$category->getData('entity_id'),
					'label'=>$category->getData('name')
				];
			}
		}
		$fieldset->addField(
			'category',
			'select',
			[
				'name' => 'category',
				'label'	=> __('Category'),
				'required' => false,
				'values' => $categories
			]
		);
		$_items = $this->_objectManager->get('SY\Menu\Model\Item')->getCollection()
			->addFieldToSelect(['id', 'label'])
			->addFieldToFilter('id', ['neq'=>$model->getData('id')])
			->addFieldToFilter('active', true)
			->addFieldToFilter('parent', 0);
		$options = [['value'=>false,'label'=>__('None')]];
		if($_items->count()>0){
			foreach ($_items as $_item) {
				$options = $this->appendOption($options, $_item);
			}
		}
		$fieldset->addField(
			'parent',
			'select',
			[
				'name' => 'parent',
				'label'	=> __('Parent'),
				'required' => false,
				'values' => $options
			]
		);
		$data = $model->getData();
		$form->setValues($data);
		$this->setForm($form);
 
		return parent::_prepareForm();
	}
	public function getTabLabel(){
		return __('Item');
	}
	public function getTabTitle(){
		return __('Item');
	}
	public function canShowTab(){
		return true;
	}
	public function isHidden(){
		return false;
	}
	public function appendOption($options, $_item, $level = 0){
		$options[] = [
			'value'=>$_item->getData('id'),
			'label'=>str_repeat('-', $level).$_item->getData('label')
		];
		if($_item->getChildsCount()>0){
			$level++;
			foreach ($_item->getChilds() as $childItem) {
				$options = $this->appendOption($options, $childItem, $level);
			}
		}
		return $options;
	}
}