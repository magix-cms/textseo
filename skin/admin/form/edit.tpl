{include file="language/brick/dropdown-lang.tpl"}
<div class="row">
    <form id="edit_textseo" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=edit&edit={$page.id_to}" method="post" class="validate_form edit_form col-ph-12 col-lg-8">
        <div class="row">
            <div class="col-ph-12 col-md-3">
                <div class="form-group">
                    <label for="type_to">{#type_to#}</label>
                    <input type="text" class="form-control" id="type_to" name="toData[type_to]" value="{$page.type_to}" placeholder="{#ph_type_to#|ucfirst}">
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
        <input type="hidden" id="id_to" name="id" value="{$page.id_to}">
        <button class="btn btn-main-theme pull-right" type="submit" name="action" value="edit">{#save#|ucfirst}</button>
    </form>
</div>