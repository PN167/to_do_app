<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\Model\Entity\Task;

/**
 * Tasks Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Task newEmptyEntity()
 * @method \App\Model\Entity\Task newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Task> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Task get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Task findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Task patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Task> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Task|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Task saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Task>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Task>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Task>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Task> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Task>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Task>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Task>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Task> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TasksTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('tasks');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title', __('Task title is required'))
            ->minLength('title', 3, __('Task title must be at least 3 characters long'));

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('status')
            ->inList('status', [
                Task::STATUS_NOT_STARTED,
                Task::STATUS_IN_PROGRESS,
                Task::STATUS_COMPLETED
            ], __('Invalid status value'))
            ->requirePresence('status', 'create')
            ->notEmptyString('status');

        $validator
            ->scalar('priority')
            ->inList('priority', [
                Task::PRIORITY_LOW,
                Task::PRIORITY_MEDIUM,
                Task::PRIORITY_HIGH
            ], __('Invalid priority value'))
            ->requirePresence('priority', 'create')
            ->notEmptyString('priority');

        $validator
            ->dateTime('due_date')
            ->allowEmptyDateTime('due_date');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }

    /**
     * Custom finder to get tasks for a specific user
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object
     * @param int $userId User ID
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findByUser(SelectQuery $query, int $userId): SelectQuery
    {
        return $query->where(['Tasks.user_id' => $userId]);
    }

    /**
     * Custom finder to get tasks by status
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object
     * @param string $status Task status
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findByStatus(SelectQuery $query, string $status): SelectQuery
    {
        return $query->where(['Tasks.status' => $status]);
    }

    /**
     * Custom finder to get overdue tasks
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findOverdue(SelectQuery $query): SelectQuery
    {
        return $query
            ->where([
                'Tasks.due_date <' => new \Cake\I18n\DateTime(),
                'Tasks.status !=' => Task::STATUS_COMPLETED
            ]);
    }

    /**
     * Get statistics for a user's tasks
     *
     * @param int $userId User ID
     * @return array<string, mixed>
     */
    public function getStatistics(int $userId): array
    {
        $total = $this->find()->where(['user_id' => $userId])->count();

        $byStatus = $this->find()
            ->select([
                'status',
                'count' => $this->find()->func()->count('*')
            ])
            ->where(['user_id' => $userId])
            ->groupBy('status')
            ->all()
            ->combine('status', 'count')
            ->toArray();

        $notStarted = $byStatus['not_started'] ?? 0;
        $inProgress = $byStatus['in_progress'] ?? 0;
        $completed = $byStatus['completed'] ?? 0;

        $completionRate = $total > 0 ? round(($completed / $total) * 100, 2) : 0;

        $overdue = $this->find()
            ->where([
                'user_id' => $userId,
                'due_date <' => new \Cake\I18n\DateTime(),
                'status !=' => 'completed'
            ])
            ->count();

        return [
            'total' => $total,
            'not_started' => $notStarted,
            'in_progress' => $inProgress,
            'completed' => $completed,
            'completion_rate' => $completionRate,
            'overdue' => $overdue,
        ];
    }
}
