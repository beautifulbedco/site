<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


namespace Amasty\CompanyAccount\Api\Data;

interface PermissionInterface
{
    const TABLE_NAME = 'amasty_company_account_permission';
    const PERMISSION_ID = 'permission_id';
    const ROLE_ID = 'role_id';
    const RESOURCE_ID = 'resource_id';

    /**
     * @return int
     */
    public function getPermissionId();

    /**
     * @param int $permissionId
     *
     * @return \Amasty\CompanyAccount\Api\Data\PermissionInterface
     */
    public function setPermissionId($permissionId);

    /**
     * @return int
     */
    public function getRoleId();

    /**
     * @param int $roleId
     *
     * @return \Amasty\CompanyAccount\Api\Data\PermissionInterface
     */
    public function setRoleId($roleId);

    /**
     * @return string
     */
    public function getResourceId();

    /**
     * @param string $resourceId
     *
     * @return \Amasty\CompanyAccount\Api\Data\PermissionInterface
     */
    public function setResourceId($resourceId);
}
