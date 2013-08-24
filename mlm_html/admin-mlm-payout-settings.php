<?php
function mlmPayout()
{
	//get database table prefix
	$table_prefix = mlm_core_get_table_prefix();
	
	$error = '';
	$chk = 'error';
	//most outer if condition 
	if(isset($_POST['mlm_payout_settings']))
	{
		$pair1 = sanitize_text_field( $_POST['pair1'] );
		$pair2 = sanitize_text_field( $_POST['pair2'] );
		$initial_pair = sanitize_text_field( $_POST['initial_pair'] );
		$initial_amount = sanitize_text_field( $_POST['initial_amount'] );
		$further_amount = sanitize_text_field( $_POST['further_amount'] );

		if ( checkPair($pair1, $pair2) ) 
			$error .= "\n Your pair ratio is wrong.";
			
		if ( checkInputField($initial_pair) ) 
			$error .= "\n Your initial pair value is wrong.";
			
		if ( checkInputField($initial_amount) ) 
			$error .= "\n Your initial amount value is wrong.";
		
		if ( checkInitial($further_amount) ) 
			$error .= "\n Your further amount value is wrong.";
			
		//if any error occoured
		if(!empty($error))
			$error = nl2br($error);
		else
		{
			$chk = '';
			update_option('wp_mlm_payout_settings', $_POST);
			$url = get_bloginfo('url')."/wp-admin/admin.php?page=admin-settings&tab=bonus";
			_e("<script>window.location='$url'</script>");
			$msg = "<span style='color:green;'>Your payout settings has been successfully updated.</span>";
		}
	}// end outer if condition
	if($chk!='')
	{
		$mlm_settings = get_option('wp_mlm_payout_settings');
		?>


<div class='wrap1'>
	<h2><?php _e('Payout Settings','binary-mlm-pro');?>  </h2>
	<div class="notibar msginfo">
		<a class="close"></a>
		<p><?php _e('Use this screen to define the basic parameters of your pay plan.','binary-mlm-pro');?></p>
		<p><strong><?php _e('Pair','binary-mlm-pro');?> - </strong><?php _e('How many paid members in the left and right leg individually will make 1 pair for calculating commissions.','binary-mlm-pro');?></p>
		<p><strong><?php _e('Initial Pairs','binary-mlm-pro');?></strong> - <?php _e('To incentivise members in the initial stages the amount paid for initial pairs is slightly higher than the regular payout amount.','binary-mlm-pro');?>
		<?php _e('Specify the number of initial pairs for which you would like to pay a higher payout amount.','binary-mlm-pro');?></p>
		<p><strong><?php _e('Initial Pair Amount','binary-mlm-pro');?> - </strong><?php _e('This is the per pair amount that is paid for the each Initial Pair.','binary-mlm-pro');?></p>
		<p><strong><?php _e('Further Pair Amount','binary-mlm-pro');?> - </strong> <?php _e('This is the payout amount for every Pair after the Initial Pairs.','binary-mlm-pro');?></p>
		<p><strong><?php _e('Direct Referral Commission','binary-mlm-pro');?> - </strong><?php _e('This is the amount paid to a sponsor for sponsoring a new member in the network. This amount is paid for an infinite number of referrals.','binary-mlm-pro');?></p>
		<p><strong><?php _e('Service Charges','binary-mlm-pro');?> - </strong> <?php _e('An amount that is deducted from each Payout paid to the member as a fixed Service Charge. eg. $2 as processing fee for each payout.','binary-mlm-pro');?></p>
		<p><strong><?php _e('Tax Deduction','binary-mlm-pro');?> - </strong><?php _e('Some countries have a legislation of deducting Income Tax at source while making commission payments to your members.','binary-mlm-pro');?></p>
    	<p><strong><?php _e('Cap Limit','binary-mlm-pro');?> - </strong><?php _e('Maximum amount that can be paid to a member in one payout cycle. Anything above the cap limit will be flushed out.','binary-mlm-pro');?></p>
	</div>
	<?php if($error) :?>
	<div class="notibar msgerror">
		<a class="close"></a>
		<p> <strong><?php _e('Please Correct the following Error','binary-mlm-pro');?> :</strong> <?php _e($error); ?></p>
	</div>
	<?php endif; ?>
<?php
	if(empty($mlm_settings))
	{
?>
		<form name="admin_payout_settings" method="post" action="">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('admin-mlm-pair');"><?php _e('Pair','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
				<input type="text" name="pair1" id="pair1" size="2" value="<?php if(!empty($_POST['pair1'])) _e(htmlentities($_POST['pair1']));?>"> : 
				<input type="text" name="pair2" id="pair2" size="2" value="<?php if(!empty($_POST['pair2'])) _e(htmlentities($_POST['pair2']));?>">
				<div class="toggle-visibility" id="admin-mlm-pair"><?php _e('Please mention here pair ratio.','binary-mlm-pro');?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-initial-pair');">
				<?php _e('Initial Pairs','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
				<input type="text" name="initial_pair" id="initial_pair" size="2" value="<?php if(!empty($_POST['initial_pair'])) _e(htmlentities($_POST['initial_pair']));?>">
				<div class="toggle-visibility" id="admin-mlm-initial-pair"><?php _e('Please mention here initial pair.','binary-mlm-pro');?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
			<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-initial-amount');">
			<?php _e('Initial Pair Amount','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
			<input type="text" name="initial_amount" id="initial_amount" size="10" value="<?php if(!empty($_POST['initial_amount'])) _e(htmlentities($_POST['initial_amount']));?>">
				<div class="toggle-visibility" id="admin-mlm-initial-amount"><?php _e('Please mention here initial amount.','binary-mlm-pro');?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-further-amount');">
				<?php _e('Further Pair Amount','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
			<input type="text" name="further_amount" id="further_amount" size="10" value="<?php if(!empty($_POST['further_amount'])) _e(htmlentities($_POST['further_amount']));?>">
				<div class="toggle-visibility" id="admin-mlm-further-amount"><?php _e('Please mention here further pair amount.','binary-mlm-pro');?></div>
			</td>
		</tr>
		<tr>
	<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-direct-referal-commission');">
				<?php _e('Direct Referral Commission','binary-mlm-pro');?>: </a>
			</th>
			<td>
			<input type="text" name="referral_commission_amount" id="referral_commission_amount" size="10" value="<?php if(!empty($mlm_settings['referral_commission_amount'])) _e($mlm_settings['referral_commission_amount']);?>">
				<div class="toggle-visibility" id="admin-mlm-direct-referal-commission"><?php _e('Please specify referral_commission_amount.','binary-mlm-pro');?></div>
			</td>
		</tr>
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-service-charege');">
				<?php _e('Service Charge (If any)','binary-mlm-pro');?> : </a>
			</th>
			<td>
			<input type="text" name="service_charge" id="service_charge" size="10" value="<?php if(!empty($_POST['service_charge'])) _e(htmlentities($_POST['service_charge']));?>">
				<div class="toggle-visibility" id="admin-mlm-service-charege"><?php _e('Please specify service charge.','binary-mlm-pro');?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-tds');">
				<?php _e('Tax Deduction','binary-mlm-pro');?> :</a>
			</th>
			<td>
			<input type="text" name="tds" id="tds" size="10" value="<?php if(!empty($_POST['tds'])) _e(htmlentities($_POST['tds']));?>">&nbsp;%
				<div class="toggle-visibility" id="admin-mlm-tds"><?php _e('Please specify TDS.','binary-mlm-pro');?></div>
			</td>
		</tr>
        <tr>
	<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-cap_limit');">
				<?php _e('Cap Limit Amount','binary-mlm-pro');?> :</a>
			</th>
			<td>
			<input type="text" name="cap_limit_amount" id="cap_limit_amount" size="10" value="<?php if(!empty($mlm_settings['cap_limit_amount'])) _e($mlm_settings['cap_limit_amount']);?>">
				<div class="toggle-visibility" id="admin-mlm-cap_limit"><?php _e('Please specify Cap Limit Amount.','binary-mlm-pro');?></div>
			</td>
		</tr>        
		</table>
		<p class="submit">
	<input type="submit" name="mlm_payout_settings" id="mlm_payout_settings" value="<?php _e('Update Options', 'binary-mlm-pro');?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
	</p>
	</form>

	<?php
		}
		else if(!empty($mlm_settings))
		{
			?>
			<form name="admin_payout_settings" method="post" action="">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('admin-mlm-pair');"><?php _e('Pair','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
				<input type="text" name="pair1" id="pair1" size="2" value="<?php if(!empty($mlm_settings['pair1']))  _e($mlm_settings['pair1']);?>"> : 
				<input type="text" name="pair2" id="pair2" size="2" value="<?php if(!empty($mlm_settings['pair2']))  _e($mlm_settings['pair2']);?>">
				<div class="toggle-visibility" id="admin-mlm-pair"><?php _e('Please mention here pair ratio.','binary-mlm-pro');?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-initial-pair');">
				<?php _e('Initial Pair','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
				<input type="text" name="initial_pair" id="initial_pair" size="2" value="<?php if(!empty($mlm_settings['initial_pair'])) _e($mlm_settings['initial_pair']);?>">
				<div class="toggle-visibility" id="admin-mlm-initial-pair"><?php _e('Please mention here initial pair.','binary-mlm-pro');?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
			<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-initial-amount');">
			<?php _e('Initial Amount','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
			<input type="text" name="initial_amount" id="initial_amount" size="10" value="<?php if(!empty($mlm_settings['initial_amount'])) _e($mlm_settings['initial_amount']);?>">
				<div class="toggle-visibility" id="admin-mlm-initial-amount"><?php _e('Please mention here initial amount.','binary-mlm-pro');?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-further-amount');">
				<?php _e('Further Pair Amount','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
			<input type="text" name="further_amount" id="further_amount" size="10" value="<?php if(!empty($mlm_settings['further_amount'])) _e($mlm_settings['further_amount']);?>">
				<div class="toggle-visibility" id="admin-mlm-further-amount"><?php _e('Please mention here further pair amount.','binary-mlm-pro');?></div>
			</td>
		</tr>
		<tr>
	<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-direct-referal-commission');">
				<?php _e('Direct Referral Commission','binary-mlm-pro');?>:</a>
			</th>
			<td>
			<input type="text" name="referral_commission_amount" id="referral_commission_amount" size="10" value="<?php if(!empty($mlm_settings['referral_commission_amount'])) _e($mlm_settings['referral_commission_amount']);?>">
				<div class="toggle-visibility" id="admin-mlm-direct-referal-commission"><?php _e('Please specify referral_commission_amount.','binary-mlm-pro');?></div>
			</td>
		</tr>
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-service-charege');">
				<?php _e('Service Charge (If any)','binary-mlm-pro');?> :</a>
			</th>
			<td>
			<input type="text" name="service_charge" id="service_charge" size="10" value="<?php if(!empty($mlm_settings['service_charge'])) _e($mlm_settings['service_charge']);?>">
				<div class="toggle-visibility" id="admin-mlm-service-charege"><?php _e('Please specify service charge.','binary-mlm-pro');?></div>
			</td>
		</tr>
		
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-tds');">
				<?php _e('Tax Deduction','binary-mlm-pro');?>: </a>
			</th>
			<td>
			<input type="text" name="tds" id="tds" size="10" value="<?php if(!empty($mlm_settings['tds'])) _e($mlm_settings['tds']);?>">&nbsp;%
				<div class="toggle-visibility" id="admin-mlm-tds"><?php _e('Please specify TDS.','binary-mlm-pro');?></div>
			</td>
		</tr>
        <tr>
	<th scope="row" class="admin-settings">
				<a style="cursor:pointer;"title="Click for Help!" onclick="toggleVisibility('admin-mlm-cap-limit');">
				<?php _e('Cap Limit Amount','binary-mlm-pro');?>: </a>
			</th>
			<td>
			<input type="text" name="cap_limit_amount" id="cap_limit_amount" size="10" value="<?php if(!empty($mlm_settings['cap_limit_amount'])) _e($mlm_settings['cap_limit_amount']);?>">
				<div class="toggle-visibility" id="admin-mlm-cap-limit"><?php _e('Please specify Cap Limit Amount.','binary-mlm-pro');?></div>
			</td>
		</tr>        
		</table>
		<p class="submit">
	<input type="submit" name="mlm_payout_settings" id="mlm_payout_settings" value="<?php _e('Update Options', 'binary-mlm-pro');?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
	</p>
	</form>

	<script language="JavaScript">
  populateArrays();
</script>
<?php
		}
		
	?>
	</div>
	<?php 	

	
	} // end if statement
	else
		 _e($msg);
		
		
} //end mlmPayout function
?>
