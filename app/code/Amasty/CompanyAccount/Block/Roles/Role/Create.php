<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Block\Roles\Role;

use Amasty\CompanyAccount\Api\Data\PermissionInterface;
use Amasty\CompanyAccount\Api\Data\RoleInterface;
use Amasty\CompanyAccount\Api\Data\RoleInterfaceFactory;
use Amasty\CompanyAccount\Api\PermissionRepositoryInterface;
use Amasty\CompanyAccount\Api\RoleRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Acl\AclResource\ProviderInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Create extends \Magento\Framework\View\Element\Template
{
    const AMASTY_COMPANY_SAVE_ROLE = 'amasty_company/role/saveRole';
    const AMASTY_COMPANY_UPDATE_ROLE = 'amasty_company/role/updateRole';

    /**
     * @var RoleInterface
     */
    private $role;

    /**
     * @var RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @var RoleInterfaceFactory
     */
    private $roleFactory;

    /**
     * @var ProviderInterface
     */
    private $aclProvider;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var PermissionRepositoryInterface
     */
    private $permissionRepository;

    public function __construct(
        Template\Context $context,
        RoleInterfaceFactory $roleFactory,
        RoleRepositoryInterface $roleRepository,
        ProviderInterface $aclProvider,
        PermissionRepositoryInterface $permissionRepository,
        Json $json,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->roleRepository = $roleRepository;
        $this->roleFactory = $roleFactory;
        $this->aclProvider = $aclProvider;
        $this->json = $json;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * @param int $roleId
     * @return string
     */
    public function getSaveActionUrl(int $roleId): string
    {
        return $roleId
            ? $this->getUrl(self::AMASTY_COMPANY_UPDATE_ROLE)
            : $this->getUrl(self::AMASTY_COMPANY_SAVE_ROLE);
    }

    /**
     * @return RoleInterface
     */
    public function getCurrentRole()
    {
        if (!$this->role) {
            $roleId = $this->getRequest()->getParam(RoleInterface::ROLE_ID);
            try {
                $this->role = $this->roleRepository->getById($roleId);
            } catch (NoSuchEntityException $e) {
                $this->role = $this->roleFactory->create();
            }
        }

        return $this->role;
    }

    /**
     * @return bool|false|string
     */
    public function getTreeJson()
    {
        $resources = $this->aclProvider->getAclResources();
        $resourceNames = [];
        foreach ($this->getPermissions() as $permission) {
            $resourceNames[$permission[PermissionInterface::RESOURCE_ID]] = $permission[PermissionInterface::ROLE_ID];
        }
        $this->prepareTreeData($resources, $resourceNames);

        return $this->json->serialize(['aclTree' => ['data'  => $resources]]);
    }

    /**
     * @return array
     */
    private function getPermissions()
    {
        $roleId = $this->getCurrentRole()->getRoleId();
        $permissions = [];
        if ($roleId) {
            $permissions = $this->permissionRepository->getByRoleId((int)$roleId);
            $permissions = $permissions->toArray();
            $permissions = $permissions['items'] ?? [];
        }

        return $permissions;
    }

    /**
     * @param array $resources
     * @param array $permissions
     * @param int $level
     */
    private function prepareTreeData(array &$resources, array $permissions, $level = 0)
    {
        $countResources = count($resources);
        for ($counter = 0; $counter < $countResources; $counter++) {
            $resources[$counter]['text'] = $resources[$counter]['title'];

            if ($resources[$counter]['children']) {
                $this->prepareTreeData($resources[$counter]['children'], $permissions, $level + 1);
                $resources[$counter]['state']['opened'] = 'open';
            }
            if (isset($permissions[$resources[$counter]['id']])) {
                $resources[$counter]['state']['selected'] = true;
            }
            if ($level == 0) {
                $resources[$counter]['li_attr'] = ['class' => 'amcompany-collapsible-root'];
            }
        }
    }
}
