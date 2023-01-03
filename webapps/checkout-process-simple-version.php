<?php
// This is just for very basic implementation reference, in production, you should validate the incoming requests and implement your backend more securely.
// Please refer to this docs for snap popup:
// https://docs.midtrans.com/en/snap/integration-guide?id=integration-steps-overview

namespace Midtrans;

require_once 'Midtrans.php';
// Set Your server key
// can find in Merchant Portal -> Settings -> Access keys
Config::$serverKey = 'SB-Mid-server-aGsdxxrtOFmJpCrmUfF6vi_r';
Config::$clientKey = 'SB-Mid-client-yQwE9eVe0E3VxgHa';

$count = $_REQUEST["count"];
$name_product = $_REQUEST["name_product"];
$jumlah = $_REQUEST["jumlah"];



// non-relevant function only used for demo/example purpose
printExampleWarningMessage();

// Uncomment for production environment
// Config::$isProduction = true;
Config::$isSanitized = Config::$is3ds = true;

// Required
$transaction_details = array(
    'order_id' => rand(),
    'gross_amount' => $count, // no decimal allowed for creditcard
);
// Optional
$item_details = array (
    array(
        'id' => 'a1',
        'price' => $count,
        'quantity' => $jumlah,
        'name' => $name_product
    ),
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
// Fill transaction details
$transaction = array(
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
// echo "snapToken = ".$snap_token;

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

?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo Config::$clientKey;?>"></script>
        <script type="text/javascript">
            snap.pay('<?php echo $snap_token?>', {
                // Optional
                onSuccess: function(result){
                     /* Anda dapat melakukan implementasi yang anda inginkan pada saat onSuccess dipanggil */
                    alert("payment success!"); console.log(result);
                },
                onPending: function(result){
                    /* Anda dapat melakukan implementasi yang anda inginkan pada saat onPending dipanggil */
                    alert("wating your payment!"); console.log(result);
                },
                onError: function(result){
                    /* Anda dapat melakukan implementasi yang anda inginkan pada saat onError dipanggil */
                    alert("payment failed!"); console.log(result);
                },
                onClose: function(){
                    /* Anda dapat melakukan implementasi yang anda inginkan pada saat onClose dipanggil */
                    alert('you closed the popup without finishing the payment');
                }
            });
        </script>
    </head>


</html>
