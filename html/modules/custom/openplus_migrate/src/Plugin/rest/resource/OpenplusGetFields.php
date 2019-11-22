<?php
namespace Drupal\openplus_migrate\Plugin\rest\resource;

use Drupal\node\Entity\Node;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\Core\Session\AccountProxyInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Cache\CacheableResponseInterface;
use Drupal\openplus_migrate\Util\MigrationUtil;

/**
 *
 * @RestResource(
 *   id = "openplus_get_fields",
 *   label = @Translation("Bundle fields GET"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/get-fields/{type}"
 *   }
 * )
 */
class OpenplusGetFields extends ResourceBase {

  public function get($bundle) {
    $output = array();

    $skip_fields = ['body', 'comment', 'field_meta_tags', 'field_migration_state', 'layout_selection'];
    $entityFieldManager = \Drupal::service('entity_field.manager');
    $fields = $entityFieldManager->getFieldDefinitions('node', $bundle);
    $output = [];
    foreach ($fields as $field_name => $field_definition) {
      if ($field_definition->getFieldStorageDefinition()->isBaseField() == FALSE && !in_array($field_name, $skip_fields)) {
        $output[$field_name]['type'] = $field_definition->getType();
        $output[$field_name]['label'] = $field_definition->getLabel();
      }
    }

    $build = array(
      '#cache' => array(
        'max-age' => 0,
      ),
    );

    return (new ResourceResponse($output))->addCacheableDependency($build);
  }


}
