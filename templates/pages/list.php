<div class="list">
  <section>
    <div class="message">
      <?php
      if (!empty($params['error'])) {
        switch ($params['error']) {
          case 'missingNoteId':
            echo 'Zły parametr id notatki :/';
            break;
          case 'noteNotFound':
            echo 'Notatka nie została znaleziona :/';
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
            echo 'Notatka została utworzona !!!';
            break;
          case 'edited':
            echo 'Notatka została zaktualizowana!';
            break;
          case 'deleted':
            echo 'Notatka została pomyślnie usunięta!';
            break;
        }
      }
      ?>

    </div>

    <?php
    $sort = $params['sort'] ?? [];
    $by = $sort['by'] ?? 'title';
    $order = $sort['order'] ?? 'desc';

    $page = $params['page'] ?? [];
    $size = $page['size'] ?? 10;
    $currentPage = $page['number'] ?? 1;
    $pages = $page['pages'] ?? 1;

    $phrase= $params['phrase'] ?? null;
    ?>

    <div>
      <form class="settings-form" action="/Notes" method="GET">
        <div>
          <label>Wyszukaj:<input type="text" name="phrase" value="<?php echo $phrase; ?>"/></label>
        </div>
        <div>
          <div>Sortuj po:</div>
          <label>Tytule:
            <input name="sortby" type="radio" value="title" <?php echo $by === 'title' ? 'checked' :  ''; ?> />
          </label>
          <label>Dacie:
            <input name="sortby" type="radio" value="created" <?php echo $by === 'created' ? 'checked' :  ''; ?> />
          </label>
        </div>
        <div>
          <div>Kierunek sortowania:</div>
          <div>
            <label>Rosnąco:
              <input name="sortorder" type="radio" value="asc" <?php echo $order === 'asc' ? 'checked' :  ''; ?> />
            </label>
            <label>Malejąco:
              <input name="sortorder" type="radio" value="desc" <?php echo $order === 'desc' ? 'checked' :  ''; ?> />
            </label>
          </div>
          <div><br />
            <div>Notatek na stronie: </div>
            <label>1<input name="pagesize" type="radio" value="1" <?php echo $size === 1 ? 'checked' : '' ?> /></label>
            <label>5<input name="pagesize" type="radio" value="5" <?php echo $size === 5 ? 'checked' : '' ?> /></label>
            <label>10<input name="pagesize" type="radio" value="10" <?php echo $size === 10 ? 'checked' : '' ?> /></label>
            <label>25<input name="pagesize" type="radio" value="25" <?php echo $size === 25 ? 'checked' : '' ?> /></label>
          </div>
          <input type="submit" value="Wyślij" />
      </form>
    </div>
    <div class="tbl-header">
      <table cellpadding="0" cellspacing="0" border="0">
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
      <table cellpadding="0" cellspacing="0" border="0">
        <tbody>
          <?php foreach ($params['notes'] ?? [] as $note) : ?>
            <tr>
              <td><?php echo $note['id']; ?></td>
              <td><?php echo $note['title']; ?></td>
              <td><?php echo $note['created']; ?></td>
              <td>
                <a href="/Notes/?action=show&id=<?php echo (int)$note['id']; ?>">
                  <button>Szczegóły</button>
                </a>
                <a href="/Notes/?action=delete&id=<?php echo (int)$note['id']; ?>">
                  <button>Usuń</button>
                </a>
              </td>
            </tr>

          <?php endforeach; ?>
        </tbody>
      </table>

    </div>
    <?php
    $paginationUrl = "&phrase=$phrase&pagesize=$size?sortby=$by&sortorder=$order";
    ?>
    <ul class="pagination">
      <?php if ($currentPage !== 1) : ?>
        <li>
          <a href="/Notes/?page=<?php echo $currentPage - 1 . $paginationUrl; ?>">
            <button>
              << </button>
          </a>
        </li>
      <?php endif; ?>
      <?php for ($i = 1; $i <= $pages; $i++) : ?>
        <li>
          <a href="/Notes/?page=<?php echo $i . $paginationUrl; ?>">
            <button>
              <?php echo $i ?>
            </button>
          </a>
        </li>
      <?php endfor; ?>
      <?php if ($currentPage < $pages) : ?>
        <li>
          <a href="/Notes/?page=<?php echo $currentPage + 1 . $paginationUrl; ?>">
            <button> >> </button>
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </section>
</div>