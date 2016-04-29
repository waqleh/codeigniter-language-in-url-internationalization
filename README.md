# What it does

Language in URL:

- jorider.com/en/about
- jorider.com/ar/about

Keep using CodeIgniter [Language Class](https://codeigniter.com/user_guide/libraries/language.html)

# Example

View

    <p><?php echo lang('I\'m a man')?></p>
English language file

    $lang['I\'m a man'] = "I'm a man";
Arabic language file

    $lang['I\'m a man'] = "انا رجل";
Result with maestric.com/en/about

    <p>I'm a man</p>
Result with maestric.com/ar/about

    <p>انا رجل</p>

# Installation

- Clone repository
- Put `MY_Lang.php` and `MY_Config.php` in application/core

# Configuration
In application/config/routes.php add

    // example: '/en/about' -> use controller 'about'
    $route['^en/(.+)$'] = "$1";
    $route['^ar/(.+)$'] = "$1";
    // '/en' and '/ar' -> use default controller
    $route['^(en|ar)$'] = $route['default_controller'];

# Use
Let's build a bilingual English/Arabic page.

### language files

`application/language/english/about_lang.php`

    <?php
 
    $lang['I\'m a man'] = "I'm a man";
 
`application/language/arabic/about_lang.php`

    <?php
 
    $lang['I\'m a man'] = "انا رجل";

### controller

`application/controllers/about.php`

    <?php
    class About extends CI_Controller {
     
    	function index()
    	{
    		// you might want to just autoload these two helpers
    		$this->load->helper('language');
    		$this->load->helper('url');
     
    		// load language file
    		$this->lang->load('about');
     
     
    		$this->load->view('about');
    	}
    }

### view

`application/views/about.php`

    <p><?php echo lang('I\'m a man')?></p>
 
### Test

`http://your_base_url/en/about`

    <p>I'm a man</p>
 
`http://your_base_url/ar/about`

    <p>انا رجل</p>
 
# Notes
- You might need to translate some of CodeIgniter's language files in system/language. Example: if you're using the “Form Validation” library for Arabic pages, translate `system/language/english/form_validation_lang.php` to `application/language/arabic/form_validation_lang.php`.

- links to internal pages are prefixed by the current language, but links to files are not.

    site_url('about/my_work');
    // http://mywebsite.com/en/about/my_work
 
 
    base_url('css/styles.css');
    // http://mywebsite.com/css/styles.css

- Get the current language:

    $this->lang->lang();
    // en

- Switch to another language:

    anchor($this->lang->switch_uri('ar'),'Display current page in Arabic');

- the root page (/) is supposed to be some kind of splash page, without any specific language. This can be changed: see “No splash page” below.

- `MY_Config.php` contains an override of `site_url()`: add language segment (when appropriate) to generated URLs. Also used by `anchor()`, `form_open()`...

- If a key is not found in language array a file called `strings_lang.php` will be created and the key and value will be added to it. Example:
If line('xyz') is not available strings_lang.php` will be created and will contain:

    <?php
    
    defined('BASEPATH') OR exit('No direct script access allowed');
    
    $lang['xyz'] = "xyz";



# Options 
### Special URIs

A special URI is not prefixed by a language. The root URI (/) is by default a special URI.

You might need other special URIs, like for an admin section in just one language.

In `application/core/MY_Lang.php`, add admin to the $special array. Now, links to admin won't be prefixed by the current language.

    site_url('admin');
    // http://your_base_url/admin

### No splash page

In `application/core/MY_Lang.php`

remove ”” from the $special array
set $default_uri to something else like home
now a request to / will be redirected to en/home, if English is your default language
the default language is the first item of the $languages array

### Add a new language

`application/core/MY_Lang.php`: add new language to $languages array

    // example: German (de)
    'de' => 'german',

`application/config/routes.php`: add new routes

    // example: German (de)
    $route['^de/(.+)$'] = "$1"; 
    $route['^(en|ar|de)$'] = $route['default_controller'];

- create corresponding language folder in application/language. For this “German” example, it would be called German.