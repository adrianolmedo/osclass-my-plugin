# My Plugin

This is a basic plugin for Osclass.

## Make a plugin in Osclass

![Make a plugin in Osclass](https://i.imgur.com/mVG6Y4h.png)

How to make a plugin in Osclass? What is the correct way to make a plugin in Osclass?

The content of this tutorial explains the development of a prototype plugin with basic functionalities (CRUD), but following the same Osclass Model-View-Controller (MVC), you will get a complete starter template that you can download free of charge and use as a guide. Doing this the right way you will discover that everything makes sense, it will make things much easier when it comes to scaling development and teamwork. And therefore you will also understand better the Osclass classified system.

## Contents

 * [Files structure](#files-structure)
 * [Files](#files)
   * [struct.sql](#structsql)
   * [model/MyPlugin.php](#modelmypluginphp)
     * [- Plugin installation methods](#--plugin-installation-methods)
     * [- Uninstallation method](#--uninstallation-method)
   * [helpers/hUtils.php](#helpershutilsphp)
   * [classes/datatables/CrudDataTable.php](#classesdatatablescruddatatablephp)
   * [controller/admin/crud.php](#controlleradmincrudphp)
   * [views/admin/crud.php](#viewsadmincrudphp)
   * [oc-load.php](#oc-loadphp)
   * [parts/public/my_plugin_content.php](#partspublicmy_plugin_contentphp)
   * [index.php](#indexphp)
     * [- Plugin letterhead](#--plugin-letterhead)
     * [- Plugin folder path](#--plugin-folder-path)
     * [- Load the components](#--load-the-components)
     * [- URLs routes](#--urls-routes)
     * [- Headers in the admin panel](#--headers-in-the-admin-panel)
     * [- Load controllers](#--load-controllers)
     * [- Headers or page titles](#--headers-or-page-titles)
     * [- Controller](#--controller)
     * [- Registering functions or parts of dynamic HTML code with PHP](#--registering-functions-or-parts-of-dynamic-html-code-with-php)
     * [- Functions for installation, configuration link and uninstallation](#--functions-for-installation-configuration-link-and-uninstallation)
   * [Languages](#languages)
     * [languages/en_EN/messages.po](#languagesen_enmessagespo)
       * [- Letterhead](#--letterhead)
       * [- Translation variables](#--translation-variables)
     * [languages/es_ES/messages.po](#languageses_esmessagespo)
     * [languages/es_ES/messages.mo](#languageses_esmessagesmo)
 * [Install](#install)
 * [What is this repository for?](#what-is-this-repository-for)
 * [Wiki](#wiki)

## Files structure

```bash
.
oc-content
└── plugins
    └── my_plugin
        ├── struct.sql
        ├── model
        |   └── MyPlugin.php
        ├── helpers
        │   └── hUtils.php
        ├── classes
        │   └── datatables
        │       ├── CrudDataTable.php
        │       └── index.php
        ├── controller
        |   └── admin
        |       ├── crud.php
        |       └── settings.php
        ├── views
        |   ├── admin
        │   ├── crud.php
        │   ├── index.php
        │   └── settings.php
        ├── oc-load.php
        ├── parts
        |    └── public
        |        └── my_plugin_content.php
        ├── index.php
        └── languages
            ├── en_EN
            |   └── messages.po
            └── es_ES
                ├── messages.po
                └── messages.mo
```

In Osclass a plugin is a folder that is located inside the `oc-content/plugins/` directory, create a folder there and name it  `my_plugin`. You can create all these empty files and develop them in the specific order shown here.

## Files

### struct.sql

```sql
CREATE TABLE /*TABLE_PREFIX*/t_plugin_table_one (
	pk_i_id INT NOT NULL AUTO_INCREMENT,
	s_name VARCHAR(60) NULL,
	i_num INT NULL,
	dt_pub_date DATETIME NOT NULL,
	dt_date DATETIME NOT NULL,
	s_url TEXT NOT NULL,
	b_active BOOLEAN NOT NULL DEFAULT FALSE,

	PRIMARY KEY (pk_i_id)
)	ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_plugin_table_two (
	pk_i_id INT NOT NULL AUTO_INCREMENT,
	fk_i_one_id INT NULL,

	PRIMARY KEY (pk_i_id),
	INDEX (fk_i_one_id),
	FOREIGN KEY (fk_i_one_id) REFERENCES /*TABLE_PREFIX*/t_plugin_table_one (pk_i_id)
)	ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';
```

As its name indicates, it is the sql structure of the plugin tables that store the collected data as it interacts with its functions (if required, a plugin sometimes does not require its own database tables).

Meanwhile, this tutorial uses a basic structure with two tables `t_plugin_table_one` and `t_plugin_table_two`; in this last table there is a foreign sql relation with the first one.

The structure of this prototype plugin is designed only to demonstrate the manipulation of different types of data and their relationship between them, it does not meet any specific logical objective.

### model/MyPlugin.php

To establish the model, a class (MyPlugin) must be created that extends the Osclass DAO class. It is important that this class has the singleton pattern so that it can be instantiated directly anywhere in the plugin.

```php
<?php 
/**
 * Model of My Plugin
 */
class MyPlugin extends DAO
{
    private static $instance;

    /**
     * Singleton Pattern
     */
    public static function newInstance()
    {
        if(!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    function __construct()
    {
        parent::__construct();
    }
```

The singleton pattern instantiates the class like this  `MyPlugin()::newInstance()->someMethod();`.

#### - Plugin installation methods

Other important parts of this section are the methods that allow the loading of the database that is related to the actual installation of the plugin.

```php
/**
 * Import tables to database using sql file
 */
public function import($file)
{
    $sql  = file_get_contents($file);

    if(!$this->dao->importSQL($sql)) {
        throw new Exception("Error importSQL::MyPlugin".$file);
    }
}

/**
 * Config the plugin in osclass database, settings the preferences table 
 * and import sql tables of plugin from struct.sql
 */
public function install()
{
    $this->import(MY_PLUGIN_PATH.'struct.sql');
    osc_set_preference('version', '1.0.0', MY_PLUGIN_PREF, 'STRING');
    osc_set_preference('field_one', '1', MY_PLUGIN_PREF, 'BOOLEAN');
    osc_set_preference('field_two', '0', MY_PLUGIN_PREF, 'BOOLEAN');
    osc_set_preference('field_three', '', MY_PLUGIN_PREF, 'STRING');
    osc_run_hook('my_plugin_install');
}
```

The `MY_PLUGIN_PATH` and `MY_PLUGIN_PREF` constants are explained in `index.php`.

#### - Uninstallation method

This method is in charge of completely dismantling the tables in the database and the rest of the plugin uninstallation. Remember to do the `DROP TABLE` contrary to the order in which the tables were installed, in case there is a foreign relationship.

```php
/**
 * Delete all fields from the 'preferences' table and also delete all tables of plugin
 */
public function uninstall()
{
    $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_table_two()));
    $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_table_one()));
    Preference::newInstance()->delete(array('s_section' => MY_PLUGIN_PREF));
    osc_run_hook('my_plugin_uninstall');
}
```

### helpers/hUtils.php

This is a file of general plugin functions that support it, such as URL validators or date generators. The "helpers" do not necessarily exist in the project, but in this case I have left a couple and you can see how they work within the plugin, also in case they are useful to you.

### classes/datatables/CrudDataTable.php

The `classes` subdirectory is usually also where third-party libraries are kept, an example would be: `classes/MCrypt.php`. In this case, it was necessary to create another subdirectory inside, a folder called `datatables` to contain the DataTables generating files there, since a project could have more than one. In another entry the operation of the DataTables will be explained.

### controller/admin/crud.php

```php
<?php
/**
 * Controller of My Plugin CRUD
 */
class CAdminMyPluginCRUD extends AdminSecBaseModel
{

    // Business Layer...
    public function doModel()
    {
        switch (Params::getParam('plugin_action')) {
            case 'done':
                // Form process...
                
                osc_add_flash_ok_message(__('Ok custom message!', MY_PLUGIN_PREF), 'admin');
                
                ob_get_clean();

                $this->redirectTo($_SERVER['HTTP_REFERER']);
                //$this->redirectTo(osc_route_admin_url('my-plugin-admin-settings'));
                break;

            default:
                $var = "Hello world";
                $this->_exportVariableToView('var', $var);
                break;
        }
    }
    
}
```

To create a controller in which it will be used **within** the administration (oc-admin) you must start by creating a class that extends to `AdminSecBaseModel`. The inside of `doModel()` is evaluated with the help of the `switch` structure if an action parameter is being received, if it's *done*, it executes what is inside that case, if it does not receive anything then the `switch` is in `default`, note that from there use `$this->_exportVariableToView('var', $var);` to prepare the value of `$var` to the scope of the views and can then be received from a view with the function `__get()`.

|  |  |  |
|--|--|--|
| **BaseModel** | Web | Simple controller, no security check |
| **WebSecBaseModel** | Web | You make sure user is logged in or redirect to login page |
| **AdminSecBaseModel** | Admin | You make sure the user is logged in and is an administrator or redirect to the administrator login page
 

Make sure the user is logged in and is an administrator or redirect to the administrator login page.

Credit: [How to create Osclass plugins - part 1 - MADHOUSE](https://wearemadhouse.wordpress.com/2013/10/11/how-to-develop-osclass-plugins/ "How to create Osclass plugins - part 1 - MADHOUSE")

### views/admin/crud.php

```php
<?php
// For get vars from controller
$var = __get('var');
?>

<h2 class="render-title"><?php _e("Title of this view", MY_PLUGIN_PREF); ?></h2>

<!-- Form... -->
<?php echo $var; ?>

<!-- DataTable -->

<script>
	// JavaScript function
	function function_name(var) {
	    return false;
	}
</script>
```

The views receive the variables that are passed from the controller, to obtain their value use `__get ()`. Here you can develop everything regarding the *front-end* is about, if you want to see the full content of this part, download the plugin files. Translatable text strings are contained within the typical `__e ()` function which indicates that Osclass uses the translation system with .po and .mo files (this topic is covered at the end of the tutorial).

### oc-load.php

```php
<?php

// Model
require_once MY_PLUGIN_PATH . "model/MyPlugin.php";

// Helpers
require_once MY_PLUGIN_PATH . "helpers/hUtils.php";

// Controllers
require_once MY_PLUGIN_PATH . "controller/admin/crud.php";
require_once MY_PLUGIN_PATH . "controller/admin/settings.php";
```

It is a file that loads the rest of the plugin components in a logically hierarchical and separate way. That order must be respected, otherwise you will have problems.

### parts/public/my_plugin_content.php

The *parts* directory is used to locate pieces of views or dynamic HTML content with PHP embedded in content using the `osc_add_hook ()` or `osc_add_filter ()` function, these types of content would be separated into folders that would be named *admin* , *user* or *public*, depending on whether the use is within the administration (oc-admin) or in any region of the template (public or user), that is, the same logic is followed as in *views*. Inside these files it is recommended to avoid directly installing any class of the model, use *helpers* for it.

Index.php explains how these files are registered.

### index.php

It is the main file of a plugin, even depending on the project, it could be the only file of the plugin. In this case it consists of several parts.

#### - Plugin letterhead

```php
<?php
/*
Plugin Name: My Plugin
Plugin URI: https://www.website.com/my_plugin
Description: My Plugin description
Version: 1.0.0
Author: My Name
Author URI: https://www.website.com/
Short Name: my-plugin
Plugin update URI: https://www.website.com/my_plugin/update
*/
```

![](https://i.imgur.com/NQ6Ju8T.png)

#### - Plugin folder path

```php
// Paths
define('MY_PLUGIN_FOLDER', osc_plugin_folder(__FILE__)); // 'my_plugin'
define('MY_PLUGIN_PATH', osc_plugins_path() . MY_PLUGIN_FOLDER);
define('MY_PLUGIN_PREF', basename(MY_PLUGIN_FOLDER));
```

The plugin needs to know what its own folder that contains it is called and therefore the full path of where it is located, this to be able to be installed or uninstalled, these functionalities are referred to below.

#### - Load the components

```php
// Prepare model, controllers and helpers
require_once MY_PLUGIN_PATH . "oc-load.php";
```

Load `oc-load.php` file.

#### - URLs routes

```php
// URL routes
osc_add_route('my-plugin-admin-crud', MY_PLUGIN_FOLDER.'views/admin/crud', MY_PLUGIN_FOLDER.'views/admin/crud', MY_PLUGIN_FOLDER.'views/admin/crud.php');
osc_add_route('my-plugin-admin-settings', MY_PLUGIN_FOLDER.'views/admin/settings', MY_PLUGIN_FOLDER.'views/admin/settings', MY_PLUGIN_FOLDER.'views/admin/settings.php');
```
![](https://i.imgur.com/59AZG46.png)

It also directly loads the views.

#### - Headers in the admin panel

```php
// Headers in the admin panel
osc_add_hook('admin_menu_init', function() {
    osc_add_admin_submenu_divider(
        "plugins", __("My Plugin", MY_PLUGIN_PREF), MY_PLUGIN_PREF, "administrator"
    );

    osc_add_admin_submenu_page(
        "plugins", __("CRUD", MY_PLUGIN_PREF), osc_route_admin_url("my-plugin-admin-crud"), "my-plugin-admin-crud", "administrator"
    );

    osc_add_admin_submenu_page(
        "plugins", __("Settings", MY_PLUGIN_PREF), osc_route_admin_url("my-plugin-admin-settings"), "my-plugin-admin-settings", "administrator"
    );
});
```
![](https://i.imgur.com/JhB4kig.png)

#### - Load controllers

```php
// Load the controllers, depend of url route
function my_plugin_admin_controllers() {
	switch (Params::getParam("route")) {
		case 'my-plugin-admin-crud':
			$filter = function($string) {
                return __("CRUD - My Plugin", MY_PLUGIN_PREF);
            };

            // Page title (in <head />)
            osc_add_filter("admin_title", $filter, 10);

            // Page title (in <h1 />)
            osc_add_filter("custom_plugin_title", $filter);

            $do = new CAdminMyPluginCRUD();
            $do->doModel();
			break;

		case 'my-plugin-admin-settings':
			$filter = function($string) {
                return __("Settings - My Plugin", MY_PLUGIN_PREF);
            };

            // Page title (in <head />)
            osc_add_filter("admin_title", $filter, 10);

            // Page title (in <h1 />)
            osc_add_filter("custom_plugin_title", $filter);

            $do = new CAdminMyPluginSettings();
            $do->doModel();
			break;
	}
}
osc_add_hook("renderplugin_controller", "my_plugin_admin_controllers");
```

Here you load the drivers with their respective views depending on the URL path, and at the same time add the page title to the view.

#### - Headers or page titles

```php
$filter = function($string) {
    return __("CRUD - My Plugin", MY_PLUGIN_PREF);
};

// Page title (in <head />)
osc_add_filter("admin_title", $filter, 10);

// Page title (in <h1 />)
osc_add_filter("custom_plugin_title", $filter);
```

![](https://i.imgur.com/5V3M5s7.png)

#### - Controller

```php
$do = new CAdminMyPluginCRUD();
$do->doModel();
```

#### - Registering functions or parts of dynamic HTML code with PHP

```php
function my_plugin_content() {
	// If exists custom template
	if (file_exists(WebThemes::newInstance()->getCurrentThemePath().'plugins/'.MY_PLUGIN_FOLDER.'my_plugin_content.php')) {
		osc_current_web_theme_path('plugins/'.MY_PLUGIN_FOLDER.'my_plugin_content.php');
	} else {
		include MY_PLUGIN_PATH . 'parts/public/my_plugin_content.php';
	}
}
// Add the function in a hook, you run on the template with: osc_run_hook('my_plugin');
osc_add_hook('my_plugin', 'my_plugin_content');
```

In this case the content of the `my_plugin_content.php` file will run in any region with the following function `<?php osc_run_hook('my_plugin'); ?>`.

The designer of a theme can customize this file with an appearance adapted to the style of the theme without having to modify the original, for it to take effect, a `plugins` folder must be created within the theme, and another folder with the plugin name, in this case a folder named `my_plugin`, so the file path would look like this, in case you are working with the Bender theme: `bender/plugins/my_plugin/my_plugin_content.php`.

#### - Functions for installation, configuration link and uninstallation

```php
// 'Configure' link
function my_plugin_configure_admin_link() {
	osc_redirect_to(osc_route_admin_url('my-plugin-admin-settings'));
}
// Show 'Configure' link at plugins table
osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'my_plugin_configure_admin_link');


// Call uninstallation method from model (model/MyPlugin.php)
function my_plugin_uninstall() {
	MyPlugin::newInstance()->uninstall();
}
// Show an Uninstall link at plugins table
osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'my_plugin_uninstall');


// Call the process of installation method
function my_plugin_install() {
	MyPlugin::newInstance()->install();
}
// Register plugin's installation
osc_register_plugin(osc_plugin_path(__FILE__), 'my_plugin_install');
```

![Configure link](https://i.imgur.com/W1obGVe.png)

### Languages

#### languages/en_EN/messages.po

.po files are used to collect all translatable text strings that are contained in functions like `_e("Hello", MY_PLUGIN_PREF)` or `__("Hello", MY_PLUGIN_PREF)`, and then rewritten to a different language . In the `languages` folder contains the directories of each language, these folders are named by the language code, for English it is used *en_EN*, for Spanish it would be *es_ES*.

##### - Letterhead

```
msgid ""
msgstr ""
"Project-Id-Version: My Plugin\n"
"Report-Msgid-Bugs-To: \n"
"POT-Creation-Date: 2019-08-23 01:00+0000\n"
"PO-Revision-Date: \n"
"Last-Translator: author <author@mail.com>\n"
"Language: en_US\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"X-Poedit-SourceCharset: UTF-8\n"
"X-Poedit-KeywordsList: _e;__\n"
"X-Poedit-Basepath: .\n"
"X-Generator: Poedit 1.8.7\n"
```

These files have two parts, one is the letterhead describing the version of the program that edited the file, creation date, revision date, last translator, language code, and the other part is the collection of translation variables.

##### - Translation variables

```
msgid "Settings"
msgstr ""

msgid "Settings - My Plugin"
msgstr ""

msgid "Edit register"
msgstr ""

msgid "Add new register"
msgstr ""
```

In the case of the English language, which is the main and default language of the Osclass project, your messages.po file will only contain the original variables in English (_msgid_), but the variables where there would be a respective translation (_msgstr_) would be empty, This means that this language as a section is not really necessary for any functionality in the plugin but it serves as a template to facilitate the creation of other languages.

#### languages/es_ES/messages.po

```
msgid "Settings"
msgstr "Configuración"

msgid "Settings - My Plugin"
msgstr "Configuración - My Plugin"

msgid "Edit register"
msgstr "Editar registro"

msgid "Add new register"
msgstr "Agregar nuevo registro"
```

Here it is up to the *msgstr* variables to contain the translation.

#### languages/es_ES/messages.mo

```
de12 0495 0000 0000 3700 0000 1c00 0000
d401 0000 4900 0000 8c03 0000 0000 0000
b004 0000 2100 0000 b104 0000 2300 0000
d304 0000 1f00 0000 f704 0000 0c00 0000
```

The .mo files are the compiled and binary version of the .po files, this is obtained through the Poedit program when saving the messages.po file. All languages for them to work must have their .mo version except the English language folder, because it is not necessary.

### Install

After having developed each of the above files, you can try installing the plugin in Manage Plugins from the Osclass Administration Panel, if you prefer you can move the folder of this plugin to another part, zip it and upload it from the Panel of Administration, then click 'Install'.

---

### What is this repository for?

* Require: Osclass `3.5+`, `4+`, `5` or menor.

### Wiki

[Implement a DataTable in Osclass](https://github.com/adrianolmedo/osclass-my-plugin/wiki/Implement-a-DataTable-in-Osclass)
