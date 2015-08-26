<?php

/**
 * @file
 * Contains \Drupal\web_services\Plugin\rest\resource\NodeStatusResource.
 */

namespace Drupal\web_services\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Psr\Log\LoggerInterface;
use Drupal\node\Entity\Node;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "node_status_resource",
 *   label = @Translation("Node status resource"),
 *   uri_paths = {
 *     "canonical" = "/node-status/{id}"
 *   }
 * )
 */
class NodeStatusResource extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a Drupal\rest\Plugin\ResourceBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;
    $this->node = $node;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('current_user')
    );
  }

  /**
   * Responds to node status GET requests.
   *
   * @param string $node
   *   Node nid.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function get($id = NULL) {
    if(!$this->currentUser->hasPermission('restful get node_status_resource')) {
      throw new AccessDeniedHttpException('Access denied');
    }

    // Check whether parameter is passed.
    if (empty($id)) {
      throw new HttpException(t('Parameter not passed'));
    }

    $node = Node::load($id);

    // Check whether valid node is present for the given parameter.
    if (!$node) {
      throw new NotFoundHttpException(t('Node not present for this parameter'));
    }

    return new ResourceResponse($node->isPublished());
  }

  /**
   * Responds to node status PATCH requests and updates node status.
   *
   * @param string $id
   *   Node id.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function patch($id = NULL) {
    if(!$this->currentUser->hasPermission('restful patch node_status_resource')) {
      throw new AccessDeniedHttpException('Access denied');
    }

    // Check whether parameters are passed.
    if (empty($id)) {
      throw new HttpException('Parameters not passed');
    }

    $node = Node::load($id);

    // Check whether valid node is present for the given parameter.
    if (!$node) {
      throw new HttpException('Node not present for this parameter');
    }

    return new ResourceResponse("Implement REST State POST!");
  }

}
