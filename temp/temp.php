<?php

if (empty($_REQUEST['city'])) {
  error('city is a required parameter');
  exit;
}

$gearman = new \GearmanClient();
$gearman->addServer("gearmand", "4730");
$gearman->setTimeout(1 * 500000);
$temp = $gearman->doNormal('getTemp', json_encode(array(
  'city' => $_REQUEST['city']
)));

if (!$temp) {
  error($gearman->error());
} else {
  jsonify(array('temp' => $temp));
}

function error($message) {
  jsonify(array('error' => $message), 500);
}

function jsonify($params, $status = 200) {
  header(' ', true, $status);
  header('Content-Type: application/json');
  print json_encode($params);
}
