<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Match Entity
 *
 * @property int $id
 * @property int $team1_id
 * @property int $team2_id
 * @property int $role
 * @property int $winner
 *
 * @property \App\Model\Entity\Team $team
 * @property \App\Model\Entity\Team $team1
 * @property \App\Model\Entity\Team $team2
 * @property \App\Model\Entity\Board[] $boards
 */
class Match extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_virtual = ['team1_points', 'team2_points'];
    protected $_accessible = [
        'team1_id' => true,
        'team2_id' => true,
        'role' => true,
        'winner' => true,
        'team' => true,
        'team1' => true,
        'team2' => true,
        'boards' => true
    ];
}
