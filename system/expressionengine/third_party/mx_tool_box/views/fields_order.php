<?php if($message) : ?>
<div class="mor alert notice">
<p><?php print($message); ?></p>
</div>
<?php endif; ?>

<?php //success
echo form_open($_form_base."&method=fields_order", '');
?>		
<table class="mainTable padTable" id="event_table" border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<th style="width:30px"><?= lang('id'); ?></th>
<th><?= lang('field_label'); ?></th>
<th colspan="3"><?= lang('field_name'); ?></th>
</tr>


</tbody> 
<tbody>


		<?php
				$out="";
				$c_index = '';
			
				foreach ($field_packs->result()  as $field)
				{
			//
					if  ($c_index != $field->group_id) {
					
					$out .= '<tr><th  style="width:30px">'.$group_packs[$field->group_id].'</th><th></th><th></th><th style="width:30px;"></th><th></th></tr><tr>';
					$c_index = $field->group_id;
					}
					$out .= '<tr  id="field_'.$field->field_id.'" rel="'.$field->field_id.'"><td>'.$field->field_id.'</td><td class="label">'.$field->field_label.'</td><td class="name">'. $field->field_name.'</td><td><input dir="ltr" style="width: 100%;" name="order['.$field->field_id.']" id="" value="'.$field->field_order.'" size="10" maxlength="10" class="input" type="text"></td><td></td></tr>';

				}
				echo $out;
				?>
				

</tbody>
</table>


<p class="centerSubmit">


				<input name="edit_field_group_name" value="<?= lang('save'); ?>" class="submit" type="submit">&nbsp;&nbsp;					
</p>
</form>


