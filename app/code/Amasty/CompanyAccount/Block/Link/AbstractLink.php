<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Block\Link;

use Magento\Framework\View\Element\Html\Link\Current;

class AbstractLink extends Current implements \Magento\Customer\Block\Account\SortLinkInterface
{
    /**
     * @var string
     */
    protected $resource;

    /**
     * @var \Amasty\CompanyAccount\Model\CompanyContext
     */
    protected $companyContext;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Amasty\CompanyAccount\Model\CompanyContext $companyContext,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->companyContext = $companyContext;
        $this->resource = $data['resource'] ?? null;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return $this->isAllowed() ? parent::_toHtml() : '';
    }

    /**
     * @return bool
     */
    protected function isAllowed()
    {
        return $this->companyContext->isResourceAllow($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }
}
