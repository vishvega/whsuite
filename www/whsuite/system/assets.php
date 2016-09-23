<?php

namespace Core;

use App;
use Illuminate\Support\Str;

class Assets
{
    /**
     * store the javascript files to load
     */
    protected $js = array();

    /**
     * store the style files to load
     */
    protected $css = array();

    /**
     * below vars - stored here and view to stop need to access VIEW class everytime
     */

    /**
     * main theme directory to in
     */
    protected $theme_dir = null;

    /**
     * current theme
     */
    protected $theme = null;

    /**
     * render an image
     *
     * @param   string  image to load
     * @param   array   (optional) extras for the image tag + whether to render img tag or not
     * @return  string  either path to image, or image with full img tag
     */
    public function image($image, $options = array())
    {
        $img_src = $this->findAsset('img', $image);

        if (! isset($options['render']) || ! $options['render']) {

            return $img_src;
        }

        // unset the render element and add the img src to the options
        unset($options['render']);

        $options['src'] = $img_src;

        $img = '<img';

        foreach ($options as $attr => $value) {

            $img .= ' ' . $attr . '="' . $value . '"';
        }

        $img .= '>';

        return $img;
    }

    /**
     * render a stylesheet file
     *
     * @param   string  stylesheet to load
     * @param   array   array of options for the 'link' tag
     * @return  string  stylesheet html
     */
    public function style($stylesheet, $options = array())
    {
        $asset_path = array(
            'href' => $this->findAsset('css', $stylesheet)
        );

        $asset = array_merge($asset_path, $options);

        return $this->buildCss(array($asset));
    }

    /**
     * render a js file
     *
     * @param   string  js to load
     * @param   array   array of options for the 'script' tag
     * @return  string  js html
     */
    public function script($script, $options = array())
    {
        $asset_path = array(
            'src' => $this->findAsset('js', $script)
        );

        $asset = array_merge($asset_path, $options);

        return $this->buildJs(array($asset));
    }

    /**
     * add a script to the array
     *
     * @param   string|array    Script files to set
     */
    public function addScript($script_array)
    {
        if (! is_array($script_array)) {

            $script_array = array(
                array(
                    'file' => $script_array
                )
            );
        } else {

            foreach ($script_array as $k => $script) {

                if (is_array($script) && isset($script['file'])) {
                    continue;
                }

                $script_array[$k] = array(
                    'file' => $script
                );
            }
        }

        $this->addAssets('js', $script_array);
    }

    /**
     * add a style to the array
     *
     * @param   string|array    Style files to set
     * @param   string  (optional) media type, default is 'screen'
     */
    public function addStyle($style_array, $media = 'screen')
    {
        if (! is_array($style_array)) {

            $style_array = array(
                array(
                    'file' => $style_array,
                    'media' => $media
                )
            );
        } else {

            foreach ($style_array as $k => $style) {

                if (is_array($style) && isset($style['file'])) {
                    continue;
                }

                $style_array[$k] = array(
                    'file' => $style
                );
            }
        }

        $this->addAssets('css', $style_array);
    }


    /**
     * process the assets and add to the view
     *
     */
    public function buildAssets()
    {
        $scripts = $styles = '';

        // process stylesheets
        if (! empty($this->css)) {

            $styles = $this->buildCss();
        }

        // process scripts
        if (! empty($this->js)) {

            $scripts = $this->buildJs();
        }

        // add css / js to view
        App::get('view')->set(array(
            'layout_css' => $styles,
            'layout_js' => $scripts
        ));
    }

    /**
     * build a stylesheet assets
     *
     * @param   array   (optional) array of styles to build, will use $this->css if null
     * @return  string  string containing html for css links
     */
    protected function buildCss($styles = null)
    {
        if (is_null($styles)) {

            $styles = $this->css;
        }

        $output = '';
        foreach ($styles as $k => $style_options) {

            if (! isset($style_options['media'])) {

                $style_options['media'] = 'screen';
            }

            if (! isset($style_options['type'])) {

                $style_options['type'] = 'text/css';
            }

            if (! isset($style_options['rel'])) {

                $style_options['rel'] = 'stylesheet';
            }

            $output .= '<link';

            foreach ($style_options as $attr => $value) {

                $output .= ' ' . $attr . '="' . $value . '"';
            }

            $output .= '>';
        }

        return $output;
    }

    /**
     * build a javascript assets
     *
     * @param   array   (optional) array of js to build, will use $this->js if null
     * @return  string  string containing html for js style tags
     */
    protected function buildJs($scripts = null)
    {
        if (is_null($scripts)) {

            $scripts = $this->js;
        }

        $output = '';
        foreach ($scripts as $k => $script_options) {

            if (! isset($script_options['type'])) {

                $script_options['type'] = 'text/javascript';
            }

            $output .= '<script';

            foreach ($script_options as $attr => $value) {

                $output .= ' ' . $attr . '="' . $value . '"';
            }

            $output .= '></script>';
        }

        return $output;
    }

    /**
     * add an asset
     *
     * @param   string  type of asset
     * @param   array   array of assets to add
     */
    protected function addAssets($type, $assets)
    {
        if ($type == 'css') {

            $key = 'href';
        } elseif ($type == 'js') {

            $key = 'src';
        } else {

            return;
        }

        foreach ($assets as $k => $asset) {
            $asset_path = array(
                $key => $this->findAsset($type, $asset['file'])
            );

            unset($asset['file']);
            $asset = array_merge($asset, $asset_path);

            $this->{$type}[] = $asset;
        }
    }


    /**
     * find an asset
     *
     * @param   string  type of asset to find
     * @param   string  asset to find
     * @param   bool    (optional) return absolute path or not? (defaults to return url path)
     * @return  string  asset we need to load / including path
     */
    protected function findAsset($type, $asset, $return_path = false)
    {
        if (Str::contains($asset, '::')) {

            // split out the addon / asset
            $ex = explode('::', $asset, 2);
            $addon = trim($ex['0']);
            $asset = trim($ex['1']);

            // build the last part of the asset path
            $addon_asset = $addon . DS . 'assets' . DS . $type . DS . $asset;

            // check the overriding theme directory
            $tpl = $this->getThemeDir() . DS . $this->getTheme() . DS;
            $tpl .= $addon_asset;

            if (file_exists($tpl)) {

                return URL_PREFIX . '/assets/' . $addon . '/' . $this->getTheme() . '/' . $type . '/' .$asset;
            } else {

                // check the main addon directory
                $tpl = ADDON_DIR . DS;
                $tpl .= $addon_asset;

                if (file_exists($tpl)) {

                    return URL_PREFIX . '/addon-assets/' . $addon . '/' . $this->getTheme() . '/' . $type . '/' .$asset;
                } else {

                    throw new \Core\Exceptions\AssetNotFoundException($asset);
                }
            }
        } else {

            $tpl = $this->getThemeDir() . DS . $this->getTheme() . DS . 'assets' . DS . $type . DS . $asset;

            if (file_exists($tpl)) {

                return URL_PREFIX . '/assets/' . $this->getTheme() . '/' . $type . '/' .$asset;
            } else {

                throw new \Core\Exceptions\AssetNotFoundException($asset);
            }
        }
    }


    /**
     * get the theme directory
     *
     * @return  string  the theme directory we have set
     */
    public function getThemeDir()
    {
        if (is_null($this->theme_dir)) {

            $this->theme_dir = App::get('view')->getThemeDir();
        }

        return $this->theme_dir;
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
     * get the theme
     *
     * @return  string  current set theme
     */
    public function getTheme()
    {
        if (is_null($this->theme)) {

            $this->theme = App::get('view')->getTheme();
        }
        return $this->theme;
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
}
