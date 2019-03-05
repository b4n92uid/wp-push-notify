<?php

class Settings
{
  public $fcmAccessKey;
  public $postCategoriesId;
  public $sendOnUpdate;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->fcmAccessKey = get_option('wppn_fcm_access_key');
    $this->postCategoriesId = explode(',', get_option('wppn_posts_categories_id'));
    $this->sendOnUpdate = get_option('wppn_send_on_update');
  }

  public function init()
  {
    // register a new setting for "reading" page
    register_setting('general', 'wppn_fcm_access_key');
    register_setting('general', 'wppn_posts_categories_id');
    register_setting('general', 'wppn_send_on_update');

    add_settings_section(
      'wppn_section',
      'WP Push Notify',
      [$this, 'sectionDescription'],
      'general'
    );

    add_settings_field(
      'wppn_fcm_access_key',
      'FCM Access Key',
      [$this, 'inputFcmAccessKey'],
      'general',
      'wppn_section'
    );

    add_settings_field(
      'wppn_posts_categories_id',
      'Post categories ids',
      [$this, 'inputCategoriesId'],
      'general',
      'wppn_section'
    );

    add_settings_field(
      'wppn_send_on_update',
      'Send on post update',
      [$this, 'inputSendOnUpdate'],
      'general',
      'wppn_section'
    );
  }

  public function inputFcmAccessKey()
  {
    include 'include/input-fcm-access-key.php';
  }

  public function inputCategoriesId()
  {
    include 'include/input-categories-id.php';
  }

  public function inputSendOnUpdate()
  {
    include 'include/input-send-on-update.php';
  }

  public function sectionDescription()
  { }
}