<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Model;

use Amasty\CompanyAccount\Api\CompanyRepositoryInterface;
use Amasty\CompanyAccount\Api\Data\CompanyInterface;
use Amasty\CompanyAccount\Model\Source\Company\Status;

class MailManager
{
    const LINK_CUSTOMER = 'link';
    const DISABLE_CUSTOMER = 'disable';

    /**
     * @var MailSender
     */
    private $mailSender;

    public function __construct(
        MailSender $mailSender
    ) {
        $this->mailSender = $mailSender;
    }

    /**
     * @param array $customerData
     * @param string $action
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendCustomerEmails(array $customerData, string $action)
    {
        switch ($action) {
            case self::LINK_CUSTOMER:
                $this->mailSender->sendLinkCustomerToCompanyNotification($customerData);
                break;
            case self::DISABLE_CUSTOMER:
                $this->mailSender->sendDisableCustomerNotification($customerData);
                break;
        }
    }

    /**
     * @param bool $isEdited
     * @param CompanyInterface $newCompany
     * @param CompanyInterface $oldCompany
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendCompanyEmails(bool $isEdited, CompanyInterface $newCompany, CompanyInterface $oldCompany)
    {
        if (!$isEdited) {
            $this->sendCreateCompanyEmail($newCompany);
        }

        if (!$isEdited || $this->isRepresentativeChanged($newCompany, $oldCompany)) {
            $this->mailSender->sendRepresentativeSetNotification($newCompany);
        }

        if (!$isEdited || $this->isStatusChanged($newCompany, $oldCompany)) {
            $this->sendChangeStatusEmail($newCompany);
        }

        if ($this->isAdminChanged($newCompany, $oldCompany)) {
            $this->mailSender->sendChangeAdminEmail($newCompany, $oldCompany);
        }
    }

    /**
     * @param CompanyInterface $company
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function sendCreateCompanyEmail(CompanyInterface $company)
    {
        if ($company->getStatus() == Status::STATUS_PENDING) {
            $templatePath = ConfigProvider::ADMIN_NOTIF_NEW_COMPANY_REQUEST;
        } else {
            $templatePath = ConfigProvider::ADMIN_NOTIF_NEW_COMPANY_CREATE;
        }

        $this->mailSender->sendCompanyCreateNotification($templatePath, $company);
    }

    /**
     * @param CompanyInterface $company
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function sendChangeStatusEmail(CompanyInterface $company)
    {
        switch ($company->getStatus()) {
            case Status::STATUS_ACTIVE:
                $templatePath = ConfigProvider::CUSTOMER_NOTIF_ACTIVE_STATUS;
                break;
            case Status::STATUS_INACTIVE:
                $templatePath = ConfigProvider::CUSTOMER_NOTIF_INACTIVE_STATUS;
                break;
            case Status::STATUS_REJECTED:
                $templatePath = ConfigProvider::CUSTOMER_NOTIF_REJECTED_STATUS;
                break;
            default:
                $templatePath = '';
        }

        $this->mailSender->sendChangeStatusNotification($templatePath, $company);
    }

    /**
     * @param CompanyInterface $newCompany
     * @param CompanyInterface $oldCompany
     * @return bool
     */
    private function isRepresentativeChanged(CompanyInterface $newCompany, CompanyInterface $oldCompany)
    {
        return $oldCompany->getSalesRepresentativeId() !== $newCompany->getSalesRepresentativeId();
    }

    /**
     * @param CompanyInterface $newCompany
     * @param CompanyInterface $oldCompany
     * @return bool
     */
    private function isStatusChanged(CompanyInterface $newCompany, CompanyInterface $oldCompany)
    {
        return $newCompany->getStatus() !== $oldCompany->getStatus();
    }

    /**
     * @param CompanyInterface $newCompany
     * @param CompanyInterface $oldCompany
     * @return bool
     */
    private function isAdminChanged(CompanyInterface $newCompany, CompanyInterface $oldCompany)
    {
        return $newCompany->getSuperUserId() !== $oldCompany->getSuperUserId();
    }
}
