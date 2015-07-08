<?php 


class MapHelper {

  public static function getStaticMapURL($options=array()) {

  $defaults = array(  'width'=>50, 
              'height'=>50, 
              'lat'=>-1, 
              'lng'=>-1, 
              'zoom'=>9,
              'obj'=>null);
      
      $options = array_merge($defaults, is_object($options)?$options->toArray():$options);

      $url = 'https://maps.googleapis.com/maps/api/staticmap?';
      $url .= 'center='.$options['lat'].','.$options['lng'];
      $url .= '&size='.$options['width'].'x'.$options['height'];
      $url .= '&zoom='.$options['zoom'];
      $url .= '&markers='. $options['lat'].','.$options['lng'];
      $url .= '&maptype=roadmap';

      return $url;
    }
}