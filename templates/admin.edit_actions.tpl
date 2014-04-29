<div class="pageoverflow2">
    <p class="pagetext2">Specific module actions</p>
    <div class="pageinput2">
        <table style="width: 100%;" class="pagetable">
            <thead>
            <th>Name</th>
            <th>Is a public action</th>
            <th></th>
            </thead>
            <tbody id="actions">
            {foreach from=$actions item=action}
                <tr>
                    <td><a href="{$action->edit_url}">{$action->name}</a></td>
                    <td>{$action->is_public_icon}</td>
                    <td>{$action->edit_button} {$action->delete_url}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        <p><input type="button" id="action_add" value="Add action" onClick="return parent.location='{$add_action_url}'" /></p>
    </div>
</div>