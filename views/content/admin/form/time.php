<div class="input-append date time_picker">
	<?php
		echo Form::input($name, date('h:i A', strtotime($value)), array('data-format' => "HH:mm PP"));
	?>
	<span class="add-on">
		<i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
	</span>
</div>