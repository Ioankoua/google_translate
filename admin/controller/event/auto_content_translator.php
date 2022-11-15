<?php
namespace Opencart\Admin\Controller\Extension\AutoContentTranslator\Event;

class AutoContentTranslator extends \Opencart\System\Engine\Controller {

    private $codename = 'auto_content_translator';
    private $route = 'extension/auto_content_translator/module/auto_content_translator';

    public function view_common_header_after($route, &$data, &$output) {
  
        if($this->config->get($this->codename . '_status')){
            if ($this->user->isLogged()) {
                $this->load->model($this->route);
                $this->load->model('localisation/language');
                $this->config->load('../../extension/auto_content_translator/system/config/auto_content_translator/auto_content_translator');
                $languages = $this->model_localisation_language->getLanguages();
                $default_language = $this->model_localisation_language->getLanguageByCode($this->config->get('config_language'));
                $language_ids = array();
    
                foreach ($languages as $language) {
                    if ($language['language_id'] != $default_language['language_id']) {
                        $language_ids[] = $language['language_id'];
                    }
                }
    
                $fields = $this->config->get('fields');    
                $data = array();
                $data['fields'] = implode("', '", $fields);
                $data['url'] = $this->url->link($this->route . '|translate', '&user_token=' . $this->session->data['user_token'], true);
                $data['language_ids'] = implode('_', $language_ids);
                $data['default_id'] =  $default_language['language_id'];
                
                $button = $this->load->view($this->route . '/translate_buttons', $data);

                $this->load->library('extension/auto_content_translator/dv_simple_html_dom');
                $this->extension_auto_content_translator_dv_simple_html_dom->load((string)$output, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);
                $this->extension_auto_content_translator_dv_simple_html_dom->find('#header', 0)->innertext = $button . $this->extension_auto_content_translator_dv_simple_html_dom->find('#header', 0)->innertext;
                $output = (string)$this->extension_auto_content_translator_dv_simple_html_dom;
            }
        }
    }

    public function controller_common_header_before() {
        if($this->config->get($this->codename . '_status')){
            $this->document->addStyle(HTTP_CATALOG. 'extension/auto_content_translator/admin/view/stylesheet/auto_content_translator/auto_content_translator.css');
        }
    }

    public function view_catalog_category_form_after(&$route, &$data, &$output) {
        $this->createButtonTranslate($output, 'category');
    }

    public function view_catalog_product_form_after(&$route, &$data, &$output) {
        $this->createButtonTranslate($output, 'product');
    }

    public function view_catalog_information_form_after(&$route, &$data, &$output) {
        $this->createButtonTranslate($output, 'information');
    }

    public function view_extension_d_blog_module_post_form_after(&$route, &$data, &$output) {
        $this->createButtonTranslate($output, 'blog_post');
    }

    public function view_catalog_option_form_after(&$route, &$data, &$output) {
        $this->createButtonTranslate($output, 'option');
    }

    private function createButtonTranslate(&$output, $type) {
        $this->load->language($this->route);
        if($this->config->get($this->codename . '_status')){
            if($type == 'option'){
                $this->load->model($this->route);
                $data['max_option'] = $this->model_extension_auto_content_translator_module_auto_content_translator->getMaxOption();
            }
            $data['url'] = $this->url->link($this->route . '|translateData', '&user_token=' . $this->session->data['user_token'], true);
            $data['text_translate'] = $this->language->get('text_translate');
            $data['languages'] = $this->model_localisation_language->getLanguages();
            $data['auto_content_translator_google_codes'] = $this->config->get($this->codename . '_google_codes');

            $button = $this->load->view($this->route . '/button_translate_'.$type, $data);
            $this->load->library('extension/auto_content_translator/dv_simple_html_dom');
            $this->extension_auto_content_translator_dv_simple_html_dom->load((string)$output, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);
            $this->extension_auto_content_translator_dv_simple_html_dom->find('.page-header .float-end', 0)->innertext = $button . $this->extension_auto_content_translator_dv_simple_html_dom->find('.page-header .float-end', 0)->innertext;
            $output = (string)$this->extension_auto_content_translator_dv_simple_html_dom;
          
        }
    }

}