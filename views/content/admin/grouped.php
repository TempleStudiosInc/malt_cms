<div class="well">
    <h3><?php echo $node['label'] ?></h3>
    
    <div>
        <?php echo HTML::anchor('/admin_content/add/'.$node_name, 'Add', array('class' => 'btn btn-success')); ?>
    </div>
    
    <h/>
   	<table class="table table-striped table-hover" style="background:white;">
        <thead>
            <tr>
                <th>Group</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
                if (count($contents) > 0)
                {
                    foreach ($contents as $content)
                    {
                        echo '<tr>';
						$grouped_field_title = $content->grouped_field;
						if ($grouped_field_title == '')
						{
							$grouped_field_title = 'No Group';
						}
						echo '<td>'.$grouped_field_title.'</td>';
                        echo '<td><div class="btn-group pull-right">';
                        echo HTML::anchor('/admin_content/view/'.$node_name.'?group='.$content->grouped_field, 'View', array('alt' => 'View', 'class' => 'btn btn-small edit'));
                        echo '</div></td>';
                        echo '</tr>';
                    }
                }
            ?>
        </tbody>
    </table>
</div>