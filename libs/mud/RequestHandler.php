<?php  

namespace Mud;

/**
 * Much like a controller, a RequestHandler accesses the 
 * request and sends a response.
 */
abstract class RequestHandler {

    function __construct() {}

    /**
     * Echoes a reponse.
     *
     * @param  string $str
     * @return void
     */
    public function response($str) 
    {
        echo $str;
    }

    /**
     * Returns the value of a _GET or _POST variable matching $arg.
     *
     * @param  mixed $arg
     * @return mixed
     */
    public function request($arg) 
    {
        if (is_array($arg)) 
        {
            $array = array();

            foreach ($arg as $a) 
            {
                if (array_key_exists($a, $_GET) && ! empty($_GET[$a]))
                {
                    $array[$a] = $_GET[$a];
                }
                elseif (array_key_exists($a, $_POST) && !empty($_POST[$a]))
                {
                    $array[$a] = $_POST[$a];
                }
            }

            return $array;
        }
        else 
        {
            if (array_key_exists($arg, $_GET) && !empty($_GET[$arg]))
            {
                return $_GET[$arg];
            }
            elseif (array_key_exists($arg, $_POST) && !empty($_POST[$arg]))
            {
                return $_POST[$arg];
            }
        }

        return FALSE;
    }

    // Just for semantics.

    protected function get($args) {}

    protected function post($args) {}

    protected function put($args) {}

    protected function head($args) {}

    protected function options($args) {}

    protected function delete($args) {}
}