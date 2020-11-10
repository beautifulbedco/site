<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Block\Link;

class RolesLink extends AbstractLink
{
    /**
     * @return bool
     */
    protected function isAllowed()
    {
        return $this->companyContext->isActiveOrInactiveCompany()
            && $this->companyContext->isCurrentUserCompanyUser()
            && parent::isAllowed();
    }
}
