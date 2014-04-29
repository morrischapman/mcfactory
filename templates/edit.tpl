{*<div>*}
    {*{if isset($module_design)}*}
        {*<a href="{$module_design}" class="btn">Design</a>*}
    {*{/if}*}
{*</div>*}



{$form_start}
{$input_module_id}

<p style="float: right;">
	{$publish_button}
	{$save_button}
	{$cancel_button}
</p>

<h1>{$module->getModuleFriendlyName()}</h1>


{$tabs->headers()}
{$tabs->tabs()}

<p style="text-align: right;">
	{$publish_button}
	{$save_button}
	{$cancel_button}
</p>

</form>



