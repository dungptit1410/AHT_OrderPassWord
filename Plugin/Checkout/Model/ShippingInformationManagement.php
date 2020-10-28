<?php
namespace AHT\OrderPassword\Plugin\Checkout\Model;
use Magento\Quote\Model\QuoteRepository;

class ShippingInformationManagement
{
    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    protected $quoteRepository;
    protected $encryptor;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    ){
        $this->quoteRepository = $quoteRepository;
        $this->encryptor = $encryptor;
    }

    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $address = $addressInformation->getShippingAddress();
        $extAttributes = $address->getExtensionAttributes();
        $password = $extAttributes->getPassword();
        $encryptPassword = $this->encryptor->encrypt($password);
        //$extAttributes = $addressInformation->getExtensionAttributes();
        //$password = $extAttributes->getPassword();
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->setPassword($encryptPassword);

        //$extAttributes = $addressInformation->getExtensionAttributes();
        //error_log(print_r($extAttributes,1));
        // $quote = $this->quoteRepository->getActive($cartId);
        // $quote->setPassword($extAttributes->getPassword());
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($extAttributes->getPassword());
    }
}