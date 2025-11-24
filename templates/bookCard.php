<div class="book"
            data-id="<?= htmlspecialchars($book->getBookId()) ?>"
            data-title="<?= htmlspecialchars($book->getTitle()) ?>"
            data-authors="<?= htmlspecialchars($book->getAuthors()) ?>"
            data-description="<?= htmlspecialchars($book->getDescription()) ?>"
            data-page-count="<?= htmlspecialchars($book->getPageCount()) ?>"
            data-thumbnail="<?= htmlspecialchars($book->getThumbnail()) ?>">

            <h3><?= htmlspecialchars($book->getTitle()) ?></h3>
            <?php if ($book->getThumbnail()): ?>
                <img src="<?= htmlspecialchars($book->getThumbnail()) ?>" height="100" alt="Omslag">
            <?php endif; ?>            
            <p><strong>Forfatter:</strong> <?= htmlspecialchars($book->getAuthors()) ?></p>
            <p><strong>Antall sider:</strong> <?= htmlspecialchars($book->getPageCount()) ?></p>
            <p><?= htmlspecialchars($book->getDescription()) ?></p>                
            
            <button type="button" class="saveBookBtn">Putt boken i hyllen</button>
</div>