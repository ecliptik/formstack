<?php

$worker = new GearmanWorker();
$worker->addServer("gearmand", "4730");
$worker->addFunction('getTemp', 'getTemp');

print "Waiting for a job...\n";
while($worker->work()) {
  if ($worker->returnCode() !== GEARMAN_SUCCESS) {
    print 'Exiting with return code: ' . $gmworker->returnCode();
    break;
  }
}

function getTemp($job) {

  $ch = curl_init();

  $payload = json_decode($job->workload());
  print "Getting current temperature for {$payload->city}...\n";
  $city = urlencode($payload->city);

  curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => "http://api.openweathermap.org/data/2.5/weather?q=$city,us"
  ));
  $result = json_decode(curl_exec($ch));

  return round((($result->main->temp - 273.15) * 1.8) + 32);
}
