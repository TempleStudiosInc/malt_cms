<div class="well" input_name='<?php echo $name ?>'>
	<ul class="nav nav-tabs">
		<li class="active" id="files">
			<a href="#" id="upload_files" class='upload_files_button'>Upload File</a>
	  	</li>
	  	<li id="library">
	  		<a href="#"  id="med_lib" class='media_lib_button'>Select File From Library</a>
	  	</li>
	</ul>
	<div class="tab-content">
		<div class="upload_file_upload">
	    	<div class="input-prepend">
	    <?php
	        echo Form::input($name.'_display', '', array('id' => $name.'_display', 'readonly' => 'readonly', 'class' => 'span4'));
	    ?>
		        <div class="btn btn-primary fileinput-button" id="<?php echo $name ?>_container">
		            <i class="icon-plus icon-white"></i> File
		        	<?php
		        		echo Form::file($name.'_fileupload', array('id' => $name.'_fileupload', 'targetinput' => $name, 'class' => 'fileupload', 'data-url' => '/admin_asset/handle_upload'));
						echo Form::hidden($name, $value, array('id' => $name.'_content_selected_field_'.$type, 'class' => 'upload_input_field_'.$type));
					?>
		        </div>
	        </div>
	        <div id="<?php echo $name ?>_progress">
	            <div class="bar" style="width: 0%;"></div>
	        </div>
	    </div>
	    <div class="media_lib_upload">
	    <?php
			echo Form::label(''.$name.'_content_search_title_'.$type, 'Choose From Media Library');
			echo '<div class="input-append">';
			echo Form::input(''.$name.'_content_search_title_'.$type, '', array('class' => 'span4', 'placeholder' => 'Enter Search Term Here'));
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
    var <?php echo $name ?>_data = false;
    $(function () {
        $('#<?php echo $name ?>_fileupload').fileupload({
            replaceFileInput: false,
            autoUpload: false,
            dataType: 'json',
            add: function(e, data) {
                if ($('#<?php echo $name ?>_display').val() == '') {
                    files_to_upload++;
                }
                <?php echo $name ?>_data = data;
                $('#<?php echo $name ?>_display').val(data.files[0].name);
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#<?php echo $name ?>_progress .bar').css('width', progress + '%');
            },
            success: function(result, textStatus, jqXHR) {
                $.each(result, function (index, file) {
                    $('<p/>').text(file.name+' upload complete.').appendTo($('#status_report'));
                    $('<p/>').text(file.name+' processing.').appendTo($('#status_report'));
                });
                $.ajax({
                    url: '/admin_asset/upload_complete',
                    data: { url: result[0]['url'], type: '<?php echo $type ?>'},
                    dataType:'json',
                    success:function(data, textStatus, jqXHR){
                        var asset_id = data.asset_id;
                        $('input[name="<?php echo $name ?>"]').val(asset_id);
                        
                        $.each(result, function (index, file) {
                            $('<p/>').text(file.name+' processing complete.').appendTo($('#status_report'));
                        });
                        
                        $('#<?php echo $name ?>_display').remove();
                        $('#<?php echo $name ?>_fileupload').remove();
                        
                        files_to_upload--;
                        submit_content_form();
                    }
                });
            }
        });
    });
</script>