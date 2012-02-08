<?php

namespace Mud;

class Template {

    /**
     * Render a template file using h2o.
     *
     * @param  string $file
     * @param  string $vars
     * @uses   h2o
     * @return string
     */
    public static function render($file, $vars = array())
    {
        use h2o;

        $vars['base_url'] = BASE_URL;
        $h2o = new h2o($file);

        return $h2o->render($vars);
    }

    /**
     * Wrapper for h2o::addFilter()
     *
     * @param  array $array
     * @uses   h2o
     * @return void
     */
    public static function add_filter($array)
    {
        use h2o;

        h2o::addFilter($array);
    }
    
    /**
     * Render a template file parsing all variables and PHP.
     *
     * @param  string $file
     * @param  string $vars
     * @return string
     */
    public static function parse($file, $vars = array()) 
    {
        extract($vars);

        ob_start();
        include $file;
        $out = ob_get_contents();
        ob_end_clean();

        return $out;
    }
}