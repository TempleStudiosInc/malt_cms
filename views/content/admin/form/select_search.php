<?php
	$options = array('0' => 'Select An Option');
	switch($type)
	{
		case 'blog_posts':
			$posts = ORM::factory('Blogs_Post')->where('status', '=', 1)->find_all();
			foreach ($posts as $post)
			{
				$options[$post->id] = $post->title;
			}
			break;
	}
	
	echo Form::select($name, $options, $value, array('style'=>'width:25%', 'class'=> $type.'_select_search'));
?>