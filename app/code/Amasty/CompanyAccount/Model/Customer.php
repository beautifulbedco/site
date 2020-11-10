<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Model;

use Amasty\CompanyAccount\Api\Data\CustomerInterface;
use Amasty\CompanyAccount\Model\Repository\CompanyRepository;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Model\AbstractExtensibleModel;

class Customer extends AbstractExtensibleModel implements CustomerInterface
{
    /**
     * @var ResourceModel\Customer
     */
    private $customer;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    public function __construct(
        \Amasty\CompanyAccount\Model\ResourceModel\Customer $customer,
        CompanyRepository $companyRepository,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->customer = $customer;
        $this->companyRepository = $companyRepository;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\CompanyAccount\Model\ResourceModel\Customer::class);
    }

    /**
     * @param int $customerId
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCompanyNameByCustomerId(int $customerId)
    {
        $company = $this->getCompanyByCustomerId($customerId);

        return $company ? $company->getCompanyName() : null;
    }

    /**
     * @param int $customerId
     * @return \Amasty\CompanyAccount\Api\Data\CompanyInterface|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCompanyByCustomerId($customerId)
    {
        $companyId = $this->customer->getCompanyIdByCustomerId($customerId);

        return $companyId ? $this->companyRepository->getById($companyId) : null;
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return $this->_getData(CustomerInterface::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($customerId)
    {
        $this->setData(CustomerInterface::CUSTOMER_ID, $customerId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCompanyId()
    {
        return $this->_getData(CustomerInterface::COMPANY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCompanyId($companyId)
    {
        $this->setData(CustomerInterface::COMPANY_ID, $companyId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getJobTitle()
    {
        return $this->_getData(CustomerInterface::JOB_TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setJobTitle($jobTitle)
    {
        $this->setData(CustomerInterface::JOB_TITLE, $jobTitle);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->_getData(CustomerInterface::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->setData(CustomerInterface::STATUS, $status);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTelephone()
    {
        return $this->_getData(CustomerInterface::TELEPHONE);
    }

    /**
     * @inheritdoc
     */
    public function setTelephone($telephone)
    {
        $this->setData(CustomerInterface::TELEPHONE, $telephone);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRoleId()
    {
        return $this->_getData(CustomerInterface::ROLE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setRoleId($roleId)
    {
        $this->setData(CustomerInterface::ROLE_ID, $roleId);

        return $this;
    }
}
