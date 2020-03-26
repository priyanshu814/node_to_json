<?php

namespace Drupal\axelerant_test\Controller;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CustomUrlController.
 */
class NodeToJson extends ControllerBase {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Constructs a new CustomUrlController object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ConfigFactoryInterface $config) {
    $this->entityTypeManager = $entity_type_manager;
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
            $container->get('entity_type.manager'), $container->get('config.factory')
    );
  }

  /**
   * Generate.
   *
   * @return string
   *   Return Hello string.
   */
  public function generate($siteapikey, $nodeid) {
    // Check the site api key.
    $key = $this->config->getEditable('system.site')->get('siteapikey');
    // Load the nid based on url.
    $node = $this->entityTypeManager->getStorage('node')->load($nodeid);
    // Check for condition.
    if ($node && $siteapikey == $key && $node->getType() == 'page') {
      return new JsonResponse($node->toArray());
    }
    // Throw access denied.
    else {
      throw new AccessDeniedHttpException();
    }
  }

}
