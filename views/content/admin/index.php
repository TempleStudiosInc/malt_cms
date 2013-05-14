<div class="well">
    <h3><?php echo $node['label'] ?></h3>
    
    <div>
        <?php echo HTML::anchor('/admin_content/add/'.$node_name, 'Add', array('class' => 'btn btn-success')); ?>
    </div>
    
    <h/>
   	<table class="table table-striped table-hover" style="background:white;">
        <thead>
            <tr>
                <?php
                    $field_count = 0;
                    foreach ($node['fields'] as $key => $value)
                    {
                        if (Arr::get($value, 'table_display', 'true') == 'true')
                        {
                            echo '<th>'.$value['label'].'</th>';
                            $field_count++;
                        }
                    }
                ?>
                <th></th>
            </tr>
        </thead>
          <?php 
		    	if (isset($node['sortable']))
				{
					if ($node['sortable'])
					{
						echo '<tbody class="sortable_content_table">';
					}		
				}
				else
				{
					echo '<tbody>';	
				}    
    	   ?>
       
            <?php
                if (count($contents) > 0)
                {
                    foreach ($contents as $content)
                    {
                        echo '<tr id="content_'.$content->id.'">';
                        $content = ORM::factory('content', $content->id);
                        
                        foreach ($node['fields'] as $key => $value)
                        {
                            if (Arr::get($value, 'table_display', 'true') == 'true')
                            {
                                $form_field = $content->contentfields->where('field_name', '=', $key)->find();
                                
                                echo '<td>';
								if ($key == 'order')
								{
									echo '<div class="drag_handle" style="float:left;"><i class="icon-resize-vertical"></i></div>';
								}
                                if ($value['type'] == 'file_image')
                                {
                                    if ($form_field->value)
                                    {
                                        $asset = ORM::factory('Asset', $form_field->value);
                                        $image_url = $asset->files->where('type', '=', 'image_tiny')->find()->url;
                                        echo HTML::image($image_url, array('class' => 'img-polaroid'));
                                    }
                                }
								elseif ($value['type'] == 'file_video')
                                {
                                    if ($form_field->value)
                                    {
                                        $asset = ORM::factory('Asset', $form_field->value);
                                        $image_url = $asset->files->where('type', '=', 'image_tiny')->find()->url;
                                        echo HTML::image($image_url, array('class' => 'img-polaroid'));
                                    }
                                }
                                elseif ($value['type'] == 'file_raw')
                                {
                                    if ($form_field->value)
                                    {
                                        $asset = ORM::factory('Asset', $form_field->value);
                                        if ($asset->type == 'image')
                                        {
                                            $image_url = $asset->files->where('type', '=', 'image_tiny')->find()->url;
                                            echo HTML::image($image_url, array('class' => 'img-polaroid'));
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
                                    }
                                }
                                else
                                {
                                    echo Text::limit_chars($form_field->value, 100, '...', true);
                                }
                                echo '</td>';
                            }
                        }
                        echo '<td>';
                        echo '<div class="btn-group pull-right">';
                        echo HTML::anchor('/admin_content/edit/'.$content->id, 'Edit', array('alt' => 'Edit', 'class' => 'btn btn-small edit'));
                        echo HTML::anchor('/admin_content/delete/'.$content->id, 'Delete', array('alt' => 'Delete', 'class' => 'delete btn btn-small btn-danger'));
                        echo '</div>';
                        echo '</td>';
                        echo '</tr>';
                    }
                }
                else
                {
                    echo '<tr><td colspan='.$field_count.'>No Records Found.</td></tr>';
                }
            ?>
        </tbody>
    </table>
</div>
    
<div class="modal hide dialog" id="delete_dialog">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3>Confirmation Required</h3>
    </div>
    <div class="modal-body">
        <p>Are you sure you want to delete this?</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn modal_hide">No</a>
        <a href="#" class="btn btn-primary modal_delete_yes_button">Yes</a>
    </div>
</div>