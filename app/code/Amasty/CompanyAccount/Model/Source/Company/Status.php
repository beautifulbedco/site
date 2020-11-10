<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Model\Source\Company;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;
    const STATUS_REJECTED = 3;

    /**
     * @return array
     */
    public function toOptionArray() : array
    {
        return [
            [
                'value' => self::STATUS_PENDING,
                'label' => __('Pending')
            ],
            [
                'value' => self::STATUS_INACTIVE,
                'label' => __('Inactive')
            ],
            [
                'value' => self::STATUS_ACTIVE,
                'label' => __('Active')
            ],
            [
                'value' => self::STATUS_REJECTED,
                'label' => __('Rejected')
            ],
        ];
    }

    /**
     * @param int $status
     * @return string
     */
    public function getStatusLabelByValue($status)
    {
        $options = $this->toOptionArray();
        foreach ($options as $optionStatus) {
            if ($optionStatus['value'] == $status) {
                return $optionStatus['label'];
            }
        }

        return '';
    }
}
