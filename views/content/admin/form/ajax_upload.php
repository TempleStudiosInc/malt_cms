<div class="well" input_name='<?php echo $name ?>'>
	<ul class="nav nav-tabs">
	  	<li id="library" class="active">
	  		<a href="#"  id="med_lib" class='media_lib_button'>Select File From Library</a>
	  	</li>
	</ul>
	<div class="tab-content">
	    <div class="media_lib_upload" style="display:block;">
	    <?php
			echo Form::label(''.$name.'_content_search_title_'.$type, 'Choose From Media Library');
			echo '<div class="input-append">';
			echo Form::input(''.$name.'_content_search_title_'.$type, '', array('style'=> 'height:30px;','class' => 'span4', 'placeholder' => 'Enter Search Term Here'));
	   		echo Form::button(NULL, '<i class="icon-search icon-white"></i> Search', array('class' => 'content_search_'.$type.' btn btn-primary'));
			echo '</div>';
			echo '<div id="'.$name.'_'.$type.'"></div>';
			echo '<div class="'.$type.'_container" style="background-repeat: no-repeat"></div>';
	    ?>
	    </div>
	    <?php
	    	echo '<div id="'.$name.'_content_selected_'.$type.'" class="selected_'.$type.'"></div>';
	        echo '<div id="'.$name.'_container_'.$type.'" class="'.$name.'_container_'.$type.'"></div>';
	    ?>
	</div>
</div>
<script>
    $(function () {
    	$('#med_lib').trigger('click');
    	
    	$('.content_add_image').live('click', function(e){
    		e.preventDefault();
    		var asset_id = $(this).parents('tr').attr('asset_id');
    		$.get('/ajax/ajax_get_image_url', {asset_id:asset_id}, function(data){
    			window.opener.CKEDITOR.tools.callFunction( 1, data );
    			window.close();
    		});
    	})
    });
</script>