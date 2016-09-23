<?php

namespace Core;

use App;
use Illuminate\Support\Str;

class View
{
    /**
     * store the variables
     */
    protected $vars = array();

    /**
     * main theme directory to in
     */
    protected $theme_dir = null;

    /**
     * current theme
     */
    protected $theme = null;

    /**
     * hold all the protected vars
     */
    protected $protectedVars = array(
        'view',
        'assets',
        'router'
    );

    /**
     * display a template
     * pass to fetch but tell it to echo rather then return
     *
     * @param   string  template to display
     */
    public function display($template)
    {
        if (App::check('assets')) {
            App::get('assets')->buildAssets();
        }

        $this->fetch($template, false, true);
    }

    /**
     * fetch and parse a template
     * then return or display
     *
     * @param   string  template
     * @param   array   array of any data to make available on the fly
     * @param   bool    whether to display the template or not
     * @return  string  parsed php template
     */
    public function fetch($template, $vars = array(), $display = false)
    {
        $template = $this->findTemplate($template);

        // extract the variables
        extract($this->vars);

        // check for any variables to be added on the fly
        if ($vars !== false && is_array($vars)) {
            extract($vars);
        }

        if ($display) {
            include_once($template);
            return;
        } else {
            // we're just fetching the contents, get and eval them so we can return the output
            $tpl = file_get_contents($template);

            ob_start();
            eval("?>".$tpl);
            $tpl = ob_get_contents();
            ob_end_clean();

            return $tpl;
        }
    }

    /**
     * find a template
     *
     * @param   string  template
     * @return  string  template we need to load / including path
     */
    public function findTemplate($template)
    {
        if (Str::contains($template, '::')) {
            // split out the addon / template

            $ex = explode('::', $template, 2);
            $addon = trim($ex['0']);
            $template = trim($ex['1']);

            // build the last part of the template path
            $addon_view = $addon . DS . 'views' . DS . $template;

            // check the overriding theme directory
            $tpl = $this->getThemeDir() . DS . $this->getTheme() . DS;
            $tpl .= $addon_view;

            if (file_exists($tpl)) {
                return $tpl;
            } else {
                // check the main addon directory
                $tpl = ADDON_DIR . DS;
                $tpl .= $addon_view;

                if (file_exists($tpl)) {
                    return $tpl;
                } else {
                    throw new \Core\Exceptions\TemplateNotFoundException($template);
                }
            }
        } else {
            $tpl = $this->getThemeDir() . DS . $this->getTheme() . DS . 'views' . DS . $template;

            if (file_exists($tpl)) {
                return $tpl;
            } else {
                throw new \Core\Exceptions\TemplateNotFoundException($template);
            }
        }
    }


    /**
     * set the variables to the view
     *
     * @param   mixed   either string if second parameter passed or associative array of variables to assign
     * @param   mixed
    */
    public function set()
    {
        $args = func_get_args();

        if (! is_array($args['0'])) {
            $args['0'] = array(
                $args['0'] => $args['1']
            );
        }

        foreach ($args['0'] as $key => $value) {
            if (in_array(strtolower($key), $this->protectedVars) && isset($this->vars[$key])) {
                continue;
            }

            $this->vars[$key] = $value;
        }
    }

    /**
     * system display - used by exceptions when something goes wrong
     * check for any overwritten templates but then check the system templates folder
     *
     * @param   string  template to load
     */
    public function systemDisplay($template)
    {
        $tpl = $this->getThemeDir() . DS . $this->getTheme() . DS . 'views' . DS . $template;
        if (! file_exists($tpl)) {
            $tpl = $this->getThemeDir() . DS . 'system' . DS . $template;
        }

        // extract the variables
        extract($this->vars);

        include_once($tpl);
        return;
    }

    /**
     * set the theme directory
     *
     * @param   string  path to the theme directory we want to use
     * @return  bool
     */
    public function setThemeDir($theme_dir)
    {
        if (file_exists($theme_dir)) {
            if (Str::endsWith($theme_dir, array('/', '\\'))) {
                $theme_dir = substr($theme_dir, 0, -1);
            }

            $this->theme_dir = $theme_dir;

            return true;
        } else {
            return false;
        }
    }

    /**
     * get the theme directory
     *
     * @return  string  the theme directory we have set
     */
    public function getThemeDir()
    {
        return $this->theme_dir;
    }

    /**
     * set the theme
     *
     * @param   string  theme to set
     * @return  bool
     */
    public function setTheme($theme)
    {
        if (file_exists($this->theme_dir . DS . $theme)) {
            $this->theme = $theme;

            return true;
        } else {
            return false;
        }
    }

    /**
     * get the theme
     *
     * @return  string  current set theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * add a new protected var
     *
     * @param string $var The var to protect
     * @return bool
     */
    public function protectVar($var)
    {
        $this->protectedVars[] = $var;
        return true;
    }

    /**
     * remove a protected var
     *
     * @param string $var The var to remove from protection
     * @return bool
     */
    public function unprotectVar($var)
    {
        $key = array_search($var, $this->protectedVars);
        if ($key === false) {
            return false;
        }

        unset($this->protectedVars[$key]);
        return true;
    }
}
