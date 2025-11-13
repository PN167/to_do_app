<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Task;

/**
 * Tasks Controller
 *
 * @property \App\Model\Table\TasksTable $Tasks
 */
class TasksController extends AppController
{
    /**
     * Index method
     * Shows only tasks belonging to the logged-in user
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // Get the logged-in user's ID
        $userId = $this->Authentication->getIdentity()->getIdentifier();

        // Get status filter from query params (optional)
        $statusFilter = $this->request->getQuery('status');

        // Query only THIS user's tasks
        $query = $this->Tasks->find()
            ->where(['Tasks.user_id' => $userId]);  // 🔒 SECURITY: Only show user's own tasks

        // Apply status filter if provided
        if ($statusFilter && in_array($statusFilter, ['not_started', 'in_progress', 'completed'])) {
            $query->where(['Tasks.status' => $statusFilter]);
        }

        $query->order(['Tasks.created' => 'DESC']);

        $tasks = $this->paginate($query);

        // Get statistics for the dashboard
        $statistics = $this->Tasks->getStatistics($userId);

        // Get available statuses and priorities for filters/forms
        $statuses = Task::getStatuses();
        $priorities = Task::getPriorities();

        // Get current filter for the view
        $currentFilter = $statusFilter;

        $this->set(compact('tasks', 'statistics', 'statuses', 'priorities', 'currentFilter'));
    }

    /**
     * View method
     * View a single task (only if it belongs to the logged-in user)
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $userId = $this->Authentication->getIdentity()->getIdentifier();

        // 🔒 SECURITY: Only allow viewing if task belongs to this user
        $task = $this->Tasks->find()
            ->where([
                'Tasks.id' => $id,
                'Tasks.user_id' => $userId
            ])
            ->firstOrFail();

        $this->set(compact('task'));
    }

    /**
     * Add method
     * Create a new task for the logged-in user
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $userId = $this->Authentication->getIdentity()->getIdentifier();

        $task = $this->Tasks->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // 🔒 SECURITY: Force user_id to be the logged-in user
            $data['user_id'] = $userId;

            $task = $this->Tasks->patchEntity($task, $data);

            if ($this->Tasks->save($task)) {
                $this->Flash->success(__('The task has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The task could not be saved. Please, try again.'));
        }

        $statuses = Task::getStatuses();
        $priorities = Task::getPriorities();

        $this->set(compact('task', 'statuses', 'priorities'));
    }

    /**
     * Edit method
     * Update a task (only if it belongs to the logged-in user)
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $userId = $this->Authentication->getIdentity()->getIdentifier();

        // 🔒 SECURITY: Only allow editing if task belongs to this user
        $task = $this->Tasks->find()
            ->where([
                'Tasks.id' => $id,
                'Tasks.user_id' => $userId
            ])
            ->firstOrFail();

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // 🔒 SECURITY: Prevent changing the owner
            unset($data['user_id']);

            $task = $this->Tasks->patchEntity($task, $data);

            if ($this->Tasks->save($task)) {
                $this->Flash->success(__('The task has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The task could not be saved. Please, try again.'));
        }

        $statuses = Task::getStatuses();
        $priorities = Task::getPriorities();

        $this->set(compact('task', 'statuses', 'priorities'));
    }

    /**
     * Delete method
     * Delete a task (only if it belongs to the logged-in user)
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $userId = $this->Authentication->getIdentity()->getIdentifier();

        // 🔒 SECURITY: Only allow deleting if task belongs to this user
        $task = $this->Tasks->find()
            ->where([
                'Tasks.id' => $id,
                'Tasks.user_id' => $userId
            ])
            ->firstOrFail();

        if ($this->Tasks->delete($task)) {
            $this->Flash->success(__('The task has been deleted.'));
        } else {
            $this->Flash->error(__('The task could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
