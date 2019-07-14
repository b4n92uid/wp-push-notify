<?php

class Settings
{
  public $fcmAccessKey;
  public $mobileTags;
  public $sendOnUpdate;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->fcmAccessKey = get_option('wppn_fcm_access_key');
    $this->mobileTags = explode(',', get_option('wppn_posts_mobile_tags'));
    $this->sendOnUpdate = get_option('wppn_send_on_update') === 'on';
  }

  public function init()
  {
    // register a new setting for "reading" page
    register_setting('general', 'wppn_fcm_access_key');
    register_setting('general', 'wppn_posts_mobile_tags');
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
      'wppn_posts_mobile_tags',
      'Post tags',
      [$this, 'inputPostTags'],
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

  public function inputPostTags()
  {
    include 'include/input-post-tags.php';
  }

  public function inputSendOnUpdate()
  {
    include 'include/input-send-on-update.php';
  }

  public function sectionDescription()
  { }
}
