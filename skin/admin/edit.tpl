{extends file="layout.tpl"}
{block name='head:title'}{#edit_textseo#|ucfirst}{/block}
{block name='body:id'}textseo{/block}
{block name='article:header'}
    <h1 class="h2"><a href="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}" title="Afficher la liste des packs textes">{#textseo#|ucfirst}</a></h1>
{/block}
{block name='article:content'}
    {if {employee_access type="edit" class_name=$cClass} eq 1}
    <div class="panels row">
        <section class="panel col-ph-12 col-md-12">
            {if $debug}
                {$debug}
            {/if}
            <header class="panel-header">
                <h2 class="panel-heading h5">{#edit_textseo#|ucfirst}</h2>
            </header>
            <div class="panel-body panel-body-form">
                <div class="mc-message-container clearfix">
                    <div class="mc-message"></div>
                </div>
                {include file="form/edit.tpl" controller="textseo"}
            </div>
        </section>
    </div>
    {include file="modal/delete.tpl" data_type='textseo' title={#modal_delete_title#|ucfirst} info_text=true delete_message={#delete_pages_message#}}
    {include file="modal/error.tpl"}
    {/if}
{/block}
{block name="foot" append}
    {include file="section/footer/editor.tpl"}
    {capture name="scriptForm"}{strip}
        /{baseadmin}/min/?f=
        libjs/vendor/tabcomplete.min.js,
        libjs/vendor/livefilter.min.js,
        libjs/vendor/bootstrap-select.min.js,
        libjs/vendor/filterlist.min.js
    {/strip}{/capture}
    {script src=$smarty.capture.scriptForm type="javascript"}
{/block}