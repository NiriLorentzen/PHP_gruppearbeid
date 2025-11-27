    <?php 
    require_once "classes/Books.php";
    require_once "classes/BookDB.php";
    require_once 'scripts/DB/db.inc.php';
    require_once 'scripts/checkLoginStatus.php';

    include 'scripts/navbar.php';


    mustBeLoggedIn();

    $usersBooks = [];
    if(isset($_SESSION['userID'])) {
        $bookDB = new BookDB($pdo);
        $usersBooks = $bookDB->userFetchAllBooks($_SESSION['userID']);
        
        // Server-side sorting: determine requested sort option (default: title ascending)
        $allowedSorts = ['title_asc','title_desc','author_asc','author_desc','pages_asc','pages_desc'];
        $sort = $_GET['sort'] ?? 'title_asc';
        if(!in_array($sort, $allowedSorts, true)) {
            $sort = 'title_asc';
        }

        switch($sort) {
            case 'title_asc':
                usort($usersBooks, function($bookA, $bookB) {
                    return strcasecmp($bookA->getTitle(), $bookB->getTitle());
                });
                break;
            case 'title_desc':
                usort($usersBooks, function($bookA, $bookB) {
                    return strcasecmp($bookB->getTitle(), $bookA->getTitle());
                });
                break;
            case 'author_asc':
                usort($usersBooks, function($bookA, $bookB) {
                    $aAuth = trim(strtolower($bookA->getAuthors() ?? ''));
                    $bAuth = trim(strtolower($bookB->getAuthors() ?? ''));
                    $unknown = 'Ukjent forfatter';
                    $aUnknown = ($aAuth === $unknown);
                    $bUnknown = ($bAuth === $unknown);

                    if ($aUnknown && !$bUnknown) return 1; // unknowns go to bottom
                    if ($bUnknown && !$aUnknown) return -1;

                    return strcasecmp($bookA->getAuthors(), $bookB->getAuthors());
                });
                break;
            case 'author_desc':
                usort($usersBooks, function($bookA, $bookB) {
                    $aAuth = trim(strtolower($bookA->getAuthors() ?? ''));
                    $bAuth = trim(strtolower($bookB->getAuthors() ?? ''));
                    $unknown = 'Ukjent forfatter';
                    $aUnknown = ($aAuth === $unknown);
                    $bUnknown = ($bAuth === $unknown);

                    if ($aUnknown && !$bUnknown) return 1; // unknowns still bottom even in desc
                    if ($bUnknown && !$aUnknown) return -1;

                    return strcasecmp($bookB->getAuthors(), $bookA->getAuthors());
                });
                break;
            case 'pages_asc':
                usort($usersBooks, function($bookA, $bookB) {
                    $aVal = $bookA->getPageCount();
                    $bVal = $bookB->getPageCount();
                    $aIsNum = is_numeric($aVal);
                    $bIsNum = is_numeric($bVal);

                    if (!$aIsNum && $bIsNum) return 1; // unknown pages go to bottom
                    if (!$bIsNum && $aIsNum) return -1;

                    return ((int)$aVal) <=> ((int)$bVal);
                });
                break;
            case 'pages_desc':
                usort($usersBooks, function($bookA, $bookB) {
                    $aVal = $bookA->getPageCount();
                    $bVal = $bookB->getPageCount();
                    $aIsNum = is_numeric($aVal);
                    $bIsNum = is_numeric($bVal);

                    if (!$aIsNum && $bIsNum) return 1; // unknown pages still bottom
                    if (!$bIsNum && $aIsNum) return -1;

                    return ((int)$bVal) <=> ((int)$aVal);
                });
                break;
        }
    }


    ?>

    <!DOCTYPE html>
    <html lang="no">
    <head>
        <meta charset="UTF-8">
        <script src="main.js" defer></script>
        <title>Bokhylle</title>
        <link rel="stylesheet" href="css/stylesheet.css">
    </head>
    <body>
    
        <h1>Din Bokhylle</h1>
        
        <?php if(empty($usersBooks)): ?>
            <h2>Bokhyllen din er tom.</h2>
        <?php else: ?>
            <form method="get" id="sortForm" style="margin-bottom:10px;">
                <label for="sort">Sorter etter:</label>
                <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="title_asc" <?= (isset($sort) && $sort === 'title_asc') ? 'selected' : '' ?>>Tittel (a-å)</option>
                    <option value="title_desc" <?= (isset($sort) && $sort === 'title_desc') ? 'selected' : '' ?>>Tittel (å-a)</option>
                    <option value="author_asc" <?= (isset($sort) && $sort === 'author_asc') ? 'selected' : '' ?>>Forfatter (a-å)</option>
                    <option value="author_desc" <?= (isset($sort) && $sort === 'author_desc') ? 'selected' : '' ?>>Forfatter (å-a)</option>
                    <option value="pages_asc" <?= (isset($sort) && $sort === 'pages_asc') ? 'selected' : '' ?>>Sider (lav-høy)</option>
                    <option value="pages_desc" <?= (isset($sort) && $sort === 'pages_desc') ? 'selected' : '' ?>>Sider (høy-lav)</option>
                </select>
            </form>
            <?php foreach($usersBooks as $book): ?>
                <div class="bookItem"style="border:1px solid #ccc; padding:10px; margin:10px;">
                    <?php include 'templates/bookCard.php'; ?>
                    <button type="button" class="removeBookBtn" data-id="<?= $book->getBookId() ?>">Fjern boken fra hyllen</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    <script>
    document.querySelectorAll(".removeBookBtn").forEach(btn => {
        btn.addEventListener("click", async () => {
                    
            const bookItem = btn.closest(".bookItem");
            const bookId = btn.dataset.id;

            if(!confirm("Er du sikker på du vil fjerne boken?")) {
                return;
            }

            const response = await fetch("api/handleBookshelf.php" ,{
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    action: "remove",
                    bookID: bookId
                })
            });

            const result = await response.json();
            alert(result.message);

            if(result.success) {
                bookItem.remove();
            }    

        });
    });

    </script> 
    </body> 
    </html>