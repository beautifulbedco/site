<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Model\Source\Customer;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    const INACTIVE = 0;
    const ACTIVE = 1;

    /**
     * @param int $value
     * @return string
     */
    public function getStatusLabelByValue(int $value)
    {
        $statuses = [
            self::INACTIVE => __('Inactive'),
            self::ACTIVE => __('Active')
        ];

        return isset($statuses[$value]) ? $statuses[$value] : '';
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ACTIVE,
                'label' => __('Active')
            ],
            [
                'value' => self::INACTIVE,
                'label' => __('Inactive')
            ]
        ];
    }
}
