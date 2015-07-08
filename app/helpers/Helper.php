<?php


class Helper {
	
	// ------------------------------------------------------------------------
	public static function dump($obj) {

		echo '<pre>';
		print_r($obj);
		echo '</pre>';
		dd('');
	}
	// ------------------------------------------------------------------------
    public static function svg($file) {
    	if(File::exists($file)) {
        $str = File::get($file);
        return $str;
    	}
    	else {
    		return 'Missing Image';
    	}
    }

	// ------------------------------------------------------------------------
    public static function getPublishTyped() {
      $types = array(
          ['id'=>1, 'title'=>'Draft'],
          ['id'=>2, 'title'=>'Publish'],
        );
      return array_to_object($types);
    }

    // ------------------------------------------------------------------------
    public static function URLencodeArray($params)
    {
      $paramsJoined = array();

      foreach($params as $param => $value) {
         $paramsJoined[] = "$param=$value";
      }

      return implode('&', $paramsJoined);
    }

}
