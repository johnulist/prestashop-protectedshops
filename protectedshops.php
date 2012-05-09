<?php
/**
 * $Id$
 *
 * protectedshops Module
 *
 * Copyright (c) 2011 touchdesign
 *
 * @category Tools
 * @version 0.5
 * @copyright 02.02.2011, touchdesign
 * @author Christin Gruber, <www.touchdesign.de>
 * @link http://www.touchdesign.de/loesungen/prestashop/protectedshops.htm
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * Description:
 *
 * Protected Shops AGB Connect and vote Connect module by touchdesign
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

class protectedshops extends Module
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
  private $protectedshops_rating = '';
  
  public function __construct()
  {
    $this->name = 'protectedshops';
    if (version_compare(_PS_VERSION_, '1.4.0', '<')) {
      $this->tab = 'Tools';
    }else{
      $this->tab = 'tools';
    }
    $this->version = '0.5';
    $this->currencies = true;
    $this->currencies_mode = 'radio';
    parent::__construct();
    $this->page = basename(__FILE__, '.php');
    $this->displayName = $this->l('Protected Shops');
    $this->description = $this->l('Protected Shops AGB connect by touchdesign');
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
      !Configuration::updateValue('PROTECTEDSHOPS_BLOCK_RATING', 'N') ||
      !Configuration::updateValue('PROTECTEDSHOPS_FORMAT', 'HTML') ||
      !Configuration::updateValue('PROTECTEDSHOPS_ID_TERMS', '') ||
      !Configuration::updateValue('PROTECTEDSHOPS_ID_PRIVACY', '') ||
      !Configuration::updateValue('PROTECTEDSHOPS_ID_IMPRINT', '') ||
      !Configuration::updateValue('PROTECTEDSHOPS_ID_REVOCATION', '') ||
      !Configuration::updateValue('PROTECTEDSHOPS_ID_SHIPPING', '') ||
      !Configuration::updateValue('PROTECTEDSHOPS_ID_BATTERIE', '') ||
      !Configuration::updateValue('PROTECTEDSHOPS_VOTE_API', 'N') ||
      !Configuration::updateValue('PROTECTEDSHOPS_VOTE_API_MAIL', 'N') ||
      !$this->registerHook('leftColumn') ||
      !$this->registerHook('rightColumn') ||
      !$this->registerHook('newOrder') ||
      !$this->registerHook('orderConfirmation')
    ) {
      return false;
    }
    $sql = "CREATE TABLE "._DB_PREFIX_."touchdesign_protectedshops_rating(
      id INT UNSIGNED NOT NULL AUTO_INCREMENT,
      id_order INT NOT NULL,
      url VARCHAR(255) NOT NULL,
      PRIMARY KEY (id) 
    ) ENGINE=MyISAM default CHARSET=utf8";
    if(!Db::getInstance()->Execute($sql)) {
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
      !Configuration::deleteByName('PROTECTEDSHOPS_BLOCK_RATING') ||
      !Configuration::deleteByName('PROTECTEDSHOPS_FORMAT') ||
      !Configuration::deleteByName('PROTECTEDSHOPS_ID_TERMS') ||
      !Configuration::deleteByName('PROTECTEDSHOPS_ID_PRIVACY') ||
      !Configuration::deleteByName('PROTECTEDSHOPS_ID_IMPRINT') ||
      !Configuration::deleteByName('PROTECTEDSHOPS_ID_REVOCATION') ||
      !Configuration::deleteByName('PROTECTEDSHOPS_ID_SHIPPING') ||
      !Configuration::deleteByName('PROTECTEDSHOPS_ID_BATTERIE') ||
      !Configuration::deleteByName('PROTECTEDSHOPS_VOTE_API') ||
      !Configuration::deleteByName('PROTECTEDSHOPS_VOTE_API_MAIL') ||
      !parent::uninstall()
    ) {
      return false;
    }
    $sql = "DROP TABLE "._DB_PREFIX_."touchdesign_protectedshops_rating";
    if(!Db::getInstance()->Execute($sql)) {
      return false;
    }
    return true;
  }

  private function _postValidation() 
  {
    if (Tools::getValue('submitUpdate')) {
      if (!Tools::getValue('PROTECTEDSHOPS_USER')) {
        $this->_postErrors[] = $this->l('Protected Shops "User" is required.');
      }
      if (!Tools::getValue('PROTECTEDSHOPS_PASSWORD')) {
        $this->_postErrors[] = $this->l('Protected Shops "Password" is required.');
      }
      if (!Tools::getValue('PROTECTEDSHOPS_SHOPID')) {
        $this->_postErrors[] = $this->l('Protected Shops "ShopId" is required.');
      }
    }
  }
  
  public function getContent()
  {
    $this->_html = '<h2>'.$this->displayName.'</h2>';
    if (Tools::isSubmit('submitUpdate')) {
      Configuration::updateValue('PROTECTEDSHOPS_USER', Tools::getValue('PROTECTEDSHOPS_USER'));
      Configuration::updateValue('PROTECTEDSHOPS_PASSWORD', Tools::getValue('PROTECTEDSHOPS_PASSWORD'));
      Configuration::updateValue('PROTECTEDSHOPS_SHOPID', Tools::getValue('PROTECTEDSHOPS_SHOPID'));
      Configuration::updateValue('PROTECTEDSHOPS_BLOCK_LOGO', Tools::getValue('PROTECTEDSHOPS_BLOCK_LOGO'));
      Configuration::updateValue('PROTECTEDSHOPS_BLOCK_RATING', Tools::getValue('PROTECTEDSHOPS_BLOCK_RATING'));
      Configuration::updateValue('PROTECTEDSHOPS_FORMAT', Tools::getValue('PROTECTEDSHOPS_FORMAT'));
      Configuration::updateValue('PROTECTEDSHOPS_ID_TERMS', Tools::getValue('PROTECTEDSHOPS_ID_TERMS'));
      Configuration::updateValue('PROTECTEDSHOPS_ID_PRIVACY', Tools::getValue('PROTECTEDSHOPS_ID_PRIVACY'));
      Configuration::updateValue('PROTECTEDSHOPS_ID_IMPRINT', Tools::getValue('PROTECTEDSHOPS_ID_IMPRINT'));
      Configuration::updateValue('PROTECTEDSHOPS_ID_REVOCATION', Tools::getValue('PROTECTEDSHOPS_ID_REVOCATION'));
      Configuration::updateValue('PROTECTEDSHOPS_ID_SHIPPING', Tools::getValue('PROTECTEDSHOPS_ID_SHIPPING'));
      Configuration::updateValue('PROTECTEDSHOPS_ID_BATTERIE', Tools::getValue('PROTECTEDSHOPS_ID_BATTERIE'));
      Configuration::updateValue('PROTECTEDSHOPS_VOTE_API', Tools::getValue('PROTECTEDSHOPS_VOTE_API'));
      Configuration::updateValue('PROTECTEDSHOPS_VOTE_API_MAIL', Tools::getValue('PROTECTEDSHOPS_VOTE_API_MAIL'));
    }
    
    $this->_postValidation();
    if (isset($this->_postErrors) && sizeof($this->_postErrors)) {
      foreach ($this->_postErrors AS $err) {
        $this->_html .= '<div class="alert error">'. $err .'</div>';
      }
    }elseif(Tools::getValue('submitUpdate') && !isset($this->_postErrors)) {
      if(Tools::getValue('PROTECTEDSHOPS_UPDATE') == 'Y') {
        foreach($this->_documents AS $key => $document) {
          $this->updateDocument($key);
        }
        $this->getSuccessMessage('Documents updated succesfully and settings saved...');
      }else{
        $this->getSuccessMessage();
      }
    }
    
    return $this->_displayForm();
  }

  public function getSuccessMessage($message='Settings updated')
  {
    $this->_html.= '
    <div class="conf confirm">
      <img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />
      '.$this->l($message).'<img width="1" height="1" alt="" src="http://www.touchdesign.de/ico/success.png?type='.$this->name.'&id='.Tools::getValue('PROTECTEDSHOPS_USER').'&host='.$_SERVER['HTTP_HOST'].'" />
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

    $this->_html .= '
      <fieldset class="space">
        <legend><img src="../img/admin/unknown.gif" alt="" class="middle" />'.$this->l('Note').'</legend>
        '.$this->l('Fuer die Nutzung dieser Schnittstelle benoetigen Sie einen persoenlichen ShopKey.').'<br />
        '.$this->l('Fordern Sie jetzt Ihren Shopkey von Protected Shops an unter:').'
        <a target="_blank" href="http://www.touchdesign.de/ico/protectedshops.htm">'.$this->l('Shopkey anfordern').'</a>
      </fieldset><br />';

    $this->_html .= '
      <form method="post" action="'.$_SERVER['REQUEST_URI'].'">
      <fieldset>

        <fieldset>
          <legend>'.$this->l('General settings').'</legend>
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
            '.touchdesign::widgetDropdown('PROTECTEDSHOPS_BLOCK_LOGO',array('Y' => $this->l('Yes, display the logo (recommended)'),'N' => $this->l('No, do not display'))).'
            <p>'.$this->l('Display logo in left column').'</p>
          </div>
          <div class="clear"></div>
          
          <label>'.$this->l('Display rating').'</label>
          <div class="margin-form">
            '.touchdesign::widgetDropdown('PROTECTEDSHOPS_BLOCK_RATING',array('Y' => $this->l('Yes'),'N' => $this->l('No'))).'
            <p>'.$this->l('Display rating in right column?').'</p>
          </div>
          <div class="clear"></div>
        </fieldset>
        
        <br />
        
        <fieldset>
          <label>'.$this->l('Document format').'</label>
          <div class="margin-form">
            '.touchdesign::widgetDropdown('PROTECTEDSHOPS_FORMAT',array('Html' => $this->l('HTML'),'HtmlLite' => $this->l('HTML-Lite'),'Text' => $this->l('Text'))).'
            <p>'.$this->l('Which format you want to import?').'</p>
          </div>
          <div class="clear"></div>
        
          <legend>'.$this->l('Document and CMS settings').'</legend>
          <label>'.$this->l('AGB').'</label>
          <div class="margin-form">
            '.touchdesign::getCmsDropdown('PROTECTEDSHOPS_ID_TERMS').'
            <p>'.$this->l('Note: Existing contents will be lost!').'</p>
          </div>
          <div class="clear"></div>
          
          <label>'.$this->l('Widerruf').'</label>
          <div class="margin-form">
            '.touchdesign::getCmsDropdown('PROTECTEDSHOPS_ID_REVOCATION').'
            <p>'.$this->l('Note: Existing contents will be lost!').'</p>
          </div>
          <div class="clear"></div>
          
          <label>'.$this->l('Versandhinweise').'</label>
          <div class="margin-form">
            '.touchdesign::getCmsDropdown('PROTECTEDSHOPS_ID_SHIPPING').'
            <p>'.$this->l('Note: Existing contents will be lost!').'</p>
          </div>
          <div class="clear"></div>
          
          <label>'.$this->l('Datenschutz').'</label>
          <div class="margin-form">
            '.touchdesign::getCmsDropdown('PROTECTEDSHOPS_ID_PRIVACY').'
            <p>'.$this->l('Note: Existing contents will be lost!').'</p>
          </div>
          <div class="clear"></div>
          
          <label>'.$this->l('Batterriegesetz').'</label>
          <div class="margin-form">
            '.touchdesign::getCmsDropdown('PROTECTEDSHOPS_ID_BATTERIE').'
            <p>'.$this->l('Note: Existing contents will be lost!').'</p>
          </div>
          <div class="clear"></div>
          
          <label>'.$this->l('Impressum').'</label>
          <div class="margin-form">
            '.touchdesign::getCmsDropdown('PROTECTEDSHOPS_ID_IMPRINT').'
            <p>'.$this->l('Note: Existing contents will be lost!').'</p>
          </div>
          <div class="clear"></div>
        </fieldset>
        
        <br />
        
        <fieldset>
          <legend>'.$this->l('Vote connect API settings').'</legend>
          <label>'.$this->l('Vote connect api').'</label>
          <div class="margin-form">
            '.touchdesign::widgetDropdown('PROTECTEDSHOPS_VOTE_API',array('Y' => $this->l('Yes'),'N' => $this->l('No'))).'
            <p>'.$this->l('Enable vote connect api?').'</p>
          </div>
          <div class="clear"></div>
          
          <label>'.$this->l('Vote connect auto mail').'</label>
          <div class="margin-form">
            '.touchdesign::widgetDropdown('PROTECTEDSHOPS_VOTE_API_MAIL',array('Y' => $this->l('Yes'),'N' => $this->l('No'))).'
            <p>'.$this->l('Enable vote connect api auto mail? (Note: Please update your privacy rules)').'</p>
          </div>
          <div class="clear"></div>
        </fieldset>
        
        <br />
        
        <div class="margin-form">
          <input type="checkbox" value="Y" id="PROTECTEDSHOPS_UPDATE" name="PROTECTEDSHOPS_UPDATE" />
          <label for="PROTECTEDSHOPS_UPDATE" class="t">'.$this->l('Update my documents now, all existing contents will be overriden!').'</label>
        </div>
        
        <div class="margin-form clear pspace"><input type="submit" name="submitUpdate" value="'.$this->l('Submit form and save settings!').'" class="button" /></div>
        
      </fieldset>
      </form>';

    $this->_html .= '
      <fieldset class="space">
        <legend><img src="../img/admin/unknown.gif" alt="" class="middle" />'.$this->l('Help').'</legend>
        <b>'.$this->l('@Link:').'</b> <a target="_blank" href="http://www.touchdesign.de/ico/protectedshops.htm">www.protectedshops.de</a><br />
        '.$this->l('@Copyright:').' by <a target="_blank" href="http://www.touchdesign.de/">touchdesign</a><br />
        <b>'.$this->l('@Description:').'</b><br /><br />
        '.$this->l('Mit der Schnittstelle zu Protected Shops koennen Sie ueber AGB Connect Ihre Rechtstexte automatisch abrufen und entspr. in Ihrem Shopsystem zuweisen. Fuer jede Plattform erhalten Sie einen universellen Shop Key welchen Sie zum Abrufen der Dokumente benoetigen.').'
      </fieldset><br />';

    return $this->_html;
  }

  function hookLeftColumn($params)
  {
    global $smarty;
    
    if(Configuration::get('PROTECTEDSHOPS_BLOCK_LOGO') == "N") {
      return false;
    }
    
    $smarty->assign('protectedshops_shopid',Configuration::get('PROTECTEDSHOPS_SHOPID'));
    
    return $this->display(__FILE__, 'blockprotectedshopslogo.tpl');
  }
  
  function hookRightColumn($params)
  {
    global $smarty;
    
    if(Configuration::get('PROTECTEDSHOPS_BLOCK_RATING') == "N") {
      return false;
    }
    
    $smarty->assign('protectedshops_shopid',Configuration::get('PROTECTEDSHOPS_SHOPID'));
    
    return $this->display(__FILE__, 'blockprotectedshopsrating.tpl');
  }
  
  function hookNewOrder($params)
  {
    global $smarty;
    
    if(Configuration::get('PROTECTEDSHOPS_VOTE_API') == "N") {
      return false;
    }

    $request =array();
    if(Configuration::get('PROTECTEDSHOPS_VOTE_API_MAIL') == "Y") {
      $request['Request'] = 'SaveOrderEmail';
      $request['Title'] = ($params['customer']->gender == 2 ? 'Frau' : 'Herr');
      $request['Email'] = $params['customer']->email;
      $request['Name'] = $params['customer']->firstname . " " . $params['customer']->lastname;
    }else{
      $request['Request'] = 'SaveOrder';
    }
    $request['Request'] = 'SaveOrder';
    $request['ShopId'] = Configuration::get('PROTECTEDSHOPS_SHOPID');
    $request['OrderId'] = sprintf("#%06d", intval($params['order']->id));
    $request['Amount'] = round($params['order']->total_paid);
    $request['Version'] = '2.0';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'touchdesign PSmod');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERPWD, Configuration::get('PROTECTEDSHOPS_USER').":".Configuration::get('PROTECTEDSHOPS_PASSWORD'));
    curl_setopt($ch, CURLOPT_URL, 'https://www.protectedshops.de/rating/api/');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    
    if(($result = curl_exec($ch)) === false) {
      return curl_error($ch);
    }
    
    curl_close ($ch);
    
    $xml = new simpleXMLElement($result);
    $sql = "INSERT INTO "._DB_PREFIX_."touchdesign_protectedshops_rating SET id_order = ".intval($params['order']->id).", url = '".pSQL(trim($xml->RatingURL))."'";
    if(!Db::getInstance()->Execute($sql)) {
      return false;
    }
    
    return true;
  }
  
  function hookOrderConfirmation($params)
  {
    global $smarty;
    
    if(Configuration::get('PROTECTEDSHOPS_API') == "N") {
      return false;
    }
    
    $url = Db::getInstance()->getValue("SELECT url FROM "._DB_PREFIX_."touchdesign_protectedshops_rating WHERE id_order = ".intval($params['objOrder']->id)."");
    if(!$url) {
      return false;
    }
    
    $smarty->assign('protectedshops_rating', $url);
    
    return $this->display(__FILE__, 'protectedshops_rating.tpl');
  }
  
  function getDocument($document='AGB')
  {
    $request = array();
    $request['Request'] = 'GetDocument';
    $request['ShopId'] = Configuration::get('PROTECTEDSHOPS_SHOPID');
    $request['Document'] = $document;
    $request['Format'] = Configuration::get('PROTECTEDSHOPS_FORMAT');
    $request['Version'] = 1;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.protectedshops.de/api/');
    curl_setopt($ch, CURLOPT_USERAGENT, 'touchdesign PSmod');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    curl_setopt($ch, CURLOPT_USERPWD, Configuration::get('PROTECTEDSHOPS_USER').":".Configuration::get('PROTECTEDSHOPS_PASSWORD'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    if(($result = curl_exec($ch)) === false) {
      return curl_error($ch);
    }

    return new simpleXMLElement($result);
  }

  function updateDocument($document)
  {
    if(isset($this->_documents[$document])) {
      $documentName = $this->_documents[$document];
    }

    $content = $this->getDocument($documentName);
    
    $cmsId = Configuration::get('PROTECTEDSHOPS_ID_'.$document);
    if(!empty($cmsId)) {
      $cms = new CMS($cmsId);
      $cms->content[Configuration::get('PS_LANG_DEFAULT')] = (string)$content->Document;
      $cms->update();
      
      return true;
    }
    
    return false;
  }

}

?>