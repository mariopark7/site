<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // API credentials
    $api_id = 'API1283430176744';
    $api_password = 'dlrudcjf761125';
    $sms_type = 'P';
    $encoding = 'T';

    // Get phone numbers and message from the form
    $phone_numbers = explode("\n", $_POST['phone_numbers']); // Split the input by newlines
    $message = $_POST['message'];

    // Generate a random 6-digit sender ID
    $sender_id = mt_rand(100000, 999999);

    // Prepare the URL for API request
    $service_url = "http://api.smsala.com/api/SendSMS?api_id=$api_id&api_password=$api_password&sms_type=$sms_type&encoding=$encoding&sender_id=$sender_id";

    $response = [];

    // Iterate over each phone number
    foreach ($phone_numbers as $phone_number) {
        // Trim leading and trailing whitespaces from each phone number
        $phone_number = trim($phone_number);

        // Validate the phone number
        if (!empty($phone_number)) {
            // Set the phone number and message in the URL
            $url = $service_url . "&phonenumber=$phone_number&textmessage=$message";

            // Initialize cURL
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            // Execute the cURL request
            $curl_response = curl_exec($curl);

            if ($curl_response === false) {
                // An error occurred during the cURL request
                $info = curl_getinfo($curl);
                $response[] = 'An error occurred during curl exec. Additional info: ' . var_export($info, true);
            } else {
                // Decode the JSON response
                $decoded = json_decode($curl_response);

                if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
                    // An error occurred in the API call
                    $response[] = 'An error occurred: ' . $decoded->response->errormessage;
                } else {
                    // Successful API call
                    $response[] = 'Message sent to ' . $phone_number;
                }
            }

            // Close the cURL connection
            curl_close($curl);
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>SMS Sender</title>
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h1>SMS Sender</h1>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="phone_numbers">Phone Numbers (one per line):</label>
        <br>
        <textarea name="phone_numbers" id="phone_numbers" rows="5" cols="30"></textarea>
        <br>
        <label for="message">Message:</label>
        <br>
        <textarea name="message" id="message" rows="5" cols="30" onkeyup="updateCharacterCount()"></textarea>
        <br>
        <span id="character_count">Characters: 0</span>
        <br>
        <?php if (isset($response)) : ?>
            <?php foreach ($response as $message) : ?>
                <p><?php echo $message; ?></p>
            <?php endforeach; ?>
        <?php endif; ?>
        <br>
        <input type="submit" value="Send SMS">
    </form>

    <script>
        function updateCharacterCount() {
            var message = document
        .getElementById('message').value;
        var characterCount = message.length;
        var characterCountElement = document.getElementById('character_count');
        
        characterCountElement.innerText = 'Characters: ' + characterCount;
        
        if (characterCount > 70) {
            characterCountElement.classList.add('error');
        } else {
            characterCountElement.classList.remove('error');
        }
    }
    </script>
</body>
</html>
