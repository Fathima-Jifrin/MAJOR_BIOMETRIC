<?php
$API = "c8cd63e1bf13c5016881652983fb615a";
$PHONE = "8590594735";        // Recipient's phone number
$CNAME = "Felishya Francis";   // Customer's name
$ACCOUNT_TYPE = "SBI SAVINGS ACCOUNT";  // Account type
$BRANCH = "EKM BRANCH";        // Branch name
$AMT = "Rs.500";              // Amount being debited

// Create the message with all the required details
$MESSAGE = "$CNAME, $ACCOUNT_TYPE, $BRANCH: Amount of $AMT Debited from Account";

// URL to send the request to, including parameters
$URL = "https://sms.renflair.in/V6.php?API=$API&PHONE=$PHONE&AMT=$AMT&MESSAGE=" . urlencode($MESSAGE);

// Initialize cURL session
$curl = curl_init($URL);

// Set cURL options
curl_setopt($curl, CURLOPT_URL, $URL);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL request
$resp = curl_exec($curl);

// Check for cURL errors
if ($resp === false) {
    // If cURL fails, print the error
    echo "cURL error: " . curl_error($curl);
    curl_close($curl);  // Close the cURL session
    exit;
}

// Close the cURL session
curl_close($curl);

// Log or print the raw response for debugging
echo "Raw Response: " . $resp; // This will print the raw API response

// Decode the response from JSON
$data = json_decode($resp, true);

// Check if decoding was successful
if ($data !== null) {
    // Check if the response contains a 'status' and if it's 'success'
    if (isset($data['status']) && $data['status'] == 'success') {
        echo "Message sent successfully!";
    } else {
        // Provide more details about the error if available
        echo "Failed to send message. Response: " . (isset($data['error_message']) ? $data['error_message'] : "No error message provided");
    }
} else {
    // If the response is not valid JSON, print the raw response for debugging
    echo "Failed to decode JSON response. Raw response: " . $resp;
}
?>
