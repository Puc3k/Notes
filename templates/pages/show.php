<div class="show">
    <?php $note = $params['note'] ?? null; ?>
    <?php if ($note) : ?>
        <ul>
            <li>Id: <?php echo (int)($note['id']); ?></li>
            <li>Tytuł: <?php echo $note['title']; ?></li>
            <li><?php echo $note['description']; ?></li>
            <li>Utworzono: <?php echo $note['created']; ?></li>
        </ul>
        <a href="/Notes/?action=edit&id=<?php echo $note['id'] ?>">
            <button>Edytuj</button>
        </a>
    <?php else : ?>
        <div>
            Brak notatki do wyświetlenia :(
        </div>
    <?php endif; ?>
    <a href="/Notes/">
        <button>Powrót do listy notatek</button>
    </a>
</div>