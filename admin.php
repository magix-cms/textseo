<?php
require_once('db.php');
class plugins_textseo_admin extends plugins_textseo_db
{
    protected $controller,$data,$template, $message, $plugin,$modelLanguage,$collectionLanguage,$header, $tableaction;
    public $edit, $action, $id;
    public $tableconfig = array(
        'all' => array(
            'id_to',
            'type_to' => array('title' => 'name'),
            'content_to' => array('type' => 'bin', 'input' => null),
            'date_register'
        )
    );
    /**
     * Page title and content
     * @var array
     */
    public $content,$toData, $id_to;
    /**
     * constructeur
     */
    public function __construct($t = null){
        $this->template = $t ? $t : new backend_model_template;
        $this->message = new component_core_message($this->template);
        $this->header = new http_header();
        $this->data = new backend_model_data($this);
        $formClean = new form_inputEscape();
        $this->modelLanguage = new backend_model_language($this->template);
        $this->collectionLanguage = new component_collections_language();

        // --- Get
        if(http_request::isGet('controller')) $this->controller = $formClean->simpleClean($_GET['controller']);
        if (http_request::isGet('edit')) $this->edit = $formClean->numeric($_GET['edit']);
        if (http_request::isGet('action')) $this->action = $formClean->simpleClean($_GET['action']);
        elseif (http_request::isPost('action')) $this->action = $formClean->simpleClean($_POST['action']);
        if (http_request::isGet('tabs')) $this->tabs = $formClean->simpleClean($_GET['tabs']);
        if (http_request::isGet('ajax')) $this->ajax = $formClean->simpleClean($_GET['ajax']);
        if (http_request::isGet('offset')) $this->offset = intval($formClean->simpleClean($_GET['offset']));
        if (http_request::isGet('plugin')) $this->plugin = $formClean->simpleClean($_GET['plugin']);
        if (http_request::isGet('tableaction')) {
            $this->tableaction = $formClean->simpleClean($_GET['tableaction']);
            $this->tableform = new backend_controller_tableform($this,$this->template);
        }

        // --- Search
        if (http_request::isGet('search')) {
            $this->search = $formClean->arrayClean($_GET['search']);
            $this->search = array_filter($this->search, function ($value) { return $value !== ''; });
        }
        // POST
        if (http_request::isPost('toData')) $this->toData = $formClean->arrayClean($_POST['toData']);
        if (http_request::isGet('id')) $this->id_to = $formClean->simpleClean($_GET['id']);
        elseif (http_request::isPost('id')) $this->id_to = $formClean->simpleClean($_POST['id']);
        // - Content
        if (http_request::isPost('content')) {
            $array = $_POST['content'];
            foreach($array as $key => $arr) {
                foreach($arr as $k => $v) {
                    $array[$key][$k] = ($k == 'content_to') ? $formClean->cleanQuote($v) : $formClean->simpleClean($v);
                }
            }
            $this->content = $array;
        }

    }
    /**
     * Method to override the name of the plugin in the admin menu
     * @return string
     */
    public function getExtensionName()
    {
        return $this->template->getConfigVars('textseo_plugin');
    }
    /**
     * Assign data to the defined variable or return the data
     * @param string $type
     * @param string|int|null $id
     * @param string $context
     * @param boolean $assign
     * @param boolean $pagination
     * @return mixed
     */
    private function getItems($type, $id = null, $context = null, $assign = true, $pagination = false) {
        return $this->data->getItems($type, $id, $context, $assign, $pagination);
    }
    /**
     * @param $ajax
     * @return mixed
     * @throws Exception
     */
    public function tableSearch($ajax = false)
    {
        $this->modelLanguage->getLanguage();
        $defaultLanguage = $this->collectionLanguage->fetchData(array('context' => 'one', 'type' => 'default'));
        $results = $this->getItems('pages', array('default_lang' => $defaultLanguage['id_lang']), 'all',false,true);
        $params = array();

        if($ajax) {
            $params['section'] = 'pages';
            $params['idcolumn'] = 'id_to';
            $params['activation'] = false;
            $params['sortable'] = false;
            $params['checkbox'] = true;
            $params['edit'] = true;
            $params['dlt'] = true;
            $params['readonly'] = array();
            $params['cClass'] = 'plugins_textseo_admin';
        }

        $this->data->getScheme(array('mc_textseo','mc_textseo_content'),array('id_to','type_to','content_to','date_register'),$this->tableconfig['all']);

        return array(
            'data' => $results,
            'var' => 'pages',
            'tpl' => 'index.tpl',
            'params' => $params
        );
    }
    /**
     * @param $data
     * @return array
     */
    public function setItemData($data){
        $arr = array();

        foreach ($data as $page) {

            if (!array_key_exists($page['id_to'], $arr)) {
                $arr[$page['id_to']] = array();
                $arr[$page['id_to']]['id_to'] = $page['id_to'];
                $arr[$page['id_to']]['type_to'] = $page['type_to'];
                $arr[$page['id_to']]['date_register'] = $page['date_register'];
            }
            $arr[$page['id_to']]['content'][$page['id_lang']] = array(
                'id_lang'           => $page['id_lang'],
                //'iso_lang'          => $page['iso_lang'],
                'content_to'        => $page['content_to'],
                'published_to'      => $page['published_to']
            );
        }
        return $arr;
    }

    /**
     * Update data
     * @param $data
     * @throws Exception
     */
    private function add($data)
    {
        switch ($data['type']) {
            case 'page':
            case 'content':
                parent::insert(
                    array(
                        'context' => $data['context'],
                        'type' => $data['type']
                    ),
                    $data['data']
                );
                break;
        }
    }

    /**
     * Update data
     * @param $data
     * @throws Exception
     */
    private function upd($data)
    {
        switch ($data['type']) {
            case 'page':
            case 'content':
                parent::update(
                    array(
                        'context' => $data['context'],
                        'type' => $data['type']
                    ),
                    $data['data']
                );
                break;
        }
    }
    /**
     * Insertion de données
     * @param $data
     * @throws Exception
     */
    private function del($data)
    {
        switch($data['type']){
            case 'delPages':
                parent::delete(
                    array(
                        'type' => $data['type']
                    ),
                    $data['data']
                );
                $this->message->json_post_response(true,'delete',$data['data']);
                break;
        }
    }
    /**
     * @param $id
     * @return void
     * @throws Exception
     */
    private function saveContent($id)
    {
        $this->toData['id_to'] = $this->id_to;
        $this->toData['type_to'] = (!empty($this->toData['type_to']) ? $this->toData['type_to'] : NULL);

        foreach ($this->content as $lang => $content) {
            $content['id_lang'] = $lang;
            $content['id_to'] = $id;
            $content['content_to'] = (!empty($content['content_to']) ? $content['content_to'] : NULL);

            $contentPage = $this->getItems('content', array('id_to' => $id, 'id_lang' => $lang), 'one', false);

            if ($contentPage != null) {
                $this->upd(
                    array(
                        'type' => 'page',
                        'data' => $this->toData
                    )
                );
                $this->upd(
                    array(
                        'type' => 'content',
                        'data' => $content
                    )
                );
            } else {
                $this->add(
                    array(
                        'type' => 'content',
                        'data' => $content
                    )
                );
            }

        }
    }
    /**
     *
     */
    /**
     * @throws Exception
     */
    public function run(){
        if(isset($this->tableaction)) {
            $this->tableform->run();
        }
        elseif(isset($this->action)) {
            switch ($this->action) {
                case 'add':
                    if (isset($this->content)) {

                        $this->toData['type_to'] = !empty($this->toData['type_to']) ? $this->toData['type_to'] : NULL;
                        //print_r($this->toData);


                        $this->add(array(
                            'type' => 'page',
                            'data' => $this->toData
                        ));

                        $page = $this->getItems('root', null, 'one', false);

                        if ($page['id_to']) {
                            $this->saveContent($page['id_to']);
                            $this->message->json_post_response(true, 'add_redirect');
                        }
                    } else {
                        $this->modelLanguage->getLanguage();
                        $defaultLanguage = $this->collectionLanguage->fetchData(array('context' => 'one', 'type' => 'default'));
                        //$this->getItems('pagesSelect', array('default_lang' => $defaultLanguage['id_lang']), 'all');
                        $this->template->display('add.tpl');
                    }
                    break;
                case 'edit':
                    if (isset($this->id_to)) {
                        $this->saveContent($this->id_to);
                        $this->message->json_post_response(true, 'update', $this->content);
                    }
                    else {
                        // Initialise l'API menu des plugins core
                        $this->modelLanguage->getLanguage();
                        $setEditData = $this->getItems('page', array('edit'=>$this->edit),'all',false);
                        $setEditData = $this->setItemData($setEditData);
                        $this->template->assign('page',$setEditData[$this->edit]);
                        $this->template->display('edit.tpl');
                    }
                    break;
                case 'delete':
                    if(isset($this->id_to)) {
                        $this->del(
                            array(
                                'type'=>'delPages',
                                'data'=>array(
                                    'id' => $this->id_to
                                )
                            )
                        );
                    }
                    break;
            }
        }else{
            $this->modelLanguage->getLanguage();
            $defaultLanguage = $this->collectionLanguage->fetchData(array('context'=>'one','type'=>'default'));
            $this->getItems('pages',array('default_lang'=>$defaultLanguage['id_lang']),'all',true,true);
            $this->data->getScheme(array('mc_textseo','mc_textseo_content'),array('id_to','type_to','content_to','date_register'),$this->tableconfig['all']);
            $this->template->display('index.tpl');
        }
    }
}
?>