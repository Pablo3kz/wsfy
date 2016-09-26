<?php
if( !class_exists('Wyde_Importer') ) {

    class Wyde_Importer {

        function __construct(){

        }

        public function import_demo_content( $data_file ){

            try{

                if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true);

                if ( !class_exists( 'WP_Import' ) ) {
                    include dirname( __FILE__ ) ."/wordpress-importer.php";
                }

                $xml_filepath = $data_file;
                if( !file_exists($xml_filepath) ) return false;
                $importer = new WP_Import();
                $importer->fetch_attachments = true;
                ob_start();
                $importer->import($xml_filepath);
                ob_end_clean(); 
                return true;

            }catch(Exception $e){
                throw $e;
            }
        }
        
        public function import_widgets( $widget_file ){
            try{
                $data_file = $widget_file; 
                $widgets_json = wp_remote_get( $data_file );
                $widget_data = $widgets_json['body'];
                $import_widgets = $this->import_widget_data( $widget_data );
            }catch(Exception $e){
                throw $e;
            }
        }

        public function import_revsliders( $data_dir ){
            global $wpdb;

            if( !class_exists('ZipArchive') ){
                return;
            }
	        // Import Revslider
	        if( class_exists('UniteFunctionsRev') ) {

		        $rev_directory = $data_dir;

                if( !file_exists($rev_directory) ){
                    return;
                }

                $rev_files = array();

		        foreach( glob( $rev_directory . '*.zip' ) as $filename ) {
			        
                    $filename = $rev_directory . basename($filename);

                    if( file_exists($filename) ) {
			            $rev_files[] = $filename;
                    }
		        }

		        foreach( $rev_files as $rev_file ) { 
			
				        $filepath = $rev_file;

                        try{

				            $zip = new ZipArchive;
				            $importZip = $zip->open($filepath, ZIPARCHIVE::CREATE);

				            if($importZip === true){

					            //check if files all exist in zip
					            $slider_export = $zip->getStream('slider_export.txt');
					            $custom_animations = $zip->getStream('custom_animations.txt');
					            $dynamic_captions = $zip->getStream('dynamic-captions.css');
					            $static_captions = $zip->getStream('static-captions.css');

					            //if(!$slider_export)  UniteFunctionsRev::throwError("slider_export.txt does not exist!");
					            //if(!$custom_animations)  UniteFunctionsRev::throwError("custom_animations.txt does not exist!");
					            //if(!$dynamic_captions) UniteFunctionsRev::throwError("dynamic-captions.css does not exist!");
					            //if(!$static_captions)  UniteFunctionsRev::throwError("static-captions.css does not exist!");

					            $content = '';
					            $animations = '';
					            $dynamic = '';
					            $static = '';

					            while (!feof($slider_export)) $content .= fread($slider_export, 1024);
					            if($custom_animations){ while (!feof($custom_animations)) $animations .= fread($custom_animations, 1024); }
					            if($dynamic_captions){ while (!feof($dynamic_captions)) $dynamic .= fread($dynamic_captions, 1024); }
					            if($static_captions){ while (!feof($static_captions)) $static .= fread($static_captions, 1024); }

					            fclose($slider_export);
					            if($custom_animations){ fclose($custom_animations); }
					            if($dynamic_captions){ fclose($dynamic_captions); }
					            if($static_captions){ fclose($static_captions); }

					            //check for images!

				            }else{ //check if fallback
					            //get content array
					            $content = @file_get_contents($filepath);
				            }

				            if($importZip === true){ //we have a zip
					            $db = new UniteDBRev();

					            //update/insert custom animations
					            $animations = @unserialize($animations);
					            if(!empty($animations)){
						            foreach($animations as $key => $animation){ //$animation['id'], $animation['handle'], $animation['params']
							            $exist = $db->fetch(GlobalsRevSlider::$table_layer_anims, "handle = '".$animation['handle']."'");
							            if(!empty($exist)){ //update the animation, get the ID
								            if($updateAnim == "true"){ //overwrite animation if exists
									            $arrUpdate = array();
									            $arrUpdate['params'] = stripslashes(json_encode(str_replace("'", '"', $animation['params'])));
									            $db->update(GlobalsRevSlider::$table_layer_anims, $arrUpdate, array('handle' => $animation['handle']));

									            $id = $exist['0']['id'];
								            }else{ //insert with new handle
									            $arrInsert = array();
									            $arrInsert["handle"] = 'copy_'.$animation['handle'];
									            $arrInsert["params"] = stripslashes(json_encode(str_replace("'", '"', $animation['params'])));

									            $id = $db->insert(GlobalsRevSlider::$table_layer_anims, $arrInsert);
								            }
							            }else{ //insert the animation, get the ID
								            $arrInsert = array();
								            $arrInsert["handle"] = $animation['handle'];
								            $arrInsert["params"] = stripslashes(json_encode(str_replace("'", '"', $animation['params'])));

								            $id = $db->insert(GlobalsRevSlider::$table_layer_anims, $arrInsert);
							            }

							            //and set the current customin-oldID and customout-oldID in slider params to new ID from $id
							            $content = str_replace(array('customin-'.$animation['id'], 'customout-'.$animation['id']), array('customin-'.$id, 'customout-'.$id), $content);
						            }
						            //dmp(__("animations imported!",REVSLIDER_TEXTDOMAIN));
					            }else{
						            //dmp(__("no custom animations found, if slider uses custom animations, the provided export may be broken...",REVSLIDER_TEXTDOMAIN));
					            }

					            //overwrite/append static-captions.css
					            if(!empty($static)){
						            if(isset( $updateStatic ) && $updateStatic == "true"){ //overwrite file
							            RevOperations::updateStaticCss($static);
						            }else{ //append
							            $static_cur = RevOperations::getStaticCss();
							            $static = $static_cur."\n".$static;
							            RevOperations::updateStaticCss($static);
						            }
					            }
					            //overwrite/create dynamic-captions.css
					            //parse css to classes
					            $dynamicCss = UniteCssParserRev::parseCssToArray($dynamic);

					            if(is_array($dynamicCss) && $dynamicCss !== false && count($dynamicCss) > 0){
						            foreach($dynamicCss as $class => $styles){
							            //check if static style or dynamic style
							            $class = trim($class);

							            if((strpos($class, ':hover') === false && strpos($class, ':') !== false) || //before, after
								            strpos($class," ") !== false || // .tp-caption.imageclass img or .tp-caption .imageclass or .tp-caption.imageclass .img
								            strpos($class,".tp-caption") === false || // everything that is not tp-caption
								            (strpos($class,".") === false || strpos($class,"#") !== false) || // no class -> #ID or img
								            strpos($class,">") !== false){ //.tp-caption>.imageclass or .tp-caption.imageclass>img or .tp-caption.imageclass .img
								            continue;
							            }

							            //is a dynamic style
							            if(strpos($class, ':hover') !== false){
								            $class = trim(str_replace(':hover', '', $class));
								            $arrInsert = array();
								            $arrInsert["hover"] = json_encode($styles);
								            $arrInsert["settings"] = json_encode(array('hover' => 'true'));
							            }else{
								            $arrInsert = array();
								            $arrInsert["params"] = json_encode($styles);
							            }
							            //check if class exists
							            $result = $db->fetch(GlobalsRevSlider::$table_css, "handle = '".$class."'");

							            if(!empty($result)){ //update
								            $db->update(GlobalsRevSlider::$table_css, $arrInsert, array('handle' => $class));
							            }else{ //insert
								            $arrInsert["handle"] = $class;
								            $db->insert(GlobalsRevSlider::$table_css, $arrInsert);
							            }
						            }
						            //dmp(__("dynamic styles imported!",REVSLIDER_TEXTDOMAIN));
					            }else{
						            //dmp(__("no dynamic styles found, if slider uses dynamic styles, the provided export may be broken...",REVSLIDER_TEXTDOMAIN));
					            }
				            }

                            //Deprecated
				            //$content = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $content); //clear errors in string

                            $content = preg_replace_callback('!s:(\d+):"(.*?)";!', array($this, 'revslider_clear_error'), $content); //clear errors in string

				            $arrSlider = @unserialize($content);
				            $sliderParams = $arrSlider["params"];

				            if(isset($sliderParams["background_image"]))
					            $sliderParams["background_image"] = UniteFunctionsWPRev::getImageUrlFromPath($sliderParams["background_image"]);

				            $json_params = json_encode($sliderParams);

				            //new slider
				            $arrInsert = array();
				            $arrInsert["params"] = $json_params;
				            $arrInsert["title"] = UniteFunctionsRev::getVal($sliderParams, "title","Slider1");
				            $arrInsert["alias"] = UniteFunctionsRev::getVal($sliderParams, "alias","slider1");
				            $sliderID = $wpdb->insert(GlobalsRevSlider::$table_sliders,$arrInsert);
				            $sliderID = $wpdb->insert_id;

				            //-------- Slides Handle -----------

				            //create all slides
				            $arrSlides = $arrSlider["slides"];

				            $alreadyImported = array();

				            foreach($arrSlides as $slide){

					            $params = $slide["params"];
					            $layers = $slide["layers"];

					            //convert params images:
					            if(isset($params["image"])){
						            //import if exists in zip folder

						            if(trim($params["image"]) !== ''){
							            if($importZip === true){ //we have a zip, check if exists
							            	
								            	$image = $zip->getStream('images/'.$params["image"]);							        	
									            if(!$image){
										            //echo $params["image"].' not found!<br>';
									            }else{
										            if(!isset($alreadyImported['zip://'.$filepath."#".'images/'.$params["image"]])){
										            	try{
												            $importImage = UniteFunctionsWPRev::import_media('zip://'.$filepath."#".'images/'.$params["image"], $sliderParams["alias"].'/');

												            if($importImage !== false){
													            $alreadyImported['zip://'.$filepath."#".'images/'.$params["image"]] = $importImage['path'];

													            $params["image"] = $importImage['path'];
												            }
											            }catch(Exception $e){

											        	}
										            }else{
											            $params["image"] = $alreadyImported['zip://'.$filepath."#".'images/'.$params["image"]];
										            }
									            }
								        	
							            }
						            }
						            $params["image"] = UniteFunctionsWPRev::getImageUrlFromPath($params["image"]);
					            }


					            //convert layers images:
					            foreach($layers as $key=>$layer){
						            if(isset($layer["image_url"])){
							            //import if exists in zip folder
							            if(trim($layer["image_url"]) !== ''){
								            if($importZip === true){ //we have a zip, check if exists
									            $image_url = $zip->getStream('images/'.$layer["image_url"]);
									            if(!$image_url){
										            //echo esc_url( $layer["image_url"] ).' not found!<br>';
									            }else{
										            if(!isset($alreadyImported['zip://'.$filepath."#".'images/'.$layer["image_url"]])){
											            $importImage = UniteFunctionsWPRev::import_media('zip://'.$filepath."#".'images/'.$layer["image_url"], $sliderParams["alias"].'/');

											            if($importImage !== false){
												            $alreadyImported['zip://'.$filepath."#".'images/'.$layer["image_url"]] = $importImage['path'];

												            $layer["image_url"] = $importImage['path'];
											            }
										            }else{
											            $layer["image_url"] = $alreadyImported['zip://'.$filepath."#".'images/'.$layer["image_url"]];
										            }
									            }
								            }
							            }
							            $layer["image_url"] = UniteFunctionsWPRev::getImageUrlFromPath($layer["image_url"]);
							            $layers[$key] = $layer;
						            }
					            }

					            //create new slide
					            $arrCreate = array();
					            $arrCreate["slider_id"] = $sliderID;
					            $arrCreate["slide_order"] = $slide["slide_order"];
					            $arrCreate["layers"] = json_encode($layers);
					            $arrCreate["params"] = json_encode($params);


					            $wpdb->insert(GlobalsRevSlider::$table_slides,$arrCreate);
				            //}
			            }

                        }catch(Exception $e){
                            throw $e;
                        }
		        }
	        }

        }

        function revslider_clear_error($matches) { 
            $string = $matches[2]; 
            $length = strlen($string); 
            return 's:' . $length . ':"' . $string . '";'; 
        }

        function import_widget_data( $widget_data ) {

            $json_data = $widget_data;
            $json_data = json_decode( $json_data, true );

            $sidebar_data = $json_data[0];
            $widget_data = $json_data[1];

            foreach ( $widget_data as $widget_data_title => $widget_data_value ) {
                $widgets[ $widget_data_title ] = '';
                foreach( $widget_data_value as $widget_data_key => $widget_data_array ) {
                    if( is_int( $widget_data_key ) ) {
                        $widgets[$widget_data_title][$widget_data_key] = 'on';
                    }
                }
            }
            unset($widgets[""]);

            foreach ( $sidebar_data as $title => $sidebar ) {
                $count = count( $sidebar );
                for ( $i = 0; $i < $count; $i++ ) {
                    $widget = array( );
                    $widget['type'] = trim( substr( $sidebar[$i], 0, strrpos( $sidebar[$i], '-' ) ) );
                    $widget['type-index'] = trim( substr( $sidebar[$i], strrpos( $sidebar[$i], '-' ) + 1 ) );
                    if ( !isset( $widgets[$widget['type']][$widget['type-index']] ) ) {
                        unset( $sidebar_data[$title][$i] );
                    }
                }
                $sidebar_data[$title] = array_values( $sidebar_data[$title] );
            }

            foreach ( $widgets as $widget_title => $widget_value ) {
                foreach ( $widget_value as $widget_key => $widget_value ) {
                    $widgets[$widget_title][$widget_key] = $widget_data[$widget_title][$widget_key];
                }
            }

            $sidebar_data = array( array_filter( $sidebar_data ), $widgets );

            $this->parse_import_data( $sidebar_data );
        }

        function parse_import_data( $import_array ) {
            global $wp_registered_sidebars;
            $sidebars_data = $import_array[0];
            $widget_data = $import_array[1];
            $current_sidebars = get_option( 'sidebars_widgets' );
            $new_widgets = array( );

            foreach ( $sidebars_data as $import_sidebar => $import_widgets ) :

                foreach ( $import_widgets as $import_widget ) :
                    //if the sidebar exists
                    if ( isset( $wp_registered_sidebars[$import_sidebar] ) ) :
                        $title = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
                        $index = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
                        $current_widget_data = get_option( 'widget_' . $title );
                        $new_widget_name = $this->get_new_widget_name( $title, $index );
                        $new_index = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );

                        if ( !empty( $new_widgets[ $title ] ) && is_array( $new_widgets[$title] ) ) {
                            while ( array_key_exists( $new_index, $new_widgets[$title] ) ) {
                                $new_index++;
                            }
                        }
                        $current_sidebars[$import_sidebar][] = $title . '-' . $new_index;
                        if ( array_key_exists( $title, $new_widgets ) ) {
                            $new_widgets[$title][$new_index] = $widget_data[$title][$index];
                            $multiwidget = $new_widgets[$title]['_multiwidget'];
                            unset( $new_widgets[$title]['_multiwidget'] );
                            $new_widgets[$title]['_multiwidget'] = $multiwidget;
                        } else {
                            $current_widget_data[$new_index] = $widget_data[$title][$index];
                            $current_multiwidget = isset($current_widget_data['_multiwidget']) ? $current_widget_data['_multiwidget'] : false;
                            $new_multiwidget = isset($widget_data[$title]['_multiwidget']) ? $widget_data[$title]['_multiwidget'] : false;
                            $multiwidget = ($current_multiwidget != $new_multiwidget) ? $current_multiwidget : 1;
                            unset( $current_widget_data['_multiwidget'] );
                            $current_widget_data['_multiwidget'] = $multiwidget;
                            $new_widgets[$title] = $current_widget_data;
                        }

                    endif;
                endforeach;
            endforeach;

            if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
                update_option( 'sidebars_widgets', $current_sidebars );

                foreach ( $new_widgets as $title => $content )
                    update_option( 'widget_' . $title, $content );

                return true;
            }

            return false;
        }

        function get_new_widget_name( $widget_name, $widget_index ) {
            $current_sidebars = get_option( 'sidebars_widgets' );
            $all_widget_array = array( );
            foreach ( $current_sidebars as $sidebar => $widgets ) {
                if ( !empty( $widgets ) && is_array( $widgets ) && $sidebar != 'wp_inactive_widgets' ) {
                    foreach ( $widgets as $widget ) {
                        $all_widget_array[] = $widget;
                    }
                }
            }
            while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
                $widget_index++;
            }
            $new_widget_name = $widget_name . '-' . $widget_index;
            return $new_widget_name;
        }

    }

}