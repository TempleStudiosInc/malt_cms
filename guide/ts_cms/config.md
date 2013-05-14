# TS CMS configuration

The CMS operates off a config file called 'content'.

Array configuration as follows:

~~~
	return array(
    'about' => array(
        'label' => 'About',
        'type' => 'single',
        'fields' => array(
            'main_blurb' => array(
                'label' => 'Main Blurb',
                'type' => 'textarea',
                'rules' => array(
                
                ),
                'class' => 'ckeditor',
                
            ),
            'title' => array(
                'label' => 'Title',
                'type' => 'input',
                'rules' => array(
                
                ),
            )
        )
    )
~~~
