<div class="pageoverflow2">
    <p class="pagetext2">Module name:</p>
    <p class="pageinput2">{$input_module_friendlyname}</p>
</div>

<div class="pageoverflow2">
    <p class="pagetext2">Title label:</p>
    <p class="pageinput2">{$input_title_label}</p>
</div>

{if isset($form_module_options)}
    {if $form_module_options->hasErrors()}<div style="color: red;">{$form_module_options->showErrors()}</div>{/if}

    {$form_module_options->showWidgets()}

    {$form_module_options->renderFieldsets()}

    <hr />
{/if}

<div class="pageoverflow2">
    <p class="pagetext2">Restore module templates</p>
    <div class="pageinput2">
        <p><input type="button" id="templates_restore" value="Restore templates" onClick="return parent.location='{$templates_restore_url}'" /></p>
    </div>
</div>
<hr />
<div class="pageoverflow2">
    <p class="pagetext2">Created by:</p>
    <p class="pageinput2">{$created_by} on {$created_at}</p>
</div>

<div class="pageoverflow2">
    <p class="pagetext2">Last updated by:</p>
    <p class="pageinput2">{$updated_by} on {$updated_at}</p>
</div>