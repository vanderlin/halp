<?php 


require('AutoLinking.php');



// ------------------------------------------------------------------------
function is_false($var) {
  return $var === false || $var === 'false' || $var === 'FALSE' || $var === 0 || $var === '0';  
}

function is_true($var) {
  return $var === true || $var === 'true' || $var === 'TRUE' || $var === 1 || $var === '1';  
}

function bool_val($val) {
  return ( is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val );
}

function pdf_asset($file) {
  return URL::to('assets/content/pdf/'.$file);
}
function common_asset($file, $relative=false) {
  return $relative ? 'assets/content/common/'.$file : asset('assets/content/common/'.$file);
}

function img($file, $relative=false) {
  return $relative ? 'assets/img/'.$file : asset('assets/img/'.$file);
}

function mobile_asset($file, $relative=false) {
  return $relative ? 'assets/content/mobile/'.$file : asset('assets/content/mobile/'.$file);
}

function js($file) {
    return asset('assets/js/'.$file);
}

function bower($file) {
  return asset('bower_components/'.$file);
}

function echo_form_error($name, $errors) {
  if ($errors->has($name)) return 'has-error';
}

function is_active($active_link, $link) {
  return (isset($active_link) && $active_link==$link) ? 'active':'';
}

function isMobile()
{
  return Agent::isMobile() || Agent::isTablet();
}

function detect_links($str) {
  
  $str = autolink($str);
  $str = autolink_email($str);
  return $str;;
}

function array_random_item($arr) {
  return $arr[array_rand($arr)];
}

function strbool($value) {
    return $value ? 'true' : 'false';
}

function array_all_key_exists($keys, $arr)
{
  $pass = [];
  foreach ($keys as $name) {
    foreach ($arr as $key => $value) {
      if($key === $name) {
        array_push($pass, $key);
      }
    }
  }
  return count($pass) === count($keys);
}

function object_to_array($obj){
    $arrObj = is_object($obj) ? get_object_vars($obj) : $obj;
    foreach ($arrObj as $key => $val) {
            $val = (is_array($val) || is_object($val)) ? object_to_array($val) : $val;
            $arr[$key] = $val;
    }
    return $arr;
}

function array_to_object($array) {
    $obj = new stdClass();
    foreach ($array as $key => $val) {
        $obj->$key = is_array($val) ? array_to_object($val) : $val;
    }
    return $obj;
}
function get_site_map() {
    $map = array(
        ['name'=>'Home', 'url'=>URL::to('/')],
        ['name'=>'Tour', 'url'=>URL::to('welcome')],
        ['name'=>'About Us', 'url'=>URL::to('about')],
        ['name'=>'Become a spotter', 'url'=>URL::to('become-a-spotter')],
        ['name'=>'FAQs', 'url'=>URL::to('faqs')],
    );
    return array_to_object($map);
}

function str_until($str, $until)
{
  $p = strpos($str, ".");
  return $p === false ? $str : substr($str, 0, $p);
}

function ends_in_number($str) {
  $length=strlen($str)-1;
  return is_numeric($str[$length]);
}

function time_ago($time, Carbon $other = null, $absolute = false)
{
  $isNow = $other === null;

  if ($isNow) {
      $other = Carbon\Carbon::now($time->tz);
  }

  $isFuture = $time->gt($other);

  $delta = $other->diffInSeconds($time);

  // a little weeks per month, 365 days per year... good enough!!
  $divs = array(
      's' => Carbon\Carbon::SECONDS_PER_MINUTE,
      'm' => Carbon\Carbon::MINUTES_PER_HOUR,
      'h' => Carbon\Carbon::HOURS_PER_DAY,
      'd' => Carbon\Carbon::DAYS_PER_WEEK,
      'w' => Carbon\Carbon::WEEKS_PER_YEAR,
      // 'w' => 30 / Carbon\Carbon::DAYS_PER_WEEK,
      // 'm' => Carbon\Carbon::MONTHS_PER_YEAR
  );

  $unit = 'y';

  foreach ($divs as $divUnit => $divValue) {
      
      if ($delta < $divValue) {

          $unit = $divUnit;
          break;
      }

      $delta = $delta / $divValue;
  }

  $delta = (int) $delta;

  if ($delta == 0) {
      $delta = 1;
  }

  $txt = $delta . $unit;

  return $txt;//.'--'.$time->diffForHumans();
 
}


function get_image_info($sURL) {
  try {
    $return = array();
    $vData = "";
    $timer = microtime(true);
    $hSock = fopen($sURL, 'rb');
    $length = 10240;

    if ($hSock) {
      while(!feof($hSock)) {
        $vData = fread($hSock, $length);
        break;
      }
      fclose($hSock);

      //get headers
      if(!empty($http_response_header)){
        $headers = array();
        foreach($http_response_header as $line){
          if(($pos = strpos($line, ':')) !== false){
            $headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos+1));
          }
        }
      }
      

      if(isset($headers['content-length']) && $headers['content-length']){
          $return['size'] = $headers['content-length'];
      }
      
      if (strpos(' ' . $vData, 'JFIF')>0) {
        $vData = substr($vData, 0, $length);
        $asResult = unpack('H*',$vData);
        $sBytes = $asResult[1];
        $width = 0;
        $height = 0;
        $hex_width = '';
        $hex_height = '';
        if (strstr($sBytes, 'ffc2')) {
          $hex_height = substr($sBytes, strpos($sBytes, 'ffc2') + 10, 4);
          $hex_width = substr($sBytes, strpos($sBytes, 'ffc2') + 14, 4);
        } else {
          $hex_height = substr($sBytes, strpos($sBytes, 'ffc0') + 10, 4);
          $hex_width = substr($sBytes, strpos($sBytes, 'ffc0') + 14, 4);
        }
        $width = hexdec($hex_width);
        $height = hexdec($hex_height);
        $return += array('width' => $width, 'height' => $height);
      } elseif (strpos(' ' . $vData, 'GIF')>0) {
        $vData = substr($vData, 0, $length);
        $asResult = unpack('h*',$vData);
        $sBytes = $asResult[1];
        $sBytesH = substr($sBytes, 16, 4);
        $height = hexdec(strrev($sBytesH));
        $sBytesW = substr($sBytes, 12, 4);
        $width = hexdec(strrev($sBytesW));
        $return += array('width' => $width, 'height' => $height);
      } elseif (strpos(' ' . $vData, 'PNG')>0) {
        $vData = substr($vData, 0, $length);
        $vDataH = substr($vData, 22, 4);
        $asResult = unpack('n',$vDataH);
        $height = $asResult[1];
        $vDataW = substr($vData, 18, 4);
        $asResult = unpack('n',$vDataW);
        $width = $asResult[1];
        $return += array('width' => $width, 'height' => $height);
      }

      if(!empty($return)){
        if($return['width'] > 0 && $return['height'] > 0){
          $return['space'] = $return['width'] * $return['height'];
        }
        $return['time'] = microtime(true)-$timer;
        $return['url'] = $sURL;
        $return['ratio_h'] = $return['height'] / $return['width'];
        $return['ratio_v'] = $return['width'] / $return['height'];
      }

      return $return;

    }
  } catch (Exception $e) {}
  return $return;
}

function get_remote_file($url) {
    

  $ch = curl_init();  
  $userAgents=array(
    "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
    "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
    "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)",
    "Opera/9.20 (Windows NT 6.0; U; en)",
    "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 8.50",
    "Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows NT 5.1) Opera 7.02 [en]",
    "Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; fr; rv:1.7) Gecko/20040624 Firefox/0.9",
    "Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/48 (like Gecko) Safari/48"       
  );
  $random = rand(0,count($userAgents)-1);

  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch,CURLOPT_HEADER, false); 
  curl_setopt($ch,CURLOPT_USERAGENT, $userAgents[$random]);
  $output = curl_exec($ch);
  
  $redirectURL = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
  if($redirectURL !== false) {
    return $redirectURL;
      curl_setopt($ch,CURLOPT_URL, $redirectURL);
      $output = curl_exec($ch);
  }

  if($output === false) {
    return false;
  }
  curl_close($ch);
  return $output;
}

// ------------------------------------------------------------------------
function get_more_locations_text($locations) {

  $moreHTML = "";
  $more = [];
  $moreHTML = '<ul class="list-unstyled popover-list">';
  foreach ($locations as $location) {
    $image; $name; $url; $locationName;

    if(get_class($location) == 'Activity' && $location->spot) {
      
      $name = $location->spot->name;
      $url = $location->spot->getURL();
      $image = $location->spot->getThumbnail();
      $locationName = $location->spot->location->getLocationName();
    }
    else {
      $name = $location->hasSpot() ? $location->spot->name : $location->name;
      $image = $location->hasSpot() ? $location->spot->getThumbnail() : $location->getThumbnail();
      $url = $location->getURL();
      $locationName = $location->getLocationName();
    }
    if($name) {
      $moreHTML .= 
        '<li class="media">
          <div class="media-left media-middle"><a href="'.$url.'"><img class="img-circle" src="'.$image->url('s30').'"></a></div>
          <div class="media-body">
            <a href="'.$url.'">'.Str::limit($name, 24).'</a>
            <div class="location"><small class="text-muted">'.$locationName.'</small></div>
          </div>
        </li>';
    }
  }
  $moreHTML .= '</ul>';
  $moreHTML = urlencode($moreHTML);

  return array( 'count'=>$locations->count(),
                'html'=>$moreHTML);
}





