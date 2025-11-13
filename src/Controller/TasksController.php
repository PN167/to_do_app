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
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $userId = $this->Authentication->getIdentity()->getIdentifier();
        $statusFilter = $this->request->getQuery('status');

        $query = $this->Tasks->find()
            ->where(['Tasks.user_id' => $userId]);

        if ($statusFilter && in_array($statusFilter, ['not_started', 'in_progress', 'completed'])) {
            $query->where(['Tasks.status' => $statusFilter]);
        }

        $query->order(['Tasks.created' => 'DESC']);
        $tasks = $this->paginate($query);

        $statistics = $this->Tasks->getStatistics($userId);
        $statuses = Task::getStatuses();
        $priorities = Task::getPriorities();
        $currentFilter = $statusFilter;

        $this->set(compact('tasks', 'statistics', 'statuses', 'priorities', 'currentFilter'));
        $this->viewBuilder()->setOption('serialize', ['tasks', 'statistics']);
    }

    /**
     * View method
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $userId = $this->Authentication->getIdentity()->getIdentifier();

        $task = $this->Tasks->find()
            ->where([
                'Tasks.id' => $id,
                'Tasks.user_id' => $userId
            ])
            ->firstOrFail();

        $this->set(compact('task'));
        $this->viewBuilder()->setOption('serialize', ['task']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $userId = $this->Authentication->getIdentity()->getIdentifier();
        $task = $this->Tasks->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
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
        $this->viewBuilder()->setOption('serialize', ['task']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $userId = $this->Authentication->getIdentity()->getIdentifier();

        $task = $this->Tasks->find()
            ->where([
                'Tasks.id' => $id,
                'Tasks.user_id' => $userId
            ])
            ->firstOrFail();

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
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
        $this->viewBuilder()->setOption('serialize', ['task']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $userId = $this->Authentication->getIdentity()->getIdentifier();

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
