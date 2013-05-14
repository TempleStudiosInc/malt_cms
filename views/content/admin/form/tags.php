<?php
	$object_value = $value;
	$value = '';
	foreach ($object_value as $object_value_item)
	{
		$value.= $object_value_item->name.',';
	}
	$value = trim($value, ',');
	echo Form::input($name, $value, array('class' => 'tags_manager', 'placeholder' => 'Tags'));
?>