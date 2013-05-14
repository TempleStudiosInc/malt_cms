<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Content extends Controller_Admin_Website {

    public function before()
    {
        $this->content = (array) Kohana::$config->load('content');
        ksort($this->content);
        
        $this->page_title = '';
        $this->default_node = '';
        $this->selected_node = '';
        
        parent::before();
    }
    
    public function after()
    {
		$request = Request::initial();
		$requested_controller = str_replace('Admin_', '', $request->controller());
		$requested_action = $request->action();
		
		$content_navigation = (array) Kohana::$config->load('content');
		ksort($content_navigation);
		
		$sidebar_navigation_view = View::factory('content/admin/navigation');
        $sidebar_navigation_view->requested_controller = $requested_controller;
        $sidebar_navigation_view->content_navigation = $content_navigation;
        $sidebar_navigation_view->node_name = $this->selected_node;
        $this->template->sidebar_navigation = $sidebar_navigation_view;
		
        parent::after();
    }
    
    public function action_index()
    {
        foreach ($this->content as $content_key => $content_item)
        {
            $node_name = $content_key;
            $this->default_node = $node_name;
            $this->selected_node = $node_name;
            break;
        }
        
        $this->action_view();
    }
    
    public function action_view()
    {
    	$this->template->content_title = 'CMS';
        $node_name = $this->request->param('id', $this->default_node);
        $this->selected_node = $node_name;
        
        $node_type = $this->content[$node_name]['type'];
        
        if($node_type ==  'single')
        {
            $view = View::factory('content/admin/edit');
            $content = ORM::factory('Content')->where('node_name','=',$node_name)->find();
            $view->content = $content;
        }
        elseif($node_type == 'multiple') 
        {
            $view = View::factory('content/admin/index');
			
			if (isset($this->content[$node_name]['sortable']))
			{
				if ($this->content[$node_name]['sortable'])
				{
					$content_model = new Model_Content;
					$contents = $content_model->get_contents($node_name, '1', array('page_limit' => '6000','order_by' => 'order'))->contents;
				}	
			}
			else
			{
				$contents = ORM::factory('Content')->where('node_name','=',$node_name)->find_all();
			}
            
            $view->contents = $contents;
        }
		elseif($node_type == 'grouped')
        {
        	$group = Arr::get($_GET, 'group', false);
			if ($group === false)
			{
				$view = View::factory('content/admin/grouped');
				$contents = ORM::factory('Content')
					->select(array('contentfields.value', 'grouped_field'))
					->where('node_name', '=', $node_name)
					->join('contentfields')
					->on('content.id', '=', 'contentfields.content_id')
					->where('field_name', '=', 'group')
					->group_by('grouped_field')
					->find_all();
			}
			else
			{
				$view = View::factory('content/admin/index');
				
				$contents = ORM::factory('Content')
					->select(array('contentfields.value', 'grouped_field'))
					->where('node_name', '=', $node_name)
					->join('contentfields')
					->on('content.id', '=', 'contentfields.content_id')
					->where('field_name', '=', 'group');
				if ($group == '_none')
				{
					$group = '';
				}
				$contents = $contents->having('grouped_field', '=', $group)->find_all();
			}
			$view->contents = $contents;
        }
        
        $view->node_name = $node_name;
        $view->node = $this->content[$node_name];
        $this->template->body = $view;
    }
    
    public function action_add()
    {
        $node_name = $this->request->param('id');
        $this->selected_node = $node_name;
        
        $view = View::factory('content/admin/edit');
        $content = ORM::factory('Content');
        $view->content = $content;
        
        $view->node_name = $node_name;
        $view->node = $this->content[$node_name];
        $this->template->body = $view;
    }
    
    public function action_edit()
    {
        $content_id = $this->request->param('id');
        
        $view = View::factory('content/admin/edit');
        $content = ORM::factory('Content', $content_id);
        $node_name = $content->node_name;
        $view->content = $content;
        $this->selected_node = $node_name;
        
        $view->node_name = $node_name;
        $view->node = $this->content[$node_name];
        $this->template->body = $view;
    }
    
    public function action_delete()
    {
        $content_id = $this->request->param('id');
        
        $content = ORM::factory('Content', $content_id);
        $node_name = $content->node_name;
        
        $form_fields = $content->contentfields->find_all();
        foreach ($form_fields as $form_field)
        {
            $form_field->delete();
        }
        $content->delete();
		
		if (Arr::get(Kohana::modules(), 'search', FALSE)) 
		{
			if (Arr::get($this->content[$node_name], 'searchable', false))
			{
				$type = 'Content_'.$node_name;
				$type = str_replace(' ', '_', ucwords(str_replace('_', ' ', $type)));
				
				$search_params = array('type' => $type);
				$search = new Model_Search($search_params);
				$result = $search->delete($content_id, $type);
			}
		}
        
        Notice::add(Notice::SUCCESS, $this->content[$node_name]['label'].' deleted.');
        $this->redirect('/admin_content/view/'.$node_name);
    }
    
    public function action_save()
    {
        $post = $this->request->post();
        
        $id = Arr::get($post, 'id');
        unset($post['id']);
        $node_name = Arr::get($post, 'node_name');
        unset($post['node_name']);
        unset($post['submit']);
        
        $validation = Validation::factory($post);
        
        foreach ($this->content[$node_name]['fields'] as $field_key => $value)
        {
            foreach ($value['rules'] as $rule_key => $rule_value)
            {
                if ($rule_value === true)
                {
                    $validation->rule($field_key, $rule_key);
                }
                else
                {
                    $validation->rule($field_key, $rule_key, array(':value', $rule_value));
                }
            }
			
			if ($value['type'] == 'toggle' AND ! Arr::get($post, $field_key, false))
			{
				$post[$field_key] = 0;
			}
        }
        
        if ($validation->check())
        {
            if ($id != 0)
            {
                $content = ORM::factory('Content', $id);
            }
            else
            {
                $content = ORM::factory('Content');
                $content->node_name = $node_name;
                $content->save();
            }
            
            foreach ($post as $key => $value)
            {
            	if (array_key_exists($key, $this->content[$node_name]['fields']))
				{
					$form_field = $content->contentfields->where('field_name', '=', $key)->find();
	                $form_field->content_id = $content->id;
	                $form_field->field_name = $key;
	                $form_field->value = $value;
	                $form_field->save();
				}
            }
			
			if (Arr::get(Kohana::modules(), 'search', FALSE)) 
			{
				if (Arr::get($this->content[$node_name], 'searchable', false))
				{
					$type = 'Content_'.$node_name;
					$type = str_replace(' ', '_', ucwords(str_replace('_', ' ', $type)));
					
					$search_data = array();
					$search_data['id'] = $content->id;
					$content_fields = $content->get_fields();
					foreach ($content_fields as $field_name)
					{
						$search_data[$field_name] = $content->get_field_value($field_name);
					}
					$search_params = array('type' => $type);
					$search = new Model_Search($search_params);
					$result = $search->index($search_data, $type);
				}
			}
            
            Notice::add(Notice::SUCCESS, $this->content[$node_name]['label'].' saved.');
            $this->redirect('/admin_content/view/'.$node_name);
        }
        else
        {
            $errors = $validation->errors();
            
            $error_message = $this->content[$node_name]['label'].' not saved.';
            $error_message.= '<ul>';
            foreach ($errors as $field_name => $error)
            {
                $error_message.= '<li>';
                $error_message.= $this->content[$node_name]['fields'][$field_name]['label'];
                switch ($error[0])
                {
                    case 'not_empty':
                        $error_message.= ' cannot be blank.';
                        break;
                    case 'min_length':
                        $min_length = $this->content[$node_name]['fields'][$field_name]['rules'][$error[0]];
                        $error_message.= ' must be at least '.$min_length.' characters.';
                        break;
                    case 'max_length':
                        $max_length = $this->content[$node_name]['fields'][$field_name]['rules'][$error[0]];
                        $error_message.= ' must be under '.$max_length.' characters.';
                        break;
                    case 'exact_length':
                        $exact_length = $this->content[$node_name]['fields'][$field_name]['rules'][$error[0]];
                        $error_message.= ' must be exactly '.$exact_length.' characters.';
                        break;
                    case 'email':
                        $error_message.= ' must be a valid email address.';
                        break;
                    case 'url':
                        $error_message.= ' must be a valid url.';
                        break;
                    case 'phone':
                        $error_message.= ' must be a valid phone number.';
                        break;
                    case 'date':
                        $error_message.= ' must be a valid date.';
                        break;
                    case 'alpha':
                        $error_message.= ' must be letters only.';
                        break;
                    case 'alpha_dash':
                        $error_message.= ' must be only letters or dashes.';
                        break;
                    case 'alpha_numeric':
                        $error_message.= ' must be only letters or numbers.';
                        break;
                    case 'digit':
                        $error_message.= ' must be only numbers.';
                        break;
                    case 'decimal':
                        $error_message.= ' must be decimal.';
                        break;
                    case 'numeric':
                        $error_message.= ' must be numeric.';
                        break;
                }
                $error_message.= '</li>';
            }
            $error_message.= '</ul>';
            
            Notice::add(Notice::ERROR, $error_message);
            $this->redirect('/admin_content/edit/'.$id);
        }
    }

    public function action_check_field()
    {
        $get = $_GET;
        $node_name = $this->request->param('id');
        $field_key = key($_GET);
        $field_value = array_pop($_GET);
        
        $validation = Validation::factory($get);
        
        $field_rules = Arr::get($this->content[$node_name]['fields'][$field_key], 'rules', false);
        if ($field_rules)
        {
            foreach ($field_rules as $rule_key => $rule_value)
            {
                if ($rule_value === true)
                {
                    $validation->rule($field_key, $rule_key);
                }
                else
                {
                    $validation->rule($field_key, $rule_key, array(':value', $rule_value));
                }
            }
        }
        
        if ($validation->check())
        {
            echo json_encode(true);
        }
        else
        {
            $errors = $validation->errors();
            
            $error_message = '';
            foreach ($errors as $field_name => $error)
            {
                $error_message.= $this->content[$node_name]['fields'][$field_name]['label'];
                switch ($error[0])
                {
                    case 'not_empty':
                        $error_message.= ' cannot be blank.';
                        break;
                    case 'min_length':
                        $min_length = $this->content[$node_name]['fields'][$field_name]['rules'][$error[0]];
                        $error_message.= ' must be at least '.$min_length.' characters.';
                        break;
                    case 'max_length':
                        $max_length = $this->content[$node_name]['fields'][$field_name]['rules'][$error[0]];
                        $error_message.= ' must be under '.$max_length.' characters.';
                        break;
                    case 'exact_length':
                        $exact_length = $this->content[$node_name]['fields'][$field_name]['rules'][$error[0]];
                        $error_message.= ' must be exactly '.$exact_length.' characters.';
                        break;
                    case 'email':
                        $error_message.= ' must be a valid email address.';
                        break;
                    case 'url':
                        $error_message.= ' must be a valid url.';
                        break;
                    case 'phone':
                        $error_message.= ' must be a valid phone number.';
                        break;
                    case 'date':
                        $error_message.= ' must be a valid date.';
                        break;
                    case 'alpha':
                        $error_message.= ' must be letters only.';
                        break;
                    case 'alpha_dash':
                        $error_message.= ' must be only letters or dashes.';
                        break;
                    case 'alpha_numeric':
                        $error_message.= ' must be only letters or numbers.';
                        break;
                    case 'digit':
                        $error_message.= ' must be only numbers.';
                        break;
                    case 'decimal':
                        $error_message.= ' must be decimal.';
                        break;
                    case 'numeric':
                        $error_message.= ' must be numeric.';
                        break;
                }
            }
            echo json_encode($error_message);
        }
        die();
    }

	public function action_update_order()
	{
		$get = Arr::get($_GET, 'content', null);
		$count = 1;
		foreach ($get as $id)
		{
			$content = ORM::factory('Content', $id);
			$field = $content->contentfields->where('field_name','=', 'order')->find();
			$field->value = $count;
			$field->save();
			$count++;	
		}
		die();
	}
}