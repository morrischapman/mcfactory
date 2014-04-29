<div class="pageoverflow2">
    <p class="pagetext2">Filters:</p>
    <div class="pageinput2">
        <div id="filters">
            {foreach from=$filters key=i item=filter}
                <p class="filters_item">
                    <label for="{$module_id}filters_{$i}_name">Name:</label> <input type="text" id="{$module_id}filters_{$i}_name" name="{$module_id}filters[{$i}][name]" value="{$filter.name}" size="30" />
                    <label for="{$module_id}filters_{$i}_field">Field:</label> <input type="text" id="{$module_id}filters_{$i}_field" name="{$module_id}filters[{$i}][field]" value="{$filter.field}" size="10" />
                    <label for="{$module_id}filters_{$i}_type">Type:</label> <select id="{$module_id}filters_{$i}_type" name="{$module_id}filters[{$i}][type]">
                        {foreach from=$filter_types item=filter_type}
                            <option value="{$filter_type.type}" {if $filter.type == $filter_type.type}selected="selected"{/if}>{$filter_type.label}</option>
                        {/foreach}
                    </select>
                    <a href="#" class="filter_remove">Remove filter</a>
                </p>
            {/foreach}
        </div>
        <p><button id="filter_add">Add filter</button></p>
    </div>
</div>


<script type="text/javascript">
    (function($) {ldelim}
        var prefix = "{$module_id}";
        var options = '{foreach from=$filter_types item=filter_type}<option value="{$filter_type.type}">{$filter_type.label}</option>{/foreach}';
        {literal}

        $("a.filter_remove").click(function() {
            $(this).parents("p.filters_item").remove();
            return false;
        });

        var i = $("#filters p.filters_item").length+1;

        $("#filter_add").button().click(function() {
            $('<p class="filters_item"><label for="'+prefix+'filters_'+i+'_name">Name:</label> <input type="text" id="'+prefix+'filters_'+i+'_name" name="'+prefix+'filters['+i+'][name]" size="30" /> Field: <input type="text" id="'+prefix+'filters['+i+'][field]" name="'+prefix+'filters['+i+'][field]" size="10" /> <label for="'+prefix+'filters['+i+'][type]">Type:</label> <select id="'+prefix+'filters['+i+'][type]" name="'+prefix+'filters['+i+'][type]" class="filter_type">' + options + '</select> <a href="#" class="filter_remove">Remove filter</a></p>')
                    .find("a.filter_remove").click(function() {
                        $(this).parents("p.filters_item").remove();
                        return false;
                    })
                    .end()
                    .appendTo("#filters");
            i++;
            return false;
        });
        {/literal}
        {rdelim})(jQuery);
</script>