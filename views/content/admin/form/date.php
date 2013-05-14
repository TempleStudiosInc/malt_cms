<div class="input-append date date_picker">
	<?php
		echo Form::input($name, date('m/d/Y', strtotime($value)), array('data-format' => "MM/dd/yyyy"));
	?>
	<span class="add-on">
		<i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
	</span>
</div>