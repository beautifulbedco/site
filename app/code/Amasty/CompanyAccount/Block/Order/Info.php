<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Block\Order;

class Info extends \Magento\Sales\Block\Order\Info
{
    public function getCustomerName(): string
    {
        return $this->getOrderInfo()->getCustomerName($this->getOrder());
    }

    private function getOrderInfo(): \Amasty\CompanyAccount\ViewModel\Order
    {
        return $this->getData('orderInfo');
    }
}
