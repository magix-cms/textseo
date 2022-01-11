<?php

/**
 * Class plugins_textseo
 */
class plugins_textseo_db
{
    /**
     * @param $config
     * @param bool $params
     * @return mixed|null
     * @throws Exception
     */
    public function fetchData($config, $params = false)
    {
        if (!is_array($config)) return '$config must be an array';

        $sql = '';
        $dateFormat = new component_format_date();

        if ($config['context'] === 'all') {
            switch ($config['type']) {
                case 'pages':
                    $limit = '';
                    if ($config['offset']) {
                        $limit = ' LIMIT 0, ' . $config['offset'];
                        if (isset($config['page']) && $config['page'] > 1) {
                            $limit = ' LIMIT ' . (($config['page'] - 1) * $config['offset']) . ', ' . $config['offset'];
                        }
                    }

                    $sql = "SELECT p.id_to, p.type_to, c.content_to, p.date_register
						FROM mc_textseo AS p
							JOIN mc_textseo_content AS c USING ( id_to )
							JOIN mc_lang AS lang ON ( c.id_lang = lang.id_lang )
							WHERE c.id_lang = :default_lang
							GROUP BY p.id_to" . $limit;

                    if (isset($config['search'])) {
                        $cond = '';
                        if (is_array($config['search']) && !empty($config['search'])) {
                            $nbc = 1;
                            foreach ($config['search'] as $key => $q) {
                                if ($q !== '') {
                                    $cond .= 'AND ';
                                    $p = 'p' . $nbc;
                                    switch ($key) {
                                        case 'id_to':
                                            $cond .= 'p.' . $key . ' = :' . $p . ' ';
                                            break;
                                        case 'type_to':
                                            $cond .= "p.".$key." LIKE CONCAT('%', :".$p.", '%') ";
                                            break;
                                        case 'date_register':
                                            $q = $dateFormat->date_to_db_format($q);
                                            $cond .= "p." . $key . " LIKE CONCAT('%', :" . $p . ", '%') ";
                                            break;
                                    }
                                    $params[$p] = $q;
                                    $nbc++;
                                }
                            }

                            $sql = "SELECT p.id_to, p.type_to, c.content_to, p.date_register
								FROM mc_textseo AS p
									JOIN mc_textseo_content AS c USING ( id_to )
									JOIN mc_lang AS lang ON ( c.id_lang = lang.id_lang )
									WHERE c.id_lang = :default_lang $cond
									GROUP BY p.id_to" . $limit;
                        }
                    }
                    break;
                case 'page':
                    $sql = 'SELECT p.*,c.*,lang.*
							FROM mc_textseo AS p
							JOIN mc_textseo_content AS c USING(id_to)
							JOIN mc_lang AS lang ON(c.id_lang = lang.id_lang)
							WHERE p.id_to = :edit';
                    break;
                case 'lastPages':
                    $sql = "SELECT p.id_to, c.content_to, p.date_register
							FROM mc_textseo AS p
							JOIN mc_textseo_content AS c USING ( id_to )
							JOIN mc_lang AS lang ON ( c.id_lang = lang.id_lang )
							WHERE c.id_lang = :default_lang
							GROUP BY p.id_to 
							ORDER BY p.id_to DESC
							LIMIT 5";
                    break;
                case 'textseo':
                    $sql = "SELECT *
							FROM `mc_textseo` as `ms`
							LEFT JOIN `mc_textseo_content` `msc` on `ms`.`id_to` = `msc`.`id_to`
							LEFT JOIN `mc_lang` `ml` on `msc`.`id_lang` = `ml`.`id_lang`
							WHERE `ml`.`iso_lang` = :iso";
                    break;
            }

            return $sql ? component_routing_db::layer()->fetchAll($sql, $params) : null;

        } elseif ($config['context'] === 'one') {
            switch ($config['type']) {
                case 'root':
                    $sql = 'SELECT * FROM mc_textseo ORDER BY id_to DESC LIMIT 0,1';
                    break;
                case 'content':
                    $sql = 'SELECT * FROM `mc_textseo_content` WHERE `id_to` = :id_to AND `id_lang` = :id_lang';
                    break;
                case 'page':
                    $sql = 'SELECT * FROM mc_textseo WHERE `id_to` = :id_to';
                    break;
                case 'pageLang':
                    $sql = 'SELECT p.*,c.*,lang.*
							FROM mc_textseo AS p
							JOIN mc_textseo_content AS c USING(id_to)
							JOIN mc_lang AS lang ON(c.id_lang = lang.id_lang)
							WHERE p.id_to = :id
							AND lang.iso_lang = :iso';
                    break;
                case 'textseo':
                    $sql = 'SELECT p.*,c.*
							FROM mc_textseo AS p
							JOIN mc_textseo_content AS c USING(id_to)
							JOIN mc_lang AS lang ON(c.id_lang = lang.id_lang)
							WHERE p.type_to = :type_to
							AND lang.iso_lang = :iso';
                    break;
            }

            return $sql ? component_routing_db::layer()->fetch($sql, $params) : null;
        }
    }
    /**
     * @param $config
     * @param array $params
     * @return bool|string
     */
    public function insert($config,$params = array())
    {
        if (!is_array($config)) return '$config must be an array';

        $sql = '';

        switch ($config['type']) {
            case 'page':
                $sql = "INSERT INTO `mc_textseo`(type_to, date_register) 
                        VALUE (:type_to, NOW())";
                break;
            case 'content':
                $sql = 'INSERT INTO `mc_textseo_content`(id_to, id_lang, content_to, last_update) 
				  		VALUES (:id_to, :id_lang, :content_to, NOW())';
                break;
        }

        if($sql === '') return 'Unknown request asked';

        try {
            component_routing_db::layer()->insert($sql,$params);
            return true;
        }
        catch (Exception $e) {
            return 'Exception reçue : '.$e->getMessage();
        }
    }
    /**
     * @param $config
     * @param array $params
     * @return bool|string
     */
    public function update($config,$params = array())
    {
        if (!is_array($config)) return '$config must be an array';

        $sql = '';

        switch ($config['type']) {
            case 'page':
                $sql = 'UPDATE mc_textseo 
						SET 
						    type_to = :type_to

                		WHERE id_to = :id_to';
                break;
            case 'content':
                $sql = 'UPDATE mc_textseo_content 
						SET 
						    content_to = :content_to

                		WHERE id_to = :id_to 
                		AND id_lang = :id_lang';
                break;
        }

        if($sql === '') return 'Unknown request asked';

        try {
            component_routing_db::layer()->update($sql,$params);
            return true;
        }
        catch (Exception $e) {
            return 'Exception reçue : '.$e->getMessage();
        }
    }
    /**
     * @param $config
     * @param array $params
     * @return bool|string
     */
    public function delete($config, $params = array())
    {
        if (!is_array($config)) return '$config must be an array';
        $sql = '';

        switch ($config['type']) {
            case 'delPages':
                $sql = 'DELETE FROM mc_textseo 
						WHERE id_to IN ('.$params['id'].')';
                $params = array();
                break;
        }

        if($sql === '') return 'Unknown request asked';

        try {
            component_routing_db::layer()->delete($sql,$params);
            return true;
        }
        catch (Exception $e) {
            return 'Exception reçue : '.$e->getMessage();
        }
    }
}