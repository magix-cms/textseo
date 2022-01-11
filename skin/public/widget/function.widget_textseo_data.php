<?php
function smarty_function_widget_textseo_data($params, $template){
    $collection = new plugins_textseo_public();
    if(isset($params['type']) && !empty($params['type'])){
        $template->assign('textseo',$collection->getContent($params['type']));
    }
}