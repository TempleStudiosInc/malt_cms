<?php
	echo HTML::style('_media/core/common/css/bootstrap-image-gallery.min.css');
	echo HTML::style('_media/core/common/jquery_file_uploader/css/jquery.fileupload-ui.css');
	echo HTML::style('_media/core/common/css/bootstrap-toggle-buttons.css');
?>
<?php  
	echo Form::open($controller.'/save_universal_image', array('class' => 'well', 'id' => 'universal_form', 'enctype' =>'multipart/form-data')); 
	echo Form::hidden('urlredirect', $urlredirect);	
?>
<div class="well" input_name='<?php echo $name ?>'>
	<ul class="nav nav-tabs">
		<li class="active" id="files">
			<a href="#" id="upload_files" class='upload_files_button'>Upload File</a>
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
		        		echo Form::file($name.'_fileupload', array('id' => $name.'_fileupload', 'targetinput' => $name, 'class' => 'fileupload', 'data-url' => '/'.$controller.'/handle_universal_upload'));
						echo Form::hidden($name, $value, array('id' => $name.'_content_selected_field_'.$type, 'class' => 'upload_input_field_'.$type));
					?>
		        </div>
	        </div>
	        <div id="<?php echo $name ?>_progress">
	            <div class="bar" style="width: 0%;"></div>
	        </div>
	    </div>
	   
	    <?php
	    	echo '<div id="'.$name.'_content_selected_'.$type.'" class="selected_'.$type.'"></div>';
	        echo '<div id="'.$name.'_container_'.$type.'" class="'.$name.'_container_'.$type.'"></div>';
	    ?>
	</div>
	<?php echo HTML::image('_media/core/common/img/loader.gif', array('id' => 'loader', 'style'=>'display:none;'));?>
	<?php echo Form::button('save', 'save', array('class' => 'btn btn-success save_button'));?>
</div>

<?php echo Form::close()?>
<script>
	var validation_form = false;
	var files_to_upload = 0;
	var uploads_started = false;
    var <?php echo $name ?>_data = false;
    var controller = "/<?php echo $controller?>";
    var user_type = "<?php echo $user_type?>";
    $(function () {
    	
    	if ($('[class^="upload_input_field_"]').length) {
	        $('[class^="upload_input_field_"]').each(function () {
	        	var type = '';
	        	$($(this).attr('class').split(' ')).each(function() {
		        	if (this.indexOf('upload_input_field_') >= 0) {
		        		type = this.replace('upload_input_field_', '');
		        	}
			    });
			    
	            var input_name = $(this).parents('.well').attr('input_name');
	            
	            $.ajax({
	                url: controller+'/get_universal_assets',
	                data: 'asset_ids='+$('#'+input_name+'_content_selected_field_'+type).val()+'&type='+type,
	                success: function(data) {
	                    $('#'+input_name+'_content_selected_'+type).append(data);
	                },
	                dataType: 'html'
	            });
	        });
	    }
    	
    	$('[class^="remove_"]').live('click', function(event) {
    	event.preventDefault();
    	
    	var type = '';
    	$($(this).attr('class').split(' ')).each(function() {
        	if (this.indexOf('remove_') >= 0) {
        		type = this.replace('remove_', '');
        	}
	    });
        
        var input_name = $(this).parents('.well').attr('input_name');
        var asset_dom = $(this).parents('.asset');
        var asset_id = asset_dom.attr('asset_id');
        
        $('#'+input_name+'_content_selected_field_'+type).val('');
        
        asset_dom.fadeOut('fast', function() {
            asset_dom.remove();
        });
    });
    
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
                    url: controller+'/universal_upload_complete',
                    data: { url: result[0]['url'], type: '<?php echo $type ?>', usertype: user_type},
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
<?php
	
	echo HTML::script('_media/core/common/jquery_file_uploader/js/universal.js');
	echo HTML::script('_media/core/common/jquery_file_uploader/js/vendor/jquery.ui.widget.js');
	echo HTML::script('_media/core/common/jquery_file_uploader/js/jquery.iframe-transport.js');
	echo HTML::script('_media/core/common/jquery_file_uploader/js/jquery.fileupload.js');
	echo HTML::script('_media/core/common/js/load-image.min.js');
	echo HTML::script('_media/core/common/js/canvas-to-blob.min.js');
	echo HTML::script('_media/core/common/jquery_file_uploader/js/locale.js');
?>