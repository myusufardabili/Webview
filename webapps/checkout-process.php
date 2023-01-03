<?php
// This is just for very basic implementation reference, in production, you should validate the incoming requests and implement your backend more securely.
// Please refer to this docs for snap popup:
// https://docs.midtrans.com/en/snap/integration-guide?id=integration-steps-overview

namespace Midtrans;

require_once 'Midtrans.php';
// Set Your server key
// can find in Merchant Portal -> Settings -> Access keys
Config::$serverKey = 'SB-Mid-server-g0tR-BihAKyexo5T0MazQCXA';
Config::$clientKey = 'SB-Mid-client-GM4FHFqKGj5f7ZGx';

// non-relevant function only used for demo/example purpose
printExampleWarningMessage();

// Uncomment for production environment
// Config::$isProduction = true;

// Enable sanitization
Config::$isSanitized = true;

// Enable 3D-Secure
Config::$is3ds = true;



// Uncomment for append and override notification URL
// Config::$appendNotifUrl = "https://example.com";
// Config::$overrideNotifUrl = "https://example.com";

// Required
$transaction_details = array(
    'order_id' => rand(),
    'gross_amount' => 94000, // no decimal allowed for creditcard
);

// Optional
$item1_details = array(
    'id' => 'a1',
    'price' => 18000,
    'quantity' => 3,
    'name' => "Apple"
);

// Optional
$item2_details = array(
    'id' => 'a2',
    'price' => 20000,
    'quantity' => 2,
    'name' => "Orange"
);

// Optional
$item_details = array ($item1_details, $item2_details);

// Optional
$billing_address = array(
    'first_name'    => "Andri",
    'last_name'     => "Litani",
    'address'       => "Mangga 20",
    'city'          => "Jakarta",
    'postal_code'   => "16602",
    'phone'         => "081122334455",
    'country_code'  => 'IDN'
);

// Optional
$shipping_address = array(
    'first_name'    => "Obet",
    'last_name'     => "Supriadi",
    'address'       => "Manggis 90",
    'city'          => "Jakarta",
    'postal_code'   => "16601",
    'phone'         => "08113366345",
    'country_code'  => 'IDN'
);

// Optional
$customer_details = array(
    'first_name'    => "Andri",
    'last_name'     => "Litani",
    'email'         => "andri@litani.com",
    'phone'         => "081122334455",
    'billing_address'  => $billing_address,
    'shipping_address' => $shipping_address
);

// Optional, remove this to display all available payment methods
$enable_payments = array('credit_card','cimb_clicks','mandiri_clickpay','echannel');

// Fill transaction details
$transaction = array(
    'enabled_payments' => $enable_payments,
    'transaction_details' => $transaction_details,
    'customer_details' => $customer_details,
    'item_details' => $item_details,
);

$snap_token = '';
try {
    $snap_token = Snap::getSnapToken($transaction);
}
catch (\Exception $e) {
    echo $e->getMessage();
}

echo "snapToken = ".$snap_token;

function printExampleWarningMessage() {
    if (strpos(Config::$serverKey, 'your ') != false ) {
        echo "<code>";
        echo "<h4>Please set your server key from sandbox</h4>";
        echo "In file: " . __FILE__;
        echo "<br>";
        echo "<br>";
        echo htmlspecialchars('Config::$serverKey = \'<your server key>\';');
        die();
    } 
}

// function payment_post(Request $request){
//     return $request;
// }


?>
 
<!DOCTYPE html>
<html>



    <form action="" id="submit_form" method="POST">
        <input name="json" id="json_callback">
    </form>

    <body>
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <button id="pay-button">Pay!</button>
        <pre><div id="result-json">JSON result will appear here after payment:<br></div></pre> 

        <!-- TODO: Remove ".sandbox" from script src URL for production environment. Also input your client key in "data-client-key" -->
        <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo Config::$clientKey;?>"></script>
        <script type="text/javascript">
            document.getElementById('pay-button').onclick = function(){
                // SnapToken acquired from previous step
                snap.pay('<?php echo $snap_token?>', {
                    // Optional
                    // onSuccess: function(result){
                    //     /* You may add your own js here, this is just example */ document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    //     // https://apipayment.mr-code.my.id/?count=${widget.count}&name_product=${widget.name_product}&jumlah=${widget.quantity}
                    // },
                    // // Optional
                    // onPending: function(result){
                    //     // window.location.replace("http://www.w3schools.com");
                    //     /* You may add your own js here, this is just example */ document.getElementById('json_callback').innerHTML += JSON.stringify(result, null, 2);
                    //     document.getElementById('submit_form').submit();
                    // },
                    // // Optional
                    // onError: function(result){
                    //     /* You may add your own js here, this is just example */ document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                    // }
                    onSuccess: function(result){
                     /* Anda dapat melakukan implementasi yang anda inginkan pada saat onSuccess dipanggil */
                        alert("payment success!"); console.log(result);
                        send_response_to_form(result);
                    },
                    onPending: function(result){
                        /* Anda dapat melakukan implementasi yang anda inginkan pada saat onPending dipanggil */
                        alert("wating your payment!"); console.log(result);
                        document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                        send_response_to_form(result);
                    },
                    onError: function(result){
                        /* Anda dapat melakukan implementasi yang anda inginkan pada saat onError dipanggil */
                        alert("payment failed!"); console.log(result);
                        send_response_to_form(result);
                    },
                    onClose: function(){
                        /* Anda dapat melakukan implementasi yang anda inginkan pada saat onClose dipanggil */
                        alert('you closed the popup without finishing the payment');
                        send_response_to_form(result);
                    }
                });
            };

            function send_response_to_form(result) {
                document.getElementById('json_callback').value = JSON.stringify(result);
                $('submit_form').submit();

            }

        </script>
    </body>
</html>
