<?php

class MCFModuleAdminTemplate extends MCFObject
{
    protected $module_id;
    protected $name;
    protected $template;

    protected static $table_fields = array(
        'module_id' => 'I',
        'name' => 'C(255)',
        'template' => 'XL'
    );
    const TABLE_NAME = 'module_mcfactory_module_admin_templates';
}