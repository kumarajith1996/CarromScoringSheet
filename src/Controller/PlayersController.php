<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;
/**
 * Players Controller
 *
 * @property \App\Model\Table\PlayersTable $Players
 *
 * @method \App\Model\Entity\Player[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PlayersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $players = $this->Players->find('all');
        $filter = $this->request->getQuery('filter');
        if($filter['team_id']!=null)
            $players = $players->where(['team_id' => $filter['team_id']]);
        $this->set(compact('players'));
        $this->set('_serialize', true);
    }

    /**
     * View method
     *
     * @param string|null $id Player id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
     public function view($id = null)
    {
        $players = $this->Players->getPlayerById($id);
        $this->set('players', $players);
        $this->set('_serialize', true);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $player = $this->Players->newEntity();
        if ($this->request->is('post')) {
            $player = $this->Players->patchEntity($player, $this->request->getData());
            if ($this->Players->save($player)) {
                $this->Flash->success(__('The player has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The player could not be saved. Please, try again.'));
        }
        $roles = $this->Players->Roles->find('list', ['limit' => 200]);
        $teams = $this->Players->Teams->find('list', ['limit' => 200]);
        $boards = $this->Players->Boards->find('list', ['limit' => 200]);
        $this->set(compact('player', 'roles', 'teams', 'boards'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Player id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $player = $this->Players->get($id, [
            'contain' => ['Boards']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $player = $this->Players->patchEntity($player, $this->request->getData());
            if ($this->Players->save($player)) {
                $this->Flash->success(__('The player has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The player could not be saved. Please, try again.'));
        }
        $roles = $this->Players->Roles->find('list', ['limit' => 200]);
        $teams = $this->Players->Teams->find('list', ['limit' => 200]);
        $boards = $this->Players->Boards->find('list', ['limit' => 200]);
        $this->set(compact('player', 'roles', 'teams', 'boards'));
    }

    public function getStatistics()
    {
        $connection = ConnectionManager::get('default');
        $statistic = $connection->execute('SELECT p.id, p.name, coins, opc, minus, queens, finishes, boardCount from players p left join (select player_id, sum(coins) as coins, sum(opc) as opc, sum(minus) as minus from boards_players GROUP by player_id) w on p.id = w.player_id left join (select queen as id, count(queen) as queens from boards where queen IS NOT NULL group by queen) x on p.id = x.id left join (select finisher as id, count(finisher) as finishes from boards where finisher IS NOT NULL group by finisher) y on y.id = p.id left join (SELECT player_id, count(player_id) as boardCount from boards_players GROUP by player_id) z on p.id = z.player_id')->fetchAll('assoc');
        $this->set('statistic', $statistic);
        $this->set('_serialize', true);
    }

    /**
     * Delete method
     *
     * @param string|null $id Player id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $player = $this->Players->get($id);
        if ($this->Players->delete($player)) {
            $this->Flash->success(__('The player has been deleted.'));
        } else {
            $this->Flash->error(__('The player could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
