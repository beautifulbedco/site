<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CompanyAccount
 */


declare(strict_types=1);

namespace Amasty\CompanyAccount\Block\CustomerCustomAttributes;

use Amasty\CompanyAccount\Model\Di\Wrapper;
use Magento\Framework\View\Element\Template;

class Form extends Wrapper
{
    public function __construct(
        Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        $name = '',
        array $data = []
    ) {
        parent::__construct(
            $context,
            $objectManagerInterface,
            // @phpstan-ignore-next-line
            \Magento\CustomerCustomAttributes\Block\Form::class,
            $data
        );
    }
}
