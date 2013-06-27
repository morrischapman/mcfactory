<?php

$GLOBALS['ADODB_QUOTE_FIELDNAMES'] = true;

{{*}}
// if(!class_exists('{{$module->getModuleName()}}ObjectBase'))
// {
//   // Since 1.7...
//   require dirname(__FILE__) . '/lib/class.{{$module->getModuleName()}}ObjectBase.php';
//   require dirname(__FILE__) . '/lib/class.{{$module->getModuleName()}}Object.php';
//   require dirname(__FILE__) . '/lib/class.{{$module->getModuleName()}}Views.php';
// }
{{*}}

class {{$module->getModuleName()}} extends CMSModule {
{{* TODO class {{$module->getModuleName()}} extends {{$module->getModuleName()}}Base { *}}

  public static $frontend_templates = array(
      'default'     => 'default (list)',
      'detail'       => 'detail', 
      'search'       => 'search',
      'calendar'     => 'calendar',
      'tagcloud'     => 'tagcloud', // TODO
      'rss'         => 'rss',
      'paginated'   => 'paginated',
      'user_form'   => 'user_form',
      'user_form_succes'   => 'user_form_succes',
      'direct_email'   => 'direct_email'
    );

  public function DoAction($name, $id, $params, $returnid = '') {
    $methods = get_class_methods($this);
    foreach ($methods as $method) {
      if (strpos($method, 'modifier') === 0) {
        $modifier =substr($method, 8);
        $modifier{0} = strtolower($modifier{0});
        $this->smarty->register_modifier($modifier, array($this, $method));
      }
    }
    parent::DoAction($name, $id, $params, $returnid);
  }

  public function __construct() {
    parent::__construct();
    $this->InitializeGlobal();
  }

  public function GetName() {               return '{{$module->getModuleName()}}';  }
  public function GetObjectName() {         return '{{$module->getModuleName()}}Object';  }
  public function GetFriendlyName() {       return '{{$module->getModuleFriendlyName()}}';  }
  public function GetVersion() {            return '{{$module->getModuleVersion()}}';  }
  public  function GetHelp() {              return $this->Lang('help');  }
  public  function GetAuthor() {            return 'Auto-generated by M&C Factory';  }
  public  function GetAuthorEmail() {       return 'jcc@morris-chapman.com';  }
  public  function GetChangeLog() {         return $this->Lang('changelog');  }
  public  function IsPluginModule() {       return true;  }
  public  function HasAdmin() {             return {{$has_admin}};}
  public  function GetAdminSection() {      return '{{$admin_section}}';  }
  public  function GetAdminDescription() {  return $this->Lang('admindescription');  }
  public  function VisibleToAdminUser() {   return ($this->CheckAccess() || $this->CheckAccess('Modify Templates')); }
  public  function GetDependencies() {      return array('MCFactory' => '{{$mcfactoryversion}}');  }
  public  function CheckAccess($permission = 'Manage {{$module->getModuleName()}}') {    return $this->CheckPermission($permission);  }
  
  public  function DisplayErrorPage($id, &$params, $return_id, $message='') {
    $this->smarty->assign('title_error', $this->Lang('error'));
    $this->smarty->assign_by_ref('message', $message);
    echo $this->ProcessTemplate('error.tpl');
  }
  
  public  function HasCapability($capability, $params=array()) {
    $capabilities = array(
      'digest_export',
      'cms_users'
    );
    
    if (in_array($capability, $capabilities)) return true;
    return false;
  }

  function MinimumCMSVersion() {            return '1.9';  }
  function InstallPostMessage() {           return $this->Lang('postinstall');  }
  function UninstallPostMessage() {         return $this->Lang('postuninstall');  }
  function UninstallPreMessage() {          return $this->Lang('really_uninstall');  }
  public function Install() {
    
    $config = cms_utils::get_config();
    $db = cms_utils::get_db();
    $dict = NewDataDictionary($db);

    $fields = array(
    	'id I KEY',
		'user_id I',
		'parent_id I',
		'title C(255)',
		{{foreach from=$extra_fields item=field}}
		'{{$field.name}} {{$field.column_type}}',
		{{/foreach}}
		'created_at D',
		'created_by I',
		'mcfi_created_timestamp I',
		'updated_at D',
		'updated_by I',
		'mcfi_updated_timestamp I',
		'send_update_immediately I',
		'order_by I',
		'published I',
		'parent_item I',
		'full_text_search XL'
    );

    $sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_{{$table_name}}', implode(',',$fields));
    $dict->ExecuteSQLArray($sqlarray);
    $db->CreateSequence(cms_db_prefix().'module_{{$table_name}}_seq');
    
    //$this->SetTemplate('display_list', $this->GetTemplateFromFile('template.list'));
    //$this->SetTemplate('display_paginated', $this->GetTemplateFromFile('template.paginated'));
    //$this->SetTemplate('display_details', $this->GetTemplateFromFile('template.details'));
    //$this->SetTemplate('display_search', $this->GetTemplateFromFile('template.search'));
    //$this->SetTemplate('display_calendar', $this->GetTemplateFromFile('template.calendar'));
    
    //@mkdir($config['root_path'].'/uploads/{{$module->getModuleName()}}');
    $this->CreateEvent('ContentEditPost');
    cms_utils::get_module('MCFactory')->AddEventHandler($this->getName(), 'ContentEditPost', false);
    $this->CreatePermission('Manage {{$module->getModuleName()}}', 'Manage {{$module->getModuleName()}}');  
    $this->CreatePermission('Admin {{$module->getModuleName()}}', 'Admin {{$module->getModuleName()}}');  
    $this->SetPreference('index_content', 'true');
    $this->SetPreference('twitter_template', '{$title} - {$url}');
    $this->SetPreference('mcfactory_version', '{{$mcfactory_version}}');
  }

  public function Upgrade($oldversion, $newversion)  {
    $db = $this->GetDb();
    $dict = NewDataDictionary($db);
    
    $oldversion = $this->GetPreference('mcfactory_version', '2.5.0');
    
    switch(true) {
      case version_compare($oldversion, '2.9.0', '<'):
        $this->GetPreference('index_content',$this->GetPreference('index_content', 'true'));
      case version_compare($oldversion, '2.9.9', '<'):
        $this->AddDefaultTemplate('default', 'display_list');
        $this->AddDefaultTemplate('detail', 'display_details');
        $this->AddDefaultTemplate('paginated', 'display_paginated');
        $this->AddDefaultTemplate('search', 'display_search');
        $this->AddDefaultTemplate('calendar', 'display_calendar');
      case version_compare($oldversion, '2.9.16', '<'):
          $sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_{{$table_name}}', 'user_id I');
          $dict->ExecuteSQLArray($sqlarray);
      case version_compare($oldversion, '2.12.1', '<'):
          $sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_{{$table_name}}', 'mcfi_created_timestamp I');
          $dict->ExecuteSQLArray($sqlarray);
          $sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_{{$table_name}}', 'mcfi_updated_timestamp I');
          $dict->ExecuteSQLArray($sqlarray);
      case version_compare($oldversion, '3.1.8', '<'):
          $sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_{{$table_name}}', 'send_update_immediately I');
          $dict->ExecuteSQLArray($sqlarray);
      
      
    }
    
    $this->SetPreference('mcfactory_version', '{{$mcfactory_version}}');
  }

  public function Uninstall() {
    $db = cms_utils::get_db();
    $dict = NewDataDictionary($db);
    $sql = $dict->DropTableSQL(cms_db_prefix().'module_{{$table_name}}');
    $dict->ExecuteSQLArray($sql);
    $db->DropSequence(cms_db_prefix().'module_{{$table_name}}_seq');
    cms_utils::get_module('MCFactory')->RemoveEventHandler($this->getName(), 'ContentEditPost');
    $this->RemoveEvent('ContentEditPost');
    $this->RemovePreference();
  }

  function XtendedModule () {
    return true;
  }

  function GetHeaderHTML() {
    $html = '';
    
    $mcf = cms_utils::get_module('MCFactory');
    
    if(is_object($mcf))
    {      
        $html .= '<link rel="stylesheet" type="text/css" href="'.$mcf->GetModuleURLPath(). '/lib/jquery/smoothness/jquery-ui-1.8.4.custom.css" />';

        // SELECT2
        $html .= '<link rel="stylesheet" type="text/css" href="'.$mcf->GetModuleURLPath(). '/lib/vendor/select2/select2.css" />';
        $html .= '<script type="text/javascript" src="'.$mcf->GetModuleURLPath(). '/lib/vendor/select2/select2.js"></script>';
        
        $html .= '
        <script type="text/javascript">
          $(document).ready(function() {
            $(".chzn-select").select2();
          });
        </script>';
    }
    
    return $html;
  }

  function setParameters()  {  $this->InitializeGlobal(); }

  function InitializeGlobal() {  
    $this->RegisterModulePlugin();
    // $this->RegisterRoute('/{{$module->getModuleName()|lower}}\/(?P<item_id>[0-9]+)(\/.*?)?$/', array('action' => 'detail', 'returnid' => cmsms()->GetContentOperations()->GetDefaultPageID()));
    $this->RegisterRoute('/{{$module->getModuleName()|lower}}\/(?P<item_id>[0-9]+)\/(?P<returnid>[0-9]+)(\/.*?)?$/', array('action' => 'detail'));
    $this->RegisterRoute('/{{$module->getModuleName()|lower}}\/rss\/(?P<maction>[a-zA-Z0-9_-]+)(\/.*?)?$/', array('action' => 'rss', 'showtemplate' => 'false','returnid' => cmsms()->GetContentOperations()->GetDefaultPageID()));
    $this->RegisterRoute('/{{$module->getModuleName()|lower}}\/api\/(?P<command>[a-zA-Z0-9_-]+)(\/.*?)?$/', array('action' => 'api', 'showtemplate' => 'false','returnid' => cmsms()->GetContentOperations()->GetDefaultPageID()));
    
    $this->RegisterRoute('/{{$module->getModuleName()|lower}}\/download\/(?P<item_id>[0-9]+)\/(?P<field>[a-zA-Z0-9_-]+)(\/.*?)?$/', 
      array(
      'action' => 'download', 
      'showtemplate' => 'false',
      'returnid' => cmsms()->GetContentOperations()->GetDefaultPageID()
      ));

		$this->RegisterParameters();
  }

	private function RegisterParameters()
	{
		$this->RestrictUnknownParams(false);
		// TODO: Set all parameters type
	}
	
  function createLink($id, $action, $returnid='', $contents='', $params=array(), $warn_message='', $onlyhref=false, $inline=false, $addttext='', $targetcontentonly=false, $prettyurl='',  $withslash = false) {
    if ($targetcontentonly || ($returnid != '' && !$inline)) {
      $id = 'cntnt01';
    }
    if (!$returnid) {  
        $returnid = cms_utils::get_current_pageid();
    }
    
    if (empty($prettyurl)) {
      if ($action == 'detail') {
        $item_id = $params['item_id'];
        $prettyurl = '{{$module->getModuleName()|lower}}/' . $item_id . '/' . $returnid;
       
        if (!empty($params['title'])) {
          $prettyurl .= '/' . munge_string_to_url($params['title'],false,$withslash);
        }
        
        $query = array();
        foreach ($params as $name => $value) {
          if (!in_array($name, array(
            'module', 'action', 'item_id', 'title',
            'orderby','limit'
            ))) {
            $query[$id . $name] = $value;
          }
        }
        if (count($query)) {
          $prettyurl .= '?' . http_build_query($query);
        }
      }
    }
    return parent::createLink($id, $action, $returnid, $contents, $params, $warn_message, $onlyhref, $inline, $addttext, $targetcontentonly, $prettyurl);
  }

  function SearchReindex() {
    $c = new MCFCriteria();
    $c->add('published', 1);
    $items = {{$module->getModuleName()}}Object::doSelect($c);
    foreach ($items as $item) {
      $this->index($item, true);
    }
  }
  
  function SearchDeindex() {
    $c = new MCFCriteria();
    $c->add('published', 1);
    $items = {{$module->getModuleName()}}Object::doSelect($c);
    foreach ($items as $item) {
      $this->deindex($item);
    }
  }

  function index($item, $force = false) {
    if ($this->getPreference('index_content') == 'true' || $this->getPreference('index_content') == '1' || $force)
    {
      $search = $this->GetModuleInstance('Search');
      if ($search) {
        $search->AddWords(
          '{{$module->getModuleName()}}',
          $item->getId(),
          '{{$module->getModuleName()|lower}}_item',
          $item->getSearchString()
        );
      }
    }
  }

  function deindex($item) {
    $search = $this->GetModuleInstance('Search');
    if ($search) {
      $search->DeleteWords('{{$module->getModuleName()}}', $item->getId(), '{{$module->getModuleName()|lower}}_item');
    }
  }

  function SearchResult($returnid, $id, $attr = '') {
    $result = array();
    if ($attr == '{{$module->getModuleName()|lower}}_item') {
      $c = new MCFCriteria();
      $c->add('id', $id);
      $c->add('published', 1);
      $item = {{$module->getModuleName()}}Object::doSelectOne($c);
      if ($item) {
        $result[0] = $this->GetFriendlyName();
        $result[1] = $item->getTitle();
        $result[2] = $this->CreateLink('cntnt01', 'detail', $returnid, '', array('item_id' => $id, 'title' => $item->getTitle()), '', true);
      }
    }
    return $result;
  }
  
  /*
  * Digest: New notification tool function
  */
  
  public function Digest($timestamp, $params)
  {
    return $this->NTList(date('Y-m-d', $timestamp), $params['template'], $params);
  }
  
  /**
   * NTList: Notification Tool function: Return the list of items uploaded since a certain date 
   */

  function NTList($date, $template = null, $params = array())
  {
    $returnid = $this->getPreference('default_page', $this->cms->GetContentOperations()->GetDefaultPageID());
    $c = new MCFCriteria();
    $c->add('published', '1');
    if(isset($params['created_at']))
    {
      $c->add('created_at', $date, MCFCriteria::GREATER_EQUAL);  
    }
    elseif(isset($params['date_field']))
    {
      $c->add($params['date_field'], $date, MCFCriteria::GREATER_EQUAL);  
    }
    else
    {
      $c->add('updated_at', $date, MCFCriteria::GREATER_EQUAL);
    }    
    
    {{$module->getModuleName()}}Object::buildFrontendFilters($c, $params);
    $c->addAscendingOrderByColumn('updated_at');
    $items = {{$module->getModuleName()}}Object::doSelect($c);

    if (empty($items))
    {
      return null;
    }
    
    if (!is_null($template))
    {
      $params['template'] = $template;
    }

    $detailpage = $returnid;
    if (isset($params['detailpage'])) {
        $manager = cmsms()->GetHierarchyManager();
        $node = $manager->sureGetNodeByAlias($params['detailpage']);
        if ($node) {
            $content = $node->GetContent();
            if ($content)
            {
                $detailpage = $content->Id();
            }
        } else {
            $node = $manager->sureGetNodeById($params['detailpage']);
            if ($node) {
                $detailpage = $params['detailpage'];
            }
        }
        $params['origid'] = $returnid;
    }

    foreach ($items as &$item) {
      $params['item_id'] = $item->getId();
      $params['title'] = $item->getTitle();
      $newparams = $params;
      unset($newparams['showtemplate']);
      $item->detail_link = $this->createLink($id, 'detail', $detailpage, $contents='', $newparams, '', true);
      if(class_exists('MX_XtendedModule'))
      {
        $xtended_felist = MX_XtendedModule::getRelatedItems($this->getName(), $item->getId());        
      }
    }
    unset($item);

    $this->smarty->assign('items', $items);    
    $this->smarty->assign('{{$module->getModuleName()}}', $items);
    $this->smarty->assign('{{$module->getModuleName()|lower}}', $items);
    $paramsobj = new stdClass();
    $paramsobj->params = $params;
    $this->smarty->assign('mcfactory', $paramsobj);
    $this->smarty->assign('{{$module->getModuleName()|lower}}_params', $paramsobj);
    return $this->ProcessTemplateFor('default', $params);
  }
  
  {{if isset($ratable)}}
  // Ratable module
  public static function updateRate($item_id, $rate)
  {
    $item = {{$module->getModuleName()}}Object::retrieveByPk($item_id);

    if ($item !== false)
    {
      $item->rate = $rate;
      $item->save();
    }
  }
  {{/if}}
  
  public function updateItem($item_id)
  {
    $item = {{$module->getModuleName()}}Object::retrieveByPk($item_id);
    
    if ($item !== false)
    {
      $item->forceUpdateObject('magic');  
    }
  }

  public function getAdminList($id,$returnid,$third=null)
  {

  }
  
  public static function ExportDatas()  {
    $datas = array();
    $c = new MCFCriteria();
    $items = {{$module->getModuleName()}}Object::doSelect($c);

    $datas[0] = array(
      'id',
      'title',
      {{foreach from=$extra_fields item=field}}
        {{if ! isset($field.foptions.exclude_from_export)}}
        '{{$field.friendlyname}}',
        {{/if}}
      {{/foreach}}
      'created_at',
      'created_by',
      'mcfi_created_timestamp',
      'updated_at',
      'updated_by',
      'mcfi_updated_timestamp',
      'order_by',
      'published',
      'parent_item',
      'parent_id',
      'user_id'
      );
      
    foreach($items as $item)
    {
      $datas[] = array(
        'id' => $item->getId(),
        'title' => $item->getTitle(),
        {{foreach from=$extra_fields item=field}}
          {{if ! isset($field.foptions.exclude_from_export)}}
          '{{$field.friendlyname}}' => $item->get{{$field.camelcase}}(),
          {{/if}}
        {{/foreach}}
        'created_at' => $item->getCreatedAt(),
        'created_by' => $item->getCreatedBy(),
        'mcfi_created_timestamp' => $item->getMcfiCreatedTimestamp(),
        'updated_at' => $item->getUpdatedAt(),
        'updated_by' => $item->getUpdatedBy(),
        'mcfi_updated_timestamp' =>  $item->getMcfiUpdatedTimestamp(),
        'order_by' => $item->getOrderBy(),
        'published' => $item->getPublished(),
        'parent_item' => $item->getParentItem(),
        'parent_id' => $item->getParentId(),
        'user_id' => $item->getUserId()
      );
    }
      
    return $datas;
  }
  
  // CMS User
  public function getUserFunction()
  {
    // Is User Module ?
    {{if isset($is_user_module)}}
      return 'userGetEditLink';
    {{else}}
      // First one win
      {{foreach from=$extra_fields item=field}}
        {{if $field.form_type == 'user'}}
          {{if !isset($field.foptions.multiple)}}
            return 'retrieveLinkFor{{$field.camelcase}}';
          {{/if}}
        {{/if}}
      {{/foreach}}
    {{/if}}
    return false;
  }
  
  // TEMPLATES
  
  public function GetDefaultTemplates()
  {
    $array = unserialize($this->GetPreference('default_templates'));
    if (is_array($array))
    {
      return $array;
    }
    return array();
  } 
  
  public function SetDefaultTemplates($list = array())
  {
    return $this->SetPreference('default_templates', serialize($list));
  }
  
  public function AddDefaultTemplate($action, $template)
  {
    $list = $this->GetDefaultTemplates();
    $list[$action] = $template;
    $this->SetDefaultTemplates($list);
  }
  
  public function GetDefaultTemplate($action)
  {
      $list = $this->GetDefaultTemplates();
      if (!is_array($list)) $list = array();
      if (array_key_exists($action, $list)) // TODO: Possible problem with list
      {
        return $list[$action];
      }
      else
      {
        return false;
      }
  }
  
  public function isDefaultTemplate($template)
  {    
    $list = $this->GetDefaultTemplates();
    $action = array_search($template, $list);
    if($action !== false)
    {
      return $action;
    }
    return false;
  }  
  
  public function removeDefaultTemplate($template)
  {    
    $list = $this->GetDefaultTemplates();
    $action = array_search($template, $list);
    if($action !== false)
    {
      unset($list[$action]);
      $this->SetDefaultTemplates($list);
    }
    return false;
  }
  
  public function ProcessTemplateFor($action, $params = array())
  {
    if (isset($params['template']) && $this->GetTemplate($params['template'])) {
      return $this->ProcessTemplateFromDatabase($params['template']);
    }
    elseif (($template = $this->GetDefaultTemplate($action))  &&  ($this->GetTemplate($template) !== false))
    {
      return $this->ProcessTemplateFromDatabase($template);
    }
    else
    {
      return $this->ProcessTemplate('frontend.'.$action.'.tpl');
    }
  }
  
  public function ParamsForLink($params,$include = array(),$exclude = array())
  {
    $new_params = array();
    
    foreach($params as $key => $value)
    {
      if (!in_array($key, $exclude))
      {
        $new_params[$key] = $value;
      }
    }
    
    foreach($include as $key => $value)
    {
      if (!in_array($key, $exclude))
      {
        $new_params[$key] = $value;
      }
    }
    
    // Now we'll treath the complex array problem
    
    $query = http_build_query($new_params);
    $entries = explode('&', $query);
    $output = array();
    foreach($entries as $entry)
    {
      $data = explode('=', $entry);
      $output[$data[0]] = urldecode($data[1]);
    }  
    
    return $output;
  }
  
  public function buildFiltersCriteria(MCFCriteria &$c, $filters)
  {
    // DEPRECATED
    {{$module->getModuleName()}}Object::buildFiltersCriteria($c, $filters);
  }
  
  public function buildFrontendFilters(MCFCriteria &$c, $params)
  {
    // DEPRECATED
    {{$module->getModuleName()}}Object::buildFrontendFilters($c, $params);
  }
  
  public static function jumpTo($url)
  {
    $url = self::parseUrl($url);
    if (headers_sent())
    {
      echo '
      <script type="text/javascript">
      <!--
        location.replace("'.$url.'");
      // -->
      </script>
      <noscript>
        <meta http-equiv="Refresh" content="0;URL='.$url.'">
      </noscript>';
      exit;
    }
    else
    {
      header('Location: '.$url);
    }
  }
  
  private static function parseUrl($url)
  {
    if (strpos($url,'http') === 0)
    {
      return $url;
    }
    else
    {
      $manager = cmsms()->GetHierarchyManager();
      $node = $manager->sureGetNodeByAlias($url);
      if ($node) {
        $content = $node->GetContent();
        if ($content)
        {
          return $content->GetUrl();
        }
      }
      else
      {
        $node = $manager->sureGetNodeById($url);
        if ($node) {
          $content = $node->GetContent();
          if ($content)
          {
            return $content->GetUrl();
          }
        }
      }
    }  
    return null;
  }

	{{if $events|@count > 0}}
	public function HandlesEvents()	{	return true;	}
	
	public function DoEvent($originator, $eventname, &$params) {
		{{foreach from=$events key=module_name item=events_name}}
		if('{{$module_name}}' == $originator)	{
			{{foreach from=$events_name key=event_name item=event}}
			if('{{$event_name}}' == $eventname)	{
				{{$event}}
			}
			{{/foreach}}
		}
		{{/foreach}}
	}
	{{/if}}
  
  // EXTRA ACTIONS
  
  public function getButtonsFor($place)
  {
    global $id;
    global $returnid;
    
    switch($place)
    {
      {{foreach from=$buttons item=button key=place}}
      case '{{$place}}':
        $html = '';
        {{foreach from=$button item=action}}
        $html .= $this->CreateLink($id, '{{$action->name}}', $returnid, '{{$action->button_name}}', array('class' => 'actionbutton'));
        {{/foreach}}
        return $html;
        break;
      {{/foreach}}
      default:
      break;
    }

  }
  
  public static function getTitleLabel()
  {
    return (string)'{{$title_label}}';
  }

	public function doSelect(MCFCriteria $c)
	{
		return {{$module->getModuleName()}}Object::doSelect($c);
	}
}

?>