<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
/**
 * Boards Controller
 *
 * @property \App\Model\Table\BoardsTable $Boards
 *
 * @method \App\Model\Entity\Board[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BoardsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Matches']
        ];
        $boards = $this->paginate($this->Boards);

        $this->set(compact('boards'));
        $this->set('_serialize', true);
    }

    /**
     * View method
     *
     * @param string|null $id Board id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $board = $this->Boards->get($id, [
            'contain' => ['Matches', 'Players']
        ]);

        $this->set('board', $board);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $board = $this->Boards->newEntity();
        if ($this->request->is('post')) {
            $board = $this->Boards->patchEntity($board, $this->request->getData('board'), ['validation' => false]);
            $this->Boards->save($board);
        }
        $this->set(compact('board'));
        $this->set('_serialize', true);
    }

    /**
     * Edit method
     *
     * @param string|null $id Board id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $board = $this->Boards->get($id, [
            'contain' => ['Players']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $board = $this->Boards->patchEntity($board, $this->request->getData());
            if ($this->Boards->save($board)) {
                $this->Flash->success(__('The board has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The board could not be saved. Please, try again.'));
        }
        $matches = $this->Boards->Matches->find('list', ['limit' => 200]);
        $players = $this->Boards->Players->find('list', ['limit' => 200]);
        $this->set(compact('board', 'matches', 'players'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Board id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $board = $this->Boards->get($id);
        if ($this->Boards->delete($board)) {
            $this->Flash->success(__('The board has been deleted.'));
        } else {
            $this->Flash->error(__('The board could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function beforeRender(event $event) {
        $this->setCorsHeaders();
    }

    public function beforeFilter(event $event) {
    if ($this->request->is('options')) {
        $this->setCorsHeaders();
        return $this->response;
        }
    }

    private function setCorsHeaders() {
        $this->response->cors($this->request)
            ->allowOrigin(['*'])
            ->allowMethods(['*'])
            ->allowHeaders(['*'])
            ->allowCredentials(['true'])
            ->exposeHeaders(['Link'])
            ->maxAge(300)
            ->build();
    }
}
