<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Matches Model
 *
 * @property \App\Model\Table\TeamsTable|\Cake\ORM\Association\BelongsTo $Teams
 * @property \App\Model\Table\TeamsTable|\Cake\ORM\Association\BelongsTo $Teams
 * @property \App\Model\Table\BoardsTable|\Cake\ORM\Association\HasMany $Boards
 *
 * @method \App\Model\Entity\Match get($primaryKey, $options = [])
 * @method \App\Model\Entity\Match newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Match[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Match|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Match|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Match patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Match[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Match findOrCreate($search, callable $callback = null, $options = [])
 */
class MatchesTable extends Table
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

        $this->setTable('matches');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Teams');

        $this->belongsTo('Team1', [
            'foreignKey' => 'team1_id',
            'joinType' => 'INNER',
            'className' => 'teams'
        ]);

        $this->belongsTo('Team2', [
            'foreignKey' => 'team2_id',
            'joinType' => 'INNER',
            'className' => 'teams'
        ]);

        $this->hasMany('Boards', [
            'foreignKey' => 'match_id'
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

        $validator
            ->integer('role')
            ->requirePresence('role', 'create')
            ->notEmpty('role');

        $validator
            ->integer('winner')
            ->allowEmpty('winner');

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
        $rules->add($rules->existsIn(['team1_id'], 'Teams'));
        $rules->add($rules->existsIn(['team2_id'], 'Teams'));

        return $rules;
    }
    public function getOrGenerateMatches($group, $role)
    {
        $count = $this->find()->count();
        if($count == 0)
        {
            $this->generatePlayOffs();
        }
        $results = $this->getAllMatches($group, $role);
        return $results;
    }

    public function getAllMatches($group, $role)
    {
        //$this->computePoints(); 
        //$this->generateSemiFinals();   
        $matches = $this->find()->contain(['Team1', 'Team2'])->select(['id','team1_name' => 'team1.name','team2_name' => 'team2.name', 'group' => 'team1._group']);
        if($group!=null)
            $matches = $matches -> where(['team1._group' => $group]);
        if($role!=null)
            $matches = $matches-> where(['role' => $role]);
        return $matches;
    }

    public function computePoints()
    {
        $matches = $this->find()->all();
        $points = null;
        $patchMatch;
        foreach ($matches as $match) {
            $points = $this->Boards->computePoints($match['id']);
            $patchMatch = $this->get($match['id']);
            if($points[''.$match['team1_id']] > $points[''.$match['team2_id']])
            {
                $patchMatch->winner = $match['team1_id'];
            }
            else if($points[''.$match['team1_id']] < $points[''.$match['team2_id']])
            {
                $patchMatch->winner = $match['team2_id'];
            }
            else
            {
                $patchMatch->winner = null;
            }
            $this->save($patchMatch);
            $this->Teams->updateTeamPoints($points, $match['team1_id'], $match['team2_id']);
        }
    }

    public function generatePlayOffs()
    {
        $this->Teams->assignGroups();
        $group1 = iterator_to_array($this->Teams->find()->select('id')->where(['_group =' => 1]));
        $group2 = iterator_to_array($this->Teams->find()->select('id')->where(['_group =' => 2]));
        $matches = [];
        for ($i=0; $i < sizeof($group1)-1; $i++) { 
            for($j = $i+1; $j < sizeof($group1);$j++){
                array_push($matches, ['team1_id' => $group1[$i]['id'], 'team2_id' => $group1[$j]['id'], 'role' => 1]);
            }
        }

        for ($i=0; $i < sizeof($group2)-1; $i++) { 
            for($j = $i+1; $j < sizeof($group2);$j++){
                array_push($matches, ['team1_id' => $group2[$i]['id'], 'team2_id' => $group2[$j]['id'], 'role' => 1]);
            }
        }
        $entries = $this->newEntities($matches);
        $result = $this->saveMany($entries);
    }

    public function generateSemiFinals()
    {
        $group1 = $this->Teams->find()->select('id')->where(['_group =' => 1])->order(['points' => 'DESC'])->limit(2)->toArray();
        $group2 = $this->Teams->find()->select('id')->where(['_group =' => 2])->order(['points' => 'DESC'])->limit(2)->toArray();

        $matches = [];
        array_push($matches, ['team1_id' => $group1[0]->id, 'team2_id' => $group2[1]->id, 'role' => 2]);
        array_push($matches, ['team1_id' => $group1[1]->id, 'team2_id' => $group2[0]->id, 'role' => 2]);
        $entries = $this->newEntities($matches);
        $result = $this->saveMany($entries);

    }
}
