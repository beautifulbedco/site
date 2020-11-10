<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Setup;

use Amasty\CompanyAccount\Setup\Operation\AddCompanyOrders;
use Amasty\CompanyAccount\Setup\Operation\AddCompanyPaymentsField;
use Amasty\CompanyAccount\Setup\Operation\AddUseCompanyGroupField;
use Amasty\CompanyAccount\Setup\Operation\AddUserStatusField;
use Amasty\CompanyAccount\Setup\Operation\DeleteForeignKey;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var AddCompanyOrders
     */
    private $addCompanyOrders;

    /**
     * @var AddCompanyPaymentsField
     */
    private $addCompanyPaymentsField;

    /**
     * @var AddUserStatusField
     */
    private $addUserStatusField;

    /**
     * @var AddUseCompanyGroupField
     */
    private $addUseCompanyGroupField;

    /**
     * @var DeleteForeignKey
     */
    private $deleteForeignKey;

    public function __construct(
        AddCompanyPaymentsField $addCompanyPaymentsField,
        AddCompanyOrders $addCompanyOrders,
        AddUserStatusField $addUserStatusField,
        AddUseCompanyGroupField $addUseCompanyGroupField,
        DeleteForeignKey $deleteForeignKey
    ) {
        $this->addCompanyPaymentsField = $addCompanyPaymentsField;
        $this->addCompanyOrders = $addCompanyOrders;
        $this->addUserStatusField = $addUserStatusField;
        $this->addUseCompanyGroupField = $addUseCompanyGroupField;
        $this->deleteForeignKey = $deleteForeignKey;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addCompanyPaymentsField->execute($setup);
            $this->addCompanyOrders->execute($setup);
            $this->addUserStatusField->execute($setup);
            $this->addUseCompanyGroupField->execute($setup);
            $this->deleteForeignKey->execute($setup);
        }

        $setup->endSetup();
    }
}
