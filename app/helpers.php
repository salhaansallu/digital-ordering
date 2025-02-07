<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosDataController;
use App\Http\Controllers\UserDataController;
use App\Models\Categories;
use App\Models\Credit;
use App\Models\CreditHistory;
use App\Models\customers;
use App\Models\orderProducts;
use App\Models\orders;
use App\Models\posData;
use App\Models\PosInvitation;
use App\Models\posUsers;
use App\Models\Products;
use App\Models\supplier;
use App\Models\User;
use App\Models\userData;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Mike42\Escpos\ImagickEscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

function isAdmin()
{
    return Auth::check();
}

function isCashier()
{
    return Auth::check();
}

class SMS
{
    public $contact;
    public $message;
    public $camp_type;
    public $send_at;

    function Send()
    {
        // URL to send the POST request to
        $url = env('SMS_GATEWAY_URL');

        // Data to be sent in the POST request
        $postFields = [
            'user_id' => env('SMS_USER_ID'),
            'api_key' => env('SMS_API_KEY'),
            'sender_id' => env('SMS_SENDER_ID'),
            'contact' => json_encode($this->contact),
            'message' => $this->message,
            'camp_type' => $this->camp_type,
            'send_at' => $this->send_at,
        ];

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($ch, CURLOPT_POST, true);           // Set the request method to POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields)); // Set the POST fields

        // Execute the cURL session and fetch the response
        $response = curl_exec($ch);

        // Close the cURL session
        curl_close($ch);

        // Check for errors
        if (curl_errno($ch)) {
            return (object)array(
                "error" => 1,
                "message" => 'cURL error: ' . curl_error($ch)
            );
        } else {
            // Print the response
            $response = json_decode($response);
            return (object)array(
                "error" => $response->error,
                "message" => $response->message
            );
        }
    }

    static function getBalance()
    {
        // URL to send the POST request to
        $url = env('SMS_GATEWAY_URL');

        // Data to be sent in the POST request
        $postFields = [
            'user_id' => env('SMS_USER_ID'),
            'api_key' => env('SMS_API_KEY'),
        ];

        // Initialize cURL session
        $ch = curl_init($url . 'get-balance');

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($ch, CURLOPT_POST, true);           // Set the request method to POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields)); // Set the POST fields

        // Execute the cURL session and fetch the response
        $response = curl_exec($ch);


        // Check for errors
        if (curl_errno($ch)) {
            $response = (object)array(
                "error" => 1,
                "message" => 'cURL error: ' . curl_error($ch)
            );
        } else {
            // Print the response
            $response = json_decode($response);
            $response = (object)array(
                "error" => $response->error,
                "message" => $response->message,
                "balance" => $response->error == 0 ? $response->balance : 0
            );
        }

        // Close the cURL session
        curl_close($ch);
        return $response;
    }
}

function country($country)
{
    $countryList = array(
        "Afghanistan",
        "Albania",
        "Algeria",
        "American Samoa",
        "Andorra",
        "Angola",
        "Anguilla",
        "Antarctica",
        "Antigua and Barbuda",
        "Argentina",
        "Armenia",
        "Aruba",
        "Australia",
        "Austria",
        "Azerbaijan",
        "Bahamas (the)",
        "Bahrain",
        "Bangladesh",
        "Barbados",
        "Belarus",
        "Belgium",
        "Belize",
        "Benin",
        "Bermuda",
        "Bhutan",
        "Bolivia (Plurinational State of)",
        "Bonaire, Sint Eustatius and Saba",
        "Bosnia and Herzegovina",
        "Botswana",
        "Bouvet Island",
        "Brazil",
        "British Indian Ocean Territory (the)",
        "Brunei Darussalam",
        "Bulgaria",
        "Burkina Faso",
        "Burundi",
        "Cabo Verde",
        "Cambodia",
        "Cameroon",
        "Canada",
        "Cayman Islands (the)",
        "Central African Republic (the)",
        "Chad",
        "Chile",
        "China",
        "Christmas Island",
        "Cocos (Keeling) Islands (the)",
        "Colombia",
        "Comoros (the)",
        "Congo (the Democratic Republic of the)",
        "Congo (the)",
        "Cook Islands (the)",
        "Costa Rica",
        "Croatia",
        "Cuba",
        "Curaçao",
        "Cyprus",
        "Czechia",
        "Côte d'Ivoire",
        "Denmark",
        "Djibouti",
        "Dominica",
        "Dominican Republic (the)",
        "Ecuador",
        "Egypt",
        "El Salvador",
        "Equatorial Guinea",
        "Eritrea",
        "Estonia",
        "Eswatini",
        "Ethiopia",
        "Falkland Islands (the) [Malvinas]",
        "Faroe Islands (the)",
        "Fiji",
        "Finland",
        "France",
        "French Guiana",
        "French Polynesia",
        "French Southern Territories (the)",
        "Gabon",
        "Gambia (the)",
        "Georgia",
        "Germany",
        "Ghana",
        "Gibraltar",
        "Greece",
        "Greenland",
        "Grenada",
        "Guadeloupe",
        "Guam",
        "Guatemala",
        "Guernsey",
        "Guinea",
        "Guinea-Bissau",
        "Guyana",
        "Haiti",
        "Heard Island and McDonald Islands",
        "Holy See (the)",
        "Honduras",
        "Hong Kong",
        "Hungary",
        "Iceland",
        "India",
        "Indonesia",
        "Iran (Islamic Republic of)",
        "Iraq",
        "Ireland",
        "Isle of Man",
        "Israel",
        "Italy",
        "Jamaica",
        "Japan",
        "Jersey",
        "Jordan",
        "Kazakhstan",
        "Kenya",
        "Kiribati",
        "Korea (the Democratic People's Republic of)",
        "Korea (the Republic of)",
        "Kuwait",
        "Kyrgyzstan",
        "Lao People's Democratic Republic (the)",
        "Latvia",
        "Lebanon",
        "Lesotho",
        "Liberia",
        "Libya",
        "Liechtenstein",
        "Lithuania",
        "Luxembourg",
        "Macao",
        "Madagascar",
        "Malawi",
        "Malaysia",
        "Maldives",
        "Mali",
        "Malta",
        "Marshall Islands (the)",
        "Martinique",
        "Mauritania",
        "Mauritius",
        "Mayotte",
        "Mexico",
        "Micronesia (Federated States of)",
        "Moldova (the Republic of)",
        "Monaco",
        "Mongolia",
        "Montenegro",
        "Montserrat",
        "Morocco",
        "Mozambique",
        "Myanmar",
        "Namibia",
        "Nauru",
        "Nepal",
        "Netherlands (the)",
        "New Caledonia",
        "New Zealand",
        "Nicaragua",
        "Niger (the)",
        "Nigeria",
        "Niue",
        "Norfolk Island",
        "Northern Mariana Islands (the)",
        "Norway",
        "Oman",
        "Pakistan",
        "Palau",
        "Palestine, State of",
        "Panama",
        "Papua New Guinea",
        "Paraguay",
        "Peru",
        "Philippines (the)",
        "Pitcairn",
        "Poland",
        "Portugal",
        "Puerto Rico",
        "Qatar",
        "Republic of North Macedonia",
        "Romania",
        "Russian Federation (the)",
        "Rwanda",
        "Réunion",
        "Saint Barthélemy",
        "Saint Helena, Ascension and Tristan da Cunha",
        "Saint Kitts and Nevis",
        "Saint Lucia",
        "Saint Martin (French part)",
        "Saint Pierre and Miquelon",
        "Saint Vincent and the Grenadines",
        "Samoa",
        "San Marino",
        "Sao Tome and Principe",
        "Saudi Arabia",
        "Senegal",
        "Serbia",
        "Seychelles",
        "Sierra Leone",
        "Singapore",
        "Sint Maarten (Dutch part)",
        "Slovakia",
        "Slovenia",
        "Solomon Islands",
        "Somalia",
        "South Africa",
        "South Georgia and the South Sandwich Islands",
        "South Sudan",
        "Spain",
        "Sri Lanka",
        "Sudan (the)",
        "Suriname",
        "Svalbard and Jan Mayen",
        "Sweden",
        "Switzerland",
        "Syrian Arab Republic",
        "Taiwan",
        "Tajikistan",
        "Tanzania, United Republic of",
        "Thailand",
        "Timor-Leste",
        "Togo",
        "Tokelau",
        "Tonga",
        "Trinidad and Tobago",
        "Tunisia",
        "Turkey",
        "Turkmenistan",
        "Turks and Caicos Islands (the)",
        "Tuvalu",
        "Uganda",
        "Ukraine",
        "United Arab Emirates (the)",
        "United Kingdom of Great Britain and Northern Ireland (the)",
        "United States Minor Outlying Islands (the)",
        "United States of America (the)",
        "Uruguay",
        "Uzbekistan",
        "Vanuatu",
        "Venezuela (Bolivarian Republic of)",
        "Viet Nam",
        "Virgin Islands (British)",
        "Virgin Islands (U.S.)",
        "Wallis and Futuna",
        "Western Sahara",
        "Yemen",
        "Zambia",
        "Zimbabwe",
        "Åland Islands"
    );

    if ($country == 'get') {
        return $countryList;
    } elseif (in_array($country, $countryList)) {
        return true;
    } else {
        return false;
    }
}

function company($pos_code = null)
{
    if ($pos_code != null) {
        $pos = posData::where('pos_code', $pos_code)->get();
        if ($pos) {
            return $pos[0];
        }
        return defaultValues();
    }
    return PosDataController::company();
}

function userData()
{
    return UserDataController::user();
}

function display404()
{
    return response()->view('errors.404')->setStatusCode(404);
}

function HashCode($val)
{
    return Crypt::encrypt($val);
}

function productImage($path)
{
    if (empty($path)) {
        return asset('assets/images/products/placeholder.svg');
    }

    if (file_exists(public_path('assets/images/products/' . $path))) {
        return asset('/assets/images/products/' . $path);
    } else {
        return asset('assets/images/products/placeholder.svg');
    }
}

function profileImage($path)
{
    if (empty($path)) {
        return asset('user_profile/placeholder.png');
    }

    if (file_exists(public_path('assets/images/user_profile/' . $path))) {
        return asset('/assets/images/user_profile/' . $path);
    } else {
        return asset('user_profile/placeholder.png');
    }
}

function getAllProducts()
{
    return Products::all();
}

function categoryImage($path)
{
    if (empty($path)) {
        return asset('assets/images/categories/placeholder.svg');
    }

    if (file_exists(public_path('assets/images/categories/' . $path))) {
        return asset('/assets/images/categories/' . $path);
    } else {
        return asset('assets/images/categories/placeholder.svg');
    }
}

function defaultValues()
{
    $arr = array(
        "id" => "",
        "name" => "",
        "category_name" => "",
        "pos_code" => "",
        "image" => "",
        "fname" => "",
        "lname" => "",
        "email" => "",
        "phone" => "",
        "company_name" => "",
        "industry" => "",
        "country" => "",
        "city" => "",
        "message" => "",
        "calls" => "",
        "user_id" => "",
        "created_at" => "",
        "updated_at" => "",
        "customer_id" => "",
        "ammount" => "",
        "credit_id" => "",
        "address" => "",
        "order_number" => "",
        "customer" => "",
        "total" => "",
        "total_cost" => "",
        "service_charge" => "",
        "roundup" => "",
        "payment_method" => "",
        "invoice" => "",
        "order_id" => "",
        "qr_code" => "",
        "discount" => "",
        "discount_mod" => "",
        "discounted_price" => "",
        "admin_id" => "",
        "company_name" => "",
        "industry" => "",
        "plan" => "",
        "status" => "",
        "expiry_date" => "",
        "currency" => "",
        "sku" => "",
        "pro_name" => "",
        "price" => "",
        "cost" => "",
        "qty" => "",
        "category" => "",
        "pro_image" => "",
        "supplier" => "",
        "purshace_no" => "",
        "shipping_charge" => "",
        "note" => "",
        "supplier_id" => "",
        "products" => "",
        "order_name" => "",
        "password" => "",
        "zip" => "",
        "profile" => "",
        "status" => "",
        "invitation_id" => "",
    );
    return (object)$arr;
}

function getProductImage($sku)
{
    $product = Products::where('sku', $sku)->get();
    if ($product && $product->count() > 0) {
        return productImage($product[0]->pro_image);
    }
    return productImage('');
}

function getCategory($id)
{
    if ($id == 'all') {
        $category = Categories::all();
        if ($category) {
            return (object)$category;
        }
    } else {
        $category = Categories::where('id', $id)->get();
        if ($category && $category->count() > 0) {
            return (object)$category[0];
        }
    }
    return defaultValues();
}

function getTotalOrderQTY($sku)
{
    $product = orderProducts::where('sku', $sku)->sum('qty');
    return $product;
}

function getTotalOrderSale($sku)
{
    $total = 0;
    $products = orderProducts::where('sku', $sku)->get();
    if ($products && $products->count() > 0) {
        foreach ($products as $key => $item) {
            if ($item['discount'] > 0) {
                (float)$total += (float)$item['discounted_price'] * (float)$item['qty'];
            } else {
                (float)$total += (float)$item['price'] * (float)$item['qty'];
            }
        }
    }
    return (float)$total;
}

function currency($price, $currency)
{
    if (strpos($price, '.')) {
        return $currency . " " . round((float)$price, 2);
    } elseif (empty($price) || $price == "0") {
        return "0.00";
    } else {
        return $currency . " " . $price . '.00';
    }
}

function sanitize($data)
{
    return strip_tags($data);
}

function login_redirect($path)
{
    if ($path == "get_path") {
        return !get_Cookie('login_redirect') ? '/' : get_Cookie('login_redirect');
    }
    return set_Cookie('login_redirect', $path);
}

function set_Cookie($name, $value)
{
    Cookie::queue($name, $value, 18000);
}

function get_Cookie($name)
{
    if (Cookie::has($name)) {
        return Cookie::get($name);
    } else {
        return false;
    }
}

function CodeToUrl($value)
{
    return str_replace("/", "slash", $value);
}

function UrlToCode($value)
{
    return str_replace("slash", "/", $value);
}

function formatPhoneNumber($phoneNumber)
{
    // Remove non-numeric characters
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

    // Check if the phone number is 10 digits
    if (strlen($phoneNumber) == 10) {
        // Format the phone number as (XXX) XXX-XXXX
        return sprintf("(%s) %s-%s", substr($phoneNumber, 0, 3), substr($phoneNumber, 3, 3), substr($phoneNumber, 6));
    } else {
        // Return the original phone number if it doesn't have 10 digits
        return $phoneNumber;
    }
}

function getUser($id)
{
    $user = User::where('id', $id)->get();
    if ($user && $user->count() > 0) {
        return (object)$user[0];
    }
    return defaultValues();
}

function getCreditLimit($id)
{
    $user = customers::where('id', $id)->get();
    if ($user && $user->count() > 0) {
        $credits = Credit::where('customer_id', $id)->where('ammount', '>', 0)->sum('ammount');
        return $user[0]->credit_limit - $credits;
    }
    return 0;
}

function getUserData($id)
{
    $user = userData::where('user_id', $id)->get();
    if ($user && $user->count() > 0) {
        return (object)$user[0];
    }
    return defaultValues();
}

function getCustomer($id)
{
    $user = customers::where('id', $id)->get();
    if ($user && $user->count() > 0) {
        return (object)$user[0];
    }
    return defaultValues();
}

function generateQR($data, $invoice = false)
{
    $qr = new QrCode();
    $qrCodes = $qr::size(500)->style('square')->generate($data);
    if ($invoice) {
        return $qrCodes;
    }

    $filename = rand(1111, 999999) . date('d-m-Y-h-i-s') . '.svg';

    if (File::put('qr_codes/' . $filename, $qrCodes)) {
        return (object)array('error'=>0, 'url'=> asset('/qr_codes/'.$filename), 'html'=>'<a href="'.'/qr_codes/'.$filename.'" target="_blank"><img width="100px" src="'.'/qr_codes/'.$filename.'" alt=""></a>');
    }

    return (object)array('error'=>1, 'msg'=> 'Error generating QR code');
}

function customEncrypt($data)
{
    return Crypt::encrypt($data);
}

function generateInvoice($order_id, $cashin, $inName)
{
    $products = orderProducts::where('order_id', $order_id)->get();
    $order = orders::where('order_number', $order_id)->get()[0];
    (float)$total = 0;
    $discount = 0;
    $service = $order->service_charge;
    $grandTotal = 0;
    (float)$roundup = $order->roundup;
    $company = PosDataController::company();
    $customer = $order->customer == '0' ? 'Cash Deal' : getCustomer($order->customer);
    $qr_code = base64_encode(generateQR("https://nmsware.com/customer-copy/" . substr(company()->pos_code, 0, 10) . "/" . $order_id, true));
    $qr_code_image = generateQR("https://nmsware.com/customer-copy/" . substr(company()->pos_code, 0, 10) . "/" . $order_id);
    $product_count = 0;

    $html = '
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Document</title>
            <style>
                @page {
                    margin: 10px;
                    height: auto;
                    width: 210mm;
                 }
                body { margin: 10px; }
            </style>
        </head>
        <body style="font-family: Arial, sans-serif;">

                <!-- Header -->
            <div style="text-align: center; margin-bottom: 20px; margin-top: 30px;">
                <h1 style="margin: 0;">' . $company->company_name . '</h1>
                <p style="margin: 0;">' . getUserData($company->admin_id)->address . '</p>
                <p style="margin: 0;">Tel: ' . formatPhoneNumber(getUserData($company->admin_id)->phone) . '</p>
                <h2 style="margin: 20px 0;">Sales Receipt</h2>
            </div>

                <!-- Customer Details -->
            <div style="margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <th colspan="3" style="color: #000;padding: 5px;">Customer Details</th>
                    </tr>
                    <tr>
                        <td style="width: 33%; padding: 5px; border: 1px solid black;">Customer Name</td>
                        <td style="width: 33%; padding: 5px; border: 1px solid black;">Customer Mobile</td>
                        <td style="width: 33%; padding: 5px; border: 1px solid black;">Customer Address</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px; border: 1px solid black;">' . ($order->customer != '0' && !empty($customer->name)? $customer->name : $customer) . '</td>
                        <td style="padding: 5px; border: 1px solid black;">' . ($order->customer != '0' && !empty($customer->phone)? $customer->phone : 'N/A') . '</td>
                        <td style="padding: 5px; border: 1px solid black;">' . ($order->customer != '0' && !empty($customer->address)? $customer->address : 'N/A') . '</td>
                    </tr>
                </table>
            </div>

                <!-- Bill Info -->
            <div style="margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <th style="width: 50%; padding: 8px; border: 1px solid black; text-align: left;">Bill No</th>
                        <th style="width: 50%; padding: 8px; border: 1px solid black; text-align: left;">Date</th>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid black;">' . $order_id . '</td>
                        <td style="padding: 8px; border: 1px solid black;">' . date('d-m-Y H:i:s', strtotime($order->created_at)) . '</td>
                    </tr>
                </table>
            </div>

                <!-- Item Details -->
            <div style="margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">Description (Code)</th>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">Unit Price ( Discount )</th>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">QTY</th>
                        <th style="color: #000;padding: 5px; border: 1px solid black; text-align: left;">Total</th>
                    </tr>
    ';

    foreach ($products as $key => $pro) {
        if ($pro->discount == 0) {
            $html .= '

                <tr>
                    <td style="padding: 5px; border: 1px solid black;">' . $pro->pro_name . ' (' . $pro->sku . ')</td>
                    <td style="padding: 5px; border: 1px solid black;">' . currency($pro->price, '') . ' ( 0.00 % 0.00 )</td>
                    <td style="padding: 5px; border: 1px solid black;">' . $pro->qty . '</td>
                    <td style="padding: 5px; border: 1px solid black;">' . currency($pro->price * $pro->qty, '') . '</td>
                </tr>

            ';
        }
        else {
            if ($pro->discount_mod == 'am') {
                $html .= '

                <tr>
                    <td style="padding: 5px; border: 1px solid black;">' . $pro->pro_name . ' (' . $pro->sku . ')</td>
                    <td style="padding: 5px; border: 1px solid black;">' . currency($pro->price, '') . ' ( ' . currency($pro->discount, '') . ' )</td>
                    <td style="padding: 5px; border: 1px solid black;">' . $pro->qty . '</td>
                    <td style="padding: 5px; border: 1px solid black;">' . currency($pro->discounted_price * $pro->qty, '') . '</td>
                </tr>

            ';
            } elseif ($pro->discount_mod == '%') {

                $html .= '

                <tr>
                    <td style="padding: 5px; border: 1px solid black;">' . $pro->pro_name . ' (' . $pro->sku . ')</td>
                    <td style="padding: 5px; border: 1px solid black;">' . currency($pro->price, '') . ' ( ' . $pro->discount . ' % )</td>
                    <td style="padding: 5px; border: 1px solid black;">' . $pro->qty . '</td>
                    <td style="padding: 5px; border: 1px solid black;">' . currency($pro->discounted_price * $pro->qty, '') . '</td>
                </tr>

            ';
            }
        }
    }

    $html .= '
            </table>
        </div>

        <!-- Total -->
        <div style="margin-bottom: 20px;">
            <table style="width: 30%; border-collapse: collapse;margin-left: auto;">
                <tr>
                    <td style="padding: 5px; text-align: right;font-size: 20px;font-weight: 800;">Total:</td>
                    <td style="padding: 5px; text-align: right;font-size: 20px;font-weight: 800; width: 230px;">' . currency($order->total, 'LKR') . '</td>
                </tr>
                <tr>
                    <td style="padding: 5px; text-align: right;">Paid:</td>
                    <td style="padding: 5px; text-align: right; width: 230px;">' . currency($cashin, 'LKR') . '</td>
                </tr>
                <tr>
                    <td style="padding: 5px; text-align: right;">Balance:</td>
                    <td style="padding: 5px; text-align: right; width: 230px;">' . currency(((float)$order->total - (float)$cashin), 'LKR') . '</td>
                </tr>
            </table>
        </div>


    ';

    $html .= '
        <!-- Footer -->
        <p style="text-align: center; font-weight: bold; border: 1px solid black;margin: 0;padding: 10px;">Thank You Please Come Again</p>
        <p style="text-align: center;margin-top: 20px;margin-bottom: -15px;">Software by WeFix Pvt Ltd</p>
        <p style="text-align: center;">076 028 2098</p>

        </body>
        </html>
    ';

    // $connector = new FilePrintConnector("/dev/usb/lp0");
    // $printer = new Printer($connector);

    $pdf = new Dompdf();
    $pdf->setPaper("A4", "portrait");
    $pdf->loadHtml($html, 'UTF-8');

    $GLOBALS['bodyHeight'] = 0;

    $pdf->setCallbacks([
        'myCallbacks' => [
            'event' => 'end_frame',
            'f' => function ($frame) {
                $node = $frame->get_node();
                if (strtolower($node->nodeName) === "body") {
                    $padding_box = $frame->get_padding_box();
                    $GLOBALS['bodyHeight'] += $padding_box['h'];
                }
            }
        ]
    ]);

    $pdf->render();
    unset($pdf);

    $docHeight = $GLOBALS['bodyHeight'] + 30;

    $pdf = new Dompdf();
    $pdf->setPaper("A4", "portrait");
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();
    $path = public_path('invoice/' . $inName);
    file_put_contents($path, $pdf->output());

    return (object)array('generated' => true, 'qr' => $qr_code_image);
}

function generateThermalInvoice($order_id, $cashin, $inName)
{
    $products = orderProducts::where('order_id', $order_id)->get();
    dd($products);
    $order = orders::where('order_number', $order_id)->get()[0];
    (float)$total = 0;
    $discount = 0;
    $service = $order->service_charge;
    $grandTotal = 0;
    (float)$roundup = $order->roundup;
    $company = PosDataController::company();
    $customer = json_decode($order->customer);
    $qr_code = base64_encode(generateQR("https://nmsware.com/customer-copy/" . substr(company()->pos_code, 0, 10) . "/" . $order_id, true));
    $qr_code_image = generateQR("https://nmsware.com/customer-copy/" . substr(company()->pos_code, 0, 10) . "/" . $order_id);
    $product_count = 0;

    $html = '

        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . $company->company_name . ' Invoice ' . $order_id . '</title>
            <style>
                @page {
                    margin: 10px;
                    height: auto;
                    width: 80mm;
                 }
                body { margin: 10px; }
            </style>
        </head>
        <body style="font-family: sans-serif;margin: 0;padding: 0;">
        <div class="invoice_wrap" style="width: 99%;padding: 40px 0;box-sizing: border-box;">
            <div><h2 style="text-align: center; margin: 0;">' . $company->company_name . '</h2></div>
            <div style="text-align: center; margin-bottom: 5px; font-size: 14px;">' . $company->industry . '</div>
            <hr style="border-width: 2px; border-color: #000;">
            <div style="text-align: center; font-size: 12px; margin-top: 5px;">' . getUserData($company->admin_id)->address . '</div>
            <div style="text-align: center; font-size: 12px; margin-bottom: 5px;">' . formatPhoneNumber(getUserData($company->admin_id)->phone) . '</div>
            <hr style="border-width: 1px; border-color: #000; border-style: dashed;">
            <div style="text-align: center;margin-top: 10px; font-size: 20px; font-weight: bold;text-transform: uppercase;margin-bottom: 3px;">Cash Receipt</div>
            <hr style="border-width: 3px; border-color: #505050; border-style: dotted; margin: 0 40px;">



            <div style="width: 100%;margin-top: 0px; font-size: 14px;">
                <table style="width: 100%;margin-bottom: 3px;">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody style="width: 100%;">
                        <tr style="width: 100%;">
                            <td style="font-size: 14px; width: 50%;"><div>Invoice No: ' . $order_id . '</div></td>
                            <td style="font-size: 14px; width: 50%; text-align: right;"><div>Date: ' . date('d-m-Y', strtotime($order->created_at)) . '</div></td>
                        </tr>

                        <tr style="width: 100%;">
                            <td style="font-size: 14px; width: 50%;"><div>Customer: ' . $customer->name . '</div></td>
                            <td style="font-size: 14px; width: 50%; text-align: right;"><div>Time: ' . date('H:i:s', strtotime($order->created_at)) . '</div></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <hr style="border-width: 3px; border-color: #505050; border-style: dotted; margin: 0 40px; margin-top: 0px;margin-bottom: 5px;">

                <table style="width: 100%;">
                    <thead>
                    <tr>
                        <th style="font-weight: 300; text-align: left; font-size: 15px;">Description</th>
                        <th style="font-weight: 300; text-align: center; font-size: 15px;">Price</th>
                        <th style="font-weight: 300; text-align: right; font-size: 15px;">Amount</th>
                    </tr>
                    </thead>
                    <tbody style="width: 100%;">
                        <tr>
                            <td colspan="3" style="text-align: right;"><span style="font-size: 12px;">(' . $company->currency . ')</span></td>
                        </tr>


    ';


    foreach ($products as $key => $product) {
        $product_count++;

        $sku = $product->sku == "temp" ? '' : $product->sku;

        (float)$total += $product->price * (float)$product->qty;

        $html .= '
            <tr style="width: 100%;">
                <td style="font-size: 14px;" colspan="3"><span style="margin-right: 10px;">' . $key + 1 . '. </span> <span style="margin-right: 10px;">' . $sku . ' </span> <span style="font-size: 15px;">' . $product->pro_name . '</span></td>
            </tr>
            <tr style="width: 100%;">
                <td style="font-size: 14px; border-bottom: #8d8d8d 2px dotted;"><div style="margin-left: 5px;padding-bottom: 7px;">' . currency($product->qty, '') . ' <span style="margin-left: 5px;">@</span></div></td>
                <td style="font-size: 14px; text-align: center;border-bottom: #8d8d8d 2px dotted;">' . $product->price . '</td>
                <td style="font-size: 14px; text-align: right;border-bottom: #8d8d8d 2px dotted;">' . currency($product->price * $product->qty, '') . '</td>
            </tr>
        ';
    }

    (float)$grandTotal = (($total + $service)) - $roundup;

    $html .= '

            </tbody>
            </table>


            <table style="width: 90%;float: right;margin-bottom: 30px;">
            <thead>
            <tr>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody style="width: 100%;">
                <tr style="width: 100%;">
                    <td style="font-size: 14px; width: 50%; text-align: right;"><div>Total</div></td>
                    <td style="font-size: 14px; width: 50%; text-align: right;"><div>' . currency($total, '') . '</div></td>
                </tr>

                <tr style="width: 100%;">
                    <td style="font-size: 14px; width: 50%;text-align: right;"><div>Service Charge</div></td>
                    <td style="font-size: 14px; width: 50%; text-align: right;"><div>' . currency($service, '') . '</div></td>
                </tr>

                <tr style="width: 100%;">
                    <td style="width: 50%;text-align: right;font-size: 20px;font-weight: bold;"><div>Grand Total</div></td>
                    <td style="font-size: 20px;font-weight: bold; width: 50%; text-align: right;"><div>' . currency($grandTotal, '') . '</div></td>
                </tr>
            </tbody>
            </table>

            <div style="font-size: 14px; font-weight: bold;text-align: center;margin-top: 50px;">Thank You!</div>
            <div style="font-size: 14px; text-align: center;">Please come again</div>

            <div style="font-size: 12px; text-align: center;margin-top: 20px;">Software by NMSware Technologies PVT LTD</div>
        </div>
        </body>
        </html>

    ';

    // $connector = new FilePrintConnector("/dev/usb/lp0");
    // $printer = new Printer($connector);

    $pdf = new Dompdf();
    $pdf->setPaper([0, 0, 227, 800]);
    $pdf->loadHtml($html, 'UTF-8');

    $GLOBALS['bodyHeight'] = 0;

    $pdf->setCallbacks([
        'myCallbacks' => [
            'event' => 'end_frame',
            'f' => function ($frame) {
                $node = $frame->get_node();
                if (strtolower($node->nodeName) === "body") {
                    $padding_box = $frame->get_padding_box();
                    $GLOBALS['bodyHeight'] += $padding_box['h'];
                }
            }
        ]
    ]);

    $pdf->render();
    unset($pdf);

    $docHeight = $GLOBALS['bodyHeight'] + 30;

    $pdf = new Dompdf();
    $pdf->setPaper([0, 0, 230, 850]);
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();
    $path = public_path('invoice/' . $inName);
    file_put_contents($path, $pdf->output());

    return (object)array('generated' => true, 'qr' => $qr_code_image);
}

function generateCreditPay($totalDue, $paid, $customer, $datetime, $bill_name)
{

    $watermark = '<div style="text-align: center;margin: 0 25px; margin-top: 10px; font-size: 15px; border-top: #000 1px solid;border-bottom: #000 1px solid;padding: 5px 0;">POS by NMSware Technoloigies <br> +94 76 667 3957</div>';
    if (company()->plan == 3) {
        $watermark = '';
    }

    $html = '

    <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . company()->company_name . ' Credit Payment</title>
            <style>
            @page {
                margin: 10px;
                height: auto;
                width: 80mm;
            }
            body { margin: 10px; }
            </style>
        </head>

        <body style="font-family: sans-serif;margin: 0;padding: 0;">
            <div class="invoice_wrap"
            style="width: 99%;padding: 20px 0px;box-sizing: border-box;">
                <div>
                    <h2 style="text-align: center; margin: 0;">' . company()->company_name . '</h2>
                </div>
                <div style="text-align: center;margin-top: 5px; margin-bottom: 5px; font-size: 14px;">' . company()->company_name . '</div>
                <hr style="border-width: 2px; border-color: #000;">
                <div style="text-align: center; font-size: 12px; margin-top: 5px;">' . getUserData(company()->admin_id)->address . '</div>
                <div style="text-align: center; font-size: 12px; margin-bottom: 5px;">' . formatPhoneNumber(getUserData(company()->admin_id)->phone) . '</div>
                <hr style="border-width: 1px; border-color: #000; border-style: dashed;">
                <div
                    style="text-align: center;margin-top: 10px; font-size: 20px; font-weight: bold;text-transform: uppercase;margin-bottom: 3px;">
                    Credit Payment</div>
                <hr style="border-width: 3px; border-color: #505050; border-style: dotted; margin: 0 40px;">
                <div style="width: 100%;margin-top: 0px; font-size: 14px;">
                    <table style="width: 100%;margin-bottom: 3px;">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody style="width: 100%;">
                            <tr style="width: 100%;">
                                <td style="font-size: 14px; width: 50%; text-align: left;">
                                    <div>Date: ' . date('d/m/Y', strtotime($datetime)) . '</div>
                                </td>
                                <td style="font-size: 14px; width: 50%; text-align: right;">
                                    <div>Time: ' . date('H:i:s', strtotime($datetime)) . '</div>
                                </td>
                            </tr>

                            <tr style="width: 100%;">
                                <td style="font-size: 14px; width: 50%;">
                                    <div>Customer: ' . substr(getCustomer($customer)->name, 0, 11) . '</div>
                                </td>
                                <td style="font-size: 14px; width: 50%; text-align: right;">
                                    <div></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr style="border-width: 3px; border-color: #505050; border-style: dotted; margin: 0 40px; margin-top: 0px;margin-bottom: 5px;">


                <table style="width: 90%;float: right; border-bottom: #000 2px solid;">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody style="width: 100%;">

                        <tr style="width: 100%;">
                            <td style="font-size: 14px; width: 50%;text-align: right;">
                                <div>Total Due</div>
                            </td>
                            <td style="font-size: 14px; width: 50%; text-align: right;">
                                <div>' . currency($totalDue, '') . '</div>
                            </td>
                        </tr>

                        <tr style="width: 100%;">
                            <td style="width: 50%;text-align: right;font-size: 20px;font-weight: bold;">
                                <div>Cash Paid</div>
                            </td>
                            <td style="font-size: 20px;font-weight: bold; width: 50%; text-align: right;">
                                <div>' . currency($paid, '') . '</div>
                            </td>
                        </tr>

                        <tr style="width: 100%;">
                            <td style="font-size: 14px; width: 50%;text-align: right;">
                                <div>Due Balance</div>
                            </td>
                            <td style="font-size: 14px; width: 50%; text-align: right;">
                                <div>' . currency((float)$totalDue - (float)$paid, '') . '</div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div style="font-weight: bold;text-align: center;margin-top: 15px;">Thank You!</div>
                <div style="text-align: center;">Please come again</div>
                ' . $watermark . '
            </div>
        </body>

    </html>

    ';

    $pdf = new Dompdf();
    $pdf->setPaper([0, 0, 227, 800]);
    $pdf->loadHtml($html, 'UTF-8');

    $GLOBALS['bodyHeight'] = 0;

    $pdf->setCallbacks([
        'myCallbacks' => [
            'event' => 'end_frame',
            'f' => function ($frame) {
                $node = $frame->get_node();
                if (strtolower($node->nodeName) === "body") {
                    $padding_box = $frame->get_padding_box();
                    $GLOBALS['bodyHeight'] += $padding_box['h'];
                }
            }
        ]
    ]);

    $pdf->render();
    unset($pdf);

    $docHeight = $GLOBALS['bodyHeight'] + 30;

    $pdf = new Dompdf();
    $pdf->setPaper([0, 0, 230, $docHeight]);
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->render();
    $path = public_path('credit-invoice/' . $bill_name);
    file_put_contents($path, $pdf->output());

    return true;
}

function sendInvitation($email)
{
    $verify = User::where('email', $email)->get();
    if ($verify && $verify->count() > 0) {

        $invite_code = Str::random(30) . rand(1111, 9999999);
        $invite = new PosInvitation();
        $invite->user_id = $verify[0]->id;
        $invite->pos_code = company()->pos_code;
        $invite->invitation_id = $invite_code;
        $invite->status = "pending";

        if ($invite->save()) {
            return true;
        }
    }
    return false;
}

function getOrder($order_number, $pos_id)
{
    $order = orders::where('order_number', $order_number)->get();
    if ($order) {
        foreach ($order as $key => $item) {
            if (substr($item['pos_code'], 0, 10) == $pos_id) {
                $products = orderProducts::where('order_id', $order_number)->get();
                if ($products && $products->count() > 0) {
                    return array(true, $item, $products);
                }
                break;
            }
        }
        return array(false, defaultValues());
    }
    return array(false, defaultValues());
}

function paymentMethod($method)
{
    if ($method == 'cash') {
        return (object)array("class" => 'success', 'method' => 'Cash');
    }
    if ($method == 'card') {
        return (object)array("class" => 'success', 'method' => 'Card/Online');
    }
    if ($method == 'credit') {
        return (object)array("class" => 'warning', 'method' => 'Credit');
    }

    return (object)array("class" => 'danger', 'method' => 'N/A');
}

function getCaptcha()
{
    return '<div class="cf-turnstile" data-sitekey="' . env('CAPTCHA_SITE_KEY') . '" data-callback="javascriptCallback"></div>';
}

function captchaVerify($cf_turnstile_response)
{
    $ip = '';
    $headers = getallheaders();
    $headers = array_change_key_case($headers, CASE_LOWER);
    if (array_key_exists('cf-connecting-ip', $headers)) {
        $ip = $headers['cf-connecting-ip'];
    } else {
        if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (array_key_exists('HTTP_X_FORWARDED', $_SERVER)) {
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        } else if (array_key_exists('HTTP_FORWARDED_FOR', $_SERVER)) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (array_key_exists('HTTP_FORWARDED', $_SERVER)) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        } else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    }

    if (empty($ip)) {
        return view('auth.login')->with('error', 'CAPTCHA verification error');
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'secret' => env('CAPTCHA_SITE_SECRET'),
        'response' => $cf_turnstile_response,
        'remoteip' => $ip,
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $output = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($output, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return (object) array('error' => true, 'msg' => 'Cannot verify CAPTCHA at this time');
    }
    if (!(is_array($response) && sizeof($response) > 0)) {
        return (object) array('error' => true, 'msg' => 'CAPTCHA verification error');
    }
    if (sizeof(array_diff(['success', 'hostname', 'error-codes', 'challenge_ts'], array_keys($response))) > 0) {
        // verification fail, not all required fields exists
        return (object) array('error' => true, 'msg' => 'CAPTCHA verification error');
    }
    if (!!$response['success'] && $response['hostname'] == env('APP_DOMAIN') && strtotime('now') - strtotime($response['challenge_ts']) < (strtotime('now') + 60)) {
        return (object) array('error' => false, 'msg' => 'CAPTCHA verification success');
    } else {
        return (object) array('error' => true, 'msg' => 'CAPTCHA verification error');
    }
    return (object) array('error' => true, 'msg' => 'CAPTCHA verification error');
}

function hasDashboard()
{
    if (Auth::check()) {
        $dash = posData::where('admin_id', Auth::user()->id)->where("status", 'active')->get();
        if ($dash && $dash->count() > 0) {
            return true;
        }
    }

    return false;
}

function statusToBootstrap($status)
{
    switch ($status) {
        case 'pending':
            return 'warning';
            break;
        case 'paid':
            return 'success';
            break;
        case 'returned':
            return 'danger';
            break;
        case 'blocked':
            return 'secondary';
            break;
        default:
            # code...
            break;
    }
}
