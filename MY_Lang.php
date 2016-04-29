<?php


defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Language in URL for CodeIgniter.
 *
 * @author		Walid Aqleh <waleedakleh23@hotmail.com>
 * @version		1.0.0
 * @based on	        Internationalization (i18n) library for CodeIgniter 2 by Jerome Jaglale (http://jeromejaglale.com/doc/php/codeigniter_i18n)
 * @link https://github.com/waqleh/CodeIgniter-Language-In-URL-Internationalization-
 */

class MY_Lang extends CI_Lang {
    /*     * ************************************************
      configuration
     * ************************************************* */

    // languages
    var $languages = array(
        'en' => 'english',
        'ar' => 'arabic'
    );
    // special URIs (not localized)
    var $special = array(
        ""
    );
    // where to redirect if no language in URI
    var $default_uri = '';

    /*     * *********************************************** */

    function __construct() {
        parent::__construct();

        global $CFG;
        global $URI;
        global $RTR;

        $segment = $URI->segment(1);

        if (!strlen($this->default_uri)) {
            $this->default_uri = $URI->uri_string();
        }

        if (isset($this->languages[$segment])) { // URI with language -> ok
            $language = $this->languages[$segment];
            $CFG->set_item('language', $language);
        } else if ($this->is_special($segment)) { // special URI -> no redirect
            return;
        } else { // URI without language -> redirect to default_uri
            // set default language
            $CFG->set_item('language', $this->languages[$this->default_lang()]);

            // redirect
            header("Location: " . $CFG->site_url($this->localized($this->default_uri)), TRUE, 301);
            exit;
        }
    }

    // get current language
    // ex: return 'en' if language in CI config is 'english' 
    function lang() {
        global $CFG;
        $language = $CFG->item('language');

        $lang = array_search($language, $this->languages);
        if ($lang) {
            return $lang;
        }

        return NULL; // this should not happen
    }

    function is_special($uri) {
        $exploded = explode('/', $uri);
        if (in_array($exploded[0], $this->special)) {
            return TRUE;
        }
        if (isset($this->languages[$uri])) {
            return TRUE;
        }
        return FALSE;
    }

    function switch_uri($lang) {
        $CI = & get_instance();

        $uri = $CI->uri->uri_string();
        $exploded = explode('/', $uri);
        if ($exploded[0] == $this->lang() || !strlen($exploded[0])) {
            $exploded[0] = $lang;
        }
        $uri = implode('/', $exploded);
        return $uri;
    }

    // is there a language segment in this $uri?
    function has_language($uri) {
        $first_segment = NULL;

        $exploded = explode('/', $uri);
        if (isset($exploded[0])) {
            if ($exploded[0] != '') {
                $first_segment = $exploded[0];
            } else if (isset($exploded[1]) && $exploded[1] != '') {
                $first_segment = $exploded[1];
            }
        }

        if ($first_segment != NULL) {
            return isset($this->languages[$first_segment]);
        }

        return FALSE;
    }

    // default language: first element of $this->languages
    function default_lang() {
        foreach ($this->languages as $lang => $language) {
            return $lang;
        }
    }

    // add language segment to $uri (if appropriate)
    function localized($uri) {
        if ($this->has_language($uri) || $this->is_special($uri) || preg_match('/(.+)\.[a-zA-Z0-9]{2,4}$/', $uri)) {
            // we don't need a language segment because:
            // - there's already one or
            // - it's a special uri (set in $special) or
            // - that's a link to a file
        } else {
            // WALID AQLEH Jun 14 2015
            $uri = $this->lang() . (0 !== strpos($uri, '/') ? '/' : '') . $uri;
        }

        return $uri;
    }

    // WALID AQLEH May 14 2015
    function line($line = '', $log_errors = true) {
        $value = ($line == '' OR ! isset($this->language[$line])) ? FALSE : $this->language[$line];
        if ($value === FALSE) {
            global $CFG;
            $file = APPPATH . 'language/' . $this->languages[$this->lang()] . '/' . $CFG->item('general_lang_file');
            if (($found = file_exists($file)) === FALSE) {
                $this->create_lang_file($file);
            }
            $this->add_to_lang_file($file, $line);
            return $line;
        }
        return $value;
    }

    function create_lang_file($file) {
        try {
            file_put_contents($file, "<?php

          defined('BASEPATH') OR exit('No direct script access allowed');" . PHP_EOL);
        } catch (Exception $exc) {
            log_message('error', 'Could not create lang file: "' . $file . '"');
        }
    }

    function add_to_lang_file($file, $line) {
        try {
            $file_contents = file_get_contents($file);
            $pattern = '~\$lang\[(\'|")' . $line . '(\'|")\]~';
            if (!preg_match($pattern, $file_contents)) {
                $data = '$lang[\'' . addcslashes($line, '\'') . '\'] = "' . addcslashes($line, '"') . '";';
                file_put_contents($file, PHP_EOL . $data, FILE_APPEND);
            }
            $file_contents = trim(file_get_contents($file));
            $pattern = '/<\?php/';
            if (!preg_match($pattern, $file_contents)) {
                $content = '<?php ' . $file_contents;
                file_put_contents($file, $content);
            }
        } catch (Exception $exc) {
            log_message('error', 'Could not edit lang file: "' . $file . '"');
        }
    }

    /**
     * Load a language file
     *
     * @param	mixed	$langfile	Language file name
     * @param	string	$idiom		Language name (english, etc.)
     * @param	bool	$return		Whether to return the loaded array of translations
     * @param 	bool	$add_suffix	Whether to add suffix to $langfile
     * @param 	string	$alt_path	Alternative path to look for the language file
     *
     * @return	void|string[]	Array containing translations, if $return is set to TRUE
     */
    public function load($langfile, $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '') {
        if (is_array($langfile)) {
            foreach ($langfile as $value) {
                $this->load($value, $idiom, $return, $add_suffix, $alt_path);
            }

            return;
        }

        $langfile = str_replace('.php', '', $langfile);

        if ($add_suffix === TRUE) {
            $langfile = preg_replace('/_lang$/', '', $langfile) . '_lang';
        }

        $langfile .= '.php';

        if (empty($idiom) OR ! preg_match('/^[a-z_-]+$/i', $idiom)) {
            $config = & get_config();
            $idiom = empty($config['language']) ? 'english' : $config['language'];
        }

        if ($return === FALSE && isset($this->is_loaded[$langfile]) && $this->is_loaded[$langfile] === $idiom) {
            return;
        }

        // Load the base file, so any others found can override it
        $basepath = BASEPATH . 'language/' . $idiom . '/' . $langfile;
        if (($found = file_exists($basepath)) === TRUE) {
            include($basepath);
        }

        // Do we have an alternative path to look in?
        if ($alt_path !== '') {
            $alt_path .= 'language/' . $idiom . '/' . $langfile;
            if (file_exists($alt_path)) {
                include($alt_path);
                $found = TRUE;
            }
        } else {
            foreach (get_instance()->load->get_package_paths(TRUE) as $package_path) {
                $package_path .= 'language/' . $idiom . '/' . $langfile;
                if ($basepath !== $package_path && file_exists($package_path)) {
                    include($package_path);
                    $found = TRUE;
                    break;
                }
            }
        }
        if ($found !== TRUE) {
            log_message('error', 'Unable to load the requested language file: language/' . $idiom . '/' . $langfile);
            global $CFG;
            $file = APPPATH . 'language/' . $this->languages[$this->lang()] . '/' . $CFG->item('general_lang_file');
            $this->create_lang_file($file);
            echo $file;
            require($file);
        }

        if (!isset($lang) OR ! is_array($lang)) {
            log_message('error', 'Language file contains no data: language/' . $idiom . '/' . $langfile);

            if ($return === TRUE) {
                return array();
            }
            return;
        }

        if ($return === TRUE) {
            return $lang;
        }

        $this->is_loaded[$langfile] = $idiom;
        $this->language = array_merge($this->language, $lang);

        log_message('info', 'Language file loaded: language/' . $idiom . '/' . $langfile);
        return TRUE;
    }
    
    public function get_lang(){
        return $this->languages ;
    }

    // --------------------------------------------------------------------
}