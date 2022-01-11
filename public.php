<?php
require_once('db.php');
class plugins_textseo_public extends plugins_textseo_db
{
    protected $template, $data;

    /**
     * @access public
     * Constructor
     */
    public function __construct($t = null)
    {
        $this->template = $t instanceof frontend_model_template ? $t : new frontend_model_template();
        $formClean = new form_inputEscape();
        $this->data = new frontend_model_data($this,$this->template);
    }

    /**
     * Assign data to the defined variable or return the data
     * @param string $type
     * @param string|int|null $id
     * @param string $context
     * @param boolean $assign
     * @return mixed
     */
    private function getItems($type, $id = null, $context = null, $assign = true) {
        return $this->data->getItems($type, $id, $context, $assign);
    }

    /**
     * @param $params
     * @return mixed
     */
    public function getContent($params){
        return $this->getItems('textseo',array('iso' => $this->template->lang, 'type_to'=>$params),'one',false);
    }
}