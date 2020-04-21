<?php

namespace Drupal\my_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use \Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Component\HttpFoundation\JsonResponse;
use \Symfony\Component\HttpFoundation\Request;
use Drupal\node\Entity\Node;

/**
 * Class MyModuleController.
 *
 * @package Drupal\my_module\Controller
*/
class MyModuleController extends ControllerBase {

  public function accessCheck(Request $request) {
    $current_path = \Drupal::service('path.current')->getPath();
    $api_key = \Drupal::config('siteapikey.configuration')->get('siteapikey');
    $url_api_key = explode("/", $current_path);

    if($url_api_key[2] == $api_key) {
      $node = Node::load($url_api_key[3]);
      if(isset($node) && !empty($node) && $node->get('type')->target_id =='page') {
        $json_array = [];
        $json_array['data'] = [
          'type' => $node->get('type')->target_id,
          'id' => $node->get('nid')->value,
          'title' =>  $node->get('title')->value,
          'body' => $node->get('body')->value,
        ];
        return new JsonResponse($json_array);
      }
      else {
        $response = new RedirectResponse('/system/404');
        $response->send();
        return;
      }
    }
    else {
      $response = new RedirectResponse('/system/403');
      $response->send();
      return;
    }
   }

}
