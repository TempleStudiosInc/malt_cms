<?php
    echo HTML::style('_media/core/common/css/bootstrap-image-gallery.min.css');
    echo HTML::style('_media/core/common/jquery_file_uploader/css/jquery.fileupload-ui.css');
    echo HTML::style('_media/core/admin/css/asset.css');
	echo HTML::style('_media/core/common/css/bootstrap-toggle-buttons.css');
	echo HTML::style('_media/core/common/css/bootstrap-datetimepicker.min.css');
	
    echo HTML::script('/_media/core/common/js/libs/jquery.validate.min.js');
    echo HTML::script('/_media/core/admin/js/libs/ckeditor/ckeditor.js');
    
    echo Form::open('/admin_content/save', array('class' => 'well', 'id' => 'content_form', 'enctype' =>'multipart/form-data'));
    echo Form::hidden('id', $content->id);
    echo Form::hidden('node_name', $node_name);
   
    foreach ($node['fields'] as $key => $value)
    {
        $form_field = $content->contentfields->where('field_name', '=', $key)->find();
        $class = Arr::get($value, 'class', '');
      
        if ($value['type'] == 'file_raw')
        {
            if ($form_field->value)
            {
                echo '<div>';
                $asset = ORM::factory('Asset', $form_field->value);
                if ($asset->type == 'image')
                {
                    $image_url = $asset->files->where('type', '=', 'image_small')->find()->url;
                    echo Html::image($image_url, array('class' => 'img-polaroid'));
                }
                else
                {
                    $image_url = $asset->files->where('type', '=', 'image_tiny')->find()->url;
                    echo HTML::image($image_url, array('class' => 'img-polaroid'));
                    echo '&nbsp;&nbsp;';
                    $upload_url = $asset->files->where('type', '=', 'raw')->find()->url;
                    $upload_url_pathinfo = pathinfo($upload_url);
                    echo HTML::anchor($upload_url, $upload_url_pathinfo['basename'], array('target' => '_BLANK'));
                }
                echo '</div>';
            }
        }
        echo Form::label($key, $value['label']);
        echo Form::$value['type']($key, $form_field->value, array('class' => $class, 'id' => 'form_field_'.$key));
    }
    echo '<div id="status_report"></div>';
    
    echo '<div>';
	
    echo HTML::image('_media/core/common/img/loader.gif', array('id' => 'loader', 'style'=>'display:none;'));
    echo Form::button('save', 'save', array('class' => 'btn btn-success save_button'));
    echo '&nbsp;&nbsp;<small>or</small>&nbsp;&nbsp;';
    echo HTML::anchor('/admin_content/view/'.$node_name, 'cancel');
    echo '</div>';
    echo Form::close();
    
    echo HTML::script('_media/core/common/jquery_file_uploader/js/content_main.js');
    echo HTML::script('_media/core/common/jquery_file_uploader/js/vendor/jquery.ui.widget.js');
    echo HTML::script('_media/core/common/jquery_file_uploader/js/jquery.iframe-transport.js');
    echo HTML::script('_media/core/common/js/load-image.min.js');
    echo HTML::script('_media/core/common/js/canvas-to-blob.min.js');
    echo HTML::script('_media/core/common/jquery_file_uploader/js/locale.js');
	// echo HTML::script('_media/core/common/js/tmpl.min.js');
?>

<style>
    .bar {
        height: 18px;
        background: green;
    }
</style>

<script>
    $(function () {
        $('#content_form').validate({
            submitHandler: function(form) {
                validation_form = form;
                submit_content_form(form);
            },
            rules: {
                <?php
                    foreach ($node['fields'] as $key => $value)
                    {
                        $required = '';
                        if (Arr::get($value, 'rules', false))
                        {
                            if (Arr::get($value['rules'], 'not_empty', false))
                            {
                                $required = 'required: true,';
                            }
                            echo $key.':{'.$required.' remote: "/admin_content/check_field/'.$node_name.'"},';
                        }
                    }
                ?>
            }
        });
    })
</script>