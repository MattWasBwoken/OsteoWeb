document.addEventListener('DOMContentLoaded', () => {
    fetch('getSession.php')
        .then(response => response.json())
        .then(data => {
            addEventListeners();
        })

    let name = document.getElementById('title');
    let text = document.getElementById('text');
    let form = document.querySelector('form');
    if (name) {
        name.addEventListener('input', checkTitle);
    }
    if (text) {
        text.addEventListener('input', checkText);
    }
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!checkTitle() || !checkText()) {
                event.preventDefault();
            }
        });
    }

});

function addEventListeners() {
    document.querySelectorAll('.add-btn').forEach(button => {
        button.addEventListener('click', () => {
            window.location.href = 'handleNews.php?ope=create';
        });
    });

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            window.location.href = "handleNews.php?ope=edit&id="+id;
        });
    });

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', () => {
            fetch("handleNews.php?ope=delete&id="+button.getAttribute('data-id'), {
                method: 'GET'
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    location.reload();
                }
            })
            .catch(error => console.error('Errore:', error));
            
        });
    });
}

const invalidChars = /[?!'<>\/\\{}*#%|[\]^~;:"]+/;

function checkTitle() {
    let title = document.getElementById('title').value;
    if (!invalidChars.test(title)) {
        document.getElementById('title').classList.remove('invalid');
        document.getElementById('nome_error').innerHTML = '';
        document.getElementById('nome_error').classList.add('hidden');
        return true;
    }
    let error = "Il nome inserito non è valido";
    document.getElementById('title').classList.add('invalid');
    document.getElementById('nome_error').classList.remove('hidden');
    document.getElementById('nome_error').innerHTML = error;
}

function checkText() {
    
    let text = document.getElementById('text').value;
    if ( !invalidChars.test(text)) {
        document.getElementById('text').classList.remove('invalid');
        document.getElementById('text_error').innerHTML = '';
        document.getElementById('text_error').classList.add('hidden');
        return true;
    }
    let error = "Il testo inserito non è valido";
    document.getElementById('text').classList.add('invalid');
    document.getElementById('text_error').classList.remove('hidden');
    document.getElementById('text_error').innerHTML = error;
}