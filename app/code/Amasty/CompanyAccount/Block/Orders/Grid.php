<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Block\Orders;

use Amasty\CompanyAccount\Api\Data\OrderInterface;
use Amasty\CompanyAccount\Model\CompanyContext;
use Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Sales\Model\ResourceModel\Order\Collection;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class Grid extends \Magento\Sales\Block\Order\History
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_CompanyAccount::company/orders/grid.phtml';

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CompanyContext
     */
    private $companyContext;

    /**
     * @var Collection
     */
    private $companyOrders;

    /**
     * @var \Magento\Sales\Helper\Reorder
     */
    private $reorder;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    private $postHelper;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        CollectionFactory $collectionFactory,
        CompanyContext $companyContext,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Sales\Helper\Reorder $reorder,
        \Magento\Framework\Data\Helper\PostHelper $postHelper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        array $data = []
    ) {
        parent::__construct($context, $orderCollectionFactory, $customerSession, $orderConfig, $data);
        $this->collectionFactory = $collectionFactory;
        $this->companyContext = $companyContext;
        $this->reorder = $reorder;
        $this->postHelper = $postHelper;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return Collection|bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrders()
    {
        if (!$this->companyOrders) {
            $company = $this->companyContext->getCurrentCompany();
            $this->searchCriteriaBuilder->addFilter(OrderInterface::COMPANY_ID, $company->getId());
            $this->companyOrders = $this->orderRepository->getList($this->searchCriteriaBuilder->create());
        }

        return $this->companyOrders;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    public function isCanReorder(\Magento\Sales\Model\Order $order): bool
    {
        return $this->companyContext->isCurrentCustomer((int) $order->getCustomerId())
            && $this->reorder->canReorder($order->getEntityId());
    }

    /**
     * @param string $url
     * @return string
     */
    public function getPostData(string $url): string
    {
        return $this->postHelper->getPostData($url);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return string
     */
    public function getExtraChildHtml(\Magento\Sales\Model\Order $order): string
    {
        $extra = $this->getChildBlock('extra.container');
        if ($extra) {
            $extra->setOrder($order);
            $html = $extra->getChildHtml();
        }

        return $html ?? '';
    }

    public function getCustomerName(\Magento\Sales\Model\Order $order): string
    {
        return $this->getOrderInfo()->getCustomerName($order);
    }

    private function getOrderInfo(): \Amasty\CompanyAccount\ViewModel\Order
    {
        return $this->getData('orderInfo');
    }
}
