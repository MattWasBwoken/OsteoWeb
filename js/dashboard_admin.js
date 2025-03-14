document.addEventListener('DOMContentLoaded', () => {
    fetch('getSession.php')
        .then(response => response.json())
        .then(data => {
            addEventListeners();
        })
        .catch(error => console.error('Errore:', error));
});

function addEventListeners() {
    document.querySelectorAll('.accept-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            fetch("handlePrenotazione.php?ope=accept&id="+id, {
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

    document.querySelectorAll('.reject-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            fetch("handlePrenotazione.php?ope=reject&id="+id, {
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