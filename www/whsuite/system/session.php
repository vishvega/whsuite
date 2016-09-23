<?php

namespace Core;

use \Aura\Session\Manager as SessionManager;
use \Aura\Session\SegmentFactory;
use \Aura\Session\CsrfTokenFactory;
use \Aura\Session\Randval;
use \Aura\Session\Phpfunc;

class Session
{
    /**
     * main session manager
     */
    protected $session = null;

    /**
     * session data segment
     */
    protected $data = null;

    /**
     * flash data segment
     */
    protected $flash = null;

    /**
     * constructor - setup the session handler
     */
    public function __construct()
    {
        $this->session = new SessionManager(
            new SegmentFactory,
            new CsrfTokenFactory(
                new Randval(
                    new Phpfunc
                )
            ),
            $_COOKIE
        );

        // create the data and the flash data segments
        $this->data = $this->session->newSegment('_data');
        $this->flash = $this->session->newSegment('_flash');
    }

    /**
     * set session data
     *
     * @param   string  name of the value to set
     * @param   mixed   data to the session
     */
    public function setData($name, $value)
    {
        $this->data->{$name} = $value;
    }

    /**
     * get session data
     *
     * @param   string  name of the value to get
     * @return  mixed   value of the data return / null if not found
     */
    public function getData($name)
    {
        if (isset($this->data->{$name})) {
            return $this->data->{$name};
        } else {
            return null;
        }
    }

    /**
     * set flash data
     *
     * @param   string  name of the value to set
     * @param   mixed   data to the session
     */
    public function setFlash($name, $value)
    {
        $this->flash->setFlash($name, $value);
    }

    /**
     * get flash data
     *
     * @param   string  name of the value to get
     * @return  mixed   value of the data return / null if not found
     */
    public function getFlash($name)
    {
        return $this->flash->getFlash($name);
    }

    /**
     * check flash data exists
     *
     * @param   string  name of the value to get
     * @return  bool
     */
    public function hasFlash($name)
    {
        return $this->flash->hasFlash($name);
    }

    /**
     * clear the flash data
     *
     */
    public function clearFlash()
    {
        $this->flash->clearFlash();
    }

    /**
     * function overloading to prevent having to use App::get('session')->session->getCsrfToken();
     * can just do App::get('session')->getCsrfToken();
     *
     * @param   string  method name we are trying to load
     * @param   array   array of params to pass to the method
     * @retun   mixed   return of the method
     */
    public function __call($name, $params)
    {
        if (method_exists($this->session, $name)) {
            $method_reflection = new \ReflectionMethod($this->session, $name);

            return $method_reflection->invokeArgs($this->session, $params);
        } else {
            throw new \Exception('Fatal Error: Function '.$name.' does not exist!');
        }
    }
}
