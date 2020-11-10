<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Controller;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;

abstract class AbstractAction extends \Magento\Framework\App\Action\Action
{
    const RESOURCE = '';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Amasty\CompanyAccount\Model\CompanyContext
     */
    protected $companyContext;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Amasty\CompanyAccount\Model\CompanyContext $companyContext,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->companyContext = $companyContext;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface|null
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->companyContext->getCurrentCustomerId() || !$this->isAllowed()) {
            $this->_actionFlag->set('', 'no-dispatch', true);

            return $this->_redirect('noroute');
        }

        return parent::dispatch($request);
    }

    /**
     * @return bool
     */
    protected function isAllowed()
    {
        return $this->companyContext->isCurrentUserCompanyUser()
            && $this->companyContext->isResourceAllow(static::RESOURCE);
    }
}
