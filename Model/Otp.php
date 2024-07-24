<?php
namespace Techflix\OtpLogin\Model;

use Twilio\Rest\Client;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

class Otp
{
    protected $twilioClient;
    protected $twilioNumber;
    protected $customerFactory;
    protected $customerCollectionFactory;
    protected $otpValidityDuration = 300; // 5 minutes

    public function __construct(
        CustomerFactory $customerFactory,
        CollectionFactory $customerCollectionFactory
    ) {
        $this->twilioClient = new Client('AC6beb8e0d3ab6261c80977d6c9d111216', '47cdf1c54c3ad95c194f6d7f920c9dd1');
        $this->twilioNumber = '8240355265';
        $this->customerFactory = $customerFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
    }

    public function sendOtp($phoneNumber, $otp)
    {
        $message = "Your OTP is: $otp";

        $this->twilioClient->messages->create(
            $phoneNumber,
            [
                'from' => $this->twilioNumber,
                'body' => $message
            ]
        );
    }

    public function generateOtp($phoneNumber)
    {
        $otp = rand(100000, 999999);
        // Store OTP and its expiration time in the session or a database
        // Example with session:
        $session = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\Session\SessionManager');
        $session->setOtpCode($otp);
        $session->setOtpExpirationTime(time() + $this->otpValidityDuration);
        return $otp;
    }

    public function verifyOtp($phoneNumber, $otpCode)
    {
        // Example with session:
        $session = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\Session\SessionManager');
        $storedOtp = $session->getOtpCode();
        $expirationTime = $session->getOtpExpirationTime();

        return $otpCode == $storedOtp && time() <= $expirationTime;
    }

    public function getCustomerByPhone($phoneNumber)
    {
        $customerCollection = $this->customerCollectionFactory->create();
        $customerCollection->addAttributeToFilter('phone_number', $phoneNumber);

        return $customerCollection->getFirstItem();
    }
}
