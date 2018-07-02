<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * BoardsPlayers Controller
 *
 * @property \App\Model\Table\BoardsPlayersTable $BoardsPlayers
 *
 * @method \App\Model\Entity\BoardsPlayer[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BoardsPlayersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Boards', 'Players']
        ];
        $boardsPlayers = $this->paginate($this->BoardsPlayers);

        $this->set(compact('boardsPlayers'));
    }

    /**
     * View method
     *
     * @param string|null $id Boards Player id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $boardsPlayer = $this->BoardsPlayers->get($id, [
            'contain' => ['Boards', 'Players']
        ]);

        $this->set('boardsPlayer', $boardsPlayer);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $boardsPlayer = $this->BoardsPlayers->newEntity();
        if ($this->request->is('post')) {
            $boardsPlayer = $this->BoardsPlayers->patchEntity($boardsPlayer, $this->request->getData());
            if ($this->BoardsPlayers->save($boardsPlayer)) {
                $this->Flash->success(__('The boards player has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The boards player could not be saved. Please, try again.'));
        }
        $boards = $this->BoardsPlayers->Boards->find('list', ['limit' => 200]);
        $players = $this->BoardsPlayers->Players->find('list', ['limit' => 200]);
        $this->set(compact('boardsPlayer', 'boards', 'players'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Boards Player id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $boardsPlayer = $this->BoardsPlayers->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $boardsPlayer = $this->BoardsPlayers->patchEntity($boardsPlayer, $this->request->getData());
            if ($this->BoardsPlayers->save($boardsPlayer)) {
                $this->Flash->success(__('The boards player has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The boards player could not be saved. Please, try again.'));
        }
        $boards = $this->BoardsPlayers->Boards->find('list', ['limit' => 200]);
        $players = $this->BoardsPlayers->Players->find('list', ['limit' => 200]);
        $this->set(compact('boardsPlayer', 'boards', 'players'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Boards Player id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $boardsPlayer = $this->BoardsPlayers->get($id);
        if ($this->BoardsPlayers->delete($boardsPlayer)) {
            $this->Flash->success(__('The boards player has been deleted.'));
        } else {
            $this->Flash->error(__('The boards player could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
