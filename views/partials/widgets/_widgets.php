<?php
  // Without this WP_List_Table creates a warning...
  global $hook_suffix;
  
  class Widgets_List_Table extends WP_List_Table {
    function __construct() {
      parent::__construct(
        array(
          "singular" => "widget",
          "plural"   => "widgets",
          "ajax"     => false
        )
      );
    }
  
    function column_default($item, $column_name) {
      switch ($column_name) {
        //case "identifier":
        //  return $item[$column_name];
        //case "created_at":
        //  $timestamp = strtotime($item[$column_name]);
        //  $date = date("Y-m-d H:i:s", $timestamp);
        //  $format = get_option("date_format")." ".get_option("time_format");
        //  $output = get_date_from_gmt($date, $format);
        //  return $output;
        case "impressions":
        case "clicks":
          $stats_type = $column_name;
          ob_start();
          include("table/_stats.php");
          $output = ob_get_clean();
          return $output;
        default:
          ob_start();
          include("table/_$column_name.php");
          $output = ob_get_clean();
          return $output;
      }
    }
  
    function column_cb($item) {
      return "<input type='checkbox' name='".$this->_args["singular"]."[]' value='".$item["identifier"]."' />";
    }
  
    function get_columns() {
      $columns = array(
        "cb"          => "<input type='checkbox' />",
        "layout"      => "Layout",
        "impressions" => "Impressions (last 7 days)",
        "clicks"      => "Clicks (last 7 days)",
        "actions"     => "Actions"
      );
      return $columns;
    }
  
    function prepare_items() {
      $columns  = $this->get_columns();
      $hidden   = array();
      $sortable = array();
      $this->_column_headers = array($columns, $hidden, $sortable);
    }
  }
  
  $api = Product_Widgets::get_instance()->api;
  $widgets = $api->get_widgets();
  
  if (empty($widgets)) {
    include("_empty.php");
  } else {
    include("_intro.php");
    $widgets_list_table = new Widgets_List_Table();
    $widgets_list_table->items = $widgets;
    $widgets_list_table->prepare_items();
    $widgets_list_table->display();
  }
?>
