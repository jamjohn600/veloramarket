<?php

setlocale(LC_MONETARY, 'en_NG');
include('dbConnection.php');
$database = new database();
$conn = $database->getConnection();


class PortalUtility
{
       public function insertIntoPaystacks($conn,$trans_id,$domain,$status,$reference,$amount,$message,$paid_at,$created_at,$channel,$currency,$ip_address,$first_name,$lastname,$email,$phone,$mdaCode,$revenueCode){
        
        $status = "";
		if(empty($reference) || empty($amount) ){
			$this->payment_log($reference . "||" .  $mdaCode . "||" . $amount . "||" . $paid_at . "||" . date("Y-m-d h:m:s"));
			$status = json_encode(array("requestSuccessful" =>false, "scheduleId" => $reference, "responseMessage" => "emptyRequiredFields", "responseCode" => "07"));
		}else{

		$existing = $this->check_existing_record($conn, $reference);
		if ($existing == 1) {
			$this->payment_log($reference . "||" .  $mdaCode . "||" . $amount . "||" . $paid_at . "||"  . date("Y-m-d h:m:s") . "Already Exist");
			$status = json_encode(array("requestSuccessful" => true, "scheduleId" => $reference, "responseMessage" => "duplicate response", "responseCode" => "01"));
		}else {
		    
	
		     $receiptId = substr(str_shuffle(str_repeat("0123456789", 10)), 0, 10);
			$this->payment_log($reference . "||" .  $mdaCode . "||" . $amount . "||" . $paid_at . "||"  . date("Y-m-d h:m:s"));
        
        	$payloadSql = "INSERT INTO `payload`(`tran_id`, `domain`, `status`, `reference`, `amount`, `message`, `paid_at`, `created_at`, `channel`, `currency`, `ip_address`, `first_name` ,`last_name`, `email`, `phone`, `mdacode`, `revenueCode`) values ('".$trans_id."','".$domain."','".$status."','".$reference."','".$amount."','".$message."','".$paid_at."','".$created_at."','".$channel."','".$currency."','".$ip_address."','".$first_name."','".$lastname."','".$email."','".$phone."','".$mdaCode."','".$revenueCode."')";
        $payloadQry = mysqli_query($conn, $payloadSql);
        
        if ($payloadQry) {
			     $this->updatePaymentStatus($conn,$mdaCode,$email,$amount,$amount);
				$status = json_encode(array("requestSuccessful" => true, "scheduleId" => $reference,"receiptId"=>$receiptId, "responseMessage" => "success", "responseCode" => "00"));
				$this->payment_log($reference . "||" .  $mdaCode . "||" . $amount . "||" . $paid_at . "||"  . date("Y-m-d h:m:s"));
			} else {
				$status = json_encode(array("requestSuccessful" => true, "scheduleId" => $reference, "responseMessage" => "rejected transaction", "responseCode" => "02"));
				$this->payment_log($reference . "||" .  $mdaCode . "||" . $amount . "||" . $paid_at . "||"  . date("Y-m-d h:m:s"));
			
		   }
		}
		}
		return $status;	
    }
    
    
    public function updatePaymentStatus($conn, $transaction, $email, $amount, $total_deposited) {
        $username = $this->fetch_username($conn, $email);
    
        // Check if a wallet exists for this username
        $checkWalletSql = "SELECT * FROM `wallets` WHERE `username` = '$username'";
        $checkWalletResult = mysqli_query($conn, $checkWalletSql);
    
        if (mysqli_num_rows($checkWalletResult) > 0) {
            // Wallet exists, update the balance and total deposited
            $updateWalletSql = "UPDATE `wallets` 
                                SET `balance` = `balance` + '$amount', 
                                    `total_deposited` = `total_deposited` + '$amount' 
                                WHERE `username` = '$username'";
            $stmt = mysqli_query($conn, $updateWalletSql);
        } else {
            // Wallet doesn't exist, insert a new record
            $sql = "INSERT INTO `wallets`(`wallet_id`, `username`, `balance`, `total_deposited`) 
                    VALUES ('$transaction','$username','$amount','$total_deposited')";
            $stmt = mysqli_query($conn, $sql);
        }
    
        // Insert into transactions table
        $this->insertIntoTransactions($conn, $email, $amount, 'credit');
    }
    
    public function insertIntoTransactions($conn, $email, $amount, $type)
    {
        $transactionSql = "INSERT INTO `transactions` (`email`, `amount`, `type`, `transaction_date`) 
                           VALUES ('$email', '$amount', '$type', NOW())";
        $stmt = mysqli_query($conn, $transactionSql);
        
        if (!$stmt) {
            $this->payment_log("Failed to insert transaction for email: $email, amount: $amount, type: $type");
        }
    }

    public function check_existing_record($conn, $settlementId)
	{
		$query = "SELECT * FROM payload where reference = '$settlementId'"; 
		$stmt = mysqli_query($conn, $query);
		$user = mysqli_num_rows($stmt);
		return $user;
	}
	
	
	public function fetch_username($conn, $email)
	{
		$query = "SELECT * FROM users where email = '$email'"; 
		$stmt = mysqli_query($conn, $query);
		$array = mysqli_fetch_array($stmt,MYSQLI_ASSOC);
		return $array['username'];
	}
    
    function payment_log($log_msg)
	{
		$log_filename = "payment_log";
		if (!file_exists($log_filename)) {
			// create directory/folder uploads.
			mkdir($log_filename, 0777, true);
		}
		$log_file_data = $log_filename . '/log_' . date('d-M-Y') . '.log';
		// if you don't add `FILE_APPEND`, the file will be erased each time you add a log
		file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
	}
}