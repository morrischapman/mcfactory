<?php

if (!cmsms()) exit;

if (!$this->CheckAccess()) {
	return $this->DisplayErrorPage();
}

if (isset($params['module_id']) && !empty($params['module_id'])) 
{
	$module = MCFModule::retrieveByPk($params['module_id']);
	if(is_object($module))
	{		
		$module->forceUpdate();
		if ($module->publish())
		{
			$this->Redirect($id, 'defaultadmin', $returnid);
		}		
		else
		{
			echo '<h3 style="color:red">Module path unwritable! Check permissions!</h3>';
		}
	}
}
echo '<p>' . $this->lang('error_occured_publishing') . '</p>'; 