<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


namespace Amasty\CompanyAccount\Api;

/**
 * @api
 */
interface CompanyRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\CompanyAccount\Api\Data\CompanyInterface $company
     *
     * @return \Amasty\CompanyAccount\Api\Data\CompanyInterface
     */
    public function save(\Amasty\CompanyAccount\Api\Data\CompanyInterface $company);

    /**
     * Get by id
     *
     * @param int $companyId
     *
     * @return \Amasty\CompanyAccount\Api\Data\CompanyInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($companyId);

    /**
     * @param string $fieldName
     * @param string $value
     * @return \Amasty\CompanyAccount\Model\Company
     */
    public function getByField($fieldName, $value);

    /**
     * Delete
     *
     * @param \Amasty\CompanyAccount\Api\Data\CompanyInterface $company
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\CompanyAccount\Api\Data\CompanyInterface $company);

    /**
     * Delete by id
     *
     * @param int $companyId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($companyId);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
