<?php

namespace Core;

class Hooks
{
    /**
     * all listeners grouped by event
     *
     */
    protected $events = array();

    /**
     * add a listener
     *
     * @param   string  event we are listening for
     * @param   string  unique name for this listener - so we can access again if needed
     * @param   string  callback function
     */
    public function startListening($event, $name, $callback)
    {
        $this->events[$event][$name] = $callback;
    }

    /**
     * stop listening
     *
     * @param   string  event we are listening for
     * @param   string  unique name for this listener - so we can access again if needed
     */
    public function stopListening($event, $name)
    {
        if (isset($this->events[$event][$name])) {

            unset($this->events[$event][$name]);
        }
    }

    /**
     * call all listeners
     *
     * @param   string  event to call
     * @param   mixed   multiple variables can be passed after the event name
     */
    public function callListeners()
    {
        $params = func_get_args();
        $event = $params['0'];
        unset($params['0']);

        if (! empty($this->events[$event])) {

            foreach ($this->events[$event] as $hook_name => $hook) {

                call_user_func_array($hook, $params);
            }
        }
    }
}
