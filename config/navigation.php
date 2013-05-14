<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'admin' => array(
		'content' => array(
			'title' => 'Content',
			'url' => '/admin_content',
			'controller' => 'Content',
			'permission' => 'admin',
			'submenu' => array(
				'cms' => array(
					'title' => 'CMS',
					'url' => '/admin_content',
					'controller' => 'Content',
					'permission' => 'content',
					'icon' => 'icon-list-ul'
				),
			)
		),
	)
);