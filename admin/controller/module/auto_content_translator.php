<?php
namespace Opencart\Admin\Controller\Extension\AutoContentTranslator\Module;

class AutoContentTranslator extends \Opencart\System\Engine\Controller {
    
    private $codename = 'auto_content_translator';
    private $route = 'extension/auto_content_translator/module/auto_content_translator';
    private $error = array();

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model($this->route);
        $this->load->language($this->route);
        $this->load->model('setting/setting');
        $this->load->model('setting/event');
        $this->load->model('localisation/language');
        $this->config->load('../../extension/auto_content_translator/system/config/auto_content_translator/auto_content_translator');
    }

    public function index() {

        $this->document->addScript(HTTP_CATALOG. 'extension/auto_content_translator/admin/view/javascript/bootstrap_switch/js/bootstrap-switch.min.js');
        $this->document->addStyle(HTTP_CATALOG. 'extension/auto_content_translator/admin/view/javascript/bootstrap_switch/css/bootstrap-switch.css');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->model_setting_setting->editSetting($this->codename, $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', '&user_token=' . $this->session->data['user_token'], true));
		}

        $this->document->setTitle(strip_tags($this->language->get('heading_title_main')));

        // Breadcrumbs
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', '&user_token=' . $this->session->data['user_token'], true)
            );
    
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('marketplace/extension', '&user_token=' . $this->session->data['user_token'], true)
            ); 

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link($this->route, '&user_token=' . $this->session->data['user_token'], true)
        );

        if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

        if (isset($this->error[$this->codename . '_key'])) {
			$data['error_key'] = $this->error[$this->codename . '_key'];
		} else {
			$data['error_key'] = '';
		}

        // Action
        $data['action'] = $this->url->link($this->route, '&user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', '&user_token=' . $this->session->data['user_token'], true);

        // Variable
        $data['route'] = $this->route;
        $data['codename'] = $this->codename;
        $data['user_token'] =  $this->session->data['user_token'];
        $data['text_token'] = 'user_token';
        $data['status'] = $this->model_setting_setting->getValue($this->codename . '_status');
        $data['field_empty'] = $this->model_setting_setting->getValue($this->codename . '_field_empty');
        $data['background_image'] = HTTP_CATALOG . "extension/auto_content_translator/admin/view/image/auto_content_translator/bg.svg";
        $data['icon_image'] = HTTP_CATALOG . "extension/auto_content_translator/admin/view/image/auto_content_translator/icon.svg";

        // Config
        $data['google_codes'] = $this->config->get('google_codes');
        $data['types'] = $this->config->get('types');
        $data['auto_content_translator_key'] = $this->config->get($this->codename . '_key');
        $data['auto_content_translator_code'] = $this->config->get($this->codename . '_code');
        $data['auto_content_translator_translation_fields'] = $this->config->get($this->codename . '_translation_fields');
        $data['auto_content_translator_google_codes'] = $this->config->get($this->codename . '_google_codes');

        // Setup
        $data['setup'] = $this->isSetup();
        $data['setup_link'] = $this->url->link($this->route.'|setup', '&user_token=' . $this->session->data['user_token'], true);

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($this->route, $data));
    }

    public function translate() {
        $json = array();

        $translate = true;

        if (isset($this->request->post['text'])) {
            $text = html_entity_decode($this->request->post['text'], ENT_QUOTES);
        } else {
            $translate = false;
        }

        if (isset($this->request->post['to'])) {
            $to_language_id = (int)$this->request->post['to'];
        } else {
            $translate = false;
        }

        if ($translate && $text && $to_language_id) {
            $from_language = $this->model_localisation_language->getLanguage($this->config->get('config_language_id'));
            $to_language = $this->model_localisation_language->getLanguage($to_language_id);
            $json['text'] = $this->model_extension_auto_content_translator_module_auto_content_translator->translate($to_language['code'], $text);
        }

        $this->response->setOutput(json_encode($json));
    }

    public function massTranslate() {
        $json = array();

        $fields = [];
        foreach ($this->config->get($this->codename . '_translation_fields')[$this->request->post['type']] as $key => $value) {
            $fields[] = $value['enabled'];
        }
        $fields = array_filter($fields);
        
        if(empty($fields)) {
            $json['error'] = $this->language->get('error_fields');
        }

        if(!isset($json['error'])) {
            if (isset($this->request->post['source_language_id'])) {
                $from_language_id = (int)$this->request->post['source_language_id'];
            }

            if (isset($this->request->post['target_language_id'])) {
                $to_language_id = (int)$this->request->post['target_language_id'];
            }

            if (isset($this->request->post['type'])) {
                $type = $this->request->post['type'];
            }

            if (isset($this->request->get['start'])) {
                $start = (int)$this->request->get['start'];
            } else {
                $start = 0;
            }

            if (isset($this->request->get['start_from_id'])) {
                $start_from_id = (int)$this->request->get['start_from_id'];
            } else {
                $start_from_id = 0;
            }

            $total = $this->model_extension_auto_content_translator_module_auto_content_translator->getTotal($type, $from_language_id, $start_from_id);

            $this->model_extension_auto_content_translator_module_auto_content_translator->updateData($type, $from_language_id, $to_language_id, $start, $start_from_id);

            if (($start + 10) < $total) {
                $json['next'] = 'index.php?route=extension/auto_content_translator/module/auto_content_translator|massTranslate&user_token=' . $this->session->data['user_token'] . '&start=' . ($start + 10) . '&start_from_id=' .$start_from_id;

                $json['logs'] = sprintf($this->language->get('text_logs'), $start + 10, $total);
            } else {
                $json['logs'] = sprintf($this->language->get('text_logs'), $total, $total);
                $json['done'] = $this->language->get('text_done');
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    public function translateData() {

        $json = array();
        $fields = [];
       
        $type = $this->request->post['for_translate'];
        
        foreach ($this->config->get($this->codename . '_translation_fields')[$type] as $key => $value) {
            $fields[] = $value['enabled'];
        }
        $fields = array_filter($fields);

        if(empty($fields)) {
            $json['error'] = $this->language->get('error_fields');
        }

        if(!isset($json['error'])) {
            $translation_options = $this->config->get($this->codename . '_translation_fields');
            $json = $this->model_extension_auto_content_translator_module_auto_content_translator->translateData($this->request->post, $translation_options[$type]);

        }
        
        $this->response->setOutput(json_encode($json));
    }

    public function setup() {
        $translation_fields = $this->config->get('translation_fields');
        $this->installEvents();
        $languages = $this->model_localisation_language->getLanguages();
        foreach ($languages as $language) {
            $google_codes[$language['language_id']] = 'en';
        }

        $data = array();
        $data[$this->codename.'_status'] = 1;
        $data[$this->codename.'_setup'] = 1;
        $data[$this->codename.'_translation_fields'] = $translation_fields;
        $data[$this->codename.'_google_codes'] = $google_codes;

        $this->session->data['success'] = $this->language->get('success_setup');
        $this->model_setting_setting->editSetting($this->codename, $data);
        $this->response->redirect($this->url->link($this->route, '&user_token=' . $this->session->data['user_token'], true));
    }

    public function isSetup() {
        $status = $this->model_setting_setting->getValue($this->codename . '_setup');

        if($status) {
            return true;
        }
        
        return false;
    }

    public function installEvents() {
        foreach ($this->config->get('events') as $trigger => $action) {
            if(VERSION == '4.0.0.0'){
                $this->model_setting_event->addEvent($this->codename, '', $trigger, $action);
            }else{
                $this->model_setting_event->addEvent([
                    'code' => $this->codename, 
                    'description' => '', 
                    'trigger' => $trigger, 
                    'action' =>$action,
                    'status' => 1,
                    'sort_order' => 0
                ]);
            }
        }
    }

    public function install() {
       
    }
    
    public function uninstall() {
        $this->model_setting_setting->deleteSetting($this->codename);
        $this->model_setting_event->deleteEventByCode($this->codename);
    }

    protected function validate() {        
        if (!$this->user->hasPermission('modify', $this->route)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post[$this->codename . '_key']) {
            $this->error[$this->codename . '_key'] = $this->language->get('error_key');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }
    
        return !$this->error;
    }
}
