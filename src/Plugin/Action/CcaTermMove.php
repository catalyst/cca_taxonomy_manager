<?php

namespace Drupal\cca_taxonomy_manager\Plugin\Action;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\TermInterface;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsPreconfigurationInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Psr\Container\ContainerInterface;

/**
 * Term move (bulk) action.
 *
 * @todo:
 * a. What happens to fields from source vocabulary? Do they just get stranded and ignored?
 *
 * @Action(
 *   id = "cca_term_move",
 *   label = @Translation("Move term"),
 *   type = "taxonomy_term",
 *   confirm = FALSE,
 *   requirements = {
 *     "_permission" = "merge taxonomy terms",
 *   },
 * )
 */
class CcaTermMove extends ViewsBulkOperationsActionBase implements ViewsBulkOperationsPreconfigurationInterface, PluginFormInterface {

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a \Drupal\Component\Plugin\PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager = NULL) {
    $this->configuration = $configuration;
    $this->pluginId = $plugin_id;
    $this->pluginDefinition = $plugin_definition;

    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager ?: \Drupal::entityTypeManager();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildPreConfigurationForm(array $form, array $values, FormStateInterface $form_state) {
    // noop, required by inheritance.
    return $form;
  }

  /**
   * Prompt user for target vocabulary to move term(s) into.
   *
   * @param array $form
   *   Form array.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   *
   * @return array
   *   The configuration form.
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    $vids = taxonomy_vocabulary_get_names();

    $form['target_vocabulary'] = [
      '#type' => 'select',
      '#title' => t('Select target vocabulary:'),
      '#required' => true,
      '#options' => $vids,
      '#attributes' => [
        'size' => 1,
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {

    $targetVocabulary = $this->configuration['target_vocabulary'];
    // @todo: is the target vocab the same as term vocab? If so, skip.
    $entity->set('vid', $targetVocabulary);
    // @todo: what do do re parents and children?
    $entity->set('parent', 0); // remove parent term
    $entity->save();
    // Clear cache for term.
    \Drupal::entityTypeManager()->getStorage('taxonomy_term')->resetCache([$entity->id()]);

    // Because pathauto generates alias based on pattern for entity based on old value for term vocab,
    // not target vocab, we can't just call
    // \Drupal::service('pathauto.generator')->updateEntityAlias($entity, 'update');
    // and instead need to create local service and clear caches.
    if (\Drupal::moduleHandler()->moduleExists('pathauto')) {
      $pathAutoGenerator = \Drupal::service('pathauto.generator');
      $pathAutoGenerator->resetCaches();
      $pathAutoGenerator->updateEntityAlias(Term::load($entity->id()), 'update');
    }

    return sprintf('Moved term %s to %s.', $entity->getName(), $targetVocabulary);
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    if ($object->getEntityType() === 'taxonomy_term') {
      $access = $object->access('update', $account, TRUE)
        ->andIf($object->status->access('edit', $account, TRUE));
      return $return_as_object ? $access : $access->isAllowed();
    }

    // Other entity types may have different
    // access methods and properties.
    return TRUE;
  }

  /**
   * Returns the vocabulary ID from the first selected term's bundle.
   * (from term_merge module)
   *
   * @return string|null
   *   Vocabulary machine name. NULL if there's none.
   */
  protected function getVocabularyId() {
    if (isset(reset($this->context['list'])[3])) {
      $firstTermId = reset($this->context['list'])[3];
      $firstTerm = $this->entityTypeManager->getStorage('taxonomy_term')->load($firstTermId);
      if ($firstTerm instanceof TermInterface) {
        return $firstTerm->bundle();
      }
    }
    return NULL;
  }
}
