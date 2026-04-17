<?php
$merchant_id     = '1235176';
$merchant_secret = 'MjYxNjc3NTk5NDEwNDYxNjg1MzA0MTcwOTc4NjA2MzU3Njk2ODk2NQ==';

// Handle PayHere notify callback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['merchant_id'])) {
    $order_id        = $_POST['order_id'];
    $payhere_amount  = $_POST['payhere_amount'];
    $payhere_currency= $_POST['payhere_currency'];
    $status_code     = $_POST['status_code'];
    $md5sig          = $_POST['md5sig'];

    $local_md5sig = strtoupper(md5(
        $_POST['merchant_id'] . $order_id . $payhere_amount .
        $payhere_currency . $status_code . strtoupper(md5($merchant_secret))
    ));

    $log = date('Y-m-d H:i:s') . " | Order: $order_id | Amount: $payhere_amount $payhere_currency | Status: ";
    $log .= ($local_md5sig === $md5sig && $status_code == 2) ? "✅ SUCCESS" : "❌ FAILED (status: $status_code)";
    file_put_contents('payment_log.txt', $log . "\n", FILE_APPEND);
    http_response_code(200);
    exit;
}

// Generate hash for checkout
$order_id = 'TEST_' . time();
$amount   = '100.00';
$currency = 'LKR';

$hash = strtoupper(md5(
    $merchant_id . $order_id . $amount . $currency . strtoupper(md5($merchant_secret))
));

$current_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PayHere Test</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: sans-serif; background: #f3f4f6; display: flex;
               justify-content: center; align-items: center; min-height: 100vh; }
        .card { background: white; border-radius: 12px; padding: 32px;
                width: 380px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 4px; color: #111; }
        .subtitle { color: #888; font-size: 14px; margin-bottom: 24px; }
        .item-row { display: flex; justify-content: space-between;
                    padding: 12px 0; border-bottom: 1px solid #f0f0f0; font-size: 15px; }
        .total-row { display: flex; justify-content: space-between;
                     padding: 16px 0; font-weight: bold; font-size: 16px; }
        .btn { width: 100%; padding: 14px; background: #f97316; color: white;
               border: none; border-radius: 8px; font-size: 16px;
               font-weight: 600; cursor: pointer; margin-top: 8px; }
        .btn:hover { background: #ea6c0a; }
        .sandbox-badge { text-align: center; margin-top: 16px; font-size: 12px; color: #aaa; }
        .test-cards { background: #f9fafb; border-radius: 8px; padding: 16px;
                      margin-top: 20px; font-size: 13px; }
        .test-cards h4 { margin-bottom: 10px; color: #555; }
        .card-row { display: flex; justify-content: space-between;
                    padding: 4px 0; color: #444; }
        .success { color: #16a34a; font-weight: 600; }
        .fail    { color: #dc2626; font-weight: 600; }

        /* Log viewer */
        .log-section { margin-top: 24px; }
        .log-section h4 { color: #555; margin-bottom: 8px; font-size: 13px; }
        .log-box { background: #1e1e1e; color: #d4d4d4; border-radius: 8px;
                   padding: 12px; font-size: 12px; font-family: monospace;
                   max-height: 120px; overflow-y: auto; min-height: 40px; }
        .refresh-btn { font-size: 12px; color: #f97316; background: none;
                       border: none; cursor: pointer; margin-left: 8px; }
    </style>
</head>
<body>
<div class="card">
    <h2>🧪 PayHere Test</h2>
    <p class="subtitle">Sandbox integration test</p>

    <div class="item-row">
        <span>Demo Product</span>
        <span>LKR 100.00</span>
    </div>
    <div class="item-row">
        <span>Shipping</span>
        <span>Free</span>
    </div>
    <div class="total-row">
        <span>Total</span>
        <span>LKR 100.00</span>
    </div>

    <!-- PayHere Form -->
    <form method="post" action="https://sandbox.payhere.lk/pay/checkout">
        <input type="hidden" name="merchant_id"  value="<?= $merchant_id ?>">
        <input type="hidden" name="return_url"   value="<?= $current_url ?>?status=return">
        <input type="hidden" name="cancel_url"   value="<?= $current_url ?>?status=cancel">
        <input type="hidden" name="notify_url"   value="<?= $current_url ?>"> <!-- ⚠️ Replace with ngrok URL -->

        <input type="hidden" name="order_id"     value="<?= $order_id ?>">
        <input type="hidden" name="items"        value="Demo Product">
        <input type="hidden" name="currency"     value="<?= $currency ?>">
        <input type="hidden" name="amount"       value="<?= $amount ?>">

        <input type="hidden" name="first_name"   value="John">
        <input type="hidden" name="last_name"    value="Doe">
        <input type="hidden" name="email"        value="test@example.com">
        <input type="hidden" name="phone"        value="0771234567">
        <input type="hidden" name="address"      value="No.1, Galle Road">
        <input type="hidden" name="city"         value="Colombo">
        <input type="hidden" name="country"      value="Sri Lanka">

        <input type="hidden" name="hash"         value="<?= $hash ?>">
        <button class="btn" type="submit">Pay with PayHere →</button>
    </form>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'return'): ?>
            <p style="margin-top:14px; color:#f97316; text-align:center;">
                ✅ Payment submitted! Check log below for confirmation.
            </p>
        <?php elseif ($_GET['status'] === 'cancel'): ?>
            <p style="margin-top:14px; color:#dc2626; text-align:center;">
                ❌ Payment was cancelled.
            </p>
        <?php endif; ?>
    <?php endif; ?>

    <div class="test-cards">
        <h4>🃏 Sandbox Test Cards</h4>
        <div class="card-row">
            <span>4916217501611292</span>
            <span class="success">✅ Success</span>
        </div>
        <div class="card-row">
            <span>4012001037141112</span>
            <span class="fail">❌ Declined</span>
        </div>
        <div class="card-row" style="color:#888; font-size:12px; margin-top:6px;">
            <span>Any future expiry · Any CVV</span>
        </div>
    </div>

    <div class="log-section">
        <h4>
            📋 Payment Log
            <button class="refresh-btn" onclick="loadLog()">↻ Refresh</button>
        </h4>
        <div class="log-box" id="log-box">Waiting for payments...</div>
    </div>

    <p class="sandbox-badge">🔒 Sandbox Mode — No real payments</p>
</div>

<script>
function loadLog() {
    fetch('payment_log.txt?t=' + Date.now())
        .then(r => r.text())
        .then(text => {
            const box = document.getElementById('log-box');
            box.textContent = text.trim() || 'No payments yet.';
            box.scrollTop = box.scrollHeight;
        })
        .catch(() => {
            document.getElementById('log-box').textContent = 'No log file yet.';
        });
}
loadLog();
// Auto-refresh log every 5 seconds
setInterval(loadLog, 5000);
</script>
</body>
</html>