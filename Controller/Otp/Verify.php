<?php
namespace Techflix\OtpLogin\Controller\Otp;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Techflix\OtpLogin\Model\Otp;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\CustomerFactory;

class Verify extends Action
{
    protected $otp;
    protected $customerSession;
    protected $customerFactory;

    public function __construct(
        Context $context,
        Otp $otp,
        Session $customerSession,
        CustomerFactory $customerFactory
    ) {
        parent::__construct($context);
        $this->otp = $otp;
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
    }

    public function execute()
    {
        $otpCode = $this->getRequest()->getParam('otp');
        $phoneNumber = $this->customerSession->getOtpPhoneNumber();

        if ($this->otp->verifyOtp($phoneNumber, $otpCode)) {
            $customer = $this->otp->getCustomerByPhone($phoneNumber);

            $this->customerSession->setCustomerAsLoggedIn($customer);

            $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            return $result->setData(['success' => true, 'message' => 'Login successful']);
        } else {
            $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            return $result->setData(['success' => false, 'message' => 'Invalid OTP']);
        }
    }
}
