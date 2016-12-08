// Load the Google API PHP Client Library.
require_once 'google-api-php-client-2.1.0/vendor/autoload.php';

$profile_ID = 'GA_PROFILE_ID';
$analytics = initializeAnalytics();
$results = getResults($analytics,$profile_ID);

$users = 0;
if (count($results->getRows()) > 0) {
    $rows = $results->getRows();
    $users = $rows[0][0];
}

$result = output($users);
print_r($result);


function initializeAnalytics()
{
  // Creates and returns the Analytics Reporting service object.

  // Use the developers console and download your service account
  // credentials in JSON format. Place them in this directory or
  // change the key file location if necessary.
  $KEY_FILE_LOCATION = 'YOUR_KEY_FILE_LOCATION';

  // Create and configure a new client object.
  $client = new Google_Client();
  $client->setApplicationName("cloudBit + Analytics");
  $client->setAuthConfig($KEY_FILE_LOCATION);
  $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
  $analytics = new Google_Service_Analytics($client);

  return $analytics;
}

function getResults($analytics, $profileId) {
	$results = $analytics->data_realtime->get(
      'ga:' . $profileId,
      'rt:activeUsers'
      );
	return $results;
}

function output($value){
	$max = 5;
	$device_id = 'YOUR_CLOUDBIT_DEVICE_ID';
	$access_token = 'YOUR_CLOUDBIT_ACCESS_TOKEN';
	$url = "https://api-http.littlebitscloud.cc/v2/devices/{$device_id}/output";

	$percent = round($value / $max * 100);
    if($persent > 0){$persent = 100;}

	$params = json_encode(array("percent" => $percent, "duration_ms" => -1));

	$defaults = array(
		CURLOPT_URL => $url,
		CURLOPT_HEADER => false,
		CURLOPT_POST => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POSTFIELDS => $params,
		CURLOPT_HTTPHEADER => array(
	  		"Authorization: Bearer {$access_token}",
			"Content-type: application/json",
		),
	);

	$ch = curl_init();
	curl_setopt_array($ch,$defaults);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
?>
