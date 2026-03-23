<?php
require_once 'functions.php';
$userNotes = [];
if (isLoggedIn()) {
    $userId = $_SESSION['user_id'];
    $userNotes = getUserNotes($userId);
}
?>
<?php include 'header.php'; ?>

<?php if (isLoggedIn()): ?>
    <div class="welcome-section">
        
        <?php if (empty($userNotes)): ?>
            <div id="empty-notes-message">
                <p>Это главная страница. Здесь будут ваши заметки.</p>
                <button id="show-note-form-btn" class="create-note-btn">Создать заметку</button>
            </div>
        <?php else: ?>
            <div id="has-notes">
                <button id="show-note-form-btn" class="create-note-btn">Создать заметку</button>
            </div>
        <?php endif; ?>
        
        <div id="note-form-container" style="display: none;">
            <div class="note-form-box">
                <h3>Новая заметка</h3>
                <form id="quickNoteForm">
                    <input type="text" name="title" placeholder="Заголовок (до 50 символов)" required maxlength="50">
                    <textarea id="new-note-content" name="content" rows="3" placeholder="Содержание заметки (до 2000 символов)..." required maxlength="2000"></textarea>
                    <div class="form-buttons">
                        <button type="submit">Готово</button>
                        <button type="button" id="cancel-note-btn">Отмена</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div id="main-notes-list" class="notes-list">
            <?php if (!empty($userNotes)): ?>
                <?php foreach ($userNotes as $note): ?>
                    <div class="note-card" data-id="<?= $note['id'] ?>">
                        <h3><?= h($note['title']) ?></h3>
                        <p><?= nl2br(h($note['content'])) ?></p>
                        <small><?= h($note['created_at']) ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function autoResize(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }
    
    document.querySelectorAll('textarea').forEach(textarea => {
        autoResize(textarea);
        textarea.addEventListener('input', function() {
            autoResize(this);
        });
    });
    
    document.getElementById('show-note-form-btn').onclick = function() {
        document.getElementById('note-form-container').style.display = 'block';
        let textarea = document.getElementById('new-note-content');
        setTimeout(() => autoResize(textarea), 0);
    };
    
    document.getElementById('cancel-note-btn').onclick = function() {
        document.getElementById('note-form-container').style.display = 'none';
        document.getElementById('quickNoteForm').reset();
    };
    
    document.getElementById('quickNoteForm').onsubmit = async function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let response = await fetch('/mysite/api.php?action=add', {
            method: 'POST',
            body: formData
        });
        let result = await response.json();
        
        if (result.success) {
            document.getElementById('note-form-container').style.display = 'none';
            this.reset();
            await refreshMainNotes();
            
            let emptyMsg = document.getElementById('empty-notes-message');
            if (emptyMsg) {
                emptyMsg.style.display = 'none';
            }
        } else {
            alert('Ошибка: ' + result.error);
        }
    };
    
    async function refreshMainNotes() {
        let response = await fetch('/mysite/api.php?action=list');
        let html = await response.text();
        let notesList = document.getElementById('main-notes-list');
        if (notesList) {
            notesList.innerHTML = html;
        }
    }
    </script>
<?php else: ?>
    <div class="guest-section">
        <h2>Добро пожаловать!</h2>
        <p>Это главная страница. Зарегистрируйтесь, чтобы создавать заметки.</p>
        <div class="guest-buttons">
            <a href="/mysite/login.php" class="btn">Войти</a>
        </div>
    </div>
<?php endif; ?>

</body>
</html>