{include file="language/brick/dropdown-lang.tpl"}
<div class="row">
    <form id="add_textseo" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=add" method="post" class="validate_form add_form col-ph-12 col-lg-8 collapse in">
        <div class="row">
            <div class="col-ph-12 col-md-6">
                <div class="form-group">
                    <label for="type_to">{#type_to#}</label>
                    <input type="text" class="form-control" id="type_to" name="toData[type_to]" value="" placeholder="{#ph_type_to#|ucfirst}">
                </div>
            </div>
        </div>
        <div class="tab-content">
            {foreach $langs as $id => $iso}
                <fieldset role="tabpanel" class="tab-pane{if $iso@first} active{/if}" id="lang-{$id}">
                    <div class="form-group">
                        <label for="content[{$id}][content_to]">{#content#|ucfirst} :</label>
                        <textarea name="content[{$id}][content_to]" id="content[{$id}][content_to]" class="form-control mceEditor">{call name=cleantextarea field=$page.content[{$id}].content_to}</textarea>
                    </div>
                </fieldset>
            {/foreach}
        </div>
        <div id="submit" class="col-ph-12 col-md-6">
            <button class="btn btn-main-theme pull-right" type="submit" name="action" value="add">{#save#|ucfirst}</button>
        </div>
    </form>
</div>