<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Task Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $description
 * @property string $status
 * @property string $priority
 * @property \Cake\I18n\DateTime|null $due_date
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\User $user
 */
class Task extends Entity
{
    /**
     * Status constants
     */
    public const STATUS_NOT_STARTED = 'not_started';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    /**
     * Priority constants
     */
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'user_id' => true,
        'title' => true,
        'description' => true,
        'status' => true,
        'priority' => true,
        'due_date' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
    ];

    /**
     * Get list of available statuses
     *
     * @return array<string, string>
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_NOT_STARTED => __('Not Started'),
            self::STATUS_IN_PROGRESS => __('In Progress'),
            self::STATUS_COMPLETED => __('Completed'),
        ];
    }

    /**
     * Get list of available priorities
     *
     * @return array<string, string>
     */
    public static function getPriorities(): array
    {
        return [
            self::PRIORITY_LOW => __('Low'),
            self::PRIORITY_MEDIUM => __('Medium'),
            self::PRIORITY_HIGH => __('High'),
        ];
    }

    /**
     * Check if task is completed
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if task is overdue
     *
     * @return bool
     */
    public function isOverdue(): bool
    {
        if ($this->due_date === null || $this->isCompleted()) {
            return false;
        }

        return $this->due_date < new \Cake\I18n\DateTime();
    }
}
