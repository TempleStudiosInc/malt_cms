<div class="well sidebar-nav">
    <ul class="nav nav-list">
        <li class="nav-header"><?php echo $requested_controller ?></li>
        <?php
            $nav_parent = false;
            $ul_opened = false;
            foreach ($content_navigation as $node => $node_content)
            {
                $subnav_node = explode('_', $node);
                if (count($subnav_node) > 1 AND $nav_parent != ucfirst($subnav_node[0]))
                {
                    if ($ul_opened)
                    {
                        echo '</ul>';
                        $ul_opened = false;
                    }
                    $ul_opened = true;
                    $nav_parent = ucfirst($subnav_node[0]);
                    
                    echo '<li class="nav-header">';
                    echo $nav_parent;
                    echo '</li>';
                    
                    echo '<ul class="nav nav-list">';
                }
                elseif (count($subnav_node) == 1)
                {
                    if ($ul_opened)
                    {
                        echo '</ul>';
                        $ul_opened = false;
                    }
                }
                
                echo '<li ';
                if ($node_name == $node)
                {
                    echo 'class="active"';
                }
                echo '>';
                echo HTML::anchor('/admin_content/view/'.$node, $node_content['label']);
                echo '</li>';
            }
        ?>
    </ul>
</div><!--/.well -->