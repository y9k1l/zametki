// Функция обновления списка заметок на главной
async function refreshMainNotes() {
    let response = await fetch('/mysite/api.php?action=list');
    let html = await response.text();
    let notesList = document.getElementById('main-notes-list');
    if (notesList) {
        notesList.innerHTML = html;
    }
}

// Функция обновления списка в кабинете
async function refreshCabinetNotes() {
    let response = await fetch('/mysite/api.php?action=list');
    let html = await response.text();
    let notesList = document.getElementById('cabinet-notes-list');
    if (notesList) {
        notesList.innerHTML = html;
        attachCabinetEvents();
    }
}

function attachCabinetEvents() {
    document.querySelectorAll('.edit-note-btn').forEach(btn => {
        btn.onclick = function() {
            let card = this.closest('.note-edit-card');
            card.querySelector('.note-view').style.display = 'none';
            card.querySelector('.note-edit').style.display = 'block';
        };
    });
    
    document.querySelectorAll('.cancel-edit-btn').forEach(btn => {
        btn.onclick = function() {
            let card = this.closest('.note-edit-card');
            card.querySelector('.note-view').style.display = 'block';
            card.querySelector('.note-edit').style.display = 'none';
        };
    });
    
    document.querySelectorAll('.save-edit-btn').forEach(btn => {
        btn.onclick = async function() {
            let card = this.closest('.note-edit-card');
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
                refreshCabinetNotes();
                showCabinetMessage('Заметка обновлена', 'green');
            } else {
                showCabinetMessage(result.error, 'red');
            }
        };
    });
    
    document.querySelectorAll('.delete-note-btn').forEach(btn => {
        btn.onclick = async function() {
            if (!confirm('Точно удалить заметку?')) return;
            
            let card = this.closest('.note-edit-card');
            let id = card.dataset.id;
            let formData = new FormData();
            formData.append('id', id);
            
            let response = await fetch('/mysite/api.php?action=delete', {
                method: 'POST',
                body: formData
            });
            let result = await response.json();
            
            if (result.success) {
                refreshCabinetNotes();
                showCabinetMessage('Заметка удалена', 'green');
            } else {
                showCabinetMessage(result.error, 'red');
            }
        };
    });
}

function showCabinetMessage(text, color) {
    let msgDiv = document.getElementById('cabinet-message');
    if (msgDiv) {
        msgDiv.innerHTML = '<p style="color: ' + color + ';">' + text + '</p>';
        setTimeout(() => msgDiv.innerHTML = '', 3000);
    }
}

// Если мы на главной странице, настраиваем форму добавления
if (document.getElementById('quickNoteForm')) {
    document.getElementById('show-note-form-btn').onclick = function() {
        document.getElementById('note-form-container').style.display = 'block';
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
            document.getElementById('note-form-container').style.display

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