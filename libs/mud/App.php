<?php

namespace Mud;

/**
 * A surefure way to handle requests.
 */
class App {

    protected $method,
              $route,
              $handler;
    
    /**
     * Get the $r (route) variable and set the Request Method.
     */
    function __construct() 
    {
        $this->method = strtolower( $_SERVER['REQUEST_METHOD'] );
        $this->route  = (isset( $_GET['r'] )) ? trim( $_GET['r'], '/\\' ) : '/';
    }

    /**
     * Matches regex to a class (handler), calling that class.
     *
     * @param  array $rules a set of regex URLs to class handlers
     * @return void
     */
    public function react($rules) 
    {
        $is_matched = FAlSE;

        foreach ($rules as $rule => $handler) 
        {
            // Append some shit so index.php is cleaner
            $rule = str_replace('/', '\/', $rule);
            $rule = '^' . $rule . '\/?$';

            if (preg_match("/$rule/i", $this->route, $matches)) 
            {
                $is_matched = TRUE;

                if ($this->load_handler($handler))
                {
                    $this->handler = new $handler;

                    if (method_exists($this->handler, $this->method)) 
                    {
                        call_user_func_array(array($this->handler, $this->method), $matches);
                    }
                }
                else 
                {
                    // Failed to load handler (not 404)
                    // TODO: Proper error handling
                    print('Failed to load handler:');
                    var_dump($handler);
                }         
            }
        }

        if ( ! $is_matched) 
        {
            // 4044 shizzz
            // TODO: a way to override the 404 handler
            $handler = 'ErrorHandler';

            if ($this->load_handler($handler)) 
            {
                $this->handler = new $handler;

                if (method_exists($this->handler, $this->method)) 
                {
                    call_user_func_array(array($this->handler, $this->method), array('404 Not Found'));
                }
            }
        }
    }

    /**
     * Loads a handler file based on class name.
     *
     * @param  string $handler class name of handler
     * @return bool
     */
    private function load_handler($handler) 
    {
        $file = 'handlers/'.$handler.'.php';

        if ( ! class_exists($handler, FALSE))
        {
            if (is_file($file))
            {
                include $file;
                
                return class_exists($handler, FALSE);
            }

            return FALSE;
        }

        return TRUE;
    }
}

?>