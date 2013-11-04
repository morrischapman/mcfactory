<?php
if (!cmsms()) exit;
/** @var $this MCFactory */

if (!$this->CheckAccess()) {
    return $this->DisplayErrorPage();
}

if(isset($params['cancel']))
{
    $this->Redirect($id, 'defaultadmin');
}

$form = new CMSForm($this->GetName(), $id, 'module_wizard', $returnid);
//$form->setButtons(array('submit'));
$form->setLabel('submit', $this->Lang('Continue'));

if(isset($params['module_id']))
{
    $module = MCFModuleRepository::retrieveByPk($params['module_id']);
}
else
{
    $module = new MCFModule();

    $form->setWidget('module_name', 'text', array('label' => 'Module name', 'tips' => 'Choose a name for your module', 'placeholder' => 'Acme Directory'));

    $template = $this->GetFileResource('admin.module_wizard_step1.tpl');
}

$smarty->assign('form', $form);

echo $smarty->fetch($template,'|mcfactory_wizard_' . md5(serialize($params)),'');