<?php if($message) : ?>
<div class="mor alert notice">
<p><?php print($message); ?></p>
</div>
<?php endif; ?>
<?php if($im_check) : ?>

<div class="mor notice  success">
<p><?php echo lang('i_complite'); ?></p>
</div>

<?php endif; ?>
<?php //success
echo form_open($_form_base."&method=import_fields", '');
?>		
<?php if(!$import_out && !$im_check) : ?>
<table class="mainTable padTable" id="event_table" border="0" cellpadding="0" cellspacing="0">
<tbody>
<!-- <tr>
<th colspan="2"><?= lang('field_label'); ?></th>
</tr> -->
<tr>
<td style="width:100px;"><?= lang('default_group'); ?></td>
<td>
<?php
				$select =  '<select name="default_group">';
				foreach ($group_packs as $field => $key)
				{
					$select .= '<option value="'.$field.'">'.$key.'</option>';
				}
				echo $select.'</select>';
?>
</td>

</tr>

<tr>
<td><?= lang('export_block'); ?></td>
<td><textarea name="import" rows="20"></textarea></td>

</tr>

</tbody> 
</table>


<p class="centerSubmit">


				<input name="edit_field_group_name" value="<?= lang('save'); ?>" class="submit" type="submit">&nbsp;&nbsp;					
</p>

</form>
<?php endif; ?>


<?php if($import_out && !$im_check) : ?>
<?php //success
echo form_open($_form_base."&method=import_fields", '');
?>	

<table class="mainTable padTable" id="event_table" border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<th></th>
<th><?= lang('field_label'); ?></th>
<th><?= lang('field_name'); ?></th>
<th><?= lang('field_group'); ?></th>
<th><?= lang('ignore'); ?></th>
</tr>
<?php

				$out="";
				$c_index = '';
				$select = '';

				foreach ($group_packs as $key => $field)
				{
					$selected = ($default_group == $key) ? 'selected="selected"'  : '';
					$select .= '<option value="'.$key.'" '.$selected.'>'.$field.'</option>';
				}
				 
				foreach ($import_out  as $key => $field)
				{
					$check = (!isset($field['uniq'])) ? '<img src="'.$img_path.'third_party/mx_tool_box/images/checkmark.png" alt="'.lang('ready2import').'" title="'.lang('ready2import').'" />' : '<img src="'.$img_path.'third_party/mx_tool_box/images/hand_1.png" alt="'.lang('d_names').'" title="'.lang('d_names').'" />' ; 
					$out .= '<tr  id="field_'.$field['field_id'].'" rel="'.$field['field_id'].'"><td style="width:30px;">'.$check.'</td><td class="label">'.$field['field_label'].'</td><td class="name">'.((isset($field['uniq'])) ? '<input dir="ltr" style="width: 100%;" name="name['.$field['field_id'].']" id="" value="'.$field['field_name'].'" size="20" maxlength="120" class="input" type="text">' : $field['field_name']) .'</td><td ><select name="groups['.$field['field_id'].']">'.$select.'</select></td><td><input type="checkbox" name="ignor[]" value="'.$field['field_id'].'"></td></tr>';
				}
				echo $out;
					
?>

</tbody> 

</table>
<p class="centerSubmit">
	<input name="edit_field_group_name" value="<?= lang('save'); ?>" class="submit" type="submit">&nbsp;&nbsp;					
</p>
				<input type="hidden" name="im_check" value="true" />
				<textarea name="import" style="visibility: hidden; "><?php echo $import; ?></textarea>
</form>
<?php endif; ?>

