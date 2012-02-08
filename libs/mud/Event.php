<?php 

namespace Mud;

/**
 * Provided by Danillo César de O. Melo
 * https://github.com/danillos/fire_event/blob/master/Event.php
 */
class Event {

    private static $instance;
  
    /**
     * Hooks are pairs of events => functions to call.
     *
     * @var array
     */
    private $hooks = array();
  
    private function __construct() { }
    private function __clone() { }
  
    /**
     * Add a function to an event.
     *
     * @param  string $event_name the name of the event to hook into
     * @param  string $fn the function to call
     * @return void
     */
    public static function hook($event_name, $fn) 
    {
        $instance = self::get_instance();
        $instance->hooks[$event_name][] = $fn;
    }
  
    /**
     * Trigger/fire/invoke an event, calling all hooked functions.
     *
     * @param  string $event_name the name of the event to invoke
     * @param  mixed  $params Parameters to pass to the hooked functions
     * @return void
     */
    public static function fire($event_name, $params = NULL) 
    {
        $instance = self::get_instance();

        if (array_key_exists($event_name, $instance->hooks)) 
        {
            foreach ($instance->hooks[$event_name] as $fn) 
            {
                if (is_array($fn) || is_string($fn))
                {
                    call_user_func_array($fn, array(&$params));
                }
            }
        }
    }

    /**
     * Get the Event instance.
     *
     * @return Event
     */
    public static function get_instance() 
    {
        if ( ! isset(self::$instance)) 
        {
            self::$instance = new Event();
        }

        return self::$instance;
    }
}