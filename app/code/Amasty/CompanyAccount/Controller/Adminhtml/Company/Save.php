<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Controller\Adminhtml\Company;

use Amasty\CompanyAccount\Api\Data\CompanyInterface;
use Amasty\CompanyAccount\Model\Source\Company\Group;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends AbstractCompany
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $companyId = (int)$this->getRequest()->getParam(CompanyInterface::COMPANY_ID);
        $returnToEdit = false;

        try {
            if ($companyId) {
                /** @var  \Amasty\CompanyAccount\Model\Company $model */
                $model = $this->companyRepository->getById($companyId);
            } else {
                $model = $this->companyFactory->create();
            }

            $data = $this->getRequest()->getPostValue();

            $model->addData($data);
            $model->setRegionId($model->getRegionId() ?: null);
            $model->setCompanyId($this->getRequest()->getParam('company_id', null) ?: null);
            $this->fillCustomerData($model);
            $this->companyRepository->save($model);
            $companyId = $model->getCompanyId();
            $this->messageManager->addSuccessMessage(__('You have saved the Company.'));
            $returnToEdit = (bool)$this->getRequest()->getParam('back', false);
        } catch (NoSuchEntityException $exception) {
            $this->messageManager->addErrorMessage(__('This Company no longer exists.'));
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $returnToEdit = true;
        }

        if ($returnToEdit && $companyId) {
            return $resultRedirect->setPath('*/*/edit', ['company_id' => $companyId]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param CompanyInterface $model
     * @return $this
     */
    private function fillCustomerData($model)
    {
        $customersData = $this->getRequest()->getPost('company_user_container', []);
        $customerIds = [];
        foreach ($customersData as $recordData) {
            $customerIds[] = $recordData['entity_id'];
        }
        $model->setCustomerIds($customerIds);

        return $this;
    }
}
