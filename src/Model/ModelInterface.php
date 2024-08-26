<?php
declare(strict_types=1);
namespace App\Model;

interface ModelInterface
{
    public function search(
        string $pharse,
        int $pageNumber, 
        int $pageSize, 
        string $sortBy = 'title' , 
        string $sordOrder = 'asc'
    ): array ;
    
    public function searchCount(
        $pharse
    ): int;

    
    public function list(
        int $pageNumber, 
        int $pageSize, 
        string $sortBy = 'title' , 
        string $sordOrder = 'asc'
        ): array;
        
    public function get(int $id): array;
    
    public function count(): int;


    public function create(
        array $data
    ): void;

    public function edit(
        int $id, 
        array $data
    ): void;

    public function delete(
    $id
    ) : void;
}