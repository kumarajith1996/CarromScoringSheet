<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
/**
 * BoardsPlayer Entity
 *
 * @property int $id
 * @property int $board_id
 * @property int $player_id
 * @property int $coins
 * @property int $opc
 * @property int $minus
 *
 * @property \App\Model\Entity\Board $board
 * @property \App\Model\Entity\Player $player
 */
class BoardsPlayer extends Entity
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
    protected $_virtual = ['team_id', 'opponent_id'];
    protected $_accessible = [
        'board_id' => true,
        'player_id' => true,
        'coins' => true,
        'opc' => true,
        'minus' => true,
        'board' => true,
        'player' => true
    ];

    protected function _getTeamId()
    {
        $id = $this->_properties['player_id'];
        $players = TableRegistry::get('Players');
        return $players->get($id)['team_id'];
    }

    protected function _getOpponentId()
    {
        $id = $this->_properties['board_id'];
        $boards = TableRegistry::get('Boards');
        $matchId = $boards->get($id)['match_id'];
        $matches = TableRegistry::get('Matches');
        $match = $matches->get($matchId);
        if($match->team1_id == $this->team_id)
            return $match->team2_id;
        else
            return $match->team1_id;
    }
}
