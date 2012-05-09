<?php
/**
 * $Id$
 *
 * touchdesign module
 *
 * Copyright (c) 2011 touchDesign
 *
 * @category Library
 * @version 0.1
 * @copyright 12.04.2010, touchDesign
 * @author Christin Gruber, <www.touchdesign.de>
 * @link http://www.touchdesign.de
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * Description:
 *
 * touchdesign module library
 *
 * --
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@touchdesign.de so we can send you a copy immediately.
 *
 */

class touchdesign
{

  static function redirect($path=null,$query=null,$scheme=null,$host=null)
  {
    $url="";
    
    if($scheme === null){
      $scheme = (Configuration::get('PS_SSL_ENABLED') == 1 ? 'https://' : 'http://');
    }

    if($host === null){
      $host = $_SERVER['HTTP_HOST'];
    }
    
    if($path === null){
      $path = __PS_BASE_URI__;
    }
    
    die(header('Location: ' . $scheme . $host . $path . ($query !== null ? '?'.$query : '')));
  }

  static function isUTF8($string)
  {
    if (is_array($string)) {
      $enc = implode('', $string);
      return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
    } else {
      return (utf8_encode(utf8_decode($string)) == $string);
    }
  }

  static function convertObjUTF8($obj)
  {
    foreach($obj AS $k => $r){
      if(self::isUTF8($r)){
        $obj->$k = utf8_encode($r);
      }
    }
  
    return $obj;
  }
  
  static function widgetDropdown($name,$items,$selected=null)
  {

    if($selected === null) {
      $selected = Tools::getValue($name) ? Tools::getValue($name,'') : Configuration::get($name);
    }
    
    $result = '<select name="'.$name.'">';
    foreach ($items as $id => $item) {
      $result.= '<option value="'.$id.'" ';
      $result.= $id == $selected ? 'selected="selected"' : '';
      $result.= '>'.$item.'</option>';
    }
    $result.= '</select>';
    
    return $result;
  }
  
  static function widgetDropdownOrderStates($name,$selected=null)
  {
    global $cookie;

    $orderStates = OrderState::getOrderStates(intval($cookie->id_lang));
    
    $items = array();
    foreach ($orderStates as $state) {
      $items[$state['id_order_state']] = $state['name'];
    }
    
    return self::widgetDropdown($name,$items,$selected);
  }
  
  static function getCmsDropdown($name,$selected=null)
  {
    $cmsPages = CMS::listCms(Configuration::get('PS_LANG_DEFAULT'));
    
    $items = array('' => 'None');
    foreach($cmsPages AS $page){
      $items[$page['id_cms']] = $page['meta_title'];
    }
    
    return self::widgetDropdown($name,$items,$selected);
  }

}

?>