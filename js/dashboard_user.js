document.addEventListener('DOMContentLoaded', () => {
    fetch('getSession.php')
        .then(response => response.json())
        .then(data => {
            addDeleteEventListeners();
        })
});


function addDeleteEventListeners() {
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            fetch('eliminaPrenotazione.php?id='+id, {
                method: 'GET'
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    location.reload();
                }
            })});
    });
}