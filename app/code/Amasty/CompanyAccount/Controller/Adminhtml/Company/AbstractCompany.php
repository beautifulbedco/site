<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Controller\Adminhtml\Company;

use Amasty\CompanyAccount\Model\ResourceModel\Company;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;

abstract class AbstractCompany extends Action
{
    const ADMIN_RESOURCE = 'Amasty_CompanyAccount::company_management';

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Amasty\CompanyAccount\Model\Repository\CompanyRepository
     */
    protected $companyRepository;

    /**
     * @var \Amasty\CompanyAccount\Model\CompanyFactory
     */
    protected $companyFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Amasty\CompanyAccount\Model\Repository\CompanyRepository $companyRepository,
        \Amasty\CompanyAccount\Model\CompanyFactory $companyFactory,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->companyRepository = $companyRepository;
        $this->companyFactory = $companyFactory;
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Amasty_CompanyAccount::company_accounts');
        $resultPage->getConfig()->getTitle()->prepend(__('Company Accounts'));

        return $resultPage;
    }
}
