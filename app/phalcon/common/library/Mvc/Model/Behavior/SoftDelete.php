<?php
namespace Webird\Mvc\Model\Behavior;

use Phalcon\Mvc\Model\Behavior,
    Phalcon\Mvc\Model\BehaviorInterface,
    Phalcon\Mvc\ModelInterface,
    Phalcon\Mvc\Model\Message,
    Phalcon\Mvc\Model\Relation;

/**
 * Webird\Mvc\Model\Behavior\SoftDelete
 */
class SoftDelete extends Behavior implements BehaviorInterface
{

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

            $this->fireEvent($model, 'beforeSoftDelete');

            $updateModel = clone $model;
            $updateModel->writeAttribute($field, $value);

            if (!$updateModel->update()) {
                foreach ($updateModel->getMessages() as $message) {
                    $model->appendMessage($message);
                }
                return false;
            }

            $model->writeAttribute($field, $value);

            if (isset($options['cascade']) && $options['cascade'] === true) {
                $this->cascadeDelete($model);
            }

            $this->fireEvent($model, 'afterSoftDelete');
        }
    }




    private function cascadeDelete($model)
    {
        $modelsManager = $model->getModelsManager();

        $hasManyRelations = $modelsManager->getHasMany($model);
        foreach ($hasManyRelations as $relation) {
            $relOptions = $relation->getOptions();

            $foreignKey = $relOptions['foreignKey'];
            if (isset($foreignKey['action']) && $foreignKey['action'] === Relation::ACTION_CASCADE) {
                $alias = $relOptions['alias'];
                $relatedModels = $model->{"get{$alias}"}();
                foreach ($relatedModels as $relModel) {
                    $relModel->delete();
                }
            }
        }
    }




    private function fireEvent($model, $eventName)
    {
        $options = $this->getOptions();

        // Force the event to fire since the delete operation is being skipped over
        if (isset($options[$eventName])) {
            $options->$eventName();
        } else if (method_exists($model, $eventName)) {
            $model->{$eventName}();
        }
    }


}
