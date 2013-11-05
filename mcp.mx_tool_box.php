<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/**
	 * -
	 * @package		MX Tool Box
	 * @subpackage	ThirdParty
	 * @category	Modules
	 * @author    Max Lazar <max@eec.ms>
	 * @copyright Copyright (c) 2010-2011 Max Lazar (http://eec.ms)
	 * @link		http://eec.ms/
	 */
class Mx_tool_box_mcp 
{
	var $base;			// the base url for this module			
	var $form_base;		// base url for forms
	var $module_name = "mx_tool_box";	

	
	function Mx_tool_box_mcp( $switch = TRUE )
	{
	
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance(); 
		
		if(defined('SITE_ID') == FALSE)
		define('SITE_ID', $this->EE->config->item('site_id'));
				
		$this->base	 	 = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->module_name;
		$this->form_base = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->module_name;

        // uncomment this if you want navigation buttons at the top
				$this->EE->cp->set_right_nav(array(
				$this->EE->lang->line('home') => $this->base.AMP.'method=clone_index',
				$this->EE->lang->line('fields_clone')		=> $this->base.AMP.'method=clone_index',
				$this->EE->lang->line('fields_order')	=> $this->base.AMP.'method=fields_order',
				$this->EE->lang->line('layouts_clone')	=> $this->base.AMP.'method=layouts_clone',
				$this->EE->lang->line('export_fields')	=> $this->base.AMP.'method=export_fields',
				$this->EE->lang->line('import_fields')	=> $this->base.AMP.'method=import_fields'
				));
	}

	function index() 
	{
		return $this->clone_index();	
	}
	//	field_group ee_channels channel_id
	
	function layouts_clone () 
	{
		$vars = array();
		$vars['message']  = false;
		$vars['export_out']  = false;

		$vars['channel_data'] = $this->EE->channel_model->get_channels()->result();
		$vars['member_groups'] = $this->EE->member_model->get_member_groups('',array('can_access_publish' => 'y'))->result();
		
		$vars['from']  = $this->EE->input->post('from');
		$vars['to']  = $this->EE->input->post('to');
		$vars['mbr_groups']  = $this->EE->input->post('mbr_groups');

		if (!empty($vars['from']) AND !empty($vars['to']) AND !empty($vars['mbr_groups'])) {
		
			$layout_id = $vars['from'];	

			$data = $this->EE->db->where('layout_id', $layout_id)->get('exp_layout_publish', 1)->result_array();

			if (!empty($vars['mbr_groups'])) {
				$this->EE->db->where('channel_id', $vars['to'])->where_in('member_group', $vars['mbr_groups'])->delete('exp_layout_publish');
			}
			
			foreach ($vars['mbr_groups'] as $val => $key )
			{
				$data[0]['layout_id'] = '';
				$data[0]['member_group'] = $key;
				$data[0]['channel_id'] = $vars['to'];
				$this->EE->db->insert('exp_layout_publish', $data[0]);
			}
	
		}
		
		$query = $this->EE->db->query( "SELECT DISTINCT lp.layout_id, lp.layout_id as layout_id, ch.channel_title as channel_title,  ch.field_group as field_group, mg.group_title as group_title, mg.group_id as group_id, ch.channel_id
		   FROM exp_layout_publish AS lp, 
		   exp_channels AS ch,
		   exp_member_groups AS mg
		   WHERE lp.site_id = " . SITE_ID . " AND  mg.group_id = lp.member_group AND lp.channel_id = ch.channel_id
		   ORDER BY lp.channel_id DESC" );

		$vars['layout_publish'] =	$query->result();
		
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $layout )
			{
			
			}
		}

		foreach ($vars['layout_publish'] as $layout)
		{
			$vars['layout_dropdown'][$layout->layout_id] = $layout->channel_title.' : '.$layout->group_title;
		}
		
		foreach ($vars['channel_data'] as $channel)
		{
			$vars['channel_dropdown'][$channel->channel_id]  = $channel->channel_title;
		}
		
        $this->EE->load->library('table');
        $this->EE->load->helper('form');
        $this->EE->load->model('tools_model');
        
        $this->EE->jquery->tablesorter('.mainTable', '{
			headers: {
			0: {sorter: false},
			2: {sorter: false}
		},
			widgets: ["zebra"]
		}');
        
        $this->EE->javascript->output('
									$(".toggle_all").toggle(
										function(){
											$("input.toggle").each(function() {
												this.checked = true;
											});
										}, function (){
											var checked_status = this.checked;
											$("input.toggle").each(function() {
												this.checked = false;
											});
										}
									);');
        
        $this->EE->javascript->compile();
		
		if (!empty($errors)) {
			$vars['message'] = $this->EE->lang->line('problems');			
		}	
		
		$vars['errors'] = (isset($errors)) ? $errors : false;		
		$vars['group_packs'] =  $this->group_packs();
		
		return $this->content_wrapper('layouts_clone', $this->EE->lang->line('layouts_clone'), $vars);				
	}		
	
	function import_fields() 
	{
		$vars = array();
		$ignor_a  = array();
		$vars['message']  = false;
		$vars['import_out']  = false;

		
		$vars['import']	=	$this->EE->input->post('import');
		$vars['names']	=	$this->EE->input->post('name');
		$vars['default_group']  =	$this->EE->input->post('default_group');
		$vars['groups']  =	$this->EE->input->post('groups');
		$vars['ignor']	=$this->EE->input->post('ignor');

		$vars['im_check']	= $this->EE->input->post('im_check');
		$vars['im_check']	= (!empty($vars['im_check'])) ? true : false;
		
		if (!empty($vars['import'])) {
			$out = unserialize($vars['import']);
			$channel_fields = $out['channel_fields'];
			$field_formatting = $out['field_formatting'];
			
			$out	=$this->EE->db->query( "SELECT *
										   FROM exp_channel_fields							  
										   WHERE site_id = " . SITE_ID . "
											ORDER BY group_id" );		
		
			foreach ($out->result()  as $field)
			{					
				$u_name[]=$field->field_name;
			}	
			
			if (!empty($vars['ignor'])) {		
				foreach ($vars['ignor']  as $ignor_id)
				{		
					$ignor_a[]=$ignor_id;
				}	
			}

			foreach ($channel_fields as $key => $field)
			{
				if (!empty($vars['names'][$field->field_id])) {		
					$arr_imort[$key]->field_name = $field->field_name = $vars['names'][$field->field_id];
				}
				
				if (!empty($vars['groups'][$field->field_id])) {
					$channel_fields[$key]->group_id = $vars['groups'][$field->field_id];
				}
				
				if (in_Array($field->field_id,$ignor_a)){	
					unset($channel_fields[$key]);
				}
				else{
				
					if (in_Array($field->field_name,$u_name)){	
						$vars['import_out'][$field->field_id]['uniq']= 0;
						$vars['im_check'] = false;
						$errors = true;
					}
					
					$vars['import_out'][$field->field_id]['field_id']= $field->field_id;
					$vars['import_out'][$field->field_id]['field_name']=$field->field_name;
					$vars['import_out'][$field->field_id]['field_label']=$field->field_label;
				
				}
			} 
			if (!$vars['im_check']) {
				$vars['import']	= serialize(array('channel_fields' => $channel_fields , 'field_formatting' =>$field_formatting));
			}
			else  {
				foreach ($channel_fields as $key => $field)
				{
						$clone_id = $field->field_id;
						$field->field_id = '';				
						
						$this->EE->db->insert('exp_channel_fields', $field);
						$new_id = $this->EE->db->insert_id();
						
						$this->field_channel_data($field->field_type,$new_id);
						$this->copy_field_format($new_id, $clone_id);
						$this->layout_data($new_id, false, $field->group_id);
				}
			}
		}
		
		if (!empty($errors)) {
			$vars['message'] = $this->EE->lang->line('problems');			
		}	
		
		$vars['errors'] = (isset($errors)) ? $errors : false;		
		$vars['group_packs'] =  $this->group_packs();
		
		return $this->content_wrapper('import_fields', $this->EE->lang->line('import_fields'), $vars);		
	}
	

	function export_fields() 
	{
		$vars = array();
		$vars['message']  = false;
		$vars['export_out']  = false;
		$export = $this->EE->input->post('export');
		if (!empty($export)) {

			$out['channel_fields']	=$this->EE->db->query( "SELECT *
								   FROM exp_channel_fields							  
								   WHERE site_id = " . SITE_ID . "
								    AND field_id  IN ( " .implode  (",", $export) . " )
									ORDER BY group_id" )->result();		
									
			$out['field_formatting']	= $this->EE->db->query( "SELECT *
								   FROM exp_field_formatting							  
								   WHERE  field_id  IN ( " .implode  (",", $export) . " )
									" )->result();	
			
					
			$col_id  = array ();		
			foreach ($out['channel_fields'] as $key => $field)
			{
				if ($field->field_type	== 'matrix') {
					$field_settings =unserialize(base64_decode($field->field_settings));
					$col_id = array_merge($col_id, $field_settings['col_ids']);
				}
			
			}
;
			if (!empty($col_id)) {
				$this->EE->db->where_in('col_id', $col_id);
				$this->EE->db->where('site_id', SITE_ID);
				$out['matrix'] = $this->EE->db->get('exp_matrix_cols')->result_array();
			}
			
			$vars['export_out']= serialize($out);
		}
				
		if (!empty($errors)) {
			$vars['message'] = $this->EE->lang->line('problems');			
		}	
		
		$vars['errors'] = (isset($errors)) ? $errors : false;		
		$vars['field_packs'] =  $this->field_packs();
		$vars['group_packs'] =  $this->group_packs();
		
		return $this->content_wrapper('export_fields',$this->EE->lang->line('export_fields'), $vars);
	}
	
	function clone_index() 
	{
		
		$vars = array();
		$vars['message']  = false;
		

			$new_settings = $this->EE->input->post('clone');
			
			if (isset ($new_settings['field_order'])) {
			
				$out	=$this->EE->db->query( "SELECT *
								   FROM exp_channel_fields							  
								   WHERE site_id = " . SITE_ID . "
									ORDER BY group_id" );		

				//rebuild the array
				$r = array();
				$u_name = array();
				$message = array();
				$errors = array();
				
			
				foreach ($out->result()  as $field)
				{					
					$r[$field->field_id] = $field;
					$u_name[]=$field->field_name;
				}
				
				foreach ($new_settings['field_order'] as $field_order => $row_id)
				{ 
					$clone_id =   $new_settings['copy_'.$row_id];
					
					if (!in_Array($new_settings['name_'.$row_id],$u_name)){
						
						$data = $r[$clone_id];
						
						$data->field_id = '';
						$data->field_label = $new_settings['label_'.$row_id];
						$data->field_name = $new_settings['name_'.$row_id];

						if ($data->field_type == 'matrix') {
							$tmp_settings = unserialize(base64_decode($data->field_settings));
							$tmp_settings['col_ids'] = $this->matrix_cloner($tmp_settings);
							$data->field_settings = base64_encode (serialize($tmp_settings));
						}
						
						$this->EE->db->insert('exp_channel_fields', $data);
						$new_id = $this->EE->db->insert_id();
						$this->field_channel_data($data->field_type,$new_id);
						
						
						
						
						$u_name[] = $new_settings['name_'.$row_id];
			
						$this->copy_field_format($new_id, $clone_id);
						$this->layout_data($new_id, $clone_id,$data->group_id);
						

						
					}
					else {
						$errors[$clone_id][$row_id]['label'] = $new_settings['label_'.$row_id];
						$errors[$clone_id][$row_id]['name'] = $new_settings['name_'.$row_id];
						$errors[$clone_id][$row_id]['id'] = $row_id;
					}
				}
			}
		
		if (!empty($errors)) {
			$vars['message'] = $this->EE->lang->line('problems');			
		}	
		
		$vars['errors'] = (isset($errors)) ? $errors : false;		
		$vars['field_packs'] =  $this->field_packs();
		$vars['group_packs'] =  $this->group_packs();
		return $this->content_wrapper('clone_fields', $this->EE->lang->line('field_clone'), $vars);

	}
	
	function fields_order() 
	{
		$vars = array();
		$vars['message']  = false;
		$vars['order'] = $this->EE->input->post('order');
		
		if (!empty($vars['order'])) {
			foreach ($vars['order'] as $field_id => $order)
			{
				if(((int)$order) != 0){
				$this->EE->db->set('field_order', (int)$order); 
				$this->EE->db->where('field_id', $field_id);
				$this->EE->db->update('exp_channel_fields');
				}
			}	
		}
		
		$vars['field_packs'] =  $this->field_packs();
		$vars['group_packs'] =  $this->group_packs();
		return $this->content_wrapper('fields_order', $this->EE->lang->line('field_order'), $vars);

	}

	function matrix_cloner ($settings) 
	{
		$out =  array ();
		$out_i = 0;

		$columns_query = $this->EE->db->where_in('col_id', $settings['col_ids'])
		->get('matrix_cols');
		
		foreach ($columns_query->result_array() as $column ) {
			$column['col_id'] = '';
			$this->EE->db->insert('matrix_cols', $column);
			$col_id = $this->EE->db->insert_id();
			$columns['col_id_'.$col_id] = array('type' => 'text');
			$out [$out_i] = $col_id;
			$out_i++;
		};
		
		$this->EE->load->dbforge();
		$this->EE->dbforge->add_column('matrix_data', $columns);

		return $out;

	}
	
	function layout_data($new_id, $clone_id, $group_id) {

						$this->EE->db->select('channel_id');
						$this->EE->db->where('field_group', $group_id);
						$this->EE->db->where('site_id', SITE_ID);
						$cquery = $this->EE->db->get('channels');
		
						if ($cquery->num_rows() > 0)
						{						
							$ch_ids = array();
							
							$default_settings = array(
								'visible'		=> 'TRUE',
								'collapse'		=> 'FALSE',
								'htmlbuttons'	=> 'FALSE',
								'width'			=> '100%'
							);		
							
							foreach ($cquery->result_array() as $row)
							{
								$ch_ids[] = $row['channel_id'];
							}
							
							$query = $this->EE->db->query( "SELECT *
							   FROM exp_layout_publish							  
							   WHERE site_id = " . SITE_ID . " 
							   AND channel_id  IN ( " .implode  (",", $ch_ids) . " )
								ORDER BY channel_id" );

							if ($query->num_rows() > 0)
							{
								foreach ($query->result_array() as $layout )
								{
									$field_layout  = unserialize($layout['field_layout']);
							
									if ($clone_id) {
										$field_layout['publish'][$new_id] = $field_layout['publish'][$clone_id];
									}
									else {
										$field_layout['publish'][$new_id] = $default_settings;
									}
									
									$layout['field_layout'] = serialize($field_layout);
			
									$this->EE->db->where('layout_id', $layout['layout_id']);
									$this->EE->db->update('exp_layout_publish', $layout);
								}
							}
						}
	
	}
	
	function copy_field_format  ($to_id , $from_id, $data=false) {
		if (!$data) {
			$this->EE->db->select('*');
			$this->EE->db->where('field_id', $from_id);
			$query = $this->EE->db->get('exp_field_formatting')->result_array();
		}

		foreach ($query as $field_formatting)
		{
			if ($field_formatting['field_id'] == $from_id) {
				$field_formatting['field_id'] = $to_id;
				$field_formatting['formatting_id'] = '';
				$this->EE->db->insert('exp_field_formatting',$field_formatting);
			}
		}
	}
	
	function field_channel_data($field_type, $field_id ) {
	
				switch($field_type)
				{
					case 'date'	:
						$this->EE->db->query("ALTER IGNORE TABLE exp_channel_data ADD COLUMN field_id_".$field_id." int(10) NOT NULL DEFAULT 0");
						$this->EE->db->query("ALTER TABLE exp_channel_data ADD COLUMN field_ft_".$field_id." tinytext NULL");
						$this->EE->db->query("ALTER TABLE exp_channel_data ADD COLUMN field_dt_".$field_id." varchar(8) AFTER field_ft_".$field_id."");
					break;
					case 'rel'	:
						$this->EE->db->query("ALTER IGNORE TABLE exp_channel_data ADD COLUMN field_id_".$field_id." int(10) NOT NULL DEFAULT 0");
						$this->EE->db->query("ALTER TABLE exp_channel_data ADD COLUMN field_ft_".$field_id." tinytext NULL");
					break;
					default		:
						$this->EE->db->query("ALTER TABLE exp_channel_data ADD COLUMN field_id_".$field_id."  text");
						$this->EE->db->query("ALTER TABLE exp_channel_data ADD COLUMN field_ft_".$field_id."  tinytext NULL");
					break;
				}
		
	}
	
	function field_packs()
	{
		
		$out	=$this->EE->db->query( "SELECT *
							   FROM exp_channel_fields							  
							   WHERE site_id = " . SITE_ID . "
								ORDER BY group_id" );		
		
		return $out;
	}
	function group_packs()
	{
		$r = array();
		$out	=$this->EE->db->query( "SELECT *
							   FROM exp_field_groups			  
							   WHERE site_id = " . SITE_ID . "
								ORDER BY group_id" );		
		foreach ($out->result()  as $group)
		{
					$r[$group->group_id] = $group->group_name;
		}								
		return $r;
	}
	
	function content_wrapper($content_view, $lang_key, $vars = array())
	{
		$vars['content_view'] = $content_view;
		$vars['_base'] = $this->base;
		$vars['_form_base'] = $this->form_base;
		$vars['img_path'] = $this->EE->config->item('theme_folder_url');
		$this->EE->cp->set_variable('cp_page_title', lang($lang_key));
		$this->EE->cp->set_breadcrumb($this->base, lang('mx_tool_box_module_name'));

		return $this->EE->load->view('_wrapper', $vars, TRUE);
	}
	
}

/* End of file mcp.mx_tool_box.php */ 
/* Location: ./system/expressionengine/third_party/mx_tool_box/mcp.mx_tool_box.php */ 
