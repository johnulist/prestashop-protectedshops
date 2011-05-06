<?php
/**
 * $Id$
 *
 * protectedshops Module
 *
 * Copyright (c) 2011 touchDesign
 *
 * @category Tools
 * @version 0.2
 * @copyright 02.02.2011, touchDesign
 * @author Christoph Gruber, <www.touchdesign.de>
 * @link http://www.touchdesign.de/loesungen/prestashop/protectedshops.htm
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * Description:
 *
 * Protected Shops AGB connect module 
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

class protectedshops extends PaymentModule
{
  private $_html = '';
  private $_documents = array(
    'terms' => 'AGB',
    'privacy' => 'Datenschutz',
    'imprint' => 'Impressum',
    'revocation' => 'Widerruf',
    'shipping' => 'Versandinfo',
    'batterie' => 'Batteriegesetz'
  );
  
  public function __construct()
  {
    $this->name = 'protectedshops';
    if (version_compare(_PS_VERSION_, '1.4.0', '<')){
      $this->tab = 'Tools';
    }else{
      $this->tab = 'tools';
    }
    $this->version = '0.2';
    $this->currencies = true;
    $this->currencies_mode = 'radio';
    parent::__construct();
    $this->page = basename(__FILE__, '.php');
    $this->displayName = $this->l('Protected Shops');
    $this->description = $this->l('Protected Shops AGB connect');
    $this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
  }

  public function install()
  {
    if (
      !parent::install() || 
      !Configuration::updateValue('PROTECTEDSHOPS_USER', '') ||
      !Configuration::updateValue('PROTECTEDSHOPS_PASSWORD', '') ||
      !Configuration::updateValue('PROTECTEDSHOPS_SHOPID', '') ||
      !Configuration::updateValue('PROTECTEDSHOPS_BLOCK_LOGO', 'Y') ||
      !Configuration::updateValue('PROTECTEDSHOPS_FORMAT', 'HTML') ||
      !Configuration::updateValue('PROTECTEDSHOPS_ID_TERMS', '') ||
      !Configuration::updateValue('PROTECTEDSHOPS_ID_PRIVACY', '') ||
      !Configuration::updateValue('PROTECTEDSHOPS_ID_IMPRINT', '') ||
      !Configuration::updateValue('PROTECTEDSHOPS_ID_REVOCATION', '') ||
      !Configuration::updateValue('PROTECTEDSHOPS_ID_SHIPPING', '') ||
      !Configuration::updateValue('PROTECTEDSHOPS_ID_BATTERIE', '') ||
      !$this->registerHook('leftColumn')
    ){
      return false;
    }
    return true;
  }

  public function uninstall()
  {
    if (
      !Configuration::deleteByName('PROTECTEDSHOPS_USER') || 
      !Configuration::deleteByName('PROTECTEDSHOPS_PASSWORD') ||
      !Configuration::deleteByName('PROTECTEDSHOPS_SHOPID') || 
      !Configuration::deleteByName('PROTECTEDSHOPS_BLOCK_LOGO') ||
      !Configuration::deleteByName('PROTECTEDSHOPS_FORMAT') ||
      !Configuration::deleteByName('PROTECTEDSHOPS_ID_TERMS') ||
      !Configuration::deleteByName('PROTECTEDSHOPS_ID_PRIVACY') ||
      !Configuration::deleteByName('PROTECTEDSHOPS_ID_IMPRINT') ||
      !Configuration::deleteByName('PROTECTEDSHOPS_ID_REVOCATION') ||
      !Configuration::deleteByName('PROTECTEDSHOPS_ID_SHIPPING') ||
      !Configuration::deleteByName('PROTECTEDSHOPS_ID_BATTERIE') ||
      !parent::uninstall()
    ){
      return false;
    }
    return true;
  }

  private function _postValidation() 
  {
    if (Tools::getValue('submitUpdate')){
      if (!Tools::getValue('PROTECTEDSHOPS_USER')){
        $this->_postErrors[] = $this->l('Protected Shops "User" is required.');
      }
      if (!Tools::getValue('PROTECTEDSHOPS_PASSWORD')){
        $this->_postErrors[] = $this->l('Protected Shops "Password" is required.');
      }
      if (!Tools::getValue('PROTECTEDSHOPS_SHOPID')){
        $this->_postErrors[] = $this->l('Protected Shops "ShopId" is required.');
      }
    }
  }
  
  public function getContent()
  {
    $this->_html = '<h2>'.$this->displayName.'</h2>';
    if (Tools::isSubmit('submitUpdate')){
      Configuration::updateValue('PROTECTEDSHOPS_USER', Tools::getValue('PROTECTEDSHOPS_USER'));
      Configuration::updateValue('PROTECTEDSHOPS_PASSWORD', Tools::getValue('PROTECTEDSHOPS_PASSWORD'));
      Configuration::updateValue('PROTECTEDSHOPS_SHOPID', Tools::getValue('PROTECTEDSHOPS_SHOPID'));
      Configuration::updateValue('PROTECTEDSHOPS_BLOCK_LOGO', Tools::getValue('PROTECTEDSHOPS_BLOCK_LOGO'));
      Configuration::updateValue('PROTECTEDSHOPS_FORMAT', Tools::getValue('PROTECTEDSHOPS_FORMAT'));
      Configuration::updateValue('PROTECTEDSHOPS_ID_TERMS', Tools::getValue('PROTECTEDSHOPS_ID_TERMS'));
      Configuration::updateValue('PROTECTEDSHOPS_ID_PRIVACY', Tools::getValue('PROTECTEDSHOPS_ID_PRIVACY'));
      Configuration::updateValue('PROTECTEDSHOPS_ID_IMPRINT', Tools::getValue('PROTECTEDSHOPS_ID_IMPRINT'));
      Configuration::updateValue('PROTECTEDSHOPS_ID_REVOCATION', Tools::getValue('PROTECTEDSHOPS_ID_REVOCATION'));
      Configuration::updateValue('PROTECTEDSHOPS_ID_SHIPPING', Tools::getValue('PROTECTEDSHOPS_ID_SHIPPING'));
      Configuration::updateValue('PROTECTEDSHOPS_ID_BATTERIE', Tools::getValue('PROTECTEDSHOPS_ID_BATTERIE'));
    }
    
    foreach($this->_documents AS $key => $document){
      $this->updateDocument($key);
    }
    
    $this->_postValidation();
    if (isset($this->_postErrors) && sizeof($this->_postErrors)){
      foreach ($this->_postErrors AS $err){
        $this->_html .= '<div class="alert error">'. $err .'</div>';
      }
    }elseif(Tools::getValue('submitUpdate') && !isset($this->_postErrors)){
      $this->getSuccessMessage();
    }
    
    return $this->_displayForm();
  }

  public function getSuccessMessage()
  {
    $this->_html.='
    <div class="conf confirm">
      <img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />
      '.$this->l('Settings updated').'
    </div>';
  }

  private function _displayForm()
  {
    require_once 'lib/touchdesign.php';
  
    $this->_html.= '
      <style type="text/css">
        fieldset a {
          color:#0099ff;
          text-decoration:underline;"
        }
        fieldset a:hover {
          color:#000000;
          text-decoration:underline;"
        }
      </style>';

    $this->_html.= '
      <div><img src="'.$this->_path.'logoBig.jpg" alt="logoBig.jpg" alt="logoBig.jpg" title="Protected Shops" /></div>
      <br /><br />';

    $this->_html .= '
      <fieldset class="space">
        <legend><img src="../img/admin/unknown.gif" alt="" class="middle" />'.$this->l('Settings').'</legend>
        '.$this->l('Fuer die Nutzung dieser Schnittstelle benoetigen Sie einen persoenlichen ShopKey.').'<br />
        '.$this->l('Fordern Sie jetzt Ihren Shopkey von Protected Shops an unter:').'
        <a target="_blank" href="http://www.touchdesign.de/ico/protectedshops.htm">'.$this->l('Shopkey anfordern').'</a>
      </fieldset><br />';

    $this->_html .= '
      <form method="post" action="'.$_SERVER['REQUEST_URI'].'">
      <fieldset>
        <legend><img src="'.$this->_path.'logo.gif" />'.$this->l('Settings').'</legend>

        <label>'.$this->l('User').'</label>
        <div class="margin-form">
          <input type="text" name="PROTECTEDSHOPS_USER" value="'.Configuration::get('PROTECTEDSHOPS_USER').'" />
          <p>'.$this->l('Leave it blank for disabling').'</p>
        </div>
        <div class="clear"></div>  
        
        <label>'.$this->l('Password').'</label>
        <div class="margin-form">
          <input type="text" name="PROTECTEDSHOPS_PASSWORD" value="'.Configuration::get('PROTECTEDSHOPS_PASSWORD').'" />
          <p>'.$this->l('Leave it blank for disabling').'</p>
        </div>
        <div class="clear"></div>  
        
        <label>'.$this->l('ShopId').'</label>
        <div class="margin-form">
          <input type="text" name="PROTECTEDSHOPS_SHOPID" value="'.Configuration::get('PROTECTEDSHOPS_SHOPID').'" />
          <p>'.$this->l('Leave it blank for disabling').'</p>
        </div>
        <div class="clear"></div>  

        <label>'.$this->l('Protected Shops Logo?').'</label>
        <div class="margin-form">
          <select name="PROTECTEDSHOPS_BLOCK_LOGO">
            <option '.(Configuration::get('PROTECTEDSHOPS_BLOCK_LOGO') == "Y" ? "selected" : "").' value="Y">'.$this->l('Yes, display the logo (recommended)').'</option>
            <option '.(Configuration::get('PROTECTEDSHOPS_BLOCK_LOGO') == "N" ? "selected" : "").' value="N">'.$this->l('No, do not display').'</option>
          </select>
          <p>'.$this->l('Display logo in left column').'</p>
        </div>
        <div class="clear"></div>

        <label>'.$this->l('Document format').'</label>
        <div class="margin-form">
          <select name="PROTECTEDSHOPS_FORMAT">
            <option '.(Configuration::get('PROTECTEDSHOPS_FORMAT') == "Html" ? "selected" : "").' value="Html">'.$this->l('HTML').'</option>
            <option '.(Configuration::get('PROTECTEDSHOPS_FORMAT') == "HtmlLite" ? "selected" : "").' value="HtmlLite">'.$this->l('HTML Lite').'</option>
            <option '.(Configuration::get('PROTECTEDSHOPS_FORMAT') == "Text" ? "selected" : "").' value="Text">'.$this->l('Text').'</option>
          </select>
          <p>'.$this->l('Which format you want to import?').'</p>
        </div>
        <div class="clear"></div>
        
        <label>'.$this->l('AGB').'</label>
        <div class="margin-form">
          '.touchdesign::getCmsDropdown('PROTECTEDSHOPS_ID_TERMS').'
        </div>
        
        <label>'.$this->l('Widerruf').'</label>
        <div class="margin-form">
          '.touchdesign::getCmsDropdown('PROTECTEDSHOPS_ID_REVOCATION').'
        </div>
        
        <label>'.$this->l('Versandhinweise').'</label>
        <div class="margin-form">
          '.touchdesign::getCmsDropdown('PROTECTEDSHOPS_ID_SHIPPING').'
        </div>
        
        <label>'.$this->l('Datenschutz').'</label>
        <div class="margin-form">
          '.touchdesign::getCmsDropdown('PROTECTEDSHOPS_ID_PRIVACY').'
        </div>
        
        <label>'.$this->l('Batterriegesetz').'</label>
        <div class="margin-form">
          '.touchdesign::getCmsDropdown('PROTECTEDSHOPS_ID_BATTERIE').'
        </div>
        
        <label>'.$this->l('Impressum').'</label>
        <div class="margin-form">
          '.touchdesign::getCmsDropdown('PROTECTEDSHOPS_ID_IMPRINT').'
        </div>
        
        <div class="margin-form clear pspace"><input type="submit" name="submitUpdate" value="'.$this->l('Update').'" class="button" /></div>
      </fieldset>
      </form>';

    $this->_html .= '
      <fieldset class="space">
        <legend><img src="../img/admin/unknown.gif" alt="" class="middle" />'.$this->l('Help').'</legend>
        <b>'.$this->l('@Link:').'</b> <a target="_blank" href="http://www.touchdesign.de/ico/protectedshops.htm">www.protectedshops.de</a><br />
        '.$this->l('@Vendor:').' Protected Shops GmbH<br />
        '.$this->l('@Copyright:').' by <a target="_blank" href="http://www.touchdesign.de/">touchDesign</a><br />
        <b>'.$this->l('@Description:').'</b><br /><br />
        '.$this->l('Mit der Schnittstelle zu Protected Shops koennen Sie ueber AGB Connect Ihre Rechtstexte automatisch abrufen und entspr. in Ihrem Shopsystem zuweisen. Fuer jede Plattform erhalten Sie einen universellen Shop Key welchen Sie zum Abrufen der Dokumente benoetigen.').'
      </fieldset><br />';

    return $this->_html;
  }

  function hookLeftColumn($params)
  {
    global $smarty;
    
    if(Configuration::get('PROTECTEDSHOPS_BLOCK_LOGO') == "N"){
      return false;
    }
    
    $smarty->assign('protectedshops_shopid',Configuration::get('PROTECTEDSHOPS_SHOPID'));
    
    return $this->display(__FILE__, 'blockprotectedshopslogo.tpl');
  }

  function getDocument($document='AGB')
  {
    $request['Request'] = 'GetDocument';
    $request['ShopId'] = Configuration::get('PROTECTEDSHOPS_SHOPID');
    $request['Document'] = $document;
    $request['Format'] = Configuration::get('PROTECTEDSHOPS_FORMAT');
    $request['Version'] = 1;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.protectedshops.de/api/');
    curl_setopt($ch, CURLOPT_USERAGENT, 'touchDesign ProtectedShops module');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    curl_setopt($ch, CURLOPT_USERPWD, Configuration::get('PROTECTEDSHOPS_USER').":".Configuration::get('PROTECTEDSHOPS_PASSWORD'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    if(($result = curl_exec($ch)) === false){
      return curl_error($ch);
    }

    return new simpleXMLElement($result);
  }

  function updateDocument($document)
  {
    if(isset($this->_documents[$document])){
      $documentName = $this->_documents[$document];
    }

    $content = $this->getDocument($documentName);
    
    $cmsId = Configuration::get('PROTECTEDSHOPS_ID_'.$document);  
    if(!empty($cmsId)){
      $cms = new CMS($cmsId);
      $cms->content[Configuration::get('PS_LANG_DEFAULT')] = (string)$content->Document;
      $cms->update();
      
      return true;
    }
    
    return false;
  }

}

?>