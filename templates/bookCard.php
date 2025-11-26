<div class="book"
            data-book-id="<?= htmlspecialchars($book->getBookId()) ?>"
            data-title="<?= htmlspecialchars($book->getTitle()) ?>"
            data-authors="<?= htmlspecialchars($book->getAuthors()) ?>"
            data-description="<?= htmlspecialchars($book->getDescription()) ?>"
            data-page-count="<?= htmlspecialchars($book->getPageCount()) ?>"
            data-thumbnail="<?= htmlspecialchars($book->getThumbnail()) ?>">

            <h3><?= htmlspecialchars($book->getTitle()) ?></h3>
            <p><strong>Bok ID:</strong> <?= htmlspecialchars($book->getBookId()) ?></p>

            <?php if ($book->getThumbnail()): ?>
                <img src="<?= htmlspecialchars($book->getThumbnail()) ?>" height="100" alt="Omslag">
            <?php endif; ?>            
            <p><strong>Forfatter:</strong> <?= htmlspecialchars($book->getAuthors()) ?></p>
            <p><strong>Antall sider:</strong> <?= htmlspecialchars($book->getPageCount()) ?></p>
            
            <label for="modal-<?= htmlspecialchars($book->getBookId()) ?>" class="description-label">
                <p class="description-preview"><?= htmlspecialchars(substr($book->getDescription(), 0, 100)) ?>...</p>
            </label>
            
            <input type="checkbox" id="modal-<?= htmlspecialchars($book->getBookId()) ?>" class="modal-toggle" hidden>
            <div class="modal-overlay">
                <div class="modal">
                    <label for="modal-<?= htmlspecialchars($book->getBookId()) ?>" class="modal-close">&times;</label>
                    <h3><?= htmlspecialchars($book->getTitle()) ?></h3>
                    <p><?= htmlspecialchars($book->getDescription()) ?></p>
                </div>
            </div>
            
            <?php if(checkLoggedIn()) : ?>
                <button type="button" class="saveBookBtn">Putt boken i hyllen</button>
            <?php else: ?>
                <p><em>Logg inn for å lagre boken i din bokhylle.</em></p>
            <?php endif; ?>
</div>