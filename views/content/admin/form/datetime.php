<div class="input-append date datetime_picker">
	<?php
		if ($value == null)
		{
			$value = 'now';
		}
		echo Form::input($name, date('m/d/Y h:i A', strtotime($value)), array('data-format' => "MM/dd/yyyy HH:mm PP"));
	?>
	<span class="add-on">
		<i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
	</span>
</div>