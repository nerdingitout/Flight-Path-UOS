 <?php

 /**
  * @file
  * Lists all available hooks within FlightPath's core code.
  *
  * This script is not meant to be included and used in FlightPath,
  * it is simply for documentation of how the various hooks work.
  *
  * In each example, you may use a hook by replacing the word "hook"
  * with the name of your custom module.
  *
  * For example, hook_menu() might be my_module_menu().  FlightPath
  * will automatically begin using the hooks in your module, once the
  * module is enabled.
 */



/**
 * Alter forms which are created using the Form API
 *
 * @param &$form
 *   This is the actual form array, retrieved from the form's callback
 *   function.  It should ALWAYS be passed by reference, so that the
 *   changes you make to it will carry through.
 * @param $form_id
 *   This is the "form_id" of the form-- usually the name of the callback
 *   function which created the form.  It can be used to identify forms
 *   before you attempt to take action.
 *
 */
function hook_form_alter(&$form, $form_id) {
  if ($form_id == "example_form_callback") {
    $form["new_element"] = array(
      // ...
    );
  }
}


/**
 * Reports status information which each module is aware of, visible on admin/config/status.
 *
 * This allows modules to let the user know about requirements which are not being met, or
 * simply to let them know that someting is working successfully.
 *
 * For example, you may want to let the user know that a nightly job failed to run the night before.
 *
 * @return array()
 *  - severity: "normal", "warning", and "alert" are the choices.
 *  - status: A short message.
 *
 */
function hook_status() {
  // Example from system.module, reporting on if Cron has run recently:
  $rtn = array();
  $rtn["severity"] = "normal";
  // Check on the last time cron was run; make sure it's working properly.
  $last_run = variable_get("cron_last_run", 0);

  if ($last_run < strtotime("-2 DAY")) {
    $rtn["severity"] = "alert";
    $rtn["status"] .= t("Cron hasn't run in over 2 days. For your installation of FlightPath to function properly, cron.php must be accessed routinely. At least once per day is recommended.");
  }
  else {
    $rtn["status"] .= t("تم تشغيل اخر Cron في %date", array("%date" => format_date($last_run)));
  }

  $rtn["status"] .= "<p style='font-size: 0.8em;'>" . t("Your site's cron URL is:");
  $rtn["status"] .= "<br>&nbsp; &nbsp; <i>" . $GLOBALS["fp_system_settings"]["base_url"] . "/cron.php?t=" . $GLOBALS["fp_system_settings"]["cron_security_token"] . "</i>
                        <br>" . t("Example linux cron command:") . "&nbsp; <i>wget -O - -q -t 1 http://ABOVE_URL</i>";
  $rtn["status"] .= "</p>";


  return $rtn;
}


/**
 * Validates form submissions from the Form API
 *
 * This function can be named anything you want (or can be considered optional).
 * If named the same thing as the form callback, it will be utilized automatically.
 * For example, if you form is named my_form, then if you have a validate function
 * named my_form_validate, it will be called automatically.
 *
 * Since $form_state is passed by reference, you may make changes to this array, and
 * those changes will carry through to other validate functions (defined with #validate_handlers)
 * and then to the submit handler(s).
 *
 * @see hook_submit($form, &$form_state)
 */
function hook_validate($form, &$form_state) {
  $age = $form_state["values"]["age"];
  if ($age < 18) {
    form_error("age", "Sorry, you must be 18 or older to submit this form.");
    return;
  }
}


/**
 * This hook is called every time the system cron is run.
 *
 * Modules should place code here which is meant to be run on a schedule.
 */
function hook_cron() {
  // Example:  check for students who have dropped, and email their advisors.
}



/**
 * Handle submissions from the Form API
 *
 * Like hook_validate, this function (if it exists) is called after you submit a form,
 * and after the validation completes without errors.  This is where you should act on
 * the submission, confident that it has passed validation, and the values in $form_state
 * are in the final state.
 *
 * @see hook_validate($form, &$form_state)
 */
function hook_submit($form, &$form_state) {
  $values = $form_state["values"];
  db_query("INSERT INTO example (f_name) ('?') ", $values["name"]);
}


/**
 * This hook will be executed the first time a module is enabled in the system.
 *
 * It is expected to go in a PHP file named [module].install.  Ex:  system.install
 *
 * @see hook_enable()
 * @see hook_disable()
 * @see hook_update($old_schema, $new_schema)
 * @see hook_uninstall()
 */
function hook_install() {
  // Perform installation functions.
  db_query("CREATE TABLE ...... ");
}


/**
 * This hook will be executed when a module is enabled in the system.  It will be
 * executed AFTER hook_install.
 *
 * It is expected to go in a PHP file named [module].install.  Ex:  system.install
 *
 * @see hook_install()
 * @see hook_disable()
 * @see hook_update($old_schema, $new_schema)
 * @see hook_uninstall()
 */
function hook_enable() {
  fp_add_message("Don't forget to go to settings...");
}


/**
 * This hook will be executed when a module is disabled in the system.
 *
 * It is expected to go in a PHP file named [module].install.  Ex:  system.install
 *
 * @see hook_install()
 * @see hook_enable()
 * @see hook_update($old_schema, $new_schema)
 * @see hook_uninstall()
 */
function hook_disable() {
  fp_add_message("Sorry to see you go!");
}


/**
 * This hook will be executed when a module is "uninstalled" in the system.  Once
 * a module is disabled, an "uninstall" link will appear.
 *
 * It is expected to go in a PHP file named [module].install.  Ex:  system.install
 *
 * @see hook_install()
 * @see hook_enable()
 * @see hook_update($old_schema, $new_schema)
 * @see hook_disable()
 */
function hook_uninstall() {
  db_query("DROP TABLE mycustomtable ... ");
}


/**
 * This hook will be executed when a module is "updated" in the system.  If the system
 * sees that the schema version defined in the .info file is different than the one
 * in the database for that module, an update link will appear.
 *
 * It is expected to go in a PHP file named [module].install.  Ex:  system.install
 *
 * @see hook_install()
 * @see hook_enable()
 * @see hook_uninstall()
 * @see hook_disable()
 */
function hook_update($old_schema, $new_schema) {
  if ($old_schema < 5) {
    db_query("ALTER TABLE ...... ");
  }
}





/**
 * Handle needed database updates when user updates a module.
 *
 * Modules can specify which schema their tables are using in their .info file.
 * if the module, in a later version, changes the table it writes to, it should increment
 * the schema number, whic causes the user to be notified (on the modules page) that they need
 * to run the DB updates.
 *
 * When the DB updates are run, every module implementing this function will be called
 * (it is expected to be in a .install file, instead of the .module file, though both get
 * included by the system first).
 *
 * In this function, the developer can see what schema they are coming FROM, and make table
 * or other alterations based on that information.
 *
 * @param $old_schema
 *    This will be the schema value we are upgrading FROM. Ex: 0, 1, 17, etc.
 * @param $new_schema
 *    This is the new schema value in the module's .info file, that
 *    we are upgrading to.  Ex: 2, 18, etc.
 *
 */
function hook_update($old_schema, $new_schema) {
  if ($new_schema < 4) {
    db_query("ALTER TABLE my_example_table ...... ");
  }
}



/**
 * Allows each module to perform actions when the cache is cleared.
 *
 * If your custom module needs to clear its own cache, for example,
 * then you should implement this hook and place your module-specific
 * code inside.
 *
 */
function hook_clear_cache() {
  db_query("DELETE FROM mymodule_cache_tables WHERE 1");
}


/**
 * Allows each module to execute code when the module is first loaded.
 *
 * Typically, this means that we will execute code when the page is first loaded.
 * Also useful for including javascript or CSS on the page, but only under certain
 * conditions (like who the user is, or what permissions they have)
 *
 * @see hook_exit
 */
function hook_init() {
  // Perform actions when page is loaded...
  if (user_has_access("some_permission")) {
    $GLOBALS["some_variable"] = TRUE;
  }
}



/**
 * Allows each module to execute code when the FlightPath page is completely finished.
 *
 * Be aware that theming and themable functions will not work, since this hook is called
 * after the page has been completely rendered.  If you need to output debug statements for yourself,
 * use echo or print, and it will be at the BOTTOM of the page.
 *
 * @see hook_init
 */
function hook_exit() {
  // ex: Close outside db connections
  // ex:  print "Time for page to load:" . $ctime;
}



/**
 * Perform actions when the user logs in successfully.
 *
 * Notice that $account is passed by reference. You may make whatever changes
 * you wish to this object.
 */
function hook_user_login(&$account) {
  if ($account->uid = 9) {
    $account->this_is_jeff = TRUE;
  }
}




/**
 * This hook defines available permissions for a module.
 * These perms are used with hook_menu() and the function user_has_permission()
 *
 * They are defined in the form of an associative array.
 *
 * Return array should look like this:
 * $rtn["machine_name_of_perm"] = array(
 *   "title" => "Human readable title for perm",
 *   "description" => "Optional longer description of what perm allows.",
 * );
 *
 * @return array
 *
 * @see hook_menu()
 * @see user_has_permission()
 *
 */
function hook_perm() {
  // An example from system.module:

  $perms = array (
    "access_logged_in_content" => array(
      "title" => t("Access logged-in content"),
      "description" => t(".FlightPath ينبغي منحه لجميع المستخدمين المصدقين. هذا يعني أن المستخدم مسموح له بإظهار منطقة تسجيل الدخول في"),
    ),

    "administer_modules" => array(
      "title" => t("وحدات الإدارة"),
      "description" => t("هذا سيسمح للمستخدم بتثبيت و تمكين وتعطيل وإلغاء تثبيت الوحدات"),
    ),

    "run_cron" => array(
      "title" => t("Run Cron"),
      "description" => t(".حسب رغبته. سيتسبب ذلك في إظهار رابط قائمة جديدة على صفحة الإدارة run hook_cron يمكن للمستخدم تشغيل وظائف"),
    ),

    "de_can_administer_system_settings" => array(
      "title" => t("يمكن إدارة إعدادات النظام"),
      "description" => t("FlightPath هذا يسمح للمستخدم بتحرير أي من إعدادات نظام"),
    ),

    "view_fpm_debug" => array(
      "title" => t("عرض إخراج التصحيح من fpm() function"),
      "description" => t("The user may view debug output from the fpm() function. Useful for developers."),
    ),

  );

  return $perms;
}



/**
 * Allows modules to hook in after a new student object is created.
 *
 * This is so that modules can modify student objects (including what courses they
 * have taken) when that student object is first created.  For example, if you need
 * to query extra databases to get all of a student's credits.
 */
function hook_student_load(&$student) {
  if ($student->gpa > 3) {
    $student->deans_list = TRUE;
  }
}



/**
 * Allows modules to specify valid URLs in FlightPath, and define what function to call
 * when the user visits that URL.
 *
 * After making changes to your hook menu, you <b>must</b> clear the menu cache for FlightPath!
 * Otherwise, FlightPath will not be aware of your changes.  This can be done on the /admin-tools/admin page.
 *
 * This function should return an array containing everything FP needs to correctly set up
 * the menu item.
 *
 * First, the index of the array should be the URL itself.
 * Ex: $items["my/url"] = array(
 *   ...
 * );
 *
 * The URL is allowed to contain wildcards (%).
 * Ex:
 * $items["content/%/edit"] = array(...);
 *
 * The wildcard is in position "1", since the URL pieces begin with zero ("0").
 *
 * Here are what the inner parts of the array mean:
 *  - title : The title of the page or menu item.  Should not be wrapped in t() function.
 *  - page_callback: A string which defines the function to call when this URL is accessed.
 *  - page_arguments: An optional array of arguments to pass to the callback function.  May contain numerican references to wildcards in the URL.
 *  - access_callback: A string which defines a function to call to determine if the user may access this URL.
 *    Can be set to TRUE if this URL is open to anyone.  May be omitted if you simply want to test if the user has a particular
 *    permission, by only setting access_arguments.
 *  - access_arguments: An array containing the arguments to pass to the access_callback function.  If the access_callback
 *    function is omitted, it is assumed to be "user_has_permission", in which case you may simply specify
 *    an array of permissions here. May contain numerican references to wildcards in the URL.
 *  - type: A constant value describing the type of menu item it is.  Examples are:
 *    - MENU_TYPE_NORMAL_ITEM - A standard menu item.  Will output as a link if the URL conforms to certain requirements.
 *    - MENU_TYPE_CALLBACK - A menu item which will not display itself on any blocks.
 *    - MENU_TYPE_TAB - A menu item which will output the page it draws with a tab at the top, or part of a tab family.
 *    - MENU_TYPE_SUB_TAB - This will output the page as a "sub tab" under the main tab.  Ex: the student search tab's subtabs, or
 *      the View tab's Display by Year and Display By Type sub tabs.
 *  - tab_family: If this menu item is a tab, specifying a machine name for the tab family will group all other tabs
 *    in that family together, and draw the other tabs in that family at the top of the screen.
 *  - tab_parent: The *path* of the parent tab for this menu item.  Will cause the parent tab's name to be displayed
 *    as a tab at the top of the page.  Ex: "admin-tools/admin"
 *  - weight: Optional number, for when menu items are being displayed in a block.  Smaller weights appear first.
 *  - file: Path to a file that this menu item's callback is located in.
 *  - page_settings: This is an optional array which allows for more detailed control over how the menu item and the page
 *    it draws will look.  Ex:  "page_settings" => array( //....//);  See below:
 *    - page_has_search: TRUE or FALSE, whether or not the search box will be drawn at the top of the screen.  This is the
 *      search box which searches for students.
 *    - page_show_title: TRUE if the page should display the title at the top of the content region.
 *    - page_banner_is_link: TRUE or FALSE.  Does the banner at the top of the screen link back to the Main tab.  Not implemented right now.
 *    - page_hide_report_error: TRUE or FALSE.  Should the "Contact the FlightPath Production Team" link be shown at the bottom of the screen.
 *    - page_is_popup: TRUE or FALSE.  Whether or not the page is going to be displayed in a popup.  Will remove the banner and other
 *      elements which might not look good in a popup window.
 *    - display_greeting: TRUE or FALSE. Should the page have the greeting text at the top? Ex: see the Main tab.
 *    - display_currently_advising: TRUE or FALSE. Should the page display the "Currently Advising" box at the top of the page?
 *    - screen_mode: If set to "not_advising" while display_currently_advising is set to TRUE, it will not display the options
 *      to change degree options or advising terms.
 *    - bool_print: Set to TRUE if this page is meant to be printable (it is formatted to print easily).
 *    - target: The anchor target of this menu item (if it is drawn in a block). Ex: "_blank"
 *    - menu_icon: A string which points to an icon to display with this link, if it is being displayed in a block.
 *    - menu_links: An array of links.  This is an array of parameters which would fit very will into the l() function.
 *      The links will appear as simple links at the top of the page.  Will be filtered through hook_menu_handle_replacement_pattern().
 *      Ex:  "menu_links" => array(0 => array("text"=>"link", "path"=>"admin/tools", "query"=>"name=bob"))
 *      - 0..n
 *        - text: The text of the link
 *        - path: The internal path of the link. Ex: "admin-tools/admin"
 *        - query: Optional query to add to the end of the URL. Ex: "de_catalog_year=%DE_CATALOG_YEAR%"
 *          Will be filtered through hook_menu_handle_replacement_pattern()
 *
 *
 * See the examples below for demonstrations of typical menu items.
 *
 * @return array
 *
 * @see hook_perm()
 * @see hook_menu_handle_replacement_pattern()
 *
 */
function hook_menu() {
  // Examples from various modules:
  $items = array();

  $items["main"] = array(
    "title" => "الصفحة الرئيسية",
    "page_callback" => "system_display_main_page",
    "access_callback" => TRUE,
    "type" => MENU_TYPE_TAB,
    "tab_family" => "system",
    "weight" => 10,
    "page_settings" => array(
      "display_greeting" => TRUE,
      "display_currently_advising" => TRUE,
      "screen_mode" => "not_advising",
      "page_has_search" => TRUE,
    ),
  );

  $items["login"] = array(
    "title" => "تسجيل الدخول",
    "page_callback" => "system_display_login_page",
    "access_callback" => TRUE,
    "type" => MENU_TYPE_NORMAL_ITEM,
  );

 $items["admin-tools/clear-cache"] = array(
    "title" => "مسح كل ذاكرة التخزين المؤقت",
    "page_callback" => "system_perform_clear_cache",
    "access_arguments" => array("administer_modules"),
    "type" => MENU_TYPE_NORMAL_ITEM,
  );


  $items["admin/db-updates"] = array(
    "title" => "تشغيل تحديثات قاعدة البيانات",
    "page_callback" => "fp_render_form",
    "page_arguments" => array("system_confirm_db_updates_form"),
    "access_arguments" => array("administer_modules"),
    "type" => MENU_TYPE_NORMAL_ITEM,
  );


  $items["admin/config/system-settings"] = array(
    "title" => "إعدادات النظام",
    "page_callback" => "fp_render_form",
    "page_arguments" => array("system_settings_form", "system_settings"),
    "access_arguments" => array("de_can_administer_system_settings"),
    "page_settings" => array(
      "page_has_search" => FALSE,
      "page_banner_is_link" => TRUE,
      "page_hide_report_error" => TRUE,
      "menu_icon" => fp_theme_location() . "/images/toolbox.gif",
      "menu_links" => array(
        0 => array(
          "text" => "العودة إلى القائمة الرئيسية",
          "path" => "admin-tools/admin",
          "query" => "de_catalog_year=%DE_CATALOG_YEAR%",
        ),
      ),
    ),
    "type" => MENU_TYPE_NORMAL_ITEM,
    "tab_parent" => "admin-tools/admin",
  );

  $items["admin-tools/admin"] = array(
     "title" => "test",
     "page_callback" => "admin_display_main",
     "access_arguments" => array("can_access_admin"),
     "tab_family" => "admin",
     "page_settings" => array(
       "page_has_search" => FALSE,
       "page_banner_is_link" => TRUE,
       "page_hide_report_error" => TRUE,
       "target" => "_blank",
     ),
     "type" => MENU_TYPE_TAB,
  );

  $items["admin/config/modules"] = array(
    "title" => "الوحدات",
    "page_callback" => "fp_render_form",
    "page_arguments" => array("system_modules_form"),
    "access_arguments" => array("administer_modules"),
    "page_settings" => array(
      "page_has_search" => FALSE,
      "page_banner_is_link" => TRUE,
      "page_hide_report_error" => TRUE,
      "menu_links" => array(
        0 => array(
          "text" => "العودة إلى القائمة الرئيسية",
          "path" => "admin-tools/admin",
          "query" => "de_catalog_year=%DE_CATALOG_YEAR%",
        ),
      ),
    ),
    "type" => MENU_TYPE_NORMAL_ITEM,
    "tab_parent" => "admin-tools/admin",
  );


 $items["view/print"] = array(
    "title" => "إظهار",
    "page_callback" => "advise_display_view",
    "page_arguments" => array("view"),
    "access_callback" => TRUE,
    "page_settings" => array (
      "display_currently_advising" => TRUE,
      "bool_print" => TRUE,
      "screen_mode" => "not_advising",
    ),
    "type" => MENU_TYPE_CALLBACK,
  );



  $items["admin/config/clear-menu-cache"] = array(
    "title" => "مسح قائمة ذاكرة التخزين المؤقت",
    "page_callback" => "system_perform_clear_menu_cache",
    "access_arguments" => array("administer_modules"),
    "type" => MENU_TYPE_NORMAL_ITEM,
  );

  $items["system-handle-form-submit"] = array(
    "page_callback" => "system_handle_form_submit",
    "access_callback" => TRUE,
    "type" => MENU_TYPE_CALLBACK,
  );

  $items["logout"] = array(
    "title" => "تسجيل الخروج",
    "page_callback" => "system_handle_logout",
    "access_callback" => TRUE,
    "type" => MENU_TYPE_CALLBACK,
  );



  $items["popup-report-contact"] = array(
    "title" => "تقرير / تواصل",
    "page_callback" => "fp_render_form",
    "page_arguments" => array("system_popup_report_contact_form"),
    "access_callback" => TRUE,
    "page_settings" => array(
      "page_is_popup" => TRUE,
      "page_hide_report_error" => TRUE,
    ),
    "type" => MENU_TYPE_CALLBACK,
  );


  $items["popup-contact-form/thank-you"] = array(
    "title" => "تقرير / تواصل",
    "page_callback" => "system_popup_report_contact_thank_you",
    "access_callback" => TRUE,
    "page_settings" => array(
      "page_is_popup" => TRUE,
      "page_hide_report_error" => FALSE,
    ),
    "type" => MENU_TYPE_CALLBACK,
  );

  $items["admin/degrees/add-degree"] = array(
    "title" => "إضافة درجة علمية",
    "page_callback" => "fp_render_form",
    "page_arguments" => array("admin_add_degree_form"),
    "access_arguments" => array("can_edit_data_entry"),
    "page_settings" => array(
      "page_has_search" => FALSE,
      "page_banner_is_link" => TRUE,
      "page_hide_report_error" => TRUE,
      "menu_links" => array(
        0 => array(
          "text" => "العودة إلى القائمة الرئيسية",
          "path" => "admin-tools/admin",
          "query" => "de_catalog_year=%DE_CATALOG_YEAR%",
        ),
        1 => array(
          "text" => "العودة إلى قائمة الدرجات العلمية",
          "path" => "admin/degrees",
          "query" => "de_catalog_year=%DE_CATALOG_YEAR%",
        ),
      ),
    ),
    "file" => menu_get_module_path("admin") . "/admin.degrees.inc",
    "type" => MENU_TYPE_NORMAL_ITEM,
    "tab_parent" => "admin/degrees",
  );

  $items["admin/config/content"] = array(
    "title" => "محتوى",
    "page_callback" => "content_display_content_admin_list",
    "access_arguments" => array("admin_content"),
    "page_settings" => array(
      "page_has_search" => FALSE,
      "page_show_title" => TRUE,
      "page_banner_is_link" => TRUE,
      "page_hide_report_error" => TRUE,
      "menu_links" => array(
        0 => array(
          "text" => "العودة إلى القائمة الرئيسية",
          "path" => "admin-tools/admin",
          "query" => "de_catalog_year=%DE_CATALOG_YEAR%",
        ),
      ),
    ),
    "type" => MENU_TYPE_TAB,
    "tab_family" => "content_list",
  );

  $items["content/%"] = array(
    "page_callback" => "content_view_content",
    "page_arguments" => array(1),
    "access_callback" => "content_user_access",
    "access_arguments" => array("view_cid", 1),
    "page_settings" => array(
      "page_has_search" => FALSE,
      "page_show_title" => TRUE,
      "page_banner_is_link" => TRUE,
      "page_hide_report_error" => TRUE,
      "menu_links" => array(
          0 => array(
            "text" => "تعديل هذا المحتوى",
            "path" => "content/%CONTENT_CID%/edit",
            "query" => "",
          ),
        ),
    ),
    "type" => MENU_TYPE_TAB,
    "tab_parent" => "admin/config/content",
  );

  $items["content/%/edit"] = array(
    "page_callback" => "fp_render_form",
    "page_arguments" => array("content_edit_content_form", "", "", 1),
    "access_callback" => "content_user_access",
    "access_arguments" => array("edit_cid", 1),
    "page_settings" => array(
      "page_has_search" => FALSE,
      "page_banner_is_link" => TRUE,
      "page_hide_report_error" => TRUE,
      "menu_links" => array(
        0 => array(
          "text" => "العودة إلى القائمة الرئيسية",
          "path" => "admin-tools/admin",
          "query" => "de_catalog_year=%DE_CATALOG_YEAR%",
        ),
        1 => array(
          "text" => "العدوة إلى قائمة المحتويات",
          "path" => "admin/config/content",
          "query" => "de_catalog_year=%DE_CATALOG_YEAR%",
        ),
      ),
    ),
    "type" => MENU_TYPE_TAB,
    "tab_parent" => "admin/config/content",
  );

  return $items;
}





/**
 * This hook is called by the menu system.  It allows each module
 * the change to replace string patterns in its menu items (defined in hook_menu).
 *
 * @see hook_menu()
 */
function hook_menu_handle_replacement_pattern($str) {

  /*
   * This example, from admin.module, will replace the pattern %DE_CATALOG_YEAR%
   * with the actual current catalog year in the system.
   *
   * An example menu item which uses this replacement pattern would be this:
   * $items["admin/config/urgent-message"] = array(
   *   "title" => "تحرير رسالة عاجلة",
   *   "page_callback" => "fp_render_form",
   *   "page_arguments" => array("admin_urgent_message_form", "system_settings"),
   *   "access_arguments" => array("can_edit_urgent_message"),
   *   "page_settings" => array(
   *     "page_has_search" => FALSE,
   *     "page_banner_is_link" => TRUE,
   *     "page_hide_report_error" => TRUE,
   *     "menu_links" => array(
   *       0 => array(
   *         "text" => "العودة إلى القائمة الرئيسية",
   *         "path" => "admin-tools/admin",
   *        "query" => "de_catalog_year=%DE_CATALOG_YEAR%",  // RIGHT HERE!
   *       ),
   *     ),
   *  ),
   *   "type" => MENU_TYPE_NORMAL_ITEM,
   *   "tab_parent" => "admin-tools/admin",
   * );
   *
   */

  if (strpos($str, "%DE_CATALOG_YEAR%") !== 0) {
    // It contains this replacement pattern!
    $str = str_replace("%DE_CATALOG_YEAR%", admin_get_de_catalog_year(), $str);
  }

  return $str;
}


/**
 * This hook allows modules to perform extra functions just after an advising
 * session is saved by the system.
 *
 * The main advising form (on the View and What If tabs) are currently _not_ defined
 * through the system's form API.  Therefore, a simple hook_submit wouldn't be possible.
 *
 * This hook was created to bridge that gap, until the advising form can be brought into
 * the form API (possibly by 5.x)
 *
 * @param $adv_id_array
 *   Since this hook is called immediately after submitting the advising session,
 *   this array will contain the database ID's to the rows added to the advising_sessions table.
 *
 */
function hook_save_advising_session($adv_id_array) {
  foreach ($adv_id_array as $id) {
    // Perform extra actions on those advising sessions.
  }
  // Example functions:
  // Email student a copy of what was just advised for them to take.
  // Maybe lift an advising flag in the mainframe system?
}
