<ul>
{foreach from=$items item=item}
	<li><a href="{$item->detail_link}">{$item->title}</a></li>
{/foreach}
</ul>

{if $pager.has_to_paginate}
    <ul class="pager">
        {if $pager.first_page}<li class="first"><a href="{$pager.first_page}">First page</a></li>{/if}
        {if $pager.previous_page}<li class="previous"><a href="{$pager.previous_page}">Previous page</a></li>{/if}
        {if $pager.less}<li>...</li>{/if}
        {foreach from=$pager.pages item=page}
            <li>{$page}</li>
        {/foreach}
        {if $pager.more}<li>...</li>{/if}
        {if $pager.next_page}<li class="next"><a href="{$pager.next_page}">Next page</a></li>{/if}
        {if $pager.last_page}<li class="last"><a href="{$pager.last_page}">Last page</a></li>{/if}
    </ul>
{/if}