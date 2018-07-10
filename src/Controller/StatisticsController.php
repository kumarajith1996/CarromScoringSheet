<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;
/**
 * Statistics Controller
 *
 *
 * @method \App\Model\Entity\Statistic[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StatisticsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $statistics = $this->paginate($this->Statistics);

        $this->set(compact('statistics'));
    }

    /**
     * View method
     *
     * @param string|null $id Statistic id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $connection = ConnectionManager::get('default');
        $statistic = $connection->execute('SELECT p.id, p.name, coins, opc, minus, queens, finishes, boardCount from players p left join (select player_id, sum(coins) as coins, sum(opc) as opc, sum(minus) as minus from boards_players GROUP by player_id) w on p.id = w.player_id left join (select queen as id, count(queen) as queens from boards where queen IS NOT NULL group by queen) x on p.id = x.id left join (select finisher as id, count(finisher) as finishes from boards where finisher IS NOT NULL group by finisher) y on y.id = p.id left join (SELECT player_id, count(player_id) as boardCount from boards_players GROUP by player_id) z on p.id = z.player_id')->fetchAll('assoc');
        $this->set('statistic', $statistic);
        $this->set('_serialize', true);
    }
}
