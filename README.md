Read this in other languages:
- [English](README.md)
- [عربى](README.ar.md)

# What it does

Language code in URL:

- jorider.com/en/about
- jorider.com/ar/about

The [CodeIgniter Language Class][1] is used in order to fetch and display (directly in the HTML) the appropriate text defined in the respective language file.

## Examples

View file

    <p><?php echo lang('I\'m a man')?></p>
English language file

    $lang['I\'m a man'] = "I'm a man";
Arabic language file

    $lang['I\'m a man'] = "انا رجل";
Result with jorider.com/en/about

    <p>I'm a man</p>
Result with jorider.com/ar/about

    <p>انا رجل</p>

# Usage

Before using this library, please read and learn about the [CodeIgniter Language Class][1] concepts and methods.

## Installation

- Clone repository;
- Place the `MY_Lang.php` and `MY_Config.php` files together in the `application/core` folder.

# Example of bilingual English/Arabic website:

## Configuration

In _application/config/routes.php_ add:

    // example: '/en/about' -> use controller 'about'
    $route['^en/(.+)$'] = "$1";
    $route['^ar/(.+)$'] = "$1";
    // '/en' and '/ar' -> use default controller
    $route['^(en|ar)$'] = $route['default_controller'];
    
Other routing examples:

    $route['^(en|ar)/contact'] = "pages/contact";
    $route['^(en|ar)/privacy-policy$'] = "pages/index/privacy_policy";
    $route['^(en|ar)/terms-of-use$'] = "pages/index/terms_of_use";

### Create _Language Files_

- `application/language/english/about_lang.php`

      <?php

      $lang['I\'m a man'] = "I'm a man";

- `application/language/arabic/about_lang.php`

      <?php

      $lang['I\'m a man'] = "انا رجل";

### Load the language helper and file in the respective _Controller_

`application/controllers/about.php`

    <?php
    class About extends CI_Controller {

      function index()
      {
        // you might want to just autoload these two helpers
        $this->load->helper(['language', 'url']);

        // load language file
        $this->lang->load('about');


        $this->load->view('about');
      }
    }

### Invoke directly the `lang` method in the _View_

`application/views/about.php`

    <p><?php echo lang('I\'m a man')?></p>

## Test

`http://your_base_url/en/about`

    <p>I'm a man</p>

`http://your_base_url/ar/about`

    <p>انا رجل</p>

## Notes

- You might need to translate some of CodeIgniter's language files in `system/language`. Example: if you're using the [Form Validation][2] library for Arabic pages, translate `system/language/english/form_validation_lang.php` to `application/language/arabic/form_validation_lang.php`.

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

- the root page (`/`) is supposed to be some kind of splash page, without any specific language. However, this can be changed. Read the **No splash page** section below.

- the CodeIgniter `system/core/Config.php` `site_url()` method is overriden in `MY_Config.php`: a language segment can be added (when appropriate) to generated URLs. It is also used by `anchor()`, `form_open()`...

- If a key is not found in the language file's array, a file called `strings_lang.php` will be created and the key and value will be added to it. E.g. if `line('xyz')` is not available, `strings_lang.php` will be created and will contain:

      <?php

      defined('BASEPATH') OR exit('No direct script access allowed');

      $lang['xyz'] = "xyz";


# Options

## Special URIs

A special URI is not prefixed by a language. The root URI (`/`) is by default a special URI.

You might need other special URIs, like for an admin section in just one language.

In `application/core/MY_Lang.php`, by adding the `'admin'` string to the `$special` array, links to the admin page will not be prefixed by the current language.

    site_url('admin');
    // http://your_base_url/admin

## No splash page

In `application/core/MY_Lang.php`:

1. remove `""` from the `$special` array;
2. set `$default_uri` to something else like `home`;
3. if English is your default language, now a request to `/` will be redirected to `en/home`;
4. the default language is the first item of the `$languages` array.

## Add a new language

`application/core/MY_Lang.php`: add a new language to `$languages` array

    // example: German (de)
    'de' => 'german',

`application/config/routes.php`: add new routes

    // example: German (de)
    $route['^de/(.+)$'] = "$1";
    $route['^(en|ar|de)$'] = $route['default_controller'];

- create the corresponding language folder in application/language. For this _German_ example, it would be called `german`.


[1]: https://codeigniter.com/user_guide/libraries/language.html
[2]: https://codeigniter.com/user_guide/libraries/form_validation.html
