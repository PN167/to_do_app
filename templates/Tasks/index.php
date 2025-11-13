<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Task> $tasks
 * @var array $statistics
 * @var array $statuses
 * @var array $priorities
 * @var string|null $currentFilter
 */
?>
<div class="tasks index content">
    <div class="page-header">
        <h3><?= __('My Tasks') ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                __('+ New Task'),
                ['action' => 'add'],
                ['class' => 'button button-primary']
            ) ?>
            <?= $this->Html->link(
                __('Logout'),
                ['controller' => 'Auth', 'action' => 'logout'],
                ['class' => 'button button-secondary']
            ) ?>
        </div>
    </div>

    <!-- Statistics Dashboard -->
    <div class="statistics-dashboard">
        <div class="stat-card">
            <div class="stat-value"><?= $statistics['total'] ?></div>
            <div class="stat-label">Total Tasks</div>
        </div>

        <div class="stat-card stat-not-started">
            <div class="stat-value"><?= $statistics['not_started'] ?></div>
            <div class="stat-label">Not Started</div>
        </div>

        <div class="stat-card stat-in-progress">
            <div class="stat-value"><?= $statistics['in_progress'] ?></div>
            <div class="stat-label">In Progress</div>
        </div>

        <div class="stat-card stat-completed">
            <div class="stat-value"><?= $statistics['completed'] ?></div>
            <div class="stat-label">Completed</div>
        </div>

        <div class="stat-card stat-completion">
            <div class="stat-value"><?= number_format($statistics['completion_rate'], 1) ?>%</div>
            <div class="stat-label">Completion Rate</div>
        </div>

        <?php if ($statistics['overdue'] > 0): ?>
            <div class="stat-card stat-overdue">
                <div class="stat-value"><?= $statistics['overdue'] ?></div>
                <div class="stat-label">Overdue</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="filters">
        <h4>Filter by Status:</h4>
        <div class="filter-buttons">
            <?= $this->Html->link(
                __('All'),
                ['action' => 'index'],
                ['class' => empty($currentFilter) ? 'filter-btn active' : 'filter-btn']
            ) ?>
            <?= $this->Html->link(
                __('Not Started'),
                ['action' => 'index', '?' => ['status' => 'not_started']],
                ['class' => $currentFilter === 'not_started' ? 'filter-btn active' : 'filter-btn']
            ) ?>
            <?= $this->Html->link(
                __('In Progress'),
                ['action' => 'index', '?' => ['status' => 'in_progress']],
                ['class' => $currentFilter === 'in_progress' ? 'filter-btn active' : 'filter-btn']
            ) ?>
            <?= $this->Html->link(
                __('Completed'),
                ['action' => 'index', '?' => ['status' => 'completed']],
                ['class' => $currentFilter === 'completed' ? 'filter-btn active' : 'filter-btn']
            ) ?>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="table-responsive">
        <?php if ($tasks->count() > 0): ?>
            <table class="tasks-table">
                <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id', '#') ?></th>
                    <th><?= $this->Paginator->sort('title', 'Task Title') ?></th>
                    <th><?= $this->Paginator->sort('status', 'Status') ?></th>
                    <th><?= $this->Paginator->sort('priority', 'Priority') ?></th>
                    <th><?= $this->Paginator->sort('due_date', 'Due Date') ?></th>
                    <th><?= $this->Paginator->sort('created', 'Created') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr class="task-row <?= $task->isOverdue() ? 'task-overdue' : '' ?>">
                        <td><?= $this->Number->format($task->id) ?></td>
                        <td>
                            <strong><?= h($task->title) ?></strong>
                            <?php if ($task->description): ?>
                                <br><small class="task-description"><?= h($this->Text->truncate($task->description, 100)) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                        <span class="status-badge status-<?= h($task->status) ?>">
                            <?= h($statuses[$task->status] ?? $task->status) ?>
                        </span>
                        </td>
                        <td>
                        <span class="priority-badge priority-<?= h($task->priority) ?>">
                            <?= h($priorities[$task->priority] ?? $task->priority) ?>
                        </span>
                        </td>
                        <td>
                            <?php if ($task->due_date): ?>
                                <?= $task->due_date->format('M d, Y') ?>
                                <?php if ($task->isOverdue()): ?>
                                    <span class="overdue-label">⚠️ Overdue</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">No due date</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $task->created->format('M d, Y') ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['action' => 'view', $task->id], ['class' => 'action-link']) ?>
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $task->id], ['class' => 'action-link']) ?>
                            <?= $this->Form->postLink(
                                __('Delete'),
                                ['action' => 'delete', $task->id],
                                [
                                    'confirm' => __('Are you sure you want to delete "{0}"?', $task->title),
                                    'class' => 'action-link delete-link'
                                ]
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>No tasks found. <?= $this->Html->link('Create your first task!', ['action' => 'add']) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($tasks->count() > 0): ?>
        <div class="paginator">
            <ul class="pagination">
                <?= $this->Paginator->first('<< ' . __('first')) ?>
                <?= $this->Paginator->prev('< ' . __('previous')) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(__('next') . ' >') ?>
                <?= $this->Paginator->last(__('last') . ' >>') ?>
            </ul>
            <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
        </div>
    <?php endif; ?>
</div>

<style>
    /* Statistics Dashboard */
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

    .statistics-dashboard {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .stat-value {
        font-size: 2.5em;
        font-weight: bold;
        color: #333;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.9em;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-not-started { border-left: 4px solid #6c757d; }
    .stat-in-progress { border-left: 4px solid #ffc107; }
    .stat-completed { border-left: 4px solid #28a745; }
    .stat-completion { border-left: 4px solid #007bff; }
    .stat-overdue { border-left: 4px solid #dc3545; }

    /* Filters */
    .filters {
        margin-bottom: 25px;
    }

    .filters h4 {
        margin-bottom: 10px;
        color: #555;
    }

    .filter-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 8px 16px;
        border: 1px solid #ddd;
        border-radius: 5px;
        text-decoration: none;
        color: #333;
        background: #fff;
        transition: all 0.3s;
    }

    .filter-btn:hover {
        background: #f8f9fa;
        border-color: #007bff;
    }

    .filter-btn.active {
        background: #007bff;
        color: #fff;
        border-color: #007bff;
    }

    /* Tasks Table */
    .tasks-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .tasks-table th {
        background: #f8f9fa;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    .tasks-table td {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
    }

    .task-row:hover {
        background: #f8f9fa;
    }

    .task-row.task-overdue {
        background: #fff5f5;
    }

    .task-description {
        color: #666;
        display: block;
        margin-top: 5px;
    }

    /* Status Badges */
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
        margin-left: 5px;
    }

    /* Actions */
    .actions {
        white-space: nowrap;
    }

    .action-link {
        margin-right: 10px;
        text-decoration: none;
        color: #007bff;
    }

    .action-link:hover {
        text-decoration: underline;
    }

    .delete-link {
        color: #dc3545;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #f8f9fa;
        border-radius: 8px;
        color: #666;
    }

    .empty-state p {
        font-size: 1.1em;
    }

    /* Buttons */
    .button {
        display: inline-block;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: 500;
        transition: all 0.3s;
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

    /* Responsive */
    @media (max-width: 768px) {
        .statistics-dashboard {
            grid-template-columns: repeat(2, 1fr);
        }

        .table-responsive {
            overflow-x: auto;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
    }
</style>
