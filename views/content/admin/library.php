<?php
    echo Form::input('content_search_image_title', '', array('class' => 'span6'));
    echo Form::button(NULL, 'Search', array('class' => 'content_image_search btn btn-primary'));
    echo Form::hidden($name, $value, array('id' => 'content_images_selected_field'));
	
?>
	<div id="images"></div>
	<div id="content_images_selected" class="images_selected"></div>
	<div class="image_container" style="height:50px; background-repeat: no-repeat"></div>



