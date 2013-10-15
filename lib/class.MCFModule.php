<?php

/**
 * MCFModule Class
 * 
 * This class handle all the manipulations on the M&C Factory Module object
 * 
 * NOTE: It should be split in two classes but for convenience, It won't.
 *
 * FIXME: Check the security processes !
 * 
 */

class MCFModule {

	////////////////////////////////////////////////////////////
	// CLASS PROPERTIES ////////////////////////////////////////
	////////////////////////////////////////////////////////////

	protected $id;
	protected $module_name;
	protected $module_friendlyname;
	protected $module_version = 1;
	protected $title_label;
	protected $created_at;
	protected $created_by;
	protected $updated_at;
	protected $updated_by;
	protected $api_enabled;
	protected $extra_fields = array();
	// protected $structure = array();
	protected $structure; // New structure of extra fields
	protected $delete_fields = array();
	protected $filters = array();
	protected $parent_module;
	protected $show_module;	
	protected $admin_section;
	protected $is_user_module;
	protected $is_protected;
	protected $files_path;
	protected $module_logic;
	protected $templates_data;
  protected $extra_features;
  
	protected $is_modified = false;

	protected $publishing_status = true; // We will assume that publishing went well until further notices
	////////////////////////////////////////////////////////////
	// CLASS METHODS ///////////////////////////////////////////
	////////////////////////////////////////////////////////////

	// GETTER FUNCTIONS

	public function getId() {
		return $this->id;
	}

	public function getModuleName() {
		if (empty($this->module_name))
		{
			$value = preg_replace('/\W/', ' ', $this->module_friendlyname);
			$value = ucwords(strtolower($value));
			$value = preg_replace('/\s/', '', $value);
			$this->set('module_name', self::checkModuleNameAvailability($value));	
		}
		return $this->module_name;
	}
	
	public function __toString()	{
		return $this->getModuleName();
	}

	private static function checkModuleNameAvailability($module_name, $iterator = 0)	{
		$module = $module_name;
		if ($iterator > 0)
		{
			$module .= $iterator;
		}
		
		if (cms_utils::get_module($module))
		{
			$iterator++;
			return self::checkModuleNameAvailability($module_name,$iterator);
		}
		else
		{
			return $module;
		}
	}

	public function getModuleFriendlyname() {
		return $this->module_friendlyname;
	}

	public function getModuleVersion() {
		return $this->module_version;
	}

	public function getTitleLabel() {
		return (!empty($this->title_label))?$this->title_label:'Title';
	}

	public function getCreatedAt() {
		return $this->created_at;
	}

	public function getCreatedBy() {
		return $this->created_by;
	}

	public function getUpdatedAt() {
		return $this->updated_at;
	}

	public function getUpdatedBy() {
		return $this->updated_by;
	}

	public function getExtraFields() {
		return $this->extra_fields;
	}

	public function getOldExtraFields() {
		return $this->getStructure()->getOldStructure();
	}

	public function getStructure() {
		if(!is_object($this->structure))
		{
			$this->structure = new MCFModuleStructure($this->extra_fields);
		}
		return $this->structure;
	}

	public function getDeleteFields() {
		return $this->delete_fields;
	}

	public function getFilters() {
		sort($this->filters);
		return $this->filters;
	}

	public function getParentModule() {
		return $this->parent_module;
	}
	
	public function getChildModules() {
		$c = new MCFCriteria();
		$c->add('parent_module', $this->getId());
		return self::doSelect($c);
	}
	
	public function getShowModule() {
			return $this->show_module;
	} 
	
  function getAPIEnabled() {
		return $this->api_enabled;
	}
	
	function getIsUserModule() {
			return $this->is_user_module;
	}
		
	function getIsProtected() {
			return $this->is_protected;
	}
		
	function getFilesPath() {
		// Could have default path but dangerous if user do not modify it when moving the website...
		return $this->files_path; // Empty path means $config['uploads_path'];
	}

	public function getModuleLogic() {
		return $this->module_logic;
	}
	
	public function getTemplatesData() {
		return $this->templates_data;
	}
	
	public function getAdminSection() {
		return $this->admin_section;
	}

  public function getExtraFeatures() {
    if(!is_object($this->extra_features))
    {
      $this->extra_features = new MCFExtraFeatures();
    }
    return $this->extra_features;
  }

	// SETTER FUNCTIONS

	protected function set($name, $value) {
		if ($this->$name !== $value) {
			$this->$name = $value;
			$this->is_modified = true;
		}
	}

	public function setId($value) {
		$this->set('id', $value);
	}

	public function setModuleName($value) {
		$this->set('module_name', $value);
	}

	public function setModuleFriendlyname($value) {
		$this->set('module_friendlyname', $value);
	}

	public function setModuleVersion($value) {
		$this->set('module_version', $value);
	}
	
	public function setTitleLabel($value) {
		$this->set('title_label', $value);
	}

	public function setCreatedAt($value) {
		$this->set('created_at', $value);
	}

	public function setCreatedBy($value) {
		$this->set('created_by', $value);
	}

	public function setUpdatedAt($value) {
		$this->set('updated_at', $value);
	}

	public function setUpdatedBy($value) {
		$this->set('updated_by', $value);
	}

	public function setShowModule($value) {
		$this->set('show_module', $value);
	}
	
  public function setIsUserModule($value) {
    $this->set('is_user_module', $value);
  } 

  public function setAPIEnabled($value) {
    $this->set('api_enabled', $value);
  } 
	
	public function setIsProtected($value) {
		$this->set('is_protected', $value);
	}	
	
	public function setFilesPath($value) {
		$this->set('files_path', $value);
	}

	public function setModuleLogic($value) {
		$this->set('module_logic', $value);
	}
		
	public function setTemplatesData($value) {
		$this->set('templates_data', $value);
	}
	
	public function setAdminSection($value) {
		$this->set('admin_section', $value);
	}
	
	public function setExtraFields($value) {
		$this->setExtraOldFields($value);
	}	
	
	public function setStructure($value) {
		if (empty($value))
		{			
			$structure = new MCFModuleStructure($this->extra_fields);
			$value = $structure;
		}
		
		if(is_object($value))
		{
			$this->set('structure', $value);	
		}
		elseif(is_array($value))
		{
			$structure = new MCFModuleStructure($value);
			$this->set('structure', $structure);
		}
		else
		{
			$structure = new MCFModuleStructure(unserialize($value));
			$this->set('structure', $structure);
		}
	}
	
	public function setExtraFeatures($value) {
	  if(is_object($value))
	  {
	    $this->extra_features = $value;
	  }
	}
	
	public function setExtraOldFields($value) {
	  
	  // DEPRECATED SEE MODULE STRUCTURE INSTEAD !!!
		$blacklist = array('id', 'parent_id', 'title', 'created_at', 'created_by', 'updated_at', 'updated_by', 'order_by', 'parent', 'parent_module', 'published', 'order', 'order_by', 'from', 'select', 'in', 'date', 'full_text_search','core_slug','coreslug','user_id', 'function', 'byid','json','asarray','searchstring', 'group', 'by', 'send_immediate_update');
		$filter = array();
		if (is_array($value)) {
			foreach ($value as $field) {
				// Reserve mcfi_ for internal use only
				$field['name'] = str_replace('mcfi_', 'mcf_', $field['name']);
				// Get label from name if undefined
				if (($field['label']) == '') { $field['label'] = $field['name'];}
				
				if(preg_match("/[A-Z]/", $field['name'])===0) {
					$field['uppercase_name'] = $field['name'];
				}				
				
				$field['name'] = preg_replace('/\W/', '', strtolower($field['name']));
				
				if (!empty($field['name'])) {
					if (!in_array($field['name'], $blacklist))
					{					
						$filter[] = $field;	
					}
					else
					{
						$field['name'] = 'mcf_'.$field['name'];
							$filter[] = $field;	
					}
				}
			}
		}
		$this->set('extra_fields', $filter);
	}

	public function orderExtraFields($id, $direction)	{
		$fields = $this->getOldExtraFields();
		if ($direction == 'up')
		{	
			$id1 = $id;
			$id2 = $id-1;			
		}
		
		if ($direction == 'down')
		{	
			$id1 = $id;
			$id2 = $id+1;			
		}
		
		if (isset($fields[$id1]) && isset($fields[$id2]))
		{
			$field1 = $fields[$id1];
			$field2 = $fields[$id2];
			$fields[$id1] = $field2;
			$fields[$id2] = $field1;			
		}
		$this->set('extra_fields',$fields);
	}

	public function setDeleteFields($value) {
		$this->set('delete_fields', $value);
	}

	public function setFilters($value) {
		if (is_array($value)) {
			foreach ($value as &$filter) {
				$filter['name'] = preg_replace('/\W/', '', $filter['name']);
				$filter['field'] = preg_replace('/\W/', '', $filter['field']);
			}
		}
		$this->set('filters', $value);
	}
		
	public function setParentModule($value) {
		$this->set('parent_module', $value);
		if ($value > 0)
		{
			if (is_array($this->filters))
			{
				$exists = false;
				foreach($this->filters as $filter)
				{
					if ($filter['name'] == 'parent_item')
					{
						$exists = true;
					}
				}
				if($exists == false)
				{
					$this->filters[] = array('name' => 'parent_item', 'field' => 'parent_item', 'type' => 'equal');
				}
			}
		}
	}

	// Properties
	
	public function hasFieldWithType($field_type)	{
		return $this->hasFieldWithTypes(array($field_type));
	}
	
	public function hasFieldWithTypes(Array $field_types)	{	
		$return = false;
		
		foreach($field_types as $field_type)
		{
			$return = $return || $this->getStructure()->hasFieldWithType($field_type);
		}
		
		return $return;
	}
	
	public function getFieldsWithTypes(Array $fields_type)	{
		return $this->getStructure()->getFieldsWithTypes($fields_type);
	}

	// OTHER FUNCTIONS

	public function getFieldTypes() {
		$array = array(
			'text' => array(
				'type' => 'text',
				'label' => 'Text field',
				'column_type' => 'C(255)',
				'form_type' => 'text',
				'options' => true
			),
			'textarea' => array(
				'type' => 'textarea',
				'label' => 'Text area',
				'column_type' => 'X',
				'form_type' => 'textarea',
				'options' => true
			),
			'textarea_plain' => array(
				'type' => 'textarea_plain',
				'label' => 'Text area (no WYSIWYG)',
				'column_type' => 'X',
				'form_type' => 'textarea_plain',
				'options' => true
			),
			'textarea_code' => array(
				'type' => 'textarea_code',
				'label' => 'Text area (code)',
				'column_type' => 'X',
				'form_type' => 'textarea_code',
				'options' => true
			),
			'select' => array(
				'type' => 'select',
				'label' => 'Select (Dropdown)',
				'column_type' => 'C(255)',
				'form_type' => 'select',
				'options' => true,
				'options_default' => 'values:option1=>Option 1,option2=>Option2;'
			),
			'checkbox' => array(
			  'type' => 'checkbox',
			  'label' => 'Checkbox',
			  'column_type' => 'I',
			  'form_type' => 'checkbox',
			  'options' => true,
			  'options_default' => 'text:My checkbox text;'
			),
			'date' => array(
				'type' => 'date',
				'label' => 'Date',
				'column_type' => 'D',
				'form_type' => 'text',
				'options' => true,
				'options_default' => 'start_year:'.date('Y', strtotime('-1 year')).';'
			),
			'time' => array(
				'type' => 'time',
				'label' => 'Time',
				'column_type' => 'T',
				'form_type' => 'text',
				'options' => true,
				'options_default' => 'midnight: false;'
			),
			'datetime' => array(
				'type' => 'datetime',
				'label' => 'Date & Time',
				'column_type' => 'I',
				'form_type' => 'datetime',
				'options' => true,
				'options_default' => 'start_year:'.date('Y', strtotime('-1 year')).';'
			),
			'document' => array(
				'type' => 'document',
				'label' => 'Document',
				'column_type' => 'C(255)',
				'form_type' => 'file',
				'options' => true
			),
			'image' => array(
				'type' => 'image',
				'label' => 'Image',
				'column_type' => 'C(255)',
				'form_type' => 'file',
				'options' => true,
				'options_default' => 'size:150x150;'
			),
			'country' => array(
				'type' => 'country',
				'label' => 'Country',
				'column_type' => 'C(255)',
				'form_type' => 'select',
				'options' => true,
				'options_default' => ''
			),
			'hidden_text' => array(
				'type' => 'hidden_text',
				'label' => 'Hidden text',
				'column_type' => 'C(255)',
				'form_type' => 'none',
				'options' => false
			),			
			'static' => array(
				'type' => 'static',
				'label' => 'Static value',
				'column_type' => 'X',
				'form_type' => 'static',
				'options' => false
			),
			'module' => array(
				'type' => 'module',
				'label' => 'Module',
				'column_type' => 'C(255)',
				'form_type' => 'module',
				'options' => true,
				'options_default' => 'module_name:MyModuleName;'
			),			
			'page' => array(
				'type' => 'page',
				'label' => 'Page',
				'column_type' => 'C(255)',
				'form_type' => 'page',
				'options' => true
			),		
			'user' => array(
				'type' => 'user',
				'label' => 'CMS User',
				'column_type' => 'C(255)',
				'form_type' => 'user',
				'options' => true
			),
			'group' => array(
				'type' => 'group',
				'label' => 'CMS Group',
				'column_type' => 'C(255)',
				'form_type' => 'group',
				'options' => true
			),
			
				// 'user' => array( // This is only an On/Off option !
				// 				'type' => 'user',
				// 				'label' => 'User',
				// 				'column_type' => 'I',
				// 				'form_type' => 'user',
				// 				'options' => false
				// 			),
		);
		
		if(class_exists('CMSFormInputFiles'))
		{
		  // TODO: Find a way to insert that info from the MCMedias module
		  $array['files'] = array(
        'type' => 'files',
        'label' => 'Multiple files',
        'column_type' => 'C(255)',
        'form_type' => 'files',
        'options' => true
		  );
		}
		
		if(class_exists('CMSFormInputImages'))
		{
		  // TODO: Find a way to insert that info from the MCMedias module
		  $array['images'] = array(
        'type' => 'images',
        'label' => 'Multiple images',
        'column_type' => 'C(255)',
        'form_type' => 'images',
        'options' => true
		  );
		}
		
		return $array;
	}

	public function getFilterTypes() {
		return array(
			'equal' => array(
							'type' => 'equal',
							'label' => 'Equal',
							'criteria' => 'EQUAL'
						),
			'not_equal' => array(
					'type' => 'not_equal',
					'label' => 'Not equal',
					'criteria' => 'NOT_EQUAL'
					),
			'like' => array(
				'type' => 'like',
				'label' => 'Like (without wildcard %)',
				'criteria' => 'LIKE'
			),
			'like_wild' => array(
				'type' => 'like_wild',
				'label' => 'Like (with wildcard %)',
				'criteria' => 'LIKE'
			),
			'multilike_wild' => array(
				'type' => 'multilike_wild',
				'label' => 'List Multiple Like',
				'criteria' => 'MULTILIKE'
			),			
			'multinotlike_wild' => array(
				'type' => 'multinotlike_wild',
				'label' => 'List Multiple Not Like',
				'criteria' => 'MULTINOTLIKE'
			),			
			'in' => array(
				'type' => 'in',
				'label' => 'In list (separated with ",")',
				'criteria' => 'IN'
			),			
			'less' => array(
				'type' => 'less',
				'label' => 'Less than',
				'criteria' => 'LESS_THAN'
			),
			'less_equal' => array(
				'type' => 'less_equal',
				'label' => 'Equal or less than',
				'criteria' => 'LESS_EQUAL'
			),
			'greater' => array(
				'type' => 'greater',
				'label' => 'Greater than',
				'criteria' => 'GREATER_THAN'
			),
			'greater_equal' => array(
				'type' => 'greater_equal',
				'label' => 'Equal or greater than',
				'criteria' => 'GREATER_EQUAL'
			),
			'not_empty' => array(
				'type' => 'not_empty',
				'label' => 'Not empty',
				'criteria' => 'ISNOTEMPTY'
			),
			'empty' => array(
				'type' => 'empty',
				'label' => 'Empty',
				'criteria' => 'ISEMPTY'
			),
			'upcoming' => array(
				'type' => 'upcoming',
				'label' => 'Upcoming date',
				'criteria' => 'UPCOMING'
			),		
			'past' => array(
				'type' => 'past',
				'label' => 'Past date',
				'criteria' => 'PAST'
			)
		);
	}

	public function populateFromArray(array $params, $modified = true) {
		if (isset($params['id'])) {
			$this->setId($params['id']);
		}
		if (isset($params['module_name'])) {
			$this->setModuleName($params['module_name']);
		}
		if (isset($params['module_friendlyname'])) {
			$this->setModuleFriendlyname($params['module_friendlyname']);
		}
		if (isset($params['module_version'])) {
			$this->setModuleVersion($params['module_version']);
		}		
		if (isset($params['title_label'])) {
			$this->setTitleLabel($params['title_label']);
		}
		if (isset($params['created_by'])) {
			$this->setCreatedBy($params['created_by']);
		}
		if (isset($params['created_at'])) {
			$this->setCreatedAt($params['created_at']);
		}
		if (isset($params['updated_by'])) {
			$this->setUpdatedBy($params['updated_by']);
		}
		if (isset($params['updated_at'])) {
			$this->setUpdatedAt($params['updated_at']);
		}
		if (isset($params['extra_fields'])) {
			$this->setExtraFields($params['extra_fields']);
		} else {
			$this->setExtraFields(array());
		}	
		if (isset($params['structure'])) {
			$this->setStructure($params['structure']);
		} else {
			//$this->setStructure(array());
		}
		if (isset($params['delete_fields'])) {
			$this->setDeleteFields($params['delete_fields']);
		}
		if (isset($params['filters'])) {
			$this->setFilters($params['filters']);
		} else {
			$this->setFilters(array());
		}
		if (isset($params['parent_module'])) {
			$this->setParentModule($params['parent_module']);
		}
		if (isset($params['show_module'])) {
			$this->setShowModule($params['show_module']);
		}
		if (isset($params['api_enabled'])) {
			$this->setAPIEnabled($params['api_enabled']);
		}		
		if (isset($params['is_user_module'])) {
			$this->setIsUserModule($params['is_user_module']);
		}		
		if (isset($params['is_protected'])) {
			$this->setIsProtected($params['is_protected']);
		}		
		if (isset($params['files_path'])) {
			$this->setFilesPath($params['files_path']);
		}
		if (isset($params['module_logic'])) {
			$this->setModuleLogic($params['module_logic']);
		}		
		if (isset($params['templates_data'])) {
			$this->setTemplatesData($params['templates_data']);
		}		
		if (isset($params['admin_section'])) {
			$this->setAdminSection($params['admin_section']);
		}
		if (isset($params['extra_features'])) {
			$this->setExtraFeatures($params['extra_features']);
		}
		
		if(!$modified)
		{
			$this->is_modified = false;
		}
	}

	// DATABASE FUNCTIONS

	public static function getById($id) {
		$sql = 'SELECT * FROM '.cms_db_prefix().'module_mcfactory_modules
			WHERE id = ?';
		$values = array($id);
		$result = self::query($sql, $values);
		if ($result && $row = $result->FetchRow()) {
			$module = new self();
			$module->populateFromArray($row);
			return $module;
		} else {
			return false;
		}
	}

	public static function query($sql, $values = array()) {
		$db = cms_utils::get_db();
		return $db->execute($sql, $values);
	}

	public static function doSelect(MCFCriteria $c) {

		return MCFModuleRepository::doSelect($c);

		// $db = cms_utils::get_db();
		// $query = $c->buildQuery(cms_db_prefix().'module_mcfactory_modules');
		// $result = $db->execute($query, $c->values);
		// $modules = array();
		// while ($result && ($row = $result->FetchRow())) {
		// 	$module = new self();
		// 	$row['extra_fields'] = unserialize($row['extra_fields']);
		// 	$row['structure'] = unserialize($row['structure']);
		// 	$row['filters'] = unserialize($row['filters']);
		// 	$row['extra_features'] = unserialize($row['extra_features']);
		// 	$module->populateFromArray($row);
		// 	$module->is_modified = false;
		//     $modules[] = $module;
		// }
		// return $modules;
	}

	public static function doSelectOne(MCFCriteria $c) {
		return MCFModuleRepository::doSelectOne($c);
		// $c->setLimit(1);
		// $result = self::doSelect($c);
		// if (count($result) > 0) {
		// 	return $result[0];
		// } else {
		// 	return false;
		// }
	}

	public static function retrieveByPk($pk) {
		return MCFModuleRepository::retrieveByPk($pk);
		// $c = new MCFCriteria();
		// $c->add('id', $pk);
		// return self::doSelectOne($c);
	}

	public function forceUpdate() {
		$this->is_modified = true;
		$this->save();
	}

	public function save() {
		if (empty($this->id)) {
			return $this->insert();
		} else {
			return $this->update();
		}
	}

	protected function insert() {
		$db = cms_utils::get_db();
		$this->setId($db->GenID(cms_db_prefix().'module_mcfactory_modules_seq'));
		$query = 'INSERT INTO '.cms_db_prefix().'module_mcfactory_modules
			SET id = ?,
				module_name = ?,
				module_friendlyname = ?,
				module_version = 1,
				title_label = ?,
				created_by = ?,
				created_at = NOW(),
				updated_by = ?,
				updated_at = NOW(),
				extra_fields = ?,
				structure = ?,
				filters = ?,
				parent_module = ?,
				show_module = ?,
				api_enabled = ?,
				is_user_module = ?,
				is_protected = ?,
				files_path = ?,
				module_logic = ?,
				templates_data = ?,
				admin_section = ?,
				extra_features = ?
				';
		$db->Execute($query, array(
			$this->getId(),
			$this->getModuleName(),
			$this->getModuleFriendlyname(),
			$this->getTitleLabel(),
			get_userid(),
			get_userid(),
			serialize($this->getStructure()->getOldStructure()),
			serialize($this->getStructure()->getStructure()),
			serialize($this->getFilters()),
			$this->getParentModule(),
			$this->getShowModule(),
			$this->getAPIEnabled(),
			$this->getIsUserModule(),
			$this->getIsProtected(),
			$this->getFilesPath(),
			$this->getModuleLogic(),
			$this->getTemplatesData(),
			$this->getAdminSection(),
			serialize($this->getExtraFeatures())
		));
		$result = $db->Execute('SELECT LAST_INSERT_ID() AS id');
		$row = $result->FetchRow();
		$this->setId($row['id']);
		return true;
	}

	protected function update() {
		$db = cms_utils::get_db();
		$this->setModuleVersion($this->getModuleVersion() + 1);
		$query = 'UPDATE '.cms_db_prefix().'module_mcfactory_modules
			SET module_name = ?,
				module_friendlyname = ?,
				module_version = ?,
				title_label = ?,
				updated_by = ?,
				updated_at = NOW(),
				extra_fields = ?,
				structure = ?,
				filters = ?,
				parent_module = ?,
				show_module = ?,
				api_enabled = ?,
				is_user_module = ?,
				is_protected = ?,
				files_path = ?,
				module_logic = ?,
				templates_data = ?,
				admin_section = ?,
				extra_features = ?
			WHERE id = ?';
		$db->Execute($query, array(
			$this->getModuleName(),
			$this->getModuleFriendlyname(),
			$this->getModuleVersion(),
			$this->getTitleLabel(),
			get_userid(),
			serialize($this->getStructure()->getOldStructure()),
			serialize($this->getStructure()->getStructure()),
			serialize($this->getFilters()),
			$this->getParentModule(),
			$this->getShowModule(),
			$this->getAPIEnabled(),
			$this->getIsUserModule(),
			$this->getIsProtected(),
			$this->getFilesPath(),
			$this->getModuleLogic(),
			$this->getTemplatesData(),
			$this->getAdminSection(),
			serialize($this->getExtraFeatures()),
			$this->getId()
		));
		return true;
	}

	public function delete() {
		if ($this->getId()) {
			$query = 'DELETE FROM '.cms_db_prefix().'module_mcfactory_modules WHERE id = ?';
			$this->query($query, array($this->getId()));
			
			// Erase actions
			$actions = MCFModuleAction::doSelect(array('where' => array('module_id' => $this->getId())));
			foreach($actions as $action)
			{
				$action->delete();
			}
		}
		return true;
	}

	protected function publishFile($destination, $source = null, $extra_assigns = array()) {
		if (is_null($source)) {
			$source = $destination . '.tpl';
		}
		$tpl = new MCFTemplate($this->getModuleName(), $source, $destination);
		$tpl->assign('module', $this);
		$tpl->assign('table_name', strtolower($this->getModuleName()));
		
		foreach($extra_assigns as $key => $value)
		{
			$tpl->assign($key,$value);
		}
				
		if ($this->getParentModule())
		{
			$tpl->assign('has_admin','false');
		}
		elseif (!is_null($this->getShowModule()) && $this->getShowModule() == 0)
		{
			$tpl->assign('has_admin','false');
		}
		else
		{
			$tpl->assign('has_admin','true');
		}
		
		$tpl->assign('admin_section', ($this->getAdminSection() != '')?$this->getAdminSection():'content');
		$tpl->assign('parent_module', $this->getParentModule() ? MCFModule::retrieveByPk($this->getParentModule()) : false);
		$tpl->assign('child_modules', $this->getChildModules());
		$tpl->assign('is_protected', $this->getIsProtected());
		$tpl->assign('files_path', $this->getFilesPath());
		$tpl->assign('mcfactoryversion', cms_utils::get_module('MCFactory')->getVersion());
		$tpl->assign('title_label', $this->getTitleLabel());
		$tpl->assign('structure', $this->getStructure());
		$extra_fields = $this->getOldExtraFields();
		$fieldTypes = $this->getFieldTypes();
		
		$tpl->assign('first_tab_fieldset', $this->getStructure()->getFirstTabFieldset());
		
		// TODO : REFACTORISE THAT FOR STRUCTURE		
		foreach ($extra_fields as &$field) {
			$words = explode('_', $field['name']);
			foreach ($words as &$word) {
				$word = ucfirst($word);
			}
			unset($word);
			$field['place'] = $this->getStructure()->findField($field['name']);
			$field['camelcase'] = implode('', $words);
			$field['friendlyname'] = implode(' ', $words);
			$field['label'] = addslashes(($field['label']));
			$field['column_type'] = $fieldTypes[$field['type']]['column_type'];
			$field['form_type'] = $fieldTypes[$field['type']]['form_type'];
		
		  if($field['name'] == 'rate')
		  {
		    $tpl->assign('ratable', true);
		  }
		
			if(isset($field['options']))  {
				if((strpos($field['options'],';') !== false) || (strpos($field['options'],':') !== false))
				{
					$options = explode(';',$field['options']);
					foreach($options as $option)
					{
						$val = explode(':',$option);
						if (count($val) > 1)	$field['foptions'][$val[0]] = $val[1];
						unset($val);
					}
				}
				else
				{
					// Means we are old school ? 
					$field['foptions']['values'] = $field['options'];
				}
			}
			
			if ($field['type'] == 'select') {				
				$values = explode(',', $field['foptions']['values']);
				foreach ($values as &$value) {
					if(strpos($value,'=>') === false)
					{					
						$value = "'".trim($value)."' => '".trim($value)."'";	
					}
					else
					{
						list($key,$nvalue) = explode('=>',$value);
						$value = "'".trim($key)."' => '".trim($nvalue)."'";
					} 
				}
				unset($value);
				$field['select_options'] = 'array(' . implode(', ', $values) . ')';
			}
			if ($field['type'] == 'country') {
				if (isset($field['foptions']['options'])) {
					$whitelist = explode(',', $field['foptions']['options']);
				}
				elseif ($field['foptions']['values']) {
					$whitelist = explode(',', $field['foptions']['values']);
				} else {
					$whitelist = array_keys(MCFactory::$countries);
				}
				$values = array();
				$countries = MCFactory::$countries;
				asort($countries);
				foreach ($countries as $code => $country) {
					$country = addslashes($country);
					if (in_array($code, $whitelist)) {
						$values[] = "'".trim($code)."' => '".trim($country)."'";
					}
				}
				$field['select_options'] = 'array(' . implode(', ', $values) . ')';
			}
			if ($field['type'] == 'date') {
				if((strpos($field['options'],';') !== false))
				{					
					$options = explode(';',$field['options']);
				}
				else
				{					
					$options = explode('|',$field['options']);
				}
				$val = array();
				foreach ($options as $option)
				{
					$v = explode(':',$option);
					if (count($v) == 2)	$val[$v[0]] = $v[1];
				}
				$field['date_options'] = $val;
			}
			if ($field['type'] == 'image') {
				if(isset($field['foptions']['size']))
				{					
					list($field['image_width'], $field['image_height']) = explode('x', $field['foptions']['size']);
				}
				else
				{					
					list($field['image_width'], $field['image_height']) = explode('x', $field['options']);
				}
			}
			if ($field['type'] == 'module') {
				//$field['module_options'] = $field['foptions'];
				if (!isset($field['foptions']['module_name']))$field['foptions']['module_name'] = $field['foptions']['values'];
				//$field['foptions']['module_name'] = isset($field['foptions']['module_name'])?$field['foptions']['module_name']:$field['foptions']['values'];
			}
		}
		unset($field);
		$tpl->assign('extra_fields', $extra_fields);
		if ($this->getIsUserModule())
		{
			$tpl->assign('is_user_module', 1);
		}
		$tpl->assign('mcfactory_version', cms_utils::get_module('MCFactory')->GetVersion());
		$filters = $this->getFilters();
		$filterTypes = $this->getFilterTypes();
		foreach ($filters as &$filter) {
			$filter['criteria'] = $filterTypes[$filter['type']]['criteria'];
		}
		unset($filter);
		$tpl->assign('filters', $filters);
		
		// Extra features
		$tpl->assign('events', $this->getExtraFeatures()->getEvents());
		
		
		if($tpl->save() === false)
		{
			$this->publishing_status = false;
		}
	}

	public function copyFile($source)	{
		$config = cms_utils::get_config();
		$source_dir = $config['root_path'] . '/modules/MCFactory/smarty/templates';
		$destination_dir = $config['root_path'] . '/modules/' . $this->getModuleName();
		if(@copy($source_dir . DIRECTORY_SEPARATOR . $source, $destination_dir . DIRECTORY_SEPARATOR . $source) === false)
		{			
			$this->publishing_status = false;
		}
		//$this->cmsConfig['root_path'] . '/modules/' . $this->module . '/' . $this->destination, $contents;
	}

	public function publish() {
		$config = cms_utils::get_config();
		@mkdir($config['root_path'] . '/modules/' . $this->getModuleName());
    // @mkdir($config['root_path'] . '/modules/' . $this->getModuleName() . '/classes');
		@mkdir($config['root_path'] . '/modules/' . $this->getModuleName() . '/lang');
		@mkdir($config['root_path'] . '/modules/' . $this->getModuleName() . '/templates');
		@mkdir($config['root_path'] . '/modules/' . $this->getModuleName() . '/images');
		@mkdir($config['root_path'] . '/modules/' . $this->getModuleName() . '/rss');
		@mkdir($config['root_path'] . '/modules/' . $this->getModuleName() . '/lib');
		
		if(is_file($config['root_path'] . '/modules/' . $this->getModuleName() . '/classes/' . $this->getModuleName() . 'ObjectBase.class.php')) unlink($config['root_path'] . '/modules/' . $this->getModuleName() . '/classes/' . $this->getModuleName() . 'ObjectBase.class.php');
		if(is_file($config['root_path'] . '/modules/' . $this->getModuleName() . '/classes/' . $this->getModuleName() . 'Object.class.php')) unlink($config['root_path'] . '/modules/' . $this->getModuleName() . '/classes/' . $this->getModuleName() . 'Object.class.php');
		if(is_file($config['root_path'] . '/modules/' . $this->getModuleName() . '/classes/' . $this->getModuleName() . 'Views.class.php')) unlink($config['root_path'] . '/modules/' . $this->getModuleName() . '/classes/' . $this->getModuleName() . 'Views.class.php');
		if(is_dir($config['root_path'] . '/modules/' . $this->getModuleName() . '/classes')) unlink($config['root_path'] . '/modules/' . $this->getModuleName() . '/classes');
		
    // $this->publishFile('classes/'. $this->getModuleName() . 'ObjectBase.class.php', 'classes/MCFModuleObjectBase.class.php.tpl');
    // $this->publishFile('classes/'. $this->getModuleName() . 'Object.class.php', 'classes/MCFModuleObject.class.php.tpl');
    // $this->publishFile('classes/'. $this->getModuleName() . 'Views.class.php', 'classes/MCFModuleViews.class.php.tpl');
				
		$this->publishFile('lib/class.'. $this->getModuleName() . 'Base.php', 'lib/class.MCFModuleBase.php.tpl');
		$this->publishFile('lib/class.'. $this->getModuleName() . 'ObjectBase.php', 'lib/class.MCFModuleObjectBase.php.tpl');
		$this->publishFile('lib/class.'. $this->getModuleName() . 'Object.php', 'lib/class.MCFModuleObject.php.tpl');
		$this->publishFile('lib/class.'. $this->getModuleName() . 'Views.php', 'lib/class.MCFModuleViews.php.tpl');
		
		$this->publishFile('lang/en_US.php');
		$this->publishFile('templates/defaultadmin.items.tpl');
		$this->publishFile('templates/defaultadmin.templates.tpl');
		$this->publishFile('templates/admin.templates.tpl');
		$this->publishFile('templates/admin.template_edit.tpl');
		$this->publishFile('templates/defaultadmin.options.tpl');
		$this->publishFile('templates/edit.tpl');
		$this->publishFile('templates/edittemplate.tpl');
		$this->publishFile('templates/error.tpl');
		
//		$this->publishFile('templates/template.details.tpl');
//		$this->publishFile('templates/template.list.tpl');
		// $this->publishFile('templates/template.paginated.tpl');
		// $this->publishFile('templates/template.search.tpl');
		// $this->publishFile('templates/template.calendar.tpl');
		
		$this->publishFile('templates/frontend.default.tpl');
		$this->publishFile('templates/frontend.detail.tpl');
		$this->publishFile('templates/frontend.search.tpl');
		$this->publishFile('templates/frontend.calendar.tpl');
		$this->publishFile('templates/frontend.paginated.tpl');
		$this->publishFile('templates/frontend.tagcloud.tpl');
		$this->publishFile('templates/frontend.rss.tpl');
		$this->publishFile('templates/frontend.direct_email.tpl');
		
		$this->publishFile('rss/rss.css');
		$this->publishFile('rss/style.xsl');
		
		if ($this->getIsUserModule())
		{
				$this->publishFile('action.user_form.php');
				$this->publishFile('templates/frontend.user_form.tpl');
				$this->publishFile('templates/frontend.user_form_success.tpl');
		}
		
		$this->copyFile('images/icon.gif');
		$this->publishFile('action.api.php');
		$this->publishFile('action.calendar.php');
		$this->publishFile('action.default.php');
		$this->publishFile('action.defaultadmin.php');
		$this->publishFile('action.delete.php');
		$this->publishFile('action.moveup.php');
		$this->publishFile('action.movedown.php');
		$this->publishFile('action.detail.php');
		$this->publishFile('action.export.php');
		$this->publishFile('action.export_dat.php');
		$this->publishFile('action.export_db.php');
		$this->publishFile('action.import_db.php');
		$this->publishFile('action.edit.php');
		$this->publishFile('action.template_edit.php');
		$this->publishFile('action.template_delete.php');
		$this->publishFile('action.edittemplate.php');
		$this->publishFile('action.deletetemplate.php');
		$this->publishFile('action.geturl.php');
		$this->publishFile('action.publish.php');
		$this->publishFile('action.rss.php');
		$this->publishFile('action.search.php');
		$this->publishFile('action.template.php');
		$this->publishFile('action.count.php');
		$this->publishFile('action.url_for.php');
		$this->publishFile('action.link_to.php');
		$this->publishFile('action.assigntitles.php');
		$this->publishFile('index.html');
		$this->publishFile('function.defaultadmin.items.php');
		$this->publishFile('function.defaultadmin.templates.php');
		$this->publishFile('function.defaultadmin.options.php');
		$this->publishFile('function.defaultadmin.help.php');
		$this->publishFile('action.updateObjects.php');
		$this->publishFile('action.tagcloud.php');
		$this->publishFile('action.ical.php');
		$this->publishFile('action.download.php');
		
		// Actions
		$actions = MCFModuleAction::doSelect(array('where' => array('module_id' => $this->id)));
		foreach($actions as $action)
		{
			$this->publishFile('action.'.$action->name.'.php', 'action.module_action.php.tpl', array('action_obj' => $action));
		}
		
		$buttons = MCFModuleAction::sortByPlace($actions);
		
		$this->publishFile($this->getModuleName() . '.module.php', 'MCFModule.module.php.tpl', array('buttons' => $buttons));
		if (cms_utils::get_module($this->getModuleName())) {
			$db = cms_utils::get_db();
			foreach ($this->getDeleteFields() as $field) {
				$dict = NewDataDictionary($db);
				$sqlarray = $dict->DropColumnSQL(cms_db_prefix() . 'module_' . strtolower($this->getModuleName()), $field);
				$dict->ExecuteSQLArray($sqlarray);
			}
			$fieldTypes = $this->getFieldTypes();
			
			// var_dump($this->getOldExtraFields());
			
			foreach ($this->getOldExtraFields() as $field) {
				
				if (isset($field['new']) or ($db->execute('SELECT COUNT('.$field['name'].') FROM ' . cms_db_prefix().'module_'.strtolower($this->getModuleName())) === false)) {
					$dict = NewDataDictionary($db);
					$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_' . strtolower($this->getModuleName()), $field['name'] . ' ' . $fieldTypes[$field['type']]['column_type']);
					$dict->ExecuteSQLArray($sqlarray);
				}
				
				// Fix uppercase names
				if(isset($field['uppercase_name']))
				{
					if($db->execute('SELECT COUNT('.$field['uppercase_name'].') FROM ' . cms_db_prefix().'module_'.strtolower($this->getModuleName())) !== false)
					{						
							$dict = NewDataDictionary($db);
							$sqlarray = $dict->RenameColumnSQL(cms_db_prefix() . 'module_' . strtolower($this->getModuleName()), $field['uppercase_name'], $field['name']);							
							$dict->ExecuteSQLArray($sqlarray);
					}
				}
				
			}
		}
		if ($this->publishing_status === false)
		{
			return false;
		}

		return true;
	}

}

?>
