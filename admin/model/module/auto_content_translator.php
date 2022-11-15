<?php
namespace Opencart\Admin\Model\Extension\AutoContentTranslator\Module;

class AutoContentTranslator extends \Opencart\System\Engine\Model {

    private $codename = 'auto_content_translator';

    public function translate($to, $text) {

        if (trim($text) == '') {
            return '';
        }

        try {
            $headers = array(
                'Content-Type: application/json'
            );

            $this->config->load('../../extension/auto_content_translator/system/config/auto_content_translator');
            $url = $this->config->get('url_google');

            $this->load->model('setting/setting');
            $key = $this->model_setting_setting->getValue('auto_content_translator_key');

            $values = array(
                'key'    => $key,
                'target' => $to,
                'q'      => $text
            );

            $handle = curl_init($url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $values);
            curl_setopt($handle, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: GET'));
            $response = curl_exec($handle);                         
            curl_close($handle);

            $result = json_decode($response, true);

            if($result['data']['translations'][0]['translatedText']) {
                return $result['data']['translations'][0]['translatedText'];
            } else {
                return 'Error!';
            }
        } catch(exception $exception){
            $this->log->write('AUTO TRANSLATE ERROR: ' . $exception->getMessage());
            
            return $exception->getMessage();
        }
    }

    public function getTotal($type, $from_language_id, $start_from_id = 0) {
        if($type == 'blog_post'){
            $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "bm_post_description WHERE post_id >= '" . (int)$start_from_id . "' AND language_id = '" . (int)$from_language_id . "'");
        }else{
            $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . $type . "_description WHERE " . $type . "_id >= '" . (int)$start_from_id . "' AND language_id = '" . (int)$from_language_id . "'");
        }
        
        return $query->row['total'];
    }

    public function updateData($type, $from_language_id, $to_language_id, $start, $start_from_id) {
        if($type == 'blog_post'){
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bm_post_description WHERE post_id >= '" . $start_from_id . "' AND language_id = '" . $from_language_id . "' ORDER BY post_id ASC LIMIT " . (int)$start . ",10");
        }else{
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . $type . "_description WHERE " . $type . "_id >= '" . $start_from_id . "' AND language_id = '" . $from_language_id . "' ORDER BY " . $type . "_id ASC LIMIT " . (int)$start . ",10");
            $query_to_lang = $this->db->query("SELECT * FROM " . DB_PREFIX . $type . "_description WHERE " . $type . "_id >= '" . $start_from_id . "' AND language_id = '" . $to_language_id . "' ORDER BY " . $type . "_id ASC LIMIT " . (int)$start . ",10");
        }
        
        $to_language_code = $this->config->get($this->codename . '_google_codes')[$to_language_id];
        $_translation_fields = $this->config->get($this->codename . '_translation_fields')[$type];
        $count = 0;
        foreach ($query->rows as $result) {
      
            $result['to_lang'] = $query_to_lang->rows[$count];
            $result['to_lang_code'] = $to_language_code;
            $translation_data = $this->translateData($result, $_translation_fields);
            $count++;
            if($type == 'blog_post'){
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bm_post_description WHERE post_id = '" . (int)$result['post_id'] . "' AND language_id = '" . $to_language_id . "'");
            }else{
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . $type . "_description WHERE " . $type . "_id = '" . (int)$result[$type . '_id'] . "' AND language_id = '" . $to_language_id . "'");
            }
            
            if(!empty($translation_data)){
                if (!$query->num_rows) {
                    $translation_entities = $this->createSqlParts($translation_data);
                    if($type == 'blog_post'){
                        $this->db->query("INSERT INTO " . DB_PREFIX . "bm_post_description SET post_id = '" . (int)$result['post_id'] . "', " . $translation_entities . " language_id = '" . $to_language_id . "'");
                    }else{
                        $this->db->query("INSERT INTO " . DB_PREFIX . $type . "_description SET " . $type . "_id = '" . (int)$result[$type .'_id'] . "', " . $translation_entities . " language_id = '" . $to_language_id . "'");
                    }
                } else {
                    $translation_entities = $this->createSqlParts($translation_data);
                    if($type == 'blog_post'){
                        $this->db->query("UPDATE " . DB_PREFIX . "bm_post_description SET " . $translation_entities . " WHERE post_id = '" . (int)$result['post_id'] . "' AND language_id = '" . $to_language_id . "'");
                    }else{
                        $this->db->query("UPDATE " . DB_PREFIX . $type . "_description SET " . $translation_entities . " WHERE " . $type . "_id = '" . (int)$result[$type .'_id'] . "' AND language_id = '" . $to_language_id . "'");
                    }
                }
            }
        }
    }

    private function createSqlParts($translation_data) {
        $sql_parts = array();
        if(isset($translation_data['name'])) {
            $sql_parts[] = "name = '" . $this->db->escape($translation_data['name']) . "'";
        }
        if(isset($translation_data['title'])) {
            $sql_parts[] = "title = '" . $this->db->escape($translation_data['title']) . "'";
        }
        if(isset($translation_data['short_description'])) {
            $sql_parts[] = "short_description = '" . $this->db->escape($translation_data['short_description']) . "'";
        }
        if(isset($translation_data['description'])) {
            $sql_parts[] = "description = '" . $this->db->escape($translation_data['description']) . "'";
        }
        if(isset($translation_data['meta_title'])) {
            $sql_parts[] = "meta_title = '" . $this->db->escape($translation_data['meta_title']) . "'";
        }
        if(isset($translation_data['meta_description'])) {
            $sql_parts[] = "meta_description = '" . $this->db->escape($translation_data['meta_description']) . "'";
        }
        if(isset($translation_data['meta_keyword'])) {
            $sql_parts[] = "meta_keyword = '" . $this->db->escape($translation_data['meta_keyword']) . "'";
        }
        if(isset($translation_data['tag'])) {
            $sql_parts[] = "tag = '" . $this->db->escape($translation_data['tag']) . "'";
        }
        
        return $translation_entities = implode(', ', $sql_parts);
    }

    public function translateData($data, $translation_options) {
        $translations = array();
        $this->load->model('setting/setting');
        $field_empty = $this->model_setting_setting->getValue($this->codename . '_field_empty');

        if(isset($translation_options['name']) && $translation_options['name']['enabled']) {
            if($field_empty){
                if(empty($data['to_lang']['name'])){
                    $translations['name'] = $this->translate($data['to_lang_code'], $data['name']);
                }
            }else{
                $translations['name'] = $this->translate($data['to_lang_code'], $data['name']);
            }
        }

        if(isset($translation_options['title']) && $translation_options['title']['enabled']) {
            if($field_empty){
                if(empty($data['to_lang']['title'])){
                    $translations['title'] = $this->translate($data['to_lang_code'], $data['title']);
                }
            }else{
                $translations['title'] = $this->translate($data['to_lang_code'], $data['title']);
            }
        }

        if(isset($translation_options['short_description']) && $translation_options['short_description']['enabled']) {
            if($field_empty){
                if(empty($data['to_lang']['short_description'])){
                    $translations['short_description'] = html_entity_decode($this->translate($data['to_lang_code'], $data['short_description']), ENT_QUOTES);
                }
            }else{
                $translations['short_description'] = html_entity_decode($this->translate($data['to_lang_code'], $data['short_description']), ENT_QUOTES);
            }
        }

        if(isset($translation_options['description']) && $translation_options['description']['enabled']) {
            if($field_empty){
                if(empty($data['to_lang']['description'])){
                    $translations['description'] = $this->descriptionTranslate($data);
                }
            }else{
                $translations['description'] = $this->descriptionTranslate($data);
            }
        }

        if(isset($translation_options['meta_title']) && $translation_options['meta_title']['enabled']) {
            if($field_empty){
                if(empty($data['to_lang']['meta_title'])){
                    $translations['meta_title'] = $this->translate($data['to_lang_code'], $data['meta_title']);
                }
            }else{
                $translations['meta_title'] = $this->translate($data['to_lang_code'], $data['meta_title']);
            }
        }

        if(isset($translation_options['meta_description']) && $translation_options['meta_description']['enabled']) {
            if($field_empty){
                if(empty($data['to_lang']['meta_description'])){
                    $translations['meta_description'] = $this->translate($data['to_lang_code'], $data['meta_description']);
                }
            }else{
                $translations['meta_description'] = $this->translate($data['to_lang_code'], $data['meta_description']);
            }
        }

        if(isset($translation_options['meta_keyword']) && $translation_options['meta_keyword']['enabled']) {
            if($field_empty){
                if(empty($data['to_lang']['meta_keyword'])){
                    $translations['meta_keyword'] = $this->translate($data['to_lang_code'], $data['meta_keyword']);
                }
            }else{
                $translations['meta_keyword'] = $this->translate($data['to_lang_code'], $data['meta_keyword']);
            }
        }

        if(isset($translation_options['tag']) && $translation_options['tag']['enabled']) {
            if($field_empty){
                if(empty($data['to_lang']['tag'])){
                    $translations['tag'] = $this->translate($data['to_lang_code'], $data['tag']);
                }
            }else{
                $translations['tag'] = $this->translate($data['to_lang_code'], $data['tag']);
            }
        }
 
        if(isset($translation_options['values']) && $translation_options['values']['enabled']) {       
            $count = count($data['values']);        
            if($field_empty){
                for ($i=0; $i < $count; $i++) { 
                    if(empty($data['to_lang']['values'][$i])){
                        $translations['values'][$i] = $this->translate($data['to_lang_code'], $data['values'][$i]);
                    }else{
                        $translations['values'][$i] = $data['empty_values'][$i];
                    }
                }
            }else{
                foreach ($data['values'] as $key) {
                    $translations['values'][] = $this->translate($data['to_lang_code'], $key);
                }
            }
        }

        return $translations;
    }

    private function descriptionTranslate($data) {
        $decoded = html_entity_decode($data['description'], ENT_QUOTES);
        
        if (strlen($decoded) > 10000) {
            $middle = strrpos(substr($decoded, 0, floor(strlen($decoded) / 2)), ' ') + 1;
            $string1 = substr($decoded, 0, $middle);
            $string2 = substr($decoded, $middle);
                
             $string1 = $this->translate($data['to_lang_code'], $string1);
             $string2 = $this->translate($data['to_lang_code'], $string2);
            return $translations['description'] = $string1 . ' ' . $string2;
        } else {
            return $translations['description'] = $this->translate($data['to_lang_code'], $decoded);
        }
    }

    public function getMaxOption() {
        $query = $this->db->query("SELECT option_id FROM " . DB_PREFIX . "option_value_description");
        $options = array_merge($query->rows);
        foreach ($options as $key => $value) {
           foreach ($value as $index => $id) {
            $arr[] = $id;
           }
        }
        $result = array_count_values($arr);
        return $result = max($result);
    }

}