<div class="mor">

<?php if($message) : ?>
<div class="mor alert notice">
<p><?php print($message); ?></p>
</div>
<?php endif; ?>


<?=form_open($_form_base."&method=layouts_clone", array('name'=>'layouts_clone', 'id'=>'layouts_clone'), '')?>

	<table class="mainTable padTable" id="event_table" border="0" cellpadding="0" cellspacing="0">
		<tr class="header" >
		<th colspan="3"><?= lang('clone_settings')?></th>
		</tr>
	<tbody>
		<tr style="width: 33%;">
			<td><?=lang('source')?></td>
			<td><?=form_dropdown('from', $layout_dropdown, '', 'id="from"').NBS.NBS?></td>
		</tr>
		<tr style="width: 33%;">
			<td><?=lang('destination_channel')?></td>
			<td><?=form_dropdown('to', $channel_dropdown, '', 'id="to"').NBS.NBS?></td>
		</tr>
		<tr style="width: 33%;">
			<td><?=lang('destination_groups')?></td>
			<td><?php
					echo "<ul>";
					foreach ($member_groups as $group ) {
						$details = array('name' => 'mbr_groups['.$group->group_id.']', 'value' => $group->group_id, false);
						echo  '<li><label>'.form_checkbox($details).' &nbsp;'.$group->group_title.'</label></li>';
					};
					echo  "</ul>";
			
			?></td>
		</tr>
	</tbody>
	</table>

<?=form_submit(array('name' => 'submit', 'value' => lang('Clone it'), 'class' => 'submit'))?>
<?=form_close()?>
