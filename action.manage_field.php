<?php

if (!cmsms()) exit;

if (!$this->CheckAccess()) {
	return $this->DisplayErrorPage();
}

if(isset($params['module_id']))
{
	$module = MCFModule::retrieveByPk($params['module_id']);
	if (
			$module 
			&& isset($params['tab_key']) 
			&& $params['tab_key'] != '' 
			&& isset($params['fieldset_key']) 
			&& $params['fieldset_key'] != ''
			)
	{
		$structure = $module->getStructure();
		
		if(isset($params['label']) && $params['label'] != '')
		{
			$structure->addField($params['tab_key'], $params['fieldset_key'], $params);
			$module->setStructure($structure);			
			$module->save();	
		}
		
		if(isset($params['field']) && $params['field'] != '')
		{
			$structure->removeField($params['field']);
			$module->setStructure($structure);			
			$module->save();
		}
			
		$this->Redirect($id, 'edit', $returnid, array('module_id' => $module->getId(), 'active_fields_tab' => $params['tab_key']));
		
	}
}
var_dump($params);
echo 'An error occurred. Did you save your module first ? Please try again.';