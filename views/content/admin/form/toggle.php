<div class="switch" data-on-label="<i class='icon-ok'></i>" data-on="success" data-off-label="<i class='icon-remove'></i>" data-off="danger">
    <?php
    	echo Form::checkbox($name, 1, (bool) $value, array('class' => 'checkbox'));
    ?>
</div>