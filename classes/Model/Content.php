<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Content extends ORM {
    
    public $memcache = false;
    
    protected $_has_many = array(
        'contentfields' => array(
            'model' => 'Contentfield'
        )
    );
    
    public function __construct($id = null)
    {
        parent::__construct($id);
    }
    
    public function clear_cache()
    {
    	$this->memcache = Cache::instance();
		
        $content_cache_key = $this->id.'_'.$this->node_name.'_';
        
        $content_fields = $this->contentfields->find_all();
        foreach ($content_fields as $content_field)
        {
            $this->memcache->delete($content_cache_key.$content_field->field_name);
        }
    }
    
    public function get_contents($node_name, $page = 1, $params = array())
    {
        $page_limit = Arr::get($params, 'page_limit', 10);
        $order_by = Arr::get($params, 'order_by', 'id');
        $order_direction = Arr::get($params, 'order_direction', 'ASC');
        $offset = $page_limit*($page-1);
        $template = Arr::get($params, 'template', 'pagination/basic');
        $total_items = ORM::factory('Content')->where('node_name', '=', $node_name)->count_all();
        
        $contents = ORM::factory('Content')
            ->select(array('contentfields.value', 'sortable_field'))
            ->join('contentfields')
            ->on('content.id', '=', 'contentfields.content_id')
            ->where('node_name', '=', $node_name)
            ->where('contentfields.field_name', '=', $order_by)
            ->limit($page_limit)
            ->offset($offset);
        
        $sortable_field = 'sortable_field';
        if ($order_by == 'id' OR $order_by == 'order')
        {
            $sortable_field = DB::expr('CAST(sortable_field AS SIGNED)');
        }
        $contents->order_by($sortable_field, $order_direction);
        $contents = $contents->find_all();
        $pagination = Pagination::factory(array(
            'items_per_page' => $page_limit,
            'total_items' => $total_items,
            'view' => $template
        ));
        
        $return = new stdClass;
        $return->pagination = $pagination;
        $return->contents = $contents;
        
        return $return;
    }
	
	public function get_fields()
	{
		$fields = array();
		$content_fields = $this->contentfields->find_all();
		foreach ($content_fields as $content_field)
		{
			$fields[] = $content_field->field_name;
		}
		return $fields;
	}
    
    public function get_field_value($field_name)
    {
        $field_value = $this->contentfields->where('field_name', '=', $field_name)->find()->value;
        return $field_value;
    }
    
    public function get_image_value($field_name, $size = null)
    {
        $field_value = $this->get_field_value($field_name);
        $asset = ORM::factory('Asset', $field_value);
        $image_url = $asset->get_image_value($size);
        return $image_url;
    }
    
    public function get_raw_value($field_name)
    {
        $field_value = $this->get_field_value($field_name);
        $asset = ORM::factory('Asset', $field_value);
        $raw_url = $asset->files->where('type', '=', 'raw')->find()->url;
        
        return $raw_url;
    }
	
	public function get_video_value($field_name)
	{
		$field_value = $this->get_field_value($field_name);
        $asset = ORM::factory('Asset', $field_value);
        return $asset;
	}
    
    private function cache_item($field_name, $field_value)
    {
    	$this->memcache = Cache::instance();
		
        $cache_key = $this->id.'_'.$this->node_name.'_'.$field_name;
        $this->memcache->set($cache_key, $field_value);
    }
}
