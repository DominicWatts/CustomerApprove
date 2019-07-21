<?php

namespace Xigen\CustomerApprove\Observer\Frontend\Customer;

/**
 * Class RegisterSuccess
 * @package Xigen\CustomerApprove\Observer\Frontend\Customer
 */
class RegisterSuccess implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * RegisterSuccess constructor.
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Customer\Model\Session $session
     */
    public function __construct(
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\Session $session
    ) {
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->session = $session;
    }

    /**
     * Execute observer
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $this->session->destroy();
        $this->messageManager->addErrorMessage(__('Your account is not approved.'));
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('customer/account/login');
    }
}
