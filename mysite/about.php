<?php
require_once 'functions.php';
?>
<?php include 'header.php'; ?>

<div class="about-section">
    <h2>О себе</h2>
    
    <?php if (isLoggedIn()): ?>
        <div id="about-content">
            <div id="about-view">
                <div id="about-text">
                    <?php
                    $userId = $_SESSION['user_id'];
                    $user = getUserById($userId);
                    $aboutText = isset($user['about']) ? $user['about'] : 'Здесь пока ничего нет. Нажмите "Редактировать", чтобы добавить информацию о себе.';
                    ?>
                    <p><?= nl2br(h($aboutText)) ?></p>
                </div>
                <button id="edit-about-btn" class="edit-about-btn">Редактировать</button>
            </div>
            
            <div id="about-edit" style="display: none;">
                <textarea id="about-input" rows="6" style="width: 100%; padding: 10px;" maxlength="1000"><?= h($aboutText) ?></textarea>
                <div class="edit-buttons">
                    <button id="save-about-btn">Сохранить</button>
                    <button id="cancel-about-btn">Отмена</button>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p>Здесь будет информация о себе. <a href="/mysite/login.php">Войдите</a>, чтобы редактировать.</p>
    <?php endif; ?>
</div>

<script>
<?php if (isLoggedIn()): ?>
function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

let editBtn = document.getElementById('edit-about-btn');
let saveBtn = document.getElementById('save-about-btn');
let cancelBtn = document.getElementById('cancel-about-btn');
let viewDiv = document.getElementById('about-view');
let editDiv = document.getElementById('about-edit');
let aboutInput = document.getElementById('about-input');

autoResize(aboutInput);
aboutInput.addEventListener('input', function() { autoResize(this); });

editBtn.onclick = function() {
    viewDiv.style.display = 'none';
    editDiv.style.display = 'block';
    autoResize(aboutInput);
};

cancelBtn.onclick = function() {
    editDiv.style.display = 'none';
    viewDiv.style.display = 'block';
};

saveBtn.onclick = async function() {
    let text = aboutInput.value;
    
    if (text.length > 1000) {
        alert('Не более 1000 символов');
        return;
    }
    
    let formData = new FormData();
    formData.append('about', text);
    
    let response = await fetch('/mysite/api.php?action=update_about', {
        method: 'POST',
        body: formData
    });
    let result = await response.json();
    
    if (result.success) {
        document.getElementById('about-text').innerHTML = '<p>' + text.replace(/\n/g, '<br>') + '</p>';
        editDiv.style.display = 'none';
        viewDiv.style.display = 'block';
        alert('Информация сохранена');
    } else {
        alert('Ошибка: ' + result.error);
    }
};
<?php endif; ?>
</script>

</body>
</html>