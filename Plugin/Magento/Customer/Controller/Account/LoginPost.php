<?php

namespace Xigen\CustomerApprove\Plugin\Magento\Customer\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class LoginPost
 * @package Xigen\CustomerApprove\Plugin\Magento\Customer\Controller\Account
 */
class LoginPost
{
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * LoginPost constructor.
     * @param Context $context
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Context $context,
        CustomerRepositoryInterface $customerRepositoryInterface,
        ManagerInterface $messageManager
    ) {
        $this->_request = $context->getRequest();
        $this->_response = $context->getResponse();
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->resultFactory = $context->getResultFactory();
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Customer\Controller\Account\LoginPost $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundExecute(
        \Magento\Customer\Controller\Account\LoginPost $loginPost,
        \Closure $proceed
    ) {
        $request = $loginPost->getRequest();
        try {
            if ($request->isPost()) {
                $login = $request->getPost('login');

                if (!isset($login['username']) || !isset($login['password'])) {
                    return $proceed();
                }

                if (!empty($login['username']) && !empty($login['password'])) {
                    $customer = $this->getCustomerByEmail($login['username']);
                    if (!$customer) {
                        return $proceed();
                    }
                    if (!empty($customer->getCustomAttributes())) {
                        if ($this->isAccountApproved($customer)) {
                            return $proceed();
                        } else {
                            $this->messageManager->addErrorMessage(__('Your account is not approved.'));
                            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
                            return $resultRedirect->setPath('customer/account/login');
                        }
                    } else {
                        return $proceed();
                    }
                }
            }
            throw new \Exception(__("Problem with login"));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('An unspecified error occurred . Please contact us for assistance . ')
            );
        }
    }

    /**
     * @param $email
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomerByEmail($email)
    {
        try {
            return $this->customerRepositoryInterface->get($email);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Is customer account is approved
     * @return bool
     */
    public function isAccountApproved($customer)
    {
        $customAttribute = $customer->getCustomAttribute('account_approved');
        if (empty($customAttribute)) {
            return false;
        }
        $isApprovedAccount = $customAttribute->getValue();
        if ($isApprovedAccount) {
            return true;
        }
        return false;
    }
}
