* اقرأ هذا في لغات أخرى: [English](README.md), [عربى](README.ar.md) . *

# ماذا يفعل

اللغة في الرابط:

- jorider.com/en/about
- jorider.com/ar/about

استمر في استخدام كودينيتر كوداجنيتر [Language Class](https://codeigniter.com/user_guide/libraries/language.html)

# مثال

عرض

    <p><?php echo lang('I\'m a man')?></p>
ملف اللغة الإنجليزية

    $lang['I\'m a man'] = "I'm a man";
ملف اللغة العربية

    $lang['I\'m a man'] = "انا رجل";
النتيجة مع jorider.com/en/about

    <p>I'm a man</p>
النتيجة مع jorider.com/ar/about

    <p>انا رجل</p>

# التركيب

- استنساخ المستودع
- ضع `MY_Lang.php` و `MY_Config.php` في application/core

# أعدادات
في application/config/routes.php أضف

    // example: '/en/about' -> use controller 'about'
    $route['^en/(.+)$'] = "$1";
    $route['^ar/(.+)$'] = "$1";
    // '/en' and '/ar' -> use default controller
    $route['^(en|ar)$'] = $route['default_controller'];

# استعمال
دعنا نبني صفحة ثنائية اللغة الإنجليزية / العربية.

### ملفات اللغة

`application/language/english/about_lang.php`

    <?php

    $lang['I\'m a man'] = "I'm a man";

`application/language/arabic/about_lang.php`

    <?php

    $lang['I\'m a man'] = "انا رجل";

### تحكم

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

### عرض

`application/views/about.php`

    <p><?php echo lang('I\'m a man')?></p>

### اختبار

`http://your_base_url/en/about`

    <p>I'm a man</p>

`http://your_base_url/ar/about`

    <p>انا رجل</p>

# ملاحظات
- قد تحتاج إلى ترجمة بعض ملفات لغة كوداجنيتر في system/language. مثال: إذا كنت تستخدم مكتبة “Form Validation” للصفحات العربية، فترجم `system/language/english/form_validation_lang.php` إلى `application/language/arabic/form_validation_lang.php`.

- روابط إلى الصفحات الداخلية مسبوقة باللغة الحالية، ولكن الروابط إلى الملفات ليست كذلك.

    site_url('about/my_work');
    // http://mywebsite.com/en/about/my_work


    base_url('css/styles.css');
    // http://mywebsite.com/css/styles.css

- الحصول على اللغة الحالية:

    $this->lang->lang();
    // en

- التبديل إلى لغة أخرى:

    anchor($this->lang->switch_uri('ar'),'Display current page in Arabic');

- الصفحة الجذر (/) من المفترض أن تكون نوعا من صفحة البداية، دون أي لغة محددة. يمكن تغيير ذلك: راجع "لا صفحة البداية" أدناه.

- يحتوي `MY_Config.php` على تجاوز `site_url()`: إضافة شريحة لغة (عند الاقتضاء) إلى عناوين رابط التي تم إنشاؤها. يستخدم أيضا بواسطة `anchor()`, `form_open()`...

- إذا لم يتم العثور على مفتاح في مجموعة اللغة سيتم إنشاء ملف يسمى `strings_lang.php` وسيتم إضافة المفتاح والقيمة إليه. مثال:

    <?php

    defined('BASEPATH') OR exit('No direct script access allowed');

    $lang['xyz'] = "xyz";



# خيارات
### عناوين ورل الخاصة

عنوان أوري الخاص غير مسبوق بلغة. عنوان أوري الجذر (/) هو بشكل افتراضي معرف أوري خاص.

قد تحتاج إلى عناوين رابط خاصة أخرى، مثل قسم المشرف بلغة واحدة فقط.

في `application/core/MY_Lang.php`, ضف مشرفا إلى المصفوفة $special. الآن، لن تكون الروابط إلى المشرف مسبوقة باللغة الحالية.

    site_url('admin');
    // http://your_base_url/admin

### لا صفحة البداية

في `application/core/MY_Lang.php`

إزالة "" من المصفوفة $special
تعيين $default_uri إلى شيء آخر مثل المنزل
الآن طلب / سيتم إعادة توجيه إلى en/home, إذا الإنجليزية هي اللغة الافتراضية الخاصة بك
اللغة الافتراضية هي العنصر الأول من مصفوفة $languages

### إضافة لغة جديدة

`application/core/MY_Lang.php`:  إضافة لغة جديدة إلى مصفوفة $languages

    // مثال: الألمانية (de)
    'de' => 'german',

`application/config/routes.php`: إضافة مسارات جديدة

    // مثال: الألمانية (de)
    $route['^de/(.+)$'] = "$1";
    $route['^(en|ar|de)$'] = $route['default_controller'];

- إنشاء مجلد اللغة المقابلة في application/language. لهذا المثال "الألماني"، سوف يطلق عليه German.
