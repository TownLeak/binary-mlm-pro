<?php 
function mlm_my_payout_page($id='')
{
if($id=='')
{
$detailsArr =  my_payout_function();
}
else
{ 
$detailsArr =  my_payout_function($id);

}
//_e("<pre>");print_r($detailsArr); exit; 
$page_id = get_post_id('mlm_my_payout_details_page');

if(count($detailsArr)>0){
$mlm_settings = get_option('wp_mlm_general_settings');
	?>
	<table width="100%" border="0" cellspacing="10" cellpadding="1" id="payout-page">
		<tr>
			<td><?PHP _e('Date','binary-mlm-pro');?></td>
			<td><?PHP _e('Amount','binary-mlm-pro');?></td>
			<td><?PHP _e('Action','binary-mlm-pro');?></td>
		</tr>
		<?php foreach($detailsArr as $row) :  
		
		//print_r($row); die;
			$amount = ($row->total_amt <= $row->cap_limit || $row->cap_limit ==0.00)?$row->capped_amt:$row->capped_amt.'(capped)';
		?>
		<tr>
			<td><?= $row->payoutDate ?></td>
			<td><?= $mlm_settings['currency'].' '.$amount ?></td>
			<?php if($id == ''){?>
			<td><a href="<?=get_post_id_or_postname_for_payout('mlm_my_payout_details_page', $row->payout_id)?>" style="text-decoration:none;"><?php _e('View','binary-mlm-pro');?></a></td>
			<?php }
				else{	?>
			<td><a href="?page=mlm-user-account&ac=payout-details&pid=<?=$row->payout_id?>" style="text-decoration:none;"><?php _e('View','binary-mlm-pro');?></a></td>				
				<?php
			}
			?>
			
		</tr>
		
		<?php endforeach; ?>
		
	</table>
	<?php 
	}else{

	?>
	<div class="no-payout"><?php _e('You have not earned any commisssions yet.','binary-mlm-pro');?> </div>
	
	<?php 
	}
}

?>
