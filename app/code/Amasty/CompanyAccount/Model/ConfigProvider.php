<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Model;

use Magento\Config\Model\Config\Source\Nooptreq;
use Magento\Framework\Data\CollectionDataSourceInterface;

class ConfigProvider extends \Amasty\Base\Model\ConfigProviderAbstract implements CollectionDataSourceInterface
{
    const GENERAL_ALLOWED_GROUPS = 'general/allowed_groups';
    const GENERAL_AUTO_APPROVE = 'general/auto_approve';
    const GENERAL_INACTIVATE_CUSTOMER = 'general/inactivate_customer';
    const ADMIN_NOTIF_SENDER = 'admin_notif/sender';
    const ADMIN_NOTIF_RECEIVER = 'admin_notif/receiver';
    const ADMIN_NOTIF_NEW_COMPANY_REQUEST = 'admin_notif/new_company_request';
    const ADMIN_NOTIF_NEW_COMPANY_CREATE = 'admin_notif/new_company_create';
    const ADMIN_NOTIF_SALES_REPRESENTATIVE_APPOINTMENT = 'admin_notif/sales_representative_appointment';
    const CUSTOMER_NOTIF_SENDER = 'customer_notif/sender';
    const CUSTOMER_NOTIF_ACTIVE_STATUS = 'customer_notif/active_status';
    const CUSTOMER_NOTIF_INACTIVE_STATUS = 'customer_notif/inactive_status';
    const CUSTOMER_NOTIF_REJECTED_STATUS = 'customer_notif/rejected_status';
    const CUSTOMER_NOTIF_CUSTOMER_LINKING = 'customer_notif/customer_linking';
    const CUSTOMER_NOTIF_CUSTOMER_DISABLE = 'customer_notif/customer_disable';
    const CUSTOMER_NOTIF_NEW_ADMIN = 'customer_notif/new_admin';
    const CUSTOMER_NOTIF_ADMIN_UNASSIGN = 'customer_notif/admin_unassign';

    /**
     * @var string
     */
    protected $pathPrefix = 'amasty_companyaccount/';

    /**
     * @return string
     */
    public function getAllowedCustomerGroups()
    {
        return $this->getValue(self::GENERAL_ALLOWED_GROUPS);
    }

    /**
     * @return bool
     */
    public function isAutoApprove()
    {
        return $this->isSetFlag(self::GENERAL_AUTO_APPROVE);
    }

    /**
     * @return string
     */
    public function getAdminSender()
    {
        return $this->getValue(self::ADMIN_NOTIF_SENDER);
    }

    /**
     * @return string
     */
    public function getAdminReceiver()
    {
        return $this->getValue(self::ADMIN_NOTIF_RECEIVER);
    }

    /**
     * @return string
     */
    public function getCustomerSender()
    {
        return $this->getValue(self::CUSTOMER_NOTIF_SENDER);
    }

    /**
     * @param string $path
     * @return string
     */
    public function getEmailTemplate(string $path)
    {
        return $this->getValue($path);
    }

    /**
     * @return bool
     */
    public function getInactivateCustomerMode()
    {
        return (bool)$this->getValue(self::GENERAL_INACTIVATE_CUSTOMER);
    }

    public function isShowPrefix(): bool
    {
        return (bool)$this->scopeConfig->getValue('customer/address/prefix_show');
    }

    public function isShowSuffix(): bool
    {
        return (bool)$this->scopeConfig->getValue('customer/address/suffix_show');
    }

    public function isVisibleCustomerPrefix(): bool
    {
        return $this->scopeConfig->getValue('customer/address/prefix_show') !== Nooptreq::VALUE_NO;
    }

    public function isVisibleCustomerMiddlename(): bool
    {
        return $this->scopeConfig->isSetFlag('customer/address/middlename_show');
    }

    public function isVisibleCustomerSuffix(): bool
    {
        return $this->scopeConfig->getValue('customer/address/suffix_show') !== Nooptreq::VALUE_NO;
    }
}
