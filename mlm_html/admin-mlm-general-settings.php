<?php
function mlmGeneral()
{
	global $wpdb;	
	//get database table prefix
	$table_prefix = mlm_core_get_table_prefix();
	
	$error = '';
	$chk = 'error';
	
	//most outer if condition
	if(isset($_POST['mlm_general_settings']))
	{
		$currency = sanitize_text_field( $_POST['currency'] );

		if ( checkInputField($currency) ) 
			$error .= "\n Please Select your currency type.";
		
		//if any error occoured
		if(!empty($error))
			$error = nl2br($error);
		else
		{
			$chk = '';
			update_option('wp_mlm_general_settings', $_POST);
			$url = get_bloginfo('url')."/wp-admin/admin.php?page=admin-settings&tab=eligibility";
			_e("<script>window.location='$url'</script>");
			$msg = "<span style='color:green;'>Your general settings has been successfully updated.</span>";
		}
	}// end outer if condition
	?>
	<script>
	jQuery(document).ready(function () {

		jQuery("input[name='ePin_activate']").change(function () {
			var value = jQuery(this).val();
			if (value == '1') {
				jQuery("#sole_id").show();
			   
			} else if (value == '0') {

			   jQuery("#sole_id").hide();
			}
		});
		});
	</script>	
	
	
	<?php
	if($chk!='')
	{
		$mlm_settings = get_option('wp_mlm_general_settings');
	?>
	
<div class='wrap1'>
	<h2><?php _e('Currency Setting','binary-mlm-pro');?> </h2>
	<div class="notibar msginfo">
		<a class="close"></a>
		<p><?php _e('Please select the base currency of your MLM Network. This option is very important as all calculations will be performed in this base currency. Once this currency is chosen and saved, it CANNOT be changed later. The entire network will need to be reset if you decide to change the currency at a later date.','binary-mlm-pro');?> </p>
		<p><strong><?php _e('Activate ePin ','binary-mlm-pro');?> - </strong><?php _e('In case you would like to Activate ePin functionality on your website, set this value to Yes. ','binary-mlm-pro');?></p>
		<p><strong><?php _e('Sole Payment Method ','binary-mlm-pro');?> - </strong><?php _e('In case members can only register on your site via ePin, set this to Yes. This would make the ePin field mandatory on the user registration form and a visitor would need a valid unused ePin to complete his registration. If this value is set to No, a visitor will be able to register on the site even without specifying a valid ePin. In this case you would need to manually mark the member as Paid / Unpaid under Users -> All Users. ','binary-mlm-pro');?></p>
		<p><strong><?php _e('ePin Length  ','binary-mlm-pro');?> - </strong><?php _e('The length of the generated ePins. ','binary-mlm-pro');?></p>
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
	<form name="admin_general_settings" method="post" action="" id="admin_general_settings">
	<table border="0" cellpadding="0" cellspacing="0" width="60%" class="form-table">
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('admin-mlm-currency');"><?php _e('Currency','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
			<?php
				$sql = "SELECT iso3, currency 
											FROM {$table_prefix}mlm_currency 
											ORDER BY iso3";
				$results = $wpdb->get_results($sql);
			?>
				<select name="currency" id="currency" >
					<option value=""><?php _e('Select Currency','binary-mlm-pro');?></option>
				<?php
					
					foreach($results as $row)
					{
						if($_POST['currency']==$row->iso3)
							$selected = 'selected';
						else
							$selected = '';
				?>
						<option value="<?= $row->iso3;?>" <?= $selected?>><?= $row->iso3." - ".$row->currency;?></option>
				<?php
					}
				?>
				</select>
				<div class="toggle-visibility" id="admin-mlm-currency"><?php _e('Select your currency which will you use.','binary-mlm-pro');?></div>
			</td>
		</tr>
		 <tr><td colspan="2">
		
		<?php epin_genaral_settings();?>
                          </td></tr>
	</table>
	<p class="submit">
	<input type="submit" name="mlm_general_settings" id="mlm_general_settings" value="<?php _e('Update Options', 'binary-mlm-pro');?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
	</p>
	</form>
	</div>
	<script language="JavaScript">
  populateArrays();
</script>
<?php
		}
		else if(!empty($mlm_settings))
		{
		?>
		
			<form name="admin_general_settings" method="post" action="" id="admin_general_settings">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="form-table">
		<tr>
			<th scope="row" class="admin-settings">
				<a style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('admin-mlm-currency');"><?php _e('Currency','binary-mlm-pro');?> <span style="color:red;">*</span>: </a>
			</th>
			<td>
			<?php
				$sql = "SELECT iso3, currency 
						FROM {$table_prefix}mlm_currency
						WHERE iso3 = '".$mlm_settings['currency']."'
						ORDER BY iso3";
				//$sql = mysql_fetch_array(mysql_query($sql));
			?>
				<input type="text" name="currency" id="currency" value="<?= $mlm_settings['currency']?>" readonly />
				<div class="toggle-visibility" id="admin-mlm-currency"><?php _e('You can not change the currency.','binary-mlm-pro');?></div>
			</td>
		</tr>
                <tr><td colspan="2">
		<?php general_settings_epin();?>
                    </td></tr>
		</table>
		<p class="submit">
	<input type="submit" name="mlm_general_settings" id="mlm_general_settings" value="<?php _e('Update Options', 'binary-mlm-pro');?> &raquo;" class='button-primary' onclick="needToConfirm = false;">
	</p>
	</form>
	</div>
		<?php
		}
	} // end if statement
	else
		 _e($msg);
} //end mlmGeneral function
?>