<h2>Extra features</h2>
<p><input type="button" value="Add event" onClick="return parent.location='{$add_event}'" /></p>

{if $events|@count > 0}
    <div class="pageoverflow2">
        <p class="pagetext2">Specific module events</p>
        <div class="pageinput2">
            <table style="width: 100%;" class="pagetable">
                <thead>
                <th>Module name</th>
                <th>Event name</th>
                <th></th>
                </thead>
                <tbody id="actions">
                {foreach from=$events item=event}
                    <tr>
                        <td>{$event.module_name}</td>
                        <td>{$event.event_name}</td>
                        <td>{$event.edit} {$event.delete}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
            <p><input type="button" id="action_add" value="Add action" onClick="return parent.location='{$add_action_url}'" /></p>
        </div>
    </div>
{/if}
<p><input type="button" value="Add event" onClick="return parent.location='{$add_event}'" /></p>