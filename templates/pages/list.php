<div class="list">
  <div class="message">
    <?php
      if (!empty($params['error'])) {
        switch ($params['error']) {
          case 'missingNoteId':
            echo 'Niepoprawny identyfikator notatki';
            break;
            case 'noteNotFound':
              echo 'Notatka nie została znaleziona';
              break;
            }
          }
          ?>
    </div>
    <div class="message">
      <?php
      if (!empty($params['before'])) {
        switch ($params['before']) {
          case 'created':
            echo 'Notatka zostało utworzona';
            break;
            case 'edited':
              echo 'Notatka została zaktualizowana';
              break;
              case 'deleted':
                echo 'Notatka została usunięta';
                break;
              }
            }
            ?>
    </div>
    
    <?php
      $sort = $params['sort'] ?? [];
      $by = $sort['createdAt'] ?? 'title';
      $order = $sort['order'] ?? 'desc';
      
      
      $page = $params['page'] ?? [];
      $size = $page['size'] ?? 10;
      $currentPage = $page['number'] ?? 1;
      $pages = $page['pages'] ?? 1;
            
      $phrase = $params['phrase'] ?? null;

      ?>

<section>
    <div>
      <form class="settings-form" action='/' method="GET">
        <div>
          <label>
              Wyszukaj: <input type="text" name="phrase" value="<?php echo $phrase; ?>" />
          </label>
        </div>
        <div>Sortuj po:</div>
        <div>
          <label >Tytuł: <input type="radio" value="title" name="sortby" <?php echo $by === 'title' ? 'checked' : ''; ?> ></label>
          <label >Date: <input type="radio" value="createdAt" name="sortby" <?php echo $by === 'createdAt' ? 'checked' : ''; ?>></label>
        </div>
        <div>Kierunek sortowania</div>
        <div>
          <label >Rosnąco: <input type="radio" value="asc" name="sortorder" <?php echo $order === 'asc' ? 'checked' : ''; ?>></label>
          <label >Malejąco: <input type="radio" value="desc" name="sortorder" <?php echo $order === 'desc' ? 'checked' : ''; ?>></label>
        </div>
        <div>Rozmiar paczki</div>
        <label>1 <input type="radio" name="pagesize" value="1" <?php echo $size === 1 ? 'checked' : '' ?>/></label>
        <label>5 <input type="radio" name="pagesize" value="5" <?php echo $size === 5 ? 'checked' : '' ?> /></label>
        <label>10 <input type="radio" name="pagesize" value="10" <?php echo $size === 10 ? 'checked' : '' ?> /></label>
        <label>25 <input type="radio" name="pagesize" value="25" <?php echo $size === 25 ? 'checked' : '' ?> /></label>
        
        
        
        <div>
          <input type="submit" value="Wyślij">
        </div>
      </form>
    </div>
  </section> 
  <section>
    <div class="tbl-header">
      <table >
        <thead>
          <tr>
            <th>Id</th>
            <th>Tytuł</th>
            <th>Data</th>
            <th>Opcje</th>
          </tr>
        </thead>
      </table>
    </div>
    <div class="tbl-content">
      <table >
        <tbody>
          <?php foreach ($params['notes'] ?? [] as $note) : ?>
            <tr>
              <td><?php echo $note['id'] ?></td>
              <td><?php echo $note['title'] ?></td>
              <td><?php echo $note['createdAt'] ?></td>
              <td>
                <span>
                  <a href="/?action=show&id=<?php echo $note['id'] ?>">
                    <button>Szczegóły</button>
                  </a>
                </span>
                <span>
                  <a href="/?action=delete&id=<?php echo $note['id'] ?>">
                    <button>Usuń</button>
                  </a>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>


    <?php $paginationUrl = "&phrase=$phrase&pagesize=$size&sortby=$by&sortorder=$order"; ?>
    <ul class="pagination">
      <li>
        <?php if($currentPage > 1): ?>
          <a href="/?page=<?php echo $currentPage-1 . $paginationUrl ?>">
              <button><<</button>
          </a>
        <?php endif ?>
      </li>
      <?php for($i=1; $i <= $pages; $i++): ?>
        <li>
          <a href="?page=<?php echo $i . $paginationUrl ?>">
            <button><?php echo $i ?></button>
          </a>
      </li>
      <?php endfor ?>
      <li>
        <?php if($currentPage < $pages): ?>
          <a href="/?page=<?php echo $currentPage+1 . $paginationUrl ?>">
              <button>>></button>
          </a>
        <?php endif ?>
      </li>
    </ul>
  </section>
</div>