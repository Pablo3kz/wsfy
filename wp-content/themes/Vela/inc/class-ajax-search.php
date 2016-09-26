<?php
class Ajax_Search{
    
    function __construct(){
        add_action('wp_ajax_ajax_search', array($this, 'get_search_results'));
        add_action('wp_ajax_nopriv_ajax_search', array($this, 'get_search_results'));
    }

    function get_search_results()
	{            
        ob_clean();
		$results = array();
		$keyword = apply_filters('ajax_search_keyword', $_POST['search_keyword']);

		if(!empty($keyword))
		{
			$search = $this->get_search_objects(false);
			foreach($search as $key => $object)
			{
				$posts_result = $this->posts($keyword, $object['name']);
				if(sizeof($posts_result) > 0) {
					$results[] = array(
                        'items' => $posts_result, 
                        'title' => $object['label'],
                        'name'  => $object['name']
                        );
				}
			}
			echo json_encode($results);
		}
        exit;
	}
    function get_search_objects($all = false)
	{
        global $wyde_options;

		$search = array();
		
		$post_types = $this->get_post_types();

		foreach($post_types as $post_type)
		{		
				$show =( isset( $wyde_options['search_post_type'][$post_type->name] ) && $wyde_options['search_post_type'][$post_type->name] );
				if($all || $show){
					$search[] = array(
						'name' => $post_type->name, 
						'label' => 	$post_type->label
					);
				}
				
		}
		return $search;
	}
    function get_post_types()
	{
		$post_types = get_post_types(array('_builtin' => false, 'exclude_from_search' => false), 'objects');
		$post_types['post'] = get_post_type_object('post');
		$post_types['page'] = get_post_type_object('page');
		unset($post_types['wpsc-product-file']);
		return $post_types;
	}

    function posts($keyword, $post_type='post')
	{
		global $wpdb, $wyde_options;
		
        $posts = array();

        $search_order = isset($wyde_options['search_order'])? $wyde_options['search_order']:'post_title';

        $search_content = isset($wyde_options['search_content'])? $wyde_options['search_content']:false;
        		
		$order_results = (  $search_order != '' ? " ORDER BY ".$search_order : "");
		
        $limit = intval( $wyde_options['search_suggestion_items'] );

        $results = array();
		
		$query = "
			SELECT 
				$wpdb->posts.ID 
			FROM 
				$wpdb->posts
			WHERE 
				(post_title LIKE '%%%s%%' ".($search_content ? "or post_content LIKE '%%%s%%')":")")." 
				AND post_status='publish' 
				AND post_type='".$post_type."' 
				$order_results 
			LIMIT 0, %d";

		$query = ($search_content ? $wpdb->prepare($query, $keyword, $keyword, $limit) : $wpdb->prepare($query, $keyword, $limit));

		$results = $wpdb->get_results( $query );

		if(sizeof($results) > 0 && is_array($results) && !is_wp_error($results))
		{
			foreach($results as $result)
			{
				$pst = $this->post_object($result->ID);
				if($pst){
					$posts[] = $pst; 
				}
			}
		}

		return $posts;
	}

    function post_object($id) {

		global $post;

		$date_format = get_option( 'date_format' );
		
        $post = get_post($id);
		
        if($post != null)
		{
			$post_object = new stdclass();
			$post_object->ID = $post->ID;
            $post_object->post_title = get_the_title($post->ID);
            
			$post_thumbnail_id = get_post_thumbnail_id( $post->ID);
			if( $post_thumbnail_id > 0)
			{
				$thumb = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');
				$post_object->post_image =  esc_url( (trim($thumb[0]) == "" ? "" : $thumb[0]) );
			}
			
			
			$post_object->post_author = get_the_author_meta('display_name', $post->post_author);
			$post_object->post_link = esc_url( get_permalink($post->ID) );
			$post_object->post_date =  get_the_date();
			return $post_object;
		}
		return false;
	}

}

new Ajax_Search();