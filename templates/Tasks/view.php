<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Task $task
 */
?>
<div class="tasks view content">
    <div class="page-header">
        <h3><?= h($task->title) ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $task->id], ['class' => 'button button-primary']) ?>
            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $task->id], ['confirm' => __('Are you sure you want to delete this task?'), 'class' => 'button button-danger']) ?>
            <?= $this->Html->link(__('← Back to List'), ['action' => 'index'], ['class' => 'button button-secondary']) ?>
        </div>
    </div>

    <div class="task-details">
        <div class="detail-section">
            <h4>Task Information</h4>
            <table class="details-table">
                <tr>
                    <th><?= __('ID') ?></th>
                    <td><?= $this->Number->format($task->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Title') ?></th>
                    <td><?= h($task->title) ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td>
                        <span class="status-badge status-<?= h($task->status) ?>">
                            <?= h($task->status) ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th><?= __('Priority') ?></th>
                    <td>
                        <span class="priority-badge priority-<?= h($task->priority) ?>">
                            <?= h($task->priority) ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th><?= __('Due Date') ?></th>
                    <td>
                        <?php if ($task->due_date): ?>
                            <?= h($task->due_date->format('F d, Y H:i')) ?>
                            <?php if ($task->isOverdue()): ?>
                                <span class="overdue-label">⚠️ Overdue</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">No due date set</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($task->created->format('F d, Y H:i')) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($task->modified->format('F d, Y H:i')) ?></td>
                </tr>
            </table>
        </div>

        <?php if ($task->description): ?>
            <div class="detail-section">
                <h4>Description</h4>
                <div class="description-content">
                    <?= $this->Text->autoParagraph(h($task->description)); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .task-details {
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .detail-section {
        margin-bottom: 30px;
    }

    .detail-section h4 {
        margin-bottom: 15px;
        color: #333;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
    }

    .details-table {
        width: 100%;
        border-collapse: collapse;
    }

    .details-table th {
        text-align: left;
        padding: 12px;
        background: #f8f9fa;
        font-weight: 600;
        width: 200px;
        border-bottom: 1px solid #dee2e6;
    }

    .details-table td {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
    }

    .description-content {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 5px;
        line-height: 1.6;
    }

    .status-badge, .priority-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.85em;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-not_started {
        background: #6c757d;
        color: #fff;
    }

    .status-in_progress {
        background: #ffc107;
        color: #000;
    }

    .status-completed {
        background: #28a745;
        color: #fff;
    }

    .priority-low {
        background: #17a2b8;
        color: #fff;
    }

    .priority-medium {
        background: #ffc107;
        color: #000;
    }

    .priority-high {
        background: #dc3545;
        color: #fff;
    }

    .overdue-label {
        color: #dc3545;
        font-weight: bold;
        font-size: 0.85em;
        margin-left: 10px;
    }

    .text-muted {
        color: #6c757d;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .header-actions {
        display: flex;
        gap: 10px;
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

    .button-danger {
        background: #dc3545;
        color: #fff;
    }

    .button-danger:hover {
        background: #c82333;
    }
</style>
