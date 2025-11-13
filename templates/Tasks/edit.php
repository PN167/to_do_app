<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Task $task
 * @var array $statuses
 * @var array $priorities
 */
?>
<div class="tasks form content">
    <div class="page-header">
        <h3><?= __('Edit Task') ?></h3>
        <?= $this->Html->link(__('← Back to Tasks'), ['action' => 'index'], ['class' => 'button button-secondary']) ?>
    </div>

    <div class="form-container">
        <?= $this->Form->create($task) ?>
        <fieldset>
            <?php
            echo $this->Form->control('title', [
                'label' => 'Task Title *',
                'placeholder' => 'Enter task title',
                'required' => true,
                'class' => 'form-input'
            ]);

            echo $this->Form->control('description', [
                'label' => 'Description',
                'type' => 'textarea',
                'rows' => 4,
                'placeholder' => 'Enter task description (optional)',
                'class' => 'form-input'
            ]);

            echo $this->Form->control('status', [
                'label' => 'Status *',
                'options' => $statuses,
                'required' => true,
                'class' => 'form-select'
            ]);

            echo $this->Form->control('priority', [
                'label' => 'Priority *',
                'options' => $priorities,
                'required' => true,
                'class' => 'form-select'
            ]);

            echo $this->Form->control('due_date', [
                'label' => 'Due Date',
                'type' => 'datetime-local',
                'empty' => true,
                'class' => 'form-input'
            ]);
            ?>
        </fieldset>
        <div class="form-actions">
            <?= $this->Form->button(__('Update Task'), ['class' => 'button button-primary']) ?>
            <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'button button-secondary']) ?>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $task->id],
                [
                    'confirm' => __('Are you sure you want to delete this task?'),
                    'class' => 'button button-danger'
                ]
            ) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>

<style>
    .form-container {
        max-width: 600px;
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    fieldset {
        border: none;
        padding: 0;
        margin: 0;
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 1em;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #333;
    }

    textarea.form-input {
        resize: vertical;
        font-family: inherit;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .button-danger {
        background: #dc3545;
        color: #fff;
        margin-left: auto;
    }

    .button-danger:hover {
        background: #c82333;
    }

    .error-message {
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 5px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .button {
        display: inline-block;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: 500;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
    }

    .button-primary {
        background: #007bff;
        color: #fff;
    }

    .button-primary:hover {
        background: #0056b3;
    }

    .button-secondary {
        background: #6c757d;
        color: #fff;
    }

    .button-secondary:hover {
        background: #545b62;
    }
</style>
