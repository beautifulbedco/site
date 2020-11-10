<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Block\Adminhtml;

use Amasty\CompanyAccount\Model\Customer;
use Amasty\CompanyAccount\Model\ResourceModel\Order;
use Magento\Framework\View\Element\Template;

abstract class AbstractInfo extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var Order
     */
    protected $orderModel;

    public function __construct(
        Customer $customer,
        \Magento\Framework\Registry $registry,
        Template\Context $context,
        Order $orderModel,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->customer = $customer;
        $this->orderModel = $orderModel;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return __('Company Name')->render();
    }
}
