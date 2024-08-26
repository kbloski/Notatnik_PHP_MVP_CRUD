<?php
declare(strict_types=1);
namespace App\Model;

use App\Model\ModelInterface;
use App\Exception\StorageException;
use App\Exception\NotFoundException;
use PDO;
use Throwable;

class NoteModel extends AbstractModel implements ModelInterface
{
  public function search(
    string $pharse, 
    int $pageNumber, 
    int $pageSize, 
    string $sortBy = 
    'title', 
    string $sordOrder = 'asc'
  ): array
  {
    return $this->findBy($pharse, $pageNumber, $pageSize, $sortBy, $sordOrder);
  }
  
  public function searchCount(
    $pharse
  ): int
  {
    try {
      $query = "SELECT 
      count(id) as cn
       FROM notes
       WHERE title LIKE '%$pharse%'
       ";

      $result = $this->conn->query($query);
      $result = $result->fetch();
      
      if (!$result) throw new StorageException('Błąd przy próbie pobierania ilości notatek',400);
      return (int) $result['cn'];
    } catch (Throwable $e) {
      throw new StorageException('Nie udało się pobrać danych o notatkach', 400, $e);
    }
  }

  public function list(
      int $pageNumber, 
      int $pageSize, 
      string $sortBy = 'title' , 
      string $sordOrder = 'asc'
      ): array
      {
      return $this->findBy(null, $pageNumber, $pageSize, $sortBy, $sordOrder);
      
    }
 
  

  public function get(int $id): array
  {
    try {
      $query = "SELECT * FROM notes WHERE id = $id";
      $result = $this->conn->query($query);
      $note = $result->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
      throw new StorageException('Nie udało się pobrać notatki', 400, $e);
    }

    if (!$note) {
      throw new NotFoundException("Notatka o id: $id nie istnieje");
    }

    return $note;
  }


  public function count(): int
  {
    try {
      $query = "SELECT 
      count(id) as cn
       FROM notes ";

      $result = $this->conn->query($query);
      $result = $result->fetch();
      
      if (!$result) throw new StorageException('Błąd przy próbie pobierania ilości notatek',400);
      return (int) $result['cn'];
    } catch (Throwable $e) {
      throw new StorageException('Nie udało się pobrać danych o notatkach', 400, $e);
    }
  }

  public function create(array $data): void
  {
    try {
      $title = $this->conn->quote($data['title']);
      $description = $this->conn->quote($data['description']);

      $query = "
        INSERT INTO notes(title, description)
        VALUES($title, $description)
      ";

      $this->conn->exec($query);
    } catch (Throwable $e) {
      throw new StorageException('Nie udało się utworzyć nowej notatki', 400, $e);
    }
  }

  public function edit(int $id, array $data): void
  {
    try {
      $title = $this->conn->quote($data['title']);
      $description = $this->conn->quote($data['description']);

      $query = "
        UPDATE notes
        SET title = $title, description = $description
        WHERE id = $id
      ";

      $this->conn->exec($query);
    } catch (Throwable $e) {
      throw new StorageException('Nie udało się zaktualizować notetki', 400, $e);
    }
  }

  public function delete($id) : void
  {
    try {
      $query = "DELETE FROM notes WHERE id = $id LIMIT 1";
      $this->conn->exec($query);

    } catch (Throwable $e){
      throw new StorageException('nie udało się usunąć notatki', 400, $e);
    }
  }


  private function findBy(
        $pharse,
        int $pageNumber, 
        int $pageSize, 
        string $sortBy = 'title' , 
        string $sordOrder = 'asc'
  ): array
  {
    try {
    
      $limit = $pageSize;
      $offset = ($pageNumber-1) * $pageSize;
      
      $whereLikeParse = '';
      if ($pharse){
        $pharse = $this->conn->quote('%'.$pharse.'%');

        $whereLikeParse = "WHERE title LIKE $pharse";
      } 
  
      $query = "SELECT 
      id, title, createdAt
       FROM notes
       $whereLikeParse
       ORDER BY $sortBy $sordOrder
       LIMIT $offset ,$limit
       ";
      $result = $this->conn->query($query);
      return $result->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
      throw new StorageException('Nie udało się pobrać danych o notatkach', 400, $e);
    }
  }
  
}
