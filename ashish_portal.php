<?php

$organizations = [];
$emailTriggerResponse = "";


if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['searchQuery'])) {
    $searchQuery = htmlspecialchars($_POST['searchQuery']);

   
    $curl = curl_init();
    $url = "https://dev.sharkdomapi.com/organization/searchByPartialName?partialName=" . urlencode($searchQuery) . "&page=0&size=20";

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['accept: application/json']);

    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

   
    if (!$err) {
        $organizations = json_decode($response, true)['content'];
    } else {
        echo "cURL Error #:" . $err;
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sendEmailTrigger'])) {
    $triggerType = $_POST['triggerType'];
    $templateCode = $_POST['templateCode'];

    $postData = json_encode([
        'triggerType' => $triggerType,
        'templateCode' => $templateCode,
    ]);


    $curl = curl_init('https://dev.sharkdomapi.com/email/trigger');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
      
    ]);

    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);


    if (!$err) {
        $emailTriggerResponse = "Email trigger sent successfully.";
    } else {
        $emailTriggerResponse = "Error sending email trigger: " . $err;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Organization Search and Email Trigger</title>
<style>
body {
    font-family: Arial, sans-serif;
    padding: 20px;
    background-color: #f4f4f4;
    margin: 0;
    box-sizing: border-box;
}

.search-container, .email-trigger-container {
    text-align: center;
    margin-bottom: 20px;
}

input[type="text"], select, button {
    padding: 10px;
    margin: 5px;
    border-radius: 5px;
    border: 1px solid #ccc;
    outline: none;
}

button {
    cursor: pointer;
    background-color: #007bff;
    color: white;
    border: none;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #0056b3;
}

.results-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    gap: 20px;
}

.organization {
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    width: calc(33.333% - 20px);
    box-sizing: border-box;
    cursor: pointer;
}

.organization:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.2);
}

.organization h2 {
    color: #007bff;
    font-size: 20px;
    margin-top: 0;
}

.organization p {
    margin: 10px 0;
    line-height: 1.5;
}

@media (max-width: 768px) {
    .organization {
        width: calc(50% - 20px);
    }
}

@media (max-width: 480px) {
    .organization {
        width: 100%;
    }
}
</style>


</head>
<body>

<div class="search-container">
    <form method="POST" action="">
        <input type="text" name="searchQuery" id="searchInput" placeholder="Search by Organization Name...">
        <button type="submit">Search</button>
    </form>
</div>

<div class="email-trigger-container">
    <form method="POST" action="">
        <label for="triggerType">Trigger Type:</label>
        <select id="triggerType" name="triggerType">
            <option value="NOT_KYB">NOT_KYB</option>
         
        </select>

        <label for="templateCode">Template Code:</label>
        <select id="templateCode" name="templateCode">
            <option value="KYB_failed">KYB_failed</option>
         
        </select>

        <button type="submit" name="sendEmailTrigger">Send Email Trigger</button>
    </form>
</div>

<?php
if (!empty($emailTriggerResponse)) {
    echo "<p>$emailTriggerResponse</p>";
}

if (!empty($organizations)) {
    echo "<div class='results-container'>";
    foreach ($organizations as $org) {
        echo "<div class='organization'>";
      
       echo '<strong>Name:</strong> ' . htmlspecialchars($org['name'] ?? 'N/A') . '<br>';
echo '<strong>About:</strong> ' . htmlspecialchars($org['about'] ?? 'N/A') . '<br>';
echo '<strong>Email:</strong> ' . htmlspecialchars($org['primaryEmail'] ?? 'N/A') . '<br>';
echo '<strong>Verification Application Status:</strong> ' . htmlspecialchars($org['verificationApplicationStatus'] ?? 'N/A') . '<br>';
echo '<strong>Subscribed:</strong> ' . ($org['subscribed'] ? 'true' : 'false') . '<br>';
        echo "</div>";
    }
    echo "</div>";
}
?>

</body>
</html>
