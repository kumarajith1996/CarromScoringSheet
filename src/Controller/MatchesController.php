<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
/**
 * Matches Controller
 *
 * @property \App\Model\Table\MatchesTable $Matches
 *
 * @method \App\Model\Entity\Match[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MatchesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
     public function index()
    {
        $filter = $this->request->getQuery('filter');
        $matches = $this->Matches->getOrGenerateMatches($filter['group'] ?? null, $filter['role'] ?? null);
        $this->set('matches', $matches);
        $this->set('_serialize', true);
    }

    /**
     * View method
     *
     * @param string|null $id Match id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $match = $this->Matches->get($id);
        $this->set('match', $match);
        $this->set('_serialize', true);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $match = $this->Matches->newEntity();
        if ($this->request->is('post')) {
            $match = $this->Matches->patchEntity($match, $this->request->getData());
            if ($this->Matches->save($match)) {
                $this->Flash->success(__('The match has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The match could not be saved. Please, try again.'));
        }
        $teams = $this->Matches->Teams->find('list', ['limit' => 200]);
        $this->set(compact('match', 'teams'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Match id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $match = $this->Matches->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $match = $this->Matches->patchEntity($match, $this->request->getData());
            if ($this->Matches->save($match)) {
                $this->Flash->success(__('The match has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The match could not be saved. Please, try again.'));
        }
        $teams = $this->Matches->Teams->find('list', ['limit' => 200]);
        $this->set(compact('match', 'teams'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Match id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $match = $this->Matches->get($id);
        if ($this->Matches->delete($match)) {
            $this->Flash->success(__('The match has been deleted.'));
        } else {
            $this->Flash->error(__('The match could not be deleted. Please, try again.'));
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
