<?php
namespace Techflix\OtpLogin\Controller\Otp;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Techflix\OtpLogin\Model\Otp;
use Magento\Customer\Model\Session;

class Send extends Action
{
    protected $otp;
    protected $customerSession;

    public function __construct(Context $context, Otp $otp, Session $customerSession)
    {
        parent::__construct($context);
        $this->otp = $otp;
        $this->customerSession = $customerSession;
    }

    public function execute()
    {
        $phoneNumber = $this->getRequest()->getParam('phone');
        $customer = $this->otp->getCustomerByPhone($phoneNumber);

        if ($customer) {
            $otp = $this->otp->generateOtp($phoneNumber);
            $this->otp->sendOtp($phoneNumber, $otp);

            $this->customerSession->setOtpPhoneNumber($phoneNumber);

            $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            return $result->setData(['success' => true, 'message' => 'OTP sent successfully']);
        } else {
            $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            return $result->setData(['success' => false, 'message' => 'Phone number not registered']);
        }
    }
}
