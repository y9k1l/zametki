<?php
require_once 'functions.php';
requireAuth();
$userId = $_SESSION['user_id'];
$user = getUserById($userId);
$notes = getUserNotes($userId);
?>
<?php include 'header.php'; ?>

<div class="cabinet-section">
    <h2>Мои заметки</h2>
    <p>Здесь вы можете редактировать и удалять свои заметки</p>
    
    <div id="cabinet-message" class="message-box"></div>
    
    <div id="cabinet-notes-list">
        <?php if (empty($notes)): ?>
            <p class="empty-notes">Нет заметок. Создайте первую на главной странице.</p>
        <?php else: ?>
            <?php foreach ($notes as $note): ?>
                <div class="note-edit-card" data-id="<?= $note['id'] ?>">
                    <div class="note-view">
                        <h3><?= h($note['title']) ?></h3>
                        <p><?= nl2br(h($note['content'])) ?></p>
                        <small><?= h($note['created_at']) ?></small>
                        <div class="note-buttons">
                            <button class="edit-note-btn">Редактировать</button>
                            <button class="delete-note-btn">Удалить</button>
                        </div>
                    </div>
                    <div class="note-edit" style="display: none;">
                        <input type="text" class="edit-title" value="<?= h($note['title']) ?>" maxlength="50">
                        <textarea class="edit-content" rows="4" maxlength="2000"><?= h($note['content']) ?></textarea>
                        <div class="edit-buttons">
                            <button class="save-edit-btn">Сохранить</button>
                            <button class="cancel-edit-btn">Отмена</button>
                        </div>
                    </div>
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

async function refreshCabinetNotes() {
    let response = await fetch('/mysite/api.php?action=list');
    let html = await response.text();
    let notesList = document.getElementById('cabinet-notes-list');
    if (notesList) {
        notesList.innerHTML = html;
        // После обновления списка заново привязываем все события
        attachCabinetEvents();
        // Заново настраиваем авто-расширение для всех textarea
        document.querySelectorAll('.edit-content').forEach(textarea => {
            autoResize(textarea);
            textarea.addEventListener('input', function() { autoResize(this); });
        });
    }
}

function showCabinetMessage(text, color) {
    let msgDiv = document.getElementById('cabinet-message');
    msgDiv.innerHTML = '<p style="color: ' + color + ';">' + text + '</p>';
    setTimeout(() => msgDiv.innerHTML = '', 3000);
}

function attachCabinetEvents() {
    // Кнопки "Редактировать"
    document.querySelectorAll('.edit-note-btn').forEach(btn => {
        btn.onclick = function(e) {
            e.stopPropagation();
            let card = this.closest('.note-edit-card');
            if (card) {
                card.querySelector('.note-view').style.display = 'none';
                card.querySelector('.note-edit').style.display = 'block';
                let textarea = card.querySelector('.edit-content');
                if (textarea) autoResize(textarea);
            }
        };
    });
    
    // Кнопки "Отмена"
    document.querySelectorAll('.cancel-edit-btn').forEach(btn => {
        btn.onclick = function(e) {
            e.stopPropagation();
            let card = this.closest('.note-edit-card');
            if (card) {
                card.querySelector('.note-view').style.display = 'block';
                card.querySelector('.note-edit').style.display = 'none';
            }
        };
    });
    
    // Кнопки "Сохранить"
    document.querySelectorAll('.save-edit-btn').forEach(btn => {
        btn.onclick = async function(e) {
            e.stopPropagation();
            let card = this.closest('.note-edit-card');
            if (!card) return;
            
            let id = card.dataset.id;
            let title = card.querySelector('.edit-title').value;
            let content = card.querySelector('.edit-content').value;
            
            let formData = new FormData();
            formData.append('id', id);
            formData.append('title', title);
            formData.append('content', content);
            
            let response = await fetch('/mysite/api.php?action=update', {
                method: 'POST',
                body: formData
            });
            let result = await response.json();
            
            if (result.success) {
                await refreshCabinetNotes();
                showCabinetMessage('Заметка обновлена', 'green');
            } else {
                showCabinetMessage(result.error, 'red');
            }
        };
    });
    
    // Кнопки "Удалить"
    document.querySelectorAll('.delete-note-btn').forEach(btn => {
        btn.onclick = async function(e) {
            e.stopPropagation();
            if (!confirm('Точно удалить заметку?')) return;
            
            let card = this.closest('.note-edit-card');
            if (!card) return;
            
            let id = card.dataset.id;
            let formData = new FormData();
            formData.append('id', id);
            
            let response = await fetch('/mysite/api.php?action=delete', {
                method: 'POST',
                body: formData
            });
            let result = await response.json();
            
            if (result.success) {
                await refreshCabinetNotes();
                showCabinetMessage('Заметка удалена', 'green');
            } else {
                showCabinetMessage(result.error, 'red');
            }
        };
    });
}

// Инициализация при загрузке
attachCabinetEvents();
document.querySelectorAll('.edit-content').forEach(textarea => {
    autoResize(textarea);
    textarea.addEventListener('input', function() { autoResize(this); });
});
</script>

</body>
</html>