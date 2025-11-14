<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Task;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\BadRequestException;

/**
 * Tasks Controller
 *
 * @property \App\Model\Table\TasksTable $Tasks
 */
class TasksController extends AppController
{
    /**
     * Initialize method
     */
    public function initialize(): void
    {
        parent::initialize();

        // Enable JSON view for API requests
        if ($this->isJsonRequest()) {
            $this->viewBuilder()->setClassName('Json');
        }
    }

    /**
     * Check if the request wants JSON
     */
    private function isJsonRequest(): bool
    {
        return $this->request->is('json') ||
            $this->request->accepts('application/json') ||
            $this->request->getParam('_ext') === 'json';
    }

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

        $query->orderBy(['Tasks.created' => 'DESC']);

        // API request
        if ($this->isJsonRequest()) {
            $limit = (int)$this->request->getQuery('limit', 100);
            $offset = (int)$this->request->getQuery('offset', 0);

            $tasks = $query->limit($limit)->offset($offset)->all();
            $total = $query->count();

            $statistics = $this->Tasks->getStatistics($userId);

            $this->set([
                'success' => true,
                'data' => [
                    'tasks' => $tasks,
                    'statistics' => $statistics,
                    'pagination' => [
                        'limit' => $limit,
                        'offset' => $offset,
                        'total' => $total
                    ]
                ],
                '_serialize' => ['success', 'data']
            ]);
            $this->viewBuilder()->setOption('serialize', ['success', 'data']);
        } else {
            // HTML view - use pagination
            $tasks = $this->paginate($query);
            $statistics = $this->Tasks->getStatistics($userId);
            $statuses = Task::getStatuses();
            $priorities = Task::getPriorities();
            $currentFilter = $statusFilter;

            $this->set(compact('tasks', 'statistics', 'statuses', 'priorities', 'currentFilter'));
        }
    }

    /**
     * View method
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function view($id = null)
    {
        $userId = $this->Authentication->getIdentity()->getIdentifier();

        try {
            $task = $this->Tasks->find()
                ->where([
                    'Tasks.id' => $id,
                    'Tasks.user_id' => $userId
                ])
                ->firstOrFail();

            if ($this->isJsonRequest()) {
                $this->set([
                    'success' => true,
                    'data' => ['task' => $task],
                    '_serialize' => ['success', 'data']
                ]);
                $this->viewBuilder()->setOption('serialize', ['success', 'data']);
            } else {
                $this->set(compact('task'));
            }
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            if ($this->isJsonRequest()) {
                return $this->response
                    ->withStatus(404)
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'Task not found'
                    ]));
            }
            throw $e;
        }
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
                if ($this->isJsonRequest()) {
                    return $this->response
                        ->withStatus(201)
                        ->withType('application/json')
                        ->withStringBody(json_encode([
                            'success' => true,
                            'message' => 'Task has been created',
                            'data' => ['task' => $task]
                        ]));
                }

                $this->Flash->success(__('The task has been saved.'));
                return $this->redirect(['action' => 'index']);
            }

            if ($this->isJsonRequest()) {
                return $this->response
                    ->withStatus(422)
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'The task could not be saved',
                        'errors' => $task->getErrors()
                    ]));
            }

            $this->Flash->error(__('The task could not be saved. Please, try again.'));
        }

        // For GET requests (HTML form view)
        if (!$this->isJsonRequest()) {
            $statuses = Task::getStatuses();
            $priorities = Task::getPriorities();
            $this->set(compact('task', 'statuses', 'priorities'));
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     */
    public function edit($id = null)
    {
        $userId = $this->Authentication->getIdentity()->getIdentifier();

        try {
            $task = $this->Tasks->find()
                ->where([
                    'Tasks.id' => $id,
                    'Tasks.user_id' => $userId
                ])
                ->firstOrFail();
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            if ($this->isJsonRequest()) {
                return $this->response
                    ->withStatus(404)
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'Task not found'
                    ]));
            }
            throw $e;
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            unset($data['user_id']);

            $task = $this->Tasks->patchEntity($task, $data);

            if ($this->Tasks->save($task)) {
                if ($this->isJsonRequest()) {
                    return $this->response
                        ->withStatus(200)
                        ->withType('application/json')
                        ->withStringBody(json_encode([
                            'success' => true,
                            'message' => 'Task has been updated',
                            'data' => ['task' => $task]
                        ]));
                }

                $this->Flash->success(__('The task has been saved.'));
                return $this->redirect(['action' => 'index']);
            }

            if ($this->isJsonRequest()) {
                return $this->response
                    ->withStatus(422)
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'The task could not be saved',
                        'errors' => $task->getErrors()
                    ]));
            }

            $this->Flash->error(__('The task could not be saved. Please, try again.'));
        }

        // For GET requests (HTML form view)
        if (!$this->isJsonRequest()) {
            $statuses = Task::getStatuses();
            $priorities = Task::getPriorities();
            $this->set(compact('task', 'statuses', 'priorities'));
        } else {
            // For GET requests to API, return the task data
            $this->set([
                'success' => true,
                'data' => ['task' => $task],
                '_serialize' => ['success', 'data']
            ]);
            $this->viewBuilder()->setOption('serialize', ['success', 'data']);
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $userId = $this->Authentication->getIdentity()->getIdentifier();

        try {
            $task = $this->Tasks->find()
                ->where([
                    'Tasks.id' => $id,
                    'Tasks.user_id' => $userId
                ])
                ->firstOrFail();
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            if ($this->isJsonRequest()) {
                return $this->response
                    ->withStatus(404)
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'Task not found'
                    ]));
            }
            throw $e;
        }

        if ($this->Tasks->delete($task)) {
            if ($this->isJsonRequest()) {
                return $this->response
                    ->withStatus(200)
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => true,
                        'message' => 'Task has been deleted'
                    ]));
            }

            $this->Flash->success(__('The task has been deleted.'));
        } else {
            if ($this->isJsonRequest()) {
                return $this->response
                    ->withStatus(500)
                    ->withType('application/json')
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'The task could not be deleted'
                    ]));
            }

            $this->Flash->error(__('The task could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
