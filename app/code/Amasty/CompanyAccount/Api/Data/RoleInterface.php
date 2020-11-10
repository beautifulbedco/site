<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


namespace Amasty\CompanyAccount\Api\Data;

interface RoleInterface extends RoleExtensionInterface
{
    const TABLE_NAME = 'amasty_company_account_role';
    const ROLE_ID = 'role_id';
    const ROLE_NAME = 'role_name';
    const COMPANY_ID = 'company_id';
    const ROLE_TYPE_ID = 'role_type_id';

    /**
     * @return int
     */
    public function getRoleId();

    /**
     * @param int $roleId
     *
     * @return \Amasty\CompanyAccount\Api\Data\RoleInterface
     */
    public function setRoleId($roleId);

    /**
     * @return string|null
     */
    public function getRoleName();

    /**
     * @param string|null $roleName
     *
     * @return \Amasty\CompanyAccount\Api\Data\RoleInterface
     */
    public function setRoleName($roleName);

    /**
     * @return int
     */
    public function getCompanyId();

    /**
     * @param int $companyId
     *
     * @return \Amasty\CompanyAccount\Api\Data\RoleInterface
     */
    public function setCompanyId($companyId);

    /**
     * @return int
     */
    public function getRoleTypeId();

    /**
     * @param int $roleId
     *
     * @return \Amasty\CompanyAccount\Api\Data\RoleInterface
     */
    public function setRoleTypeId($roleId);

    /**
     * @return \Amasty\CompanyAccount\Api\Data\RoleExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * @param \Amasty\CompanyAccount\Api\Data\RoleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Amasty\CompanyAccount\Api\Data\RoleExtensionInterface $extensionAttributes);
}
