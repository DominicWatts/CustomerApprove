<?php

namespace Xigen\CustomerApprove\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;

/**
 * Class InstallData
 * @package Xigen\CustomerApprove\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * Constructor
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $customerSetup = $this->customerSetupFactory->create(
            ['setup' => $setup]
        );

        $customerSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'account_approved',
            [
                'type' => 'int',
                'label' => 'Account Approved',
                'input' => 'boolean',
                'source' => '',
                'required' => true,
                'default' => 0,
                'visible' => true,
                'position' => 50,
                'system' => false,
                'backend' => '',
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'is_searchable_in_grid' => true,
            ]
        );

        $attribute = $customerSetup->getEavConfig()->getAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'account_approved'
        )->addData(
            ['used_in_forms' =>
                [
                    'adminhtml_customer',
                    'adminhtml_checkout',
                    'customer_account_create',
                    'customer_account_edit',
                ],
            ]
        );
        $attribute->save();
    }
}
