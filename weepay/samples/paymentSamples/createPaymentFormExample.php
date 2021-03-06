<?php

require_once '../Options.php';

//Request
$request = new \weepay\Request\FormInitializeRequest();
$request->setOrderId('11');
$request->setIpAddress('192.168.2.1');
$request->setPrice(0.10);
$request->setCurrency(\weepay\Model\Currency::TL);
$request->setLocale(\weepay\Model\Locale::TR);
$request->setDescription('Açıklama Alanı');
$request->setCallBackUrl('https://websitem.com/callback');
$request->setPaymentGroup(\weepay\Model\PaymentGroup::PRODUCT);
$request->setPaymentChannel(\weepay\Model\PaymentChannel::WEB);

//Customer
$customer = new \weepay\Model\Customer();
$customer->setCustomerId(1235); // Üye işyeri müşteri Id
$customer->setCustomerName("isim"); //Üye işyeri müşteri ismi
$customer->setCustomerSurname("soyisim"); //Üye işyeri müşteri Soyisim
$customer->setGsmNumber("50XXXXXX"); //Üye işyeri müşteri Cep Tel
$customer->setEmail("helo@weepay.co"); //Üye işyeri müşteri ismi
$customer->setIdentityNumber("00032222721"); //Üye işyeri müşteri TC numarası
$customer->setCity("istanbul"); //Üye işyeri müşteri il
$customer->setCountry("turkey"); //Üye işyeri müşteri ülke
$request->setCustomer($customer);

//Adresler
// Fatura Adresi
$BillingAddress = new \weepay\Model\Address();
$BillingAddress->setContactName("isim soyisim");
$BillingAddress->setAddress("Abdurrahman Nafiz Gürman,Mh, G. Ali Rıza Gürcan Cd. No:27");
$BillingAddress->setCity("istanbul");
$BillingAddress->setCountry("turkey");
$BillingAddress->setZipCode("34164");
$request->setBillingAddress($BillingAddress);

//Kargo / Teslimat Adresi
$ShippingAddress = new \weepay\Model\Address();
$ShippingAddress->setContactName("isim soyisim");
$ShippingAddress->setAddress("Abdurrahman Nafiz Gürman,Mh, G. Ali Rıza Gürcan Cd. No:27");
$ShippingAddress->setCity("istanbul");
$ShippingAddress->setCountry("turkey");
$ShippingAddress->setZipCode("34164");
$request->setShippingAddress($ShippingAddress);

// Sipariş Ürünleri
$Products = array();

// Birinci Ürün
$firstProducts = new \weepay\Model\Product();
$firstProducts->setName("Ürün Bir");
$firstProducts->setProductId(12344);
$firstProducts->setProductPrice(0.10);
$firstProducts->setItemType(\weepay\Model\ProductType::PHYSICAL);
$Products[0] = $firstProducts;

// İkinci Ürün
$secondProducts = new \weepay\Model\Product();
$secondProducts->setName("Ürün İki");
$secondProducts->setProductId("C550A100");
$secondProducts->setProductPrice(0.10);
$secondProducts->setItemType(\weepay\Model\ProductType::PHYSICAL);
$Products[1] = $secondProducts;

// Üçüncü Ürün
$thirdProducts = new \weepay\Model\Product();
$thirdProducts->setName("Ürün Üç");
$thirdProducts->setProductId("1000");
$thirdProducts->setProductPrice("0.10");
$thirdProducts->setItemType(\weepay\Model\ProductType::PHYSICAL);
$Products[2] = $thirdProducts;
$request->setProducts($Products);

$checkoutFormInitialize = \weepay\Model\CheckoutFormInitialize::create($request, Options::Auth());

echo "<div id='weePay-checkout-form' class='popup'>";

if ($checkoutFormInitialize->getStatus() == 'success') {

    print_r($checkoutFormInitialize->getCheckoutFormData());

} else {
    print_r($checkoutFormInitialize->getError());
    print_r($checkoutFormInitialize->getErrorCode());
    print_r($checkoutFormInitialize->getMessage());
}
