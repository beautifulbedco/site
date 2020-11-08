<?php
/**
 * Copyright © 2017 Sam Granger. All rights reserved.
 *
 * @author Sam Granger <sam.granger@gmail.com>
 */

namespace MGS\ThemeSettings\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\Page\Config;
use Magento\Store\Model\StoreManagerInterface;


class ProcessThemeSettingConfig implements ObserverInterface
{
    protected $config;
    protected $storeManager;
    protected $scopeConfig;
	protected $_request;
	protected $_registry;


    public function __construct(
        \Magento\Framework\View\Page\Config $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\App\Request\Http $request
    ){
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
		
		$this->_request = $request;
		$this->_registry = $registry;
    }

    public function execute(Observer $observer){
        $storeId = $this->storeManager->getStore()->getId();
		$configLayout = $this->getStoreConfig('themesettings/general/layout', $storeId);
		$width = $this->getStoreConfig('themesettings/general/width', $storeId);
		$parallaxFooter = $this->getStoreConfig('themesettings/footer/parallax', $storeId);

		$this->config->addBodyClass($width);

        $this->config->addBodyClass($configLayout);
		
		if($parallaxFooter){
			$this->config->addBodyClass('parallax-footer');
		}
		
		
		$layout = $observer->getLayout();
		$fullActionName = $this->_request->getFullActionName();
		
		if($this->getStoreConfig('themesettings/header/header_version', $storeId) == 'header7'){
			$this->config->addBodyClass('left-side');
		}

		if($this->getStoreConfig('themesettings/header/header_version', $storeId) == 'header13'){
			$useragent=$_SERVER['HTTP_USER_AGENT'];
    		if(!preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
				$this->config->addBodyClass('leftnav-page');
			}
		}
		
		if($this->getStoreConfig('themesettings/header/header_absolute', $storeId)){
			$this->config->addBodyClass('header_absolute');
		}
		
		/* Catalog Search Page */
		if($fullActionName=='catalogsearch_result_index'){
			$searchPageLayout = $this->getStoreConfig('themesettings/catalog_search/layout', $storeId);
			if($searchPageLayout!=''){
				$layout->getUpdate()->addHandle($searchPageLayout);
			}
		}
		
		/* Category Page */
		if($fullActionName=='catalog_category_view'){
			$category = $this->_registry->registry('current_category');
			if($category->getFullWidth()){
				$this->config->addBodyClass('category-fullwidth');
			}
			
			$_categoryLayout = $category->getPageLayout();
			$categoryPageLayout = $this->getStoreConfig('themesettings/category/layout', $storeId);
			if(($_categoryLayout=='') && ($categoryPageLayout!='') && (!$category->getIsLanding())){
				$layout->getUpdate()->addHandle($categoryPageLayout);
			}
			
			if($category->getIsLanding()){
				$this->config->addBodyClass('category-landing');
				$this->config->addBodyClass('landing-'.$category->getCateLandingType());
				$layout->getUpdate()->addHandle('themesetting_onecolumn_empty_custom');
			}
		}
		
		/* Product Details Page */
		if($fullActionName=='catalog_product_view'){
			$product = $this->_registry->registry('current_product');
			// Page Layout
			$_productLayout = $product->getPageLayout();
			$productPageLayout = $this->getStoreConfig('themesettings/product_details/layout', $storeId);
			if(($_productLayout=='') && ($productPageLayout!='')){
				$layout->getUpdate()->addHandle($productPageLayout);
			}
			
			// Template
			$template = $this->getStoreConfig('themesettings/product_details/default_template', $storeId);
			if($product->getMgsTemplate() && $product->getMgsTemplate()!=''){
				$template = $product->getMgsTemplate();
			}
			$layout->getUpdate()->addHandle($template);
		}
		
		/* Contact Us Page */
		if($fullActionName=='contact_index_index'){
			// Page Layout
			$pageLayout = $this->getStoreConfig('themesettings/contact/layout', $storeId);
			if($pageLayout!=''){
				$layout->getUpdate()->addHandle($pageLayout);
			}
		}
        
    }
	
	public function getStoreConfig($node, $storeId = NULL){
		return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
	}
}