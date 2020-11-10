<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Ui\Component\Listing\Column;

use Amasty\CompanyAccount\Model\Customer;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;

class CompanyName extends Column
{
    /**
     * @var Customer
     */
    private $customer;

    public function __construct(
        Customer $customer,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->customer = $customer;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['customer_id']) && !isset($item['company_name'])) {
                    $item['company_name'] = $this->customer->getCompanyNameByCustomerId((int) $item['customer_id']);
                }
            }
        }

        return $dataSource;
    }
}
