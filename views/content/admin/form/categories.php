<?php
	$select_values = array();
	foreach ($value as $key => $item)
	{
		$select_values[] = $item->id;
	}
	echo Form::select($name.'[]', $values, $select_values, array('class' => 'category_selection span4'));
?>