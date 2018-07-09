<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;
/**
 * Boards Model
 *
 * @property \App\Model\Table\MatchesTable|\Cake\ORM\Association\BelongsTo $Matches
 * @property \App\Model\Table\PlayersTable|\Cake\ORM\Association\BelongsToMany $Players
 *
 * @method \App\Model\Entity\Board get($primaryKey, $options = [])
 * @method \App\Model\Entity\Board newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Board[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Board|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Board|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Board patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Board[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Board findOrCreate($search, callable $callback = null, $options = [])
 */
class BoardsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('boards');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Matches', [
            'foreignKey' => 'match_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsToMany('Players', [
            'foreignKey' => 'board_id',
            'targetForeignKey' => 'player_id',
            'through' => 'boards_players'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['match_id'], 'Matches'));

        return $rules;
    }

    public function computePoints($matchId)
    {
        $boards = $this->find()->where(['match_id' => $matchId]);
        $points = [];
        $match = $this->Matches->get($matchId);
        $points[$match['team1_id']] = 0;
        $points[$match['team2_id']] = 0;
        $temp = null;
        $boards_players = TableRegistry::get('BoardsPlayers');
        foreach($boards as $board)
        {
            $temp = $boards_players->computePoints($board['id'], $match['team1_id'], $match['team2_id']);
            $points[$match['team1_id']] += $temp[$match['team1_id']];
            $points[$match['team2_id']] += $temp[$match['team2_id']];
            $queenTeamId = $this->Players->get($board['queen'])['team_id'];
            $points[$queenTeamId] += 5;
        }
        return $points;
    }
}
