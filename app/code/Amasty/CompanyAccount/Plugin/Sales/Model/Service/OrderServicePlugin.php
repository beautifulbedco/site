<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Plugin\Sales\Model\Service;

use Amasty\CompanyAccount\Model\CompanyContext;
use Amasty\CompanyAccount\Model\ResourceModel\Order as CompanyAccountOrder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Service\OrderService;
use Amasty\CompanyAccount\Api\Data\OrderInterface as AmOrderInterface;

class OrderServicePlugin
{
    /**
     * @var CompanyAccountOrder
     */
    private $order;

    /**
     * @var CompanyContext
     */
    private $companyContext;

    public function __construct(
        CompanyAccountOrder $order,
        CompanyContext $companyContext
    ) {
        $this->order = $order;
        $this->companyContext = $companyContext;
    }

    /**
     * @param OrderService $subject
     * @param OrderInterface $order
     * @return OrderInterface
     */
    public function afterPlace(OrderService $subject, OrderInterface $order): OrderInterface
    {
        $company = $this->companyContext->getCurrentCompany();
        $this->order->saveData([
            AmOrderInterface::COMPANY_ORDER_ID => $order->getId(),
            AmOrderInterface::COMPANY_ID => $company->getCompanyId(),
            AmOrderInterface::COMPANY_NAME => $company->getCompanyName()
        ]);

        return $order;
    }
}
