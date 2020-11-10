<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Model;

use Amasty\CompanyAccount\Api\Data\CompanyInterface;
use Amasty\CompanyAccount\Api\Data\CustomerInterface;
use Amasty\CompanyAccount\Model\ResourceModel\Company\CollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Amasty\CompanyAccount\Model\ResourceModel\AdminUser\CollectionFactory as UserCollectionFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class MailSender
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CountryDataProvider
     */
    private $countryDataProvider;

    /**
     * @var RegionCollectionFactory
     */
    private $regionCollectionFactory;

    /**
     * @var UserCollectionFactory
     */
    private $userCollectionFactory;

    /**
     * @var SenderResolverInterface
     */
    protected $senderResolver;

    /**
     * @var CollectionFactory
     */
    protected $companyCollectionFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var TimezoneInterface
     */
    protected $timeZone;

    public function __construct(
        TransportBuilder $transportBuilder,
        ConfigProvider $configProvider,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository,
        RegionCollectionFactory $regionCollectionFactory,
        LoggerInterface $logger,
        UserCollectionFactory $userCollectionFactory,
        SenderResolverInterface $senderResolver,
        CountryDataProvider $countryDataProvider,
        CollectionFactory $companyCollectionFactory,
        CustomerFactory $customerFactory,
        TimezoneInterface $timezone
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->configProvider = $configProvider;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
        $this->countryDataProvider = $countryDataProvider;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->userCollectionFactory = $userCollectionFactory;
        $this->senderResolver = $senderResolver;
        $this->companyCollectionFactory = $companyCollectionFactory;
        $this->customerFactory = $customerFactory;
        $this->timeZone = $timezone;
    }

    /**
     * @param string $templatePath
     * @param CompanyInterface $company
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendCompanyCreateNotification(string $templatePath, CompanyInterface $company)
    {
        $this->prepareCompanyData($company);
        $data = [
            'company' => $company,
            'customer_name' => $this->getCustomerName((int)$company->getSuperUserId()),
        ];
        $sender = $this->configProvider->getAdminSender();
        $receiver = $this->senderResolver->resolve(
            $this->configProvider->getAdminReceiver(),
            $this->storeManager->getStore()->getId()
        );
        $this->send($sender, $templatePath, $receiver['email'] ?? '', $data);
    }

    /**
     * @param CompanyInterface $company
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendRepresentativeSetNotification(CompanyInterface $company)
    {
        $representative = $this->userCollectionFactory->create()
            ->addIdsFilter($company->getSalesRepresentativeId())
            ->setPageSize(1)
            ->getFirstItem();

        $data = [
            'company_name' => $company->getCompanyName(),
            'customer_name' => $representative->getFirstname() . ' ' . $representative->getLastname(),
        ];

        $this->send(
            $this->configProvider->getAdminSender(),
            ConfigProvider::ADMIN_NOTIF_SALES_REPRESENTATIVE_APPOINTMENT,
            $representative->getEmail() ?: '',
            $data
        );
    }

    /**
     * @param string $templatePath
     * @param CompanyInterface $company
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return $this
     */
    public function sendChangeStatusNotification(string $templatePath, CompanyInterface $company)
    {
        if (!$templatePath) {
            return $this;
        }

        $sender = $this->configProvider->getCustomerSender();
        $superUser = $this->getUser($company->getSuperUserId());

        if ($superUser) {
            $date = null;
            if ($company->getRejectAt()) {
                $dateTime = (new \DateTime())->setTimestamp($company->getRejectAt());
                $date = $this->timeZone->formatDateTime(
                    $this->timeZone->date($dateTime),
                    \IntlDateFormatter::MEDIUM,
                    true
                );
            }

            $data = [
                'company_name' => $company->getCompanyName(),
                'rejected_at' => $date,
                'reject_reason' => $company->getRejectReason(),
            ];
            $this->send($sender, $templatePath, $superUser->getEmail(), $data);
        }

        return $this;
    }

    /**
     * @param CompanyInterface $newCompany
     * @param CompanyInterface $oldCompany
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendChangeAdminEmail(CompanyInterface $newCompany, CompanyInterface $oldCompany)
    {
        $sender = $this->configProvider->getCustomerSender();

        $data = [
            'company_name' => $newCompany->getCompanyName(),
        ];

        $newAdmin = $this->getUser($newCompany->getSuperUserId());
        if ($newAdmin->getId()) {
            $this->send($sender, ConfigProvider::CUSTOMER_NOTIF_NEW_ADMIN, $newAdmin->getEmail(), $data);
        }

        $oldAdmin = $this->getUser($oldCompany->getSuperUserId());
        if ($oldAdmin->getId()) {
            $this->send($sender, ConfigProvider::CUSTOMER_NOTIF_ADMIN_UNASSIGN, $oldAdmin->getEmail(), $data);
        }
    }

    /**
     * @param array $customersData
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendLinkCustomerToCompanyNotification(array $customersData)
    {
        $sender = $this->configProvider->getCustomerSender();
        $companies = $this->companyCollectionFactory->create()
            ->addCompanyIdFilter($customersData[0][CustomerInterface::COMPANY_ID])
            ->setPageSize(1);
        /**
         * @var CompanyInterface $company
         */
        $company = $companies->getFirstItem();
        if ($company->getCompanyId()) {
            $admin = $this->getUser($company->getSuperUserId());

            $companyName = $company->getCompanyName();
            $adminEmail = $admin->getEmail();
            if ($company->getCompanyId()) {
                foreach ($customersData as $customerData) {
                    $user = $this->getUser($customerData[CustomerInterface::CUSTOMER_ID]);
                    if ($user->getId() && $user->getId() != $company->getSuperUserId()) {
                        $data = [
                            'company_name' => $companyName,
                            'customer_name' => $user->getFirstname() . ' ' . $user->getLastname(),
                            'company_admin_email' => $adminEmail
                        ];
                        $this->send(
                            $sender,
                            ConfigProvider::CUSTOMER_NOTIF_CUSTOMER_LINKING,
                            $user->getEmail(),
                            $data
                        );
                    }
                }
            }
        }
    }

    /**
     * @param array $customersData
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendDisableCustomerNotification(array $customersData)
    {
        $sender = $this->configProvider->getCustomerSender();

        foreach ($customersData as $customerData) {
            $user = $this->getUser($customerData[CustomerInterface::CUSTOMER_ID]);
            if ($user->getId()) {
                $data = [
                    'customer_name' => $user->getFirstname() . ' ' . $user->getLastname()
                ];
                $this->send($sender, ConfigProvider::CUSTOMER_NOTIF_CUSTOMER_DISABLE, $user->getEmail(), $data);
            }
        }
    }

    /**
     * @param int $adminId
     * @return bool|\Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getUser($adminId)
    {
        try {
            $admin = $this->customerRepository->getById($adminId);
        } catch (NoSuchEntityException $e) {
            $admin = $this->customerFactory->create();
        }

        return $admin;
    }

    /**
     * @param int $userId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCustomerName(int $userId)
    {
        $customer = $this->getUser($userId);

        return $customer->getId() ? $customer->getFirstname() . ' ' . $customer->getLastname() : '';
    }

    /**
     * @param CompanyInterface $company
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function prepareCompanyData(CompanyInterface $company)
    {
        $company->setCountry($this->countryDataProvider->getCountryNameByCode($company->getCountryId()));
        $region = $company->getRegion() ?: $this->getRegionNameById((int)$company->getRegionId())->getName();
        $company->setRegion($region);
        $company->setAddress($company->getData(CompanyInterface::STREET));
    }

    /**
     * @param int $regionId
     * @return \Magento\Framework\DataObject
     */
    private function getRegionNameById(int $regionId)
    {
        return $this->regionCollectionFactory->create()
            ->addFieldToFilter('main_table.region_id', $regionId)
            ->getFirstItem();
    }

    /**
     * @param string $sender
     * @param string $templatePath
     * @param string $emailTo
     * @param array $data
     */
    private function send(string $sender, string $templatePath, string $emailTo, array $data)
    {
        if ($sender && $templatePath && $emailTo) {
            try {
                $template = $this->configProvider->getEmailTemplate($templatePath);
                $storeId = $this->storeManager->getStore()->getId();
                $transport = $this->transportBuilder->setTemplateIdentifier($template)
                    ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId])
                    ->setTemplateVars($data)
                    ->setFrom($sender)
                    ->addTo($emailTo)
                    ->getTransport();
                $transport->sendMessage();
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
