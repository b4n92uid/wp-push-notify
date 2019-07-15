<?php

/**
 * Plugin Name: WP Push Notify
 * Description: Basic PUSH Notification to FCM on post save
 * Author: BELDJOUHRI Abdelghani
 * Author URI: http://beldjouhri-abdelghani.com/
 */

use function GuzzleHttp\json_encode;

require 'vendor/autoload.php';
require 'settings.php';

define('FCM_URL', 'https://fcm.googleapis.com/fcm/send');

class WP_PushNotify
{
  /**
   * @var Settings $settings
   */
  protected $settings = null;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->settings = new Settings;

    if ($this->settings->sendOnUpdate) {
      add_action('save_post', [$this, 'send'], 10, 1);
    } else {
      add_action('draft_to_publish', [$this, 'send'], 10, 1);
    }

    add_action('admin_menu', [$this, 'initAdminMenu']);
  }

  public function initAdminMenu()
  {
    $this->settings->init();
  }

  public function isForMobile($postId)
  {
    $tags = get_the_tags($postId);

    foreach ($tags as $t) {
      if (in_array($t->slug, $this->settings->mobileTags))
        return true;
    }

    return false;
  }

  public function errorNotice()
  {
    include './include/error.php';
  }

  public function send($ID)
  {
    if (!$this->isForMobile($ID)) {
      return;
    }

    $post = get_post($ID);

    $notification = [
      'title' => $post->post_title,
      'body' => get_the_excerpt($ID),
    ];

    $data = [
      "postId" => $ID,
    ];

    $fcmNotification = [
      'notification' => $notification,
      'data' => $data,
      'to' => '/topics/posts'
    ];

    $headers = [
      'Authorization' => 'key=' . $this->settings->fcmAccessKey,
      'Content-Type' => 'application/json'
    ];

    try {
      $client = new \GuzzleHttp\Client();
      $client->post(FCM_URL, [
        'headers' => $headers,
        'body' => json_encode($fcmNotification),
        'verify' => false
      ]);
    } catch (\Exception $e) { }
  }
}

new WP_PushNotify();
