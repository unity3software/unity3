<?php

class Unity3_Core_Utility {

    //------------------------------------------------------------------------
    //CORE FUNCTIONS

    function Json($v, $err = false)
    {
	$a = array('jsonrpc' => '2.0');
	$a[$err? "error" : "result"] = $v;
	return json_encode($a);
    }
    
    
    function WaitContainerField($id, $echo=true) {
        $result = 
        '<div id="'. ($id ? $id : 'unity3-wait') .'" style="display: none;">
            <div style="background-color: #aaa; position: absolute; left: 0; right:0; top:0; bottom: 0; opacity: .7;"></div>
            <img src="'. (WP_PLUGIN_URL . '/unity3/includes/images/wait.gif' ) .'" style="width: 16px; height: 16px; position: absolute; left: 48%; top: 48%;"/>
        </div>';
       if ($echo === true)
           echo $result;
       else
           return $result;
           
    }
    
    
        /**
     * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
     * Gets the specified tab url
     * @param string $tab <p>
     * Specifies the tab
     * @return string returns the the specified tab url
     */
    function GetTabUrl($tab = '') {
        return $tab == '' ? null : admin_url('admin.php?page=unity3&tab='.$tab);
    }
    
    
    /**
     * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
     * Gets a value indicating if the current Wordpress page is an edit page
     * @param string $arg <p>
     * The optional argument to check a specific edit page
     * @return bool returns a true/false value
     */
    function IsEditPage($arg = '') {
        global $pagenow;
        if (!is_admin())
            return false;

        if ($arg == 'edit')
            return in_array($pagenow, array('post.php'));
        elseif ($arg == 'new')
            return in_array($pagenow, array('post-new.php'));
        else
            return in_array($pagenow, array('post.php', 'post-new.php'));
    }

    /**
     * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
     * Attempts to get the post type of the current admin edit page
     * @return bool if valid, returns the name of the post_type. If invalid, returns null
     */
    function AdminGetPostType() {
        if (!is_admin())
            return null;

        global $pagenow;

        if ($pagenow == 'post.php')
            return !isset($_GET['post']) ? null : get_post_type($_GET['post']);
        else if ($pagenow == 'post-new.php')
            return !isset($_GET['post_type']) ? 'post' : $_GET['post_type'];
        else
            return null;
    }

    
    function GetPostTaxAndTerms($post_id) {
        // get post type taxonomies
        $taxonomies = get_object_taxonomies( get_post_type($post_id), 'objects' );
        $arr_post_tax = array();
        foreach ( $taxonomies as $slug => $v){
          // get the terms related to post
          $terms = get_the_terms( $post_id, $slug );
          if (!empty($terms)) {
            $arrTerms = array(); 
            foreach($terms as $term) {
                $arrTerms[$term->slug] = $term;
            }
            $arr_post_tax[$slug] = $arrTerms;
          }
        }

        return $arr_post_tax;
    }
    
    
    
    /**
     * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
     * Performs an exact comparison of two arrays
     * @param array $a <p>
     * The array with master values to check.
     * </p>
     * @param array $b <p>
     * An array to compare values against.
     * </p>
     * @return bool returns a value indicating if both arrays are equal
     */
    function ArrayExactCompare($a, $b) {
        $a_count = count($a);
        $b_count = count($b);
        return ($a_count == $b_count) && $a_count == count(array_intersect_assoc($a, $b)) ? true : false;
    }

    function HasShortcode($content, $shortcode = '') {
        return stripos($content, '[' . $shortcode) !== false;
    }

    function Enqueue($id) {
        switch ($id) {
            case 'allow-tab' :
                wp_enqueue_script('unity3-allow-tab', WP_PLUGIN_URL . '/unity3/includes/js/jquery.allowtabchar.js');
                break;
            case 'wait-spinner' :
                wp_enqueue_script('unity3-wait-spinner', WP_PLUGIN_URL . '/unity3/includes/js/jquery.waitspinner.js', array('jquery'));
                wp_localize_script('unity3-wait-spinner', 'waitspinnerdata', array('url' => WP_PLUGIN_URL . '/unity3/includes/images/wait-spinner.gif'));
                break;
        }    
    }
    
    function GetJsonMessage() {
        $json_errors = array(
            JSON_ERROR_NONE      => 'No error has occurred',
            JSON_ERROR_DEPTH     => 'The maximum stack depth has been exceeded',
            JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
            JSON_ERROR_SYNTAX    => 'Syntax error',
            JSON_ERROR_UTF8      => 'UTF8 encoding error'
        );
        
        $index = json_last_error();
        return $index == 0 ? "" : $json_errors[$index];
    }
    
    function GetOptions($args) {
        //$args = array('option_group' => 'unity3_code', 'option_name' => array('option1', 'option2'), 'limit' => -1, 'orderby' => 'ID', 'order' => 'DESC'))
        if (!isset($args))
            return null;
        
        $option_group = $args['option_group'];
        $ID = $args['ID'];
        $order_by = isset($args['order_by']) ? $args['order_by'] : false;
        $order = isset($args['order']) ? $args['order'] : false;

        $isIDSearch = isset($ID);
        $isGroupSearch = isset($option_group);
        
        if (!$isGroupSearch && !$isIDSearch)
            throw new Exception('You must specify specify either of the two parameters:  option_group OR ID');
        else if ($isGroupSearch && $isIDSearch)
            throw new Exception('You cannot specify both ID and option_group in the same query!');
            

        $arrValues = $isGroupSearch ? $args['option_name'] : $args['ID'];
        if (!is_array($arrValues))
            $arrValues = array($arrValues);
        if (empty($arrValues[0]))
            $arrValues = array();//a zero count array
        
        $isListAll = (count($arrValues) == 0);
        if ($isListAll && $isGroupSearch)
            $arrValues = array($option_group);
        
        $strInnerTable = '';
        $index = 0;
        if (!($isIDSearch && $isListAll)) {
            foreach ($arrValues as $k) {
                $field = $isIDSearch ? $k : "'$k'";//wrap with quotes if we are specifying option_name/string fields
                $strInnerTable .= ($index++ == 0 ? "select $field as col" : " union all select $field");
            }
        }

        global $wpdb;
        $options_table_name = $wpdb->prefix . Unity3::OPTIONS_TABLE;
        $column = $isIDSearch ? 'ID' : ($isListAll ? 'option_group' : 'option_name');        
        $myrows = $wpdb->get_results('select ID, option_group, option_name, option_value from '. $options_table_name . ' as tbl'. 
                    (empty($strInnerTable) ? '' : " inner join ($strInnerTable) as x on tbl.$column = x.col").
                        ($isListAll ? '' : " AND tbl.option_group = '$option_group'").
                        (!$order_by ? '' : " ORDER BY $order_by").
                        (!$order ?    '' : " $order"));
        if (count($myrows) != 0) {
            foreach ($myrows as $k => $v) {
                $v->option_value = maybe_unserialize($v->option_value);
            }
        }
        
        return $myrows;
    }

    function SetOption($args) {
        $action = strtoupper(is_string($args['action']) ? $args['action'] : 'SET');
        if ($action != 'SET' && $action != 'ADD') 
            $action = 'SET';
        
        $ID = $args['ID'];
        $option_group = $args['option_group'];
        $option_name = $args['option_name'];
        $option_value = $args['option_value'];

        if ( is_object($option_value))
		$option_value = clone $option_value;
        
        $option_value = maybe_serialize( $option_value );
        
        
        $arrIsSet = array(
            'ID'           => isset($ID),
            'option_group' => isset($option_group),
            'option_name'  => isset($option_name),
            'option_value' => isset($option_value)
        );

        //if the user has specified an ID, they must supply at least one of the columns listed below...
        if ($arrIsSet['ID'] && !($arrIsSet['option_group'] || $arrIsSet['option_name'] || $arrIsSet['option_value'])) {
            throw new Exception('You must specify one or more of the following columns: option_group, option_name, option_value!');
        } else if
        //if the user is not using an ID to set something, they must use a key pair option_group/option_name...
        (!$arrIsSet['ID'] && !($arrIsSet['option_group'] && $arrIsSet['option_name'] && $arrIsSet['option_value'])) {
            throw new Exception('You must specify: option_group, option_name, option_value!');
        }
        
        
        global $wpdb;
        $options_table_name = $wpdb->prefix . Unity3::OPTIONS_TABLE;
        
        if ($arrIsSet['ID'])
            $action = 'SET';
        else if ($action == 'SET') {
            $result = $wpdb->get_var($wpdb->prepare(
                "
                SELECT ID FROM $options_table_name
                    WHERE option_group=%s AND option_name=%s LIMIT 1;
                ",
                $option_group, $option_name
            ));
            $action = isset($result) ? 'SET' : 'ADD';   
        }
        
        //----------  DO ACTION ----------------
        
        if ($action == 'ADD') { 
            
            $wpdb->insert($options_table_name, 
                array( 
                    'option_group' => $option_group, 
                    'option_name' => $option_name,
                    'option_value' => $option_value
                ), 
                array( '%s', '%s', '%s' ) 
            );
            $ID = $wpdb->insert_id;
            
        } else { //$action == 'SET'

            $where_clause;
            $where_format;
            $columns = array(); 
            if ($arrIsSet['ID']) {
                $where_clause = array('ID' => $ID);
                $where_format = "%d";
                $arr = array(
                    'option_group' => $option_group,
                    'option_name'  => $option_name,
                    'option_value' => $option_value
                );
                foreach ($arr as $k => $v) {
                    if ($arrIsSet[$k]) {
                        $columns[$k] = $v;
                    }
                }
                
            }else {
               $where_clause = array('option_group' => $option_group, 'option_name' => $option_name); //"option_group='$option_group' AND option_name='$option_name'";
               $where_format = "%s";
               $columns = array('option_value' => $option_value);
               
            }

            $wpdb->update( 
                $options_table_name, 
                $columns, 
                $where_clause, //WHERE
                "%s", //column data type is always string
                $where_format 
            );   
        }
        
        return $ID;
        
    }    
    
    function RemoveOption($ID) {
        global $wpdb;
        $options_table_name = $wpdb->prefix . Unity3::OPTIONS_TABLE;
        $wpdb->delete( $options_table_name, array( 'ID' => $ID ), array( '%d' ) );
        return $wpdb->last_error; //returns empty string if no errors
    }
    
    
    //END CORE FUNCTIONS
    //------------------------------------------------------------------------
    
}

?>
