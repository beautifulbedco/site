<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\ThemeSettings\Helper\Swatches;

use Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface;
use Magento\Catalog\Api\Data\ProductInterface as Product;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product as ModelProduct;
use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory as SwatchCollectionFactory;
use Magento\Swatches\Model\Swatch;
use Magento\Swatches\Model\SwatchAttributesProvider;
use Magento\Swatches\Model\SwatchAttributeType;
use Magento\Catalog\Helper\Image;
/**
 * Class Helper Data
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Swatches\Helper\Data
{
    /**
     * Method getting full media gallery for current Product
     * Array structure: [
     *  ['image'] => 'http://url/pub/media/catalog/product/2/0/blabla.jpg',
     *  ['mediaGallery'] => [
     *      galleryImageId1 => simpleProductImage1.jpg,
     *      galleryImageId2 => simpleProductImage2.jpg,
     *      ...,
     *      ]
     * ]
     * @param ModelProduct $product
     * @return array
     */
    /**
     * Catalog Image Helper
     *
     * @var Image
     */
    protected $imageHelper;

    public function __construct(
        CollectionFactory $productCollectionFactory,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        SwatchCollectionFactory $swatchCollectionFactory,
        UrlBuilder $urlBuilder,
        Json $serializer = null,
        SwatchAttributesProvider $swatchAttributesProvider = null,
        SwatchAttributeType $swatchTypeChecker = null,
        Image $imageHelper
    ) {
        $this->imageHelper = $imageHelper;
        parent::__construct($productCollectionFactory,
            $productRepository,
            $storeManager,
            $swatchCollectionFactory,
            $urlBuilder,
            $serializer,
            $swatchAttributesProvider,
            $swatchTypeChecker);
    }
    public function getProductMediaGallery(\Magento\Catalog\Model\Product $product):array
    {
        $baseImage = null;
        $gallery = [];

        $mediaGallery = $product->getMediaGalleryEntries();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('Magento\Framework\App\RequestInterface');
        
        foreach ($mediaGallery as $mediaEntry) {
            if ($mediaEntry->isDisabled()) {
                continue;
            }

            if (in_array('image', $mediaEntry->getTypes(), true)) {
                $baseImage = $mediaEntry->getFile();
            } elseif (!$baseImage) {
                $baseImage = $mediaEntry->getFile();
            }

            if($viewmode = $request->getParam('view_mode')){
                $gallery[$mediaEntry->getId()] = $this->getAllSizeImagesCustom($product, $mediaEntry->getFile(), $viewmode);
            }else{
                $gallery[$mediaEntry->getId()] = $this->getAllSizeImages($product, $mediaEntry->getFile());
            }
            
        }

        if (!$baseImage) {
            return [];
        }
        
        if($viewmode = $request->getParam('view_mode')){
            $resultGallery = $this->getAllSizeImagesCustom($product, $baseImage, $viewmode);
        }else{
            $resultGallery = $this->getAllSizeImages($product, $baseImage);
        }

        
        $resultGallery['gallery'] = $gallery;

        return $resultGallery;
    }
    
    /**
     * @param ModelProduct $product
     * @param string $imageFile
     * @return array
     */
    public function getAllSizeImages(\Magento\Catalog\Model\Product $product, $imageFile)
    {
        return [
            'large' => $this->imageHelper->init($product, 'product_page_image_large_no_frame')
                ->setImageFile($imageFile)
                ->getUrl(),
            'medium' => $this->imageHelper->init($product, 'product_page_image_medium_no_frame')
                ->setImageFile($imageFile)
                ->getUrl(),
            'small' => $this->imageHelper->init($product, 'product_page_image_small')
                ->setImageFile($imageFile)
                ->getUrl(),
        ];
    }
    
    /**
     * @param ModelProduct $product
     * @param string $imageFile
     * @return array
     */
    public function getAllSizeImagesCustom(\Magento\Catalog\Model\Product $product, $imageFile, $viewmode)
    {
        switch($viewmode){
            case 'grid':
                return [
                    'large' => $this->imageHelper->init($product, 'category_page_grid_swatches')
                        ->setImageFile($imageFile)
                        ->getUrl(),
                    'medium' => $this->imageHelper->init($product, 'category_page_grid_swatches')
                        ->setImageFile($imageFile)
                        ->getUrl(),
                    'small' => $this->imageHelper->init($product, 'product_page_image_small')
                        ->setImageFile($imageFile)
                        ->getUrl(),
                ];
                break;
            case 'list':
                return [
                    'large' => $this->imageHelper->init($product, 'category_page_list_swatches')
                        ->setImageFile($imageFile)
                        ->getUrl(),
                    'medium' => $this->imageHelper->init($product, 'category_page_list_swatches')
                        ->setImageFile($imageFile)
                        ->getUrl(),
                    'small' => $this->imageHelper->init($product, 'product_page_image_small')
                        ->setImageFile($imageFile)
                        ->getUrl(),
                ];
                break;
            default:
                return [
                    'large' => $this->imageHelper->init($product, 'product_page_image_large_no_frame')
                        ->setImageFile($imageFile)
                        ->getUrl(),
                    'medium' => $this->imageHelper->init($product, 'product_page_image_medium_no_frame')
                        ->setImageFile($imageFile)
                        ->getUrl(),
                    'small' => $this->imageHelper->init($product, 'product_page_image_small')
                        ->setImageFile($imageFile)
                        ->getUrl(),
                ];
                break;
        }
    }
}
