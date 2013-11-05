<?php if($message) : ?>
<div class="mor alert notice">
<p><?php print($message); ?></p>
</div>
<?php endif; ?>

<?php //success
echo form_open($_form_base."&method=clone_index", '');
?>		
		<table class="mainTable padTable" id="event_table" border="0" cellpadding="0" cellspacing="0">
<tbody>
<tr>
<th style="width:30px"><?= lang('id'); ?></th>
<th><?= lang('field_label'); ?></th>
<th ><?= lang('field_name'); ?></th>
<th colspan="3"><!--<?= lang('tab'); ?>--></th>
</tr>
<tr>
<td  style="width:30px"><?= lang('pattern'); ?></td>
<td><input dir="ltr" style="width: 100%;" name="" id="label_pattern" value="{field_label}_en" size="20" maxlength="120" class="input" type="text"></td>
<td ><input dir="ltr" style="width: 100%;" name="" id="name_pattern" value="{field_name}_en" size="20" maxlength="120" class="input" type="text"></td>
<td style="width:30px;"><!--<select></select>--></td>
<td style="width:30px;"></td>
<td style="width:20px;"></td>
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
					
					$out .= '<tr><th  style="width:30px">'.$group_packs[$field->group_id].'</th><th></th><th></th><th style="width:30px;"></th><th></th><th></th></tr><tr>';
					$c_index = $field->group_id;
					}
					$out .= '<tr  id="field_'.$field->field_id.'" rel="'.$field->field_id.'"><td>'.$field->field_id.'</td><td class="label">'.$field->field_label.'</td><td class="name">'. $field->field_name.'</td><td></td><td><a class="clone_btn"><img src="'.$img_path.'third_party/mx_tool_box/images/clipboard_copy.png" alt="'.lang('2clone_line').'" title="'.lang('2clone_line').'" /></a></td><td><input type="checkbox" name="clone_'.$field->field_id.'" value=""></td></tr>';
					
					if (isset($errors[$field->field_id])){
						foreach ($errors[$field->field_id]  as $error_line)
						{
							$out .= '<tr id="row_'.$error_line['id'].'"><td  style="width:30px"><input name="clone[field_order][]"  value="'.$error_line['id'].'" type="hidden"/><input name="clone[copy_'.$error_line['id'].']"  value="'.$field->field_id.'" type="hidden"/><a OnClick="delete_line(\'row_'.$error_line['id'].'\');"><img src="'.$img_path.'third_party/mx_tool_box/images/cancel.png" alt="'.lang('remove_line').'" title="'.lang('remove_line').'" /></a></td><td><input dir="ltr" style="width: 100%;" name="clone[label_'.$error_line['id'].']" id="" value="'.$error_line['label'].'" size="20" maxlength="120" class="input"type="text"></td><td><input dir="ltr" style="width: 100%;" name="clone[name_'.$error_line['id'].']" id="" value="'.$error_line['name'].'" size="20" maxlength="120" class="input" type="text"></td><td></td><td style="width:30px;"><img src="'.$img_path.'third_party/mx_tool_box/images/hand_1.png" alt="'.lang('field_name_error').'" title="'.lang('field_name_error').'" /></td><td></td></tr>';
						}
					}
				}
				echo $out;
				?>
				

</tbody>
</table>

<p class="centerSubmit">
	<input name="edit_field_group_name" value="<?= lang('save'); ?>" class="submit" type="submit">
</p>



</form>

<script type="text/javascript">
	jQuery(function() {
		index_row	=	1;
		
		
		
		jQuery(".clone_btn").click(function () {	
			id = Math.floor( Math.random ( ) *1000 + 1);
			_tr = jQuery(this).parents('tr:first');
			field_label = (jQuery("#label_pattern").val()).replace(/{field_label}/g, _tr.children(".label:first").html());
			field_name =  (jQuery("#name_pattern").val()).replace(/{field_name}/g, _tr.children(".name:first").html());
		
			template = '<tr id="row_' + id + '"><td  style="width:30px"><input name="clone[field_order][]"  value="' + id + '" type="hidden"/><input name="clone[copy_' + id+ ']"  value="' + _tr.attr("rel") + '" type="hidden"/><a OnClick="delete_line(\'row_' + id + '\');"><img src="<?php echo($img_path);?>third_party/mx_tool_box/images/cancel.png" alt="<?=lang('remove_line')?>"  title="<?=lang('remove_line')?>" /></a></td><td><input dir="ltr" style="width: 100%;" name="clone[label_'+id+']" id="" value="' + field_label + '" size="20" maxlength="120" class="input"type="text"></td><td><input dir="ltr" style="width: 100%;" name="clone[name_'+id+']" id="" value="'+ field_name + '" size="20" maxlength="120" class="input" type="text"></td><td></td><td style="width:30px;"></td><td></td></tr>';
		
			_tr.after(template);
			index_row	+=	1;
		});


		
	});	

	function delete_line (_tr){

		jQuery("#"+_tr).remove();
	}
</script> 


<?php 

function layout_publish ($layout_publish, $name) {
	$out = '<select name="'.$name.'" class="" id="" rel="">';
	foreach ($layout_publish as $layout)
	{
		$out .= '<option value="'.$layout->layout_id.'|'.$layout->group_id.'">'.$layout->channel_title.'  : '.$layout->group_title.'</option>';

	}
	$out 	 .= '</select>';
	return $out;
}
?>
