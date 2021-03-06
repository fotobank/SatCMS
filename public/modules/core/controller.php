<?php

/**
 * Core contoller
 * 
 * @package    core
 * @author     Golovkin Vladimir <r00t@skillz.ru> http://www.skillz.ru
 * @copyright  SurSoft (C) 2008
 * @version    $Id: controller.php,v 1.7.6.1 2012/09/14 06:20:57 Vova Exp $
 */
 
/**
* @package core
*/

class core_controller extends module_controller {

    /**
     * i18n all constants
     */
    function action_api_editor_i18n() {

        $this->renderer
            ->set_ajax_answer(
                $this->context->i18n->get_words()
            )
            ->ajax_flush();
    }

    /**
     * Editor nav menu
     */
    function action_api_editor_menu() {

        /** @var tf_editor $ed */
        $ed = core::lib('editor');

        $menu = array();

        $modules = array_merge(array('core' => $this->core), core::modules()->as_array());

        $default_module = 'sat';

        foreach ($modules as $module) {

            if ($this->get_user()->level >= $module->config->get('editor.level', 50)) {
                $menu [$module->get_name()]= $module->get_editor_actions();
                if ($module->config->get('editor.default', false)) {
                    $default_module = $module->get_name();
                }
            }
        }

        $menuNormalized = array();

        foreach ($menu as $key => $actions) {

            $submenuNormalized = array();

            if (!empty($actions)) {

                foreach ($actions as $subKey => $subMenu) {

                    if (!empty($subMenu['url'])) {
                        $subMenu['url'] = $ed->make_url($subMenu['url'], 1);
                    }

                    if (!empty($subMenu['title'])) {
                        $subMenu['title'] = $this->core->i18n->T(array($key, $subMenu['title']));
                    }

                    $submenuNormalized []=
                        !$subMenu
                        ? array() //separator
                        : array_merge(array(
                            'id' => $subKey),
                             $subMenu
                            )
                    ;
                }

                $_menuNormalized = array(
                    'id' => $key,
                    'title' => $this->core->i18n->T(array($key, '_name')),
                    'actions' => $submenuNormalized
                );

                if ($key == $default_module) {
                    $_menuNormalized['default'] = true;
                }

                $menuNormalized []= $_menuNormalized;


            }
        }

        return JsonResponse::create($menuNormalized);

        /*
            $this->renderer
            ->set_ajax_answer($menuNormalized)
            ->ajax_flush();
        */



    }

}

