{if isset(${{$module->getModuleName()}})}
<ul>
	{foreach from=${{$module->getModuleName()}} item=item}
	<li><a href="{{ldelim}}{{$module->getModuleName()}} action="url_for" maction="detail" item_id=$item->getId() detailpage=$detailpage title=$item->title{{rdelim}}">{$item->title}</a></li>
	{/foreach}
</ul>
{/if}