<?php
namespace Webird\Mvc\Model\Behavior;

use Phalcon\Mvc\Model\Behavior,
    Phalcon\Mvc\Model\BehaviorInterface,
    Phalcon\Mvc\ModelInterface,
    Phalcon\Mvc\Model\Message;

/**
 * Webird\Mvc\Model\Behavior\SoftDelete
 */
class SoftDelete extends Behavior implements BehaviorInterface
{

    /**
     * Class constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        if (!array_key_exists('cascadeDelete', $options)) {
            $options['cascadeDelete'] = false;
        }

        parent::__construct($options);
    }

    /**
     *  {@inheritdoc}
     *
     * @param string                      $eventType
     * @param \Phalcon\Mvc\ModelInterface $model
     */
    public function notify($eventType, $model)
    {
        if ($eventType == 'beforeDelete') {
            $options = $this->getOptions();

            $field = $options['field'];
            $value = $options['value'];

            $model->skipOperation(true);

            if ($model->readAttribute($field) === $value) {
                $model->appendMessage(new Message('Model was already deleted'));
                return false;
            }

            // Force the event to fire since the delete operation is being skipped over
            if (is_callable([$model, 'beforeSoftDelete'])) {
                $model->beforeSoftDelete();
            }

            $updateModel = clone $model;
            $updateModel->writeAttribute($field, $value);

            if (!$updateModel->update()) {
                foreach ($updateModel->getMessages() as $message) {
                    $model->appendMessage($message);
                }
                return false;
            }

            $model->writeAttribute($field, $value);

            if ($options['cascadeDelete']) {
                $modelsManager = $model->getModelsManager();
                $hasManyRelations = $modelsManager->getHasMany($model);
                foreach ($hasManyRelations as $relation) {
                    $alias = $relation->getOptions()['alias'];
                    $relatedModels = $model->{"get{$alias}"}();
                    foreach ($relatedModels as $relModel) {
                        $relModel->delete();
                    }
                }
            }

            // Force the event to fire since the delete operation is being skipped over
            if (is_callable([$model, 'afterSofDelete'])) {
                $model->afterSoftDelete();
            }
        }
    }
}
