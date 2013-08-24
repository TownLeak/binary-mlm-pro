<?php 
function wpmlm_run_pay_cycle()
{
	
	$returnVar  = wpmlm_run_pay_cycle_functions();	
	return $returnVar; 
	
}

function wpmlm_run_pay_cycle_functions()
{
	

	$payoutMasterId = createPayoutMaster(); 
	
	global $table_prefix, $wpdb; 
	 $sql=  "SELECT 
				id, date_notified, parent_id, child_ids, amount, SUM(amount) AS commission  
			FROM 
				{$table_prefix}mlm_commission 
			WHERE 
				payout_id = 0
			GROUP BY 
				parent_id	
			";
	
	//_e($sql); exit; 
	
		
	$rs = $wpdb->get_results($sql); 
	if($wpdb->num_rows > 0)
	{
		foreach($rs as $row)
		{
			
			$userId = $row->parent_id; 
			$commissionAmt = $row->commission;
			$bonusAmt = getBonusAmountById($userId);
			$directReffComm = getReferralCommissionById($userId); 
			$payout_settings = get_option('wp_mlm_payout_settings');
			$tax = $payout_settings['tds'];
			$capLimitAmt = $payout_settings['cap_limit_amount']; 
			$totalAmt = $commissionAmt + $bonusAmt+ $directReffComm;
			$service_charge = $payout_settings['service_charge'];
			$capLimitAmt = $payout_settings['cap_limit_amount']; 
			if($totalAmt <= $capLimitAmt){
			$sub_total = $totalAmt;
			$taxAmt = round(($sub_total)* $tax/100, 2);
			$netAmt = round($sub_total - $taxAmt - $service_charge);}
			else{
			$sub_total = $capLimitAmt;
			$taxAmt = round(($sub_total)* $tax/100, 2);
			$netAmt = round($sub_total - $taxAmt - $service_charge); }
				
					
			/***********************************************************
			INSERT INTO PAYOUT TABLE
			***********************************************************/ 
			 $sql_payout = "INSERT INTO 
							{$table_prefix}mlm_payout
							(
								user_id, date, payout_id, commission_amount,referral_commission_amount , 
								bonus_amount,total_amt,capped_amt,cap_limit, tax, service_charge
							) 
							VALUES 					
							(
								'".$userId."', '".date('Y-m-d H:i:s')."', '".$payoutMasterId."', '".$commissionAmt."', '".$directReffComm."', 
								'".$bonusAmt."','".$totalAmt."','".$netAmt."', '".$capLimitAmt."', '".$taxAmt."', '".$service_charge."'
							)"; 
			mysql_query("UPDATE {$table_prefix}mlm_referral_commission set payout_id='$payoutMasterId' where sponsor_id='$userId' AND payout_id=0");		
			$rs_payout = mysql_query($sql_payout);
			$insert_id = mysql_insert_id();
			
			/***********************************************************
			Update Commission table Payout Id
			***********************************************************/ 
			if(isset($insert_id) && $insert_id >0)
			{
				$sql_comm = "UPDATE {$table_prefix}mlm_commission 
								SET 
									payout_id= '".$payoutMasterId."'
								WHERE 
									parent_id = '".$userId."' AND 
									payout_id = '0'
								";
				$rs_comm = mysql_query($sql_comm); 					
			
			}
			/***********************************************************
			Update Bonus table Payout Id
			***********************************************************/ 
			if(isset($insert_id) && $insert_id >0)
			{
				$sql_bon = "UPDATE {$table_prefix}mlm_bonus 
								SET 
									payout_id= '".$payoutMasterId."'
								WHERE 
									mlm_user_id = '".$userId."' AND 
									payout_id = '0'
								";
				$rs_bon = mysql_query($sql_bon); 					
			
			}
						
		}	
	
	}
	//return $sql_payout;
	return "Payout Run Successfully";
	
}



function createPayoutMaster()
{
	global $table_prefix; 
	$mlm_payout = get_option('wp_mlm_payout_settings');
	$capLimitAmt = $mlm_payout['cap_limit_amount'];
	$sql = "INSERT INTO {$table_prefix}mlm_payout_master(date,cap_limit) VALUES ('".date('Y-m-d H:i:s')."','$capLimitAmt')"; 
	$res = mysql_query($sql);
	$pay_master_id = mysql_insert_id();
	
	return $pay_master_id; 
}

function getBonusAmountById($userId)
{
	global $table_prefix; 
	 $sql = "SELECT 
				amount, SUM(amount) AS bonus, payout_id 
			FROM 
				{$table_prefix}mlm_bonus 
			WHERE 
				mlm_user_id ='".$userId."' and payout_id = 0
			GROUP BY 
				mlm_user_id 			
		";
	
	$rs = mysql_query($sql);
	
	if(mysql_num_rows($rs)>0)
	{
		$row = mysql_fetch_array($rs); 
		
		$bonus = $row['bonus']; 
		
	}	
	
	if(!empty($bonus)) return  $bonus;
 
}

function wpmlm_run_pay_display_functions()
{ 
	global $table_prefix, $wpdb; 
 	$sql=  "SELECT 
				id, date_notified, parent_id, child_ids, amount, SUM(amount) AS commission  
			FROM 
				{$table_prefix}mlm_commission 
			WHERE 
				payout_id = 0
			GROUP BY 
				parent_id	
			";
	
	//_e($sql); exit; 
	
		
	$rs = $wpdb->get_results($sql); 
	if($wpdb->num_rows > 0)
	{
		$i = 0;		
		foreach($rs as $row)
		{
			$userId = $row->parent_id;
			$commissionAmt = $row->commission;
			$directReffComm = getReferralCommissionById($userId);
			$bonusAmt = getBonusAmountById($userId); 
			$totalAmt = $commissionAmt + $directReffComm + $bonusAmt;
			$payout_settings = get_option('wp_mlm_payout_settings');
			$tax = $payout_settings['tds'];
			$capLimitAmt = $payout_settings['cap_limit_amount']; 
				if($totalAmt <= $capLimitAmt){
				$total = $totalAmt;
				$taxAmt = round(($total)* $tax/100, 2); }
				else{ 
				$total = $capLimitAmt;
				$taxAmt = round(($total)* $tax/100, 2); }
					
			$service_charge = $payout_settings['service_charge'];	
			
				$user_info = getUserInfoByMlmUserId($userId);	
							
				$displayDataArray[$i]	['username'] = $user_info->user_login;
				$displayDataArray[$i]	['first_name'] = $user_info->first_name;
				$displayDataArray[$i]	['last_name'] = $user_info->last_name;
				$displayDataArray[$i]	['commission'] = $commissionAmt;
				$displayDataArray[$i]	['dirRefcommission'] = $directReffComm;
				$displayDataArray[$i]	['total_amt'] = $totalAmt;
				$displayDataArray[$i]	['cap_limit'] = $capLimitAmt;
				$displayDataArray[$i]	['bonus'] = $bonusAmt;
				$displayDataArray[$i]	['tax'] = $taxAmt;
				$displayDataArray[$i]	['service_charge'] = $service_charge==""?0.00:$service_charge;
				$displayDataArray[$i]	['net_amount'] = ($total- $service_charge - $taxAmt);
				$i++;
		}	
	
	}
	else
				$displayDataArray = "None";
			
return $displayDataArray;
	
}

function getUserInfoByMlmUserId($mlmUserId)
{   
		global $table_prefix, $wpdb;
		$user_id = $wpdb->get_var("SELECT user_id FROM {$table_prefix}mlm_users WHERE id = $mlmUserId");
		$user_info = get_userdata($user_id);
		return $user_info;
			
	}

function getReferralCommissionById($userId)
{  
	global $table_prefix; 
	$sql = "SELECT SUM(amount) AS reff_comm FROM {$table_prefix}mlm_referral_commission WHERE   sponsor_id ='$userId' AND   payout_id=0 "; 
	$rs = mysql_query($sql);
	if(mysql_num_rows($rs)>0){$row = mysql_fetch_array($rs);$reff_comm = $row['reff_comm']; }	
	return $reff_comm;
}

?>
