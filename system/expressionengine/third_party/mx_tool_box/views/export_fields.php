<?php if($message) : ?>
<div class="mor alert notice">
<p><?php print($message); ?></p>
</div>
<?php endif; ?>

<?php if(!$export_out) : ?>
<?php //success
echo form_open($_form_base."&method=export_fields", '');
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
					$out .= '<tr  id="field_'.$field->field_id.'" rel="'.$field->field_id.'"><td>'.$field->field_id.'</td><td class="label">'.$field->field_label.'</td><td class="name">'. $field->field_name.'</td><td></td><td><input type="checkbox" name="export[]" value="'.$field->field_id.'"></td></tr>';

				}
				echo $out;
				?>
				

</tbody>
</table>


<p class="centerSubmit">


				<input name="edit_field_group_name" value="<?= lang('save'); ?>" class="submit" type="submit">&nbsp;&nbsp;					
</p>

</form>
<?php endif; ?>
<?php if($export_out) : ?>
<textarea rows="20">
<?php echo $export_out; ?>

</textarea>
<?php endif; ?>

