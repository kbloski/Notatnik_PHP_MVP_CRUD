<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractController;
use App\Exception\NotFoundException;

class NoteController extends AbstractController
{
  private const PAGE_SIZE = 10;

  public function createAction()
  {
    if ($this->request->hasPost()) {
      $noteData = [
        'title' => $this->request->postParam('title'),
        'description' => $this->request->postParam('description')
      ];
      $this->noteModel->create($noteData);
      $this->redirect('/', ['before' => 'created']);
    }

    $this->view->render('create');
  }

  public function showAction()
  {
    $noteId = (int) $this->request->getParam('id');

    if (!$noteId) {
      $this->redirect('/', ['error' => 'missingNoteId']);
    }

    try {
      $note = $this->noteModel->get($noteId);
    } catch (NotFoundException $e) {
      $this->redirect('/', ['error' => 'noteNotFound']);
    }

    $this->view->render(
      'show',
      ['note' => $note]
    );
  }

  public function listAction()
  {
    $phrase = $this->request->getParam('phrase');
    $pageNumber = (int) $this->request->getParam('page', 1);
    $pageSize = (int) $this->request->getParam('pagesize', self::PAGE_SIZE);
    $sortOrder = $this->request->getParam('sortorder', 'desc');
    $sortBy = $this->request->getParam('sortby', 'title');

    if (!in_array($pageSize, [1,5,10,25])){
      $pageSize = self::PAGE_SIZE;
    };

    if ($phrase){

      $notes = $this->noteModel->search($phrase, $pageNumber, $pageSize, $sortBy, $sortOrder);
      $notesCount = $this->noteModel->searchCount($phrase);
    } else {
      $notesCount = $this->noteModel->count();
      $notes = $this->noteModel->list($pageNumber, $pageSize, $sortBy, $sortOrder);
    }


    $this->view->render(
      'list',
      [
        'page' => [
          'number' => $pageNumber,
          'size' => $pageSize,
          'pages' => (int) ceil($notesCount/$pageSize) ?? 1
        ],
        'sort' => [
          'by' => $this->request->getParam('sortby', 'title'),
          'order' => $this->request->getParam('sortorder', 'desc'),
        ],
        'phrase' => $phrase,
        'notes' => $notes,
        'before' => $this->request->getParam('before'),
        'error' => $this->request->getParam('error')
      ]
    );
  }

  public function editAction()
  {

    if ($this->request->isPost()) {
      $noteId = (int) $this->request->postParam('id');
      $noteData = [
        'title' => $this->request->postParam('title'),
        'description' => $this->request->postParam('description')
      ];
      $this->noteModel->edit($noteId, $noteData);
      $this->redirect('/', ['before' => 'edited']);
    }

    $note = $this->getNote();
    $this->view->render('edit', ['note' => $note]);
  }


  public function deleteAction() : void
  {
    if ($this->request->isPost()){
      $id = $this->request->postParam('id');
      $this->noteModel->delete($id);
      $this->redirect('/', ['before'=>'delete']);
    }

    $note = $this->getNote();
    $this->view->render('delete', ['note' => $note]);
  }

  private function getNote() 
  {
    $noteId = (int) $this->request->getParam('id');
    if (!$noteId) {
      $this->redirect('/', ['error' => 'missingNoteId']);
    }
    return $this->noteModel->get($noteId);
  }
}
