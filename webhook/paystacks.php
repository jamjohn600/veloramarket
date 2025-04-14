<?php
include_once('inc/portal.php');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");


$data = json_decode(@file_get_contents("php://input"), true);
$portal = new PortalUtility();

  $trans_id =  trim(mysqli_real_escape_string($conn, !empty($data['data']['id']) ? $data['data']['id']: ""));
  $domain =  trim(mysqli_real_escape_string($conn, !empty($data['data']['domain']) ? $data['data']['domain']: ""));
  $status = trim(mysqli_real_escape_string($conn, !empty($data['data']['status']) ? $data['data']['status']: ""));
  $reference = trim(mysqli_real_escape_string($conn, !empty($data['data']['reference']) ? $data['data']['reference']: ""));
  $amt = trim(mysqli_real_escape_string($conn, !empty($data['data']['amount']) ? $data['data']['amount']: ""));
  $message = trim(mysqli_real_escape_string($conn, !empty($data['data']['message']) ? $data['data']['message']: ""));
  $paid_at  = trim(mysqli_real_escape_string($conn, !empty($data['data']['paid_at']) ? $data['data']['paid_at']: ""));
  $created_at  = trim(mysqli_real_escape_string($conn, !empty($data['data']['created_at']) ? $data['data']['created_at']: ""));
  $channel = trim(mysqli_real_escape_string($conn, !empty($data['data']['channel']) ? $data['data']['channel']: ""));
  $currency = trim(mysqli_real_escape_string($conn, !empty($data['data']['currency']) ? $data['data']['currency']: ""));
  $first_name = trim(mysqli_real_escape_string($conn, !empty($data['data']['customer']['first_name'])? $data['data']['customer']['first_name'] : ""));
  $lastname = trim(mysqli_real_escape_string($conn, !empty($data['data']['customer']['last_name'])? $data['data']['customer']['last_name'] : ""));
  $email = trim(mysqli_real_escape_string($conn, !empty($data['data']['customer']['email'])? $data['data']['customer']['email'] : ""));
  $phone = trim(mysqli_real_escape_string($conn, !empty($data['data']['customer']['phone'])? $data['data']['customer']['phone'] : ""));
 $revenueCode = trim(mysqli_real_escape_string($conn, !empty($data['data']['metadata']['value'])? $data['data']['metadata']['value'] : ""));
 $mdaCode = trim(mysqli_real_escape_string($conn, !empty($data['data']['metadata']['invoice_id'])? $data['data']['metadata']['invoice_id'] : ""));
 
  $ip_address  = trim(mysqli_real_escape_string($conn, !empty($data['data']['ip_address']) ? $data['data']['ip_address']: ""));
  
	$unique = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 13)), 0, 13);

$amount = floatval($amt) / 100;
$user = $portal->insertIntoPaystacks($conn,$trans_id,$domain,$status,$reference,$amount,$message,$paid_at,$created_at,$channel,$currency,$ip_address,$first_name,$lastname,$email,$phone,$mdaCode,$revenueCode);
  
?>