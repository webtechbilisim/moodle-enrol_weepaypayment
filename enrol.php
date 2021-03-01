<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Creates a WeePay payment form and show it on course enrolment page
 *
 * This script creates a payment form for WeePay
 * let user to pay and enrol to course.
 *
 * @package    enrol_weepaypayment
 * @copyright  2019 Dualcube Team
 * @copyright  2021 WebTech Bilişim
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Disable moodle specific debug messages and any errors in output,
// comment out when debugging or better look into error log!
defined('MOODLE_INTERNAL') || die();
global $CFG, $USER;

/**
 * Generate a random string, using a cryptographically secure 
 * pseudorandom number generator (random_int)
 *
 * This function uses type hints now (PHP 7+ only), but it was originally
 * written for PHP 5 as well.
 * 
 * For PHP 7, random_int is a PHP core function
 * For PHP 5.x, depends on https://github.com/paragonie/random_compat
 * 
 * @param int $length      How many characters do we want?
 * @param string $keyspace A string of all possible characters
 *                         to select from
 * @return string
 */
function random_str(
  int $length = 64,
  string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
): string {
  if ($length < 1) {
    throw new \RangeException("Length must be a positive integer");
  }
  $pieces = [];
  $max = mb_strlen($keyspace, '8bit') - 1;
  for ($i = 0; $i < $length; ++$i) {
    $pieces[] = $keyspace[random_int(0, $max)];
  }
  return implode('', $pieces);
}

//  WeePay Payment START

require_once "weepay/weepayBootstrap.php";

weepayBootstrap::initialize();
$options = new \weepay\Auth();
$options->setBayiID($this->get_config('merchantid'));
$options->setApiKey($this->get_config('publishablekey'));
$options->setSecretKey($this->get_config('secretkey'));
$options->setBaseUrl("https://api.weepay.co/");
/* Sandbox Mode Coming Soon

if ($this->get_config('sandboxmode')) {
  $options->setBaseUrl("https://api.weepay.co/");
} else {
  $options->setBaseUrl("https://api.weepay.co/");
}*/

//Request
$OrderID = random_str(9);
$request = new \weepay\Request\FormInitializeRequest();
$request->setOrderId($OrderID . "-" . $USER->id . "-" . $course->id . "-" . $instance->id);
$request->setIpAddress($USER->lastip);
$request->setPrice($cost);
$request->setCurrency(\weepay\Model\Currency::TL);
$request->setDescription('Moodle WeePay Payment Solution');
//$request->setCallBackUrl($CFG->wwwroot . "/enrol/weepaypayment/callback.php");
$request->setCallBackUrl($CFG->wwwroot . "/enrol/weepaypayment/callback.php?oid=".$OrderID . "-" . $USER->id . "-" . $course->id . "-" . $instance->id);
$request->setPaymentGroup(\weepay\Model\PaymentGroup::PRODUCT);
$request->setPaymentChannel(\weepay\Model\PaymentChannel::WEB);
// Payment Language Selector
if($USER->lang=='en')
{
	$request->setLocale(\weepay\Model\Locale::EN);
}
else if ($USER->lang=='tr')
{
	$request->setLocale(\weepay\Model\Locale::TR);
}
else // Otherwise Turkish
{
	$request->setLocale(\weepay\Model\Locale::TR);
}

//Customer
$customer = new \weepay\Model\Customer();
$customer->setCustomerId($USER->id); // Üye işyeri müşteri Id 
$customer->setCustomerName($USER->firstname); //Üye işyeri müşteri ismi 
$customer->setCustomerSurname($USER->lastname); //Üye işyeri müşteri Soyisim
$customer->setEmail($USER->email); //Üye işyeri müşteri ismi 
$customer->setGsmNumber("8508405019"); //Üye işyeri müşteri Cep Tel
$customer->setIdentityNumber("11111111111"); //Üye işyeri müşteri TC numarası
$customer->setCity("Istanbul"); //Üye işyeri müşteri il
$customer->setCountry("Turkey");//Üye işyeri müşteri ülke
$request->setCustomer($customer);

//Adresler
// Fatura Adresi
$BillingAddress = new \weepay\Model\Address();
$BillingAddress->setContactName($USER->firstname . " " . $USER->lastname);
$BillingAddress->setAddress("Istanbul");
$BillingAddress->setCity("Istanbul");
$BillingAddress->setCountry("Turkey");
$BillingAddress->setZipCode("34000");
$request->setBillingAddress($BillingAddress);

//Kargo / Teslimat Adresi
$ShippingAddress = new \weepay\Model\Address();
$ShippingAddress->setContactName($USER->firstname . " " . $USER->lastname);
$ShippingAddress->setAddress("Istanbul");
$ShippingAddress->setCity("Istanbul");
$ShippingAddress->setCountry("Turkey");
$ShippingAddress->setZipCode("34000");
$request->setShippingAddress($ShippingAddress);

// Sipariş Ürünleri
$Products = array();

// Birinci Ürün
$firstProducts = new \weepay\Model\Product();
$firstProducts->setName($coursefullname);
$firstProducts->setProductId($course->id);
$firstProducts->setProductPrice($cost);
$firstProducts->setItemType(\weepay\Model\ProductType::VIRTUAL);
$Products[0] = $firstProducts;
$request->setProducts($Products);

$checkoutFormInitialize = \weepay\Model\CheckoutFormInitialize::create($request, $options);

echo "<div id='weePay-checkout-form' class='responsive col-lg-6 col-md-12 col-sm-12' style='float:none; margin:auto;'>";

if ($checkoutFormInitialize->getStatus() == 'success') {

    print_r($checkoutFormInitialize->getCheckoutFormData());

} else {
    print_r($checkoutFormInitialize->getError());
    print_r($checkoutFormInitialize->getErrorCode());
    print_r($checkoutFormInitialize->getMessage());
}

?>