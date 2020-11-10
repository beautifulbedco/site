<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Plugin\Customer\Model\ResourceModel\Grid;

use Amasty\CompanyAccount\Api\Data\CompanyInterface;
use Magento\Customer\Model\ResourceModel\Grid\Collection;

class CollectionPlugin
{
    public function beforeAddFieldToFilter(Collection $subject, $field, $condition = null): array
    {
        if ($field == CompanyInterface::COMPANY_NAME) {
            $field = 'company.' . $field;
        }

        return [$field, $condition];
    }
}
