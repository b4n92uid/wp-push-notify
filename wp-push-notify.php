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

    add_action('save_post', [$this, 'send'], 10, 3);

    add_action('admin_menu', [$this, 'initAdminMenu']);
  }

  public function initAdminMenu()
  {
    $this->settings->init();
  }

  public function inInCategories($postCategories)
  {
    foreach ($postCategories as $c) {
      if (in_array($c->term_id, $this->settings->postCategoriesId))
        return true;
    }

    return false;
  }

  public function errorNotice()
  {
    var_dump('errorNotice');
    die;
    include './include/error.php';
  }

  /**
   * @param int $post_id
   * @param WP_Post $post
   * @param bool $update
   */
  public function send($post_id, $post, $update)
  {
    if (!($update && $this->settings->sendOnUpdate)) {
      return;
    }

    if ('post' !== $post->post_type) {
      return;
    }

    $categories = get_the_category($post_id);

    if (!$this->inInCategories($categories)) {
      return;
    }

    $notification = [
      'title' => $post->post_title,
      'body' => get_the_excerpt($post_id),
    ];

    $data = [
      "postId" => $post_id,
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
      $res = $client->post(FCM_URL, [
        'headers' => $headers,
        'body' => json_encode($fcmNotification),
        'verify' => false
      ]);
    } catch (\Exception $e) { }
  }
}

new WP_PushNotify();
