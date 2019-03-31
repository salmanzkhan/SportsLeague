<?php
echo "<link rel='stylesheet' type='text/css' href='css/table.css' />";
class FN {

  /*  Function to Build the table*/
  public static function build_table( $db_array ) {
	
  	$display = "<table id='customers' border='1'>\n<tr>\n";
  	foreach ( $db_array[0] as $column => $field ) {
  		$display .= "<th>$column</th>\n";
  	}
  	$display .= "</tr>\n";
  	
  	foreach ( $db_array as $record ) {
  		$display .= "<tr>\n";
  		foreach ( $record as $field ) {
  			$display .= "<td>$field</td>\n";
  		}
  		$display .= "</tr>\n";
  	}
  	
  	$display .= "</table>\n";
  	
  	return $display;
  }
    
   /* Funtion to log the error or the messages*/
     public static function errorLog($msg){
         
       //  error_log(" [ ".Date('Y-m-d H:i:s')."]Error:".$msg."\n", 3, '/error/myErrors.txt');
          error_log(" [ ".Date('Y-m-d H:i:s')."]Error:".$msg."\n", 3, 'myErrors.txt');
     }
    
 /*   functio to sanitize the input*/
    public static function is_valid($input) {
                  $input = trim($input);
                  $input = stripslashes($input);
                  $input = htmlspecialchars($input);
                  return $input;
                }

}

?>