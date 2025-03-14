var users = [];
document.addEventListener("DOMContentLoaded", function () {
    fetch('getUsers.php')
        .then(response => response.json())
        .then(data => { 
            users = data;
        })
        .catch(error => { console.error('Error:', error); });

    document.getElementById('username_r').addEventListener('change', checkUsername);
    document.getElementById('password_r').addEventListener('change', checkPassword);
});

const invalidChars = /[<>\/\\{}*#%|[\]^~;:"]+/;
function checkUsername() {
    const username = document.getElementById('username_r').value;
    const correctUsername = /^[a-zA-Z0-9]+$/;
    if ((correctUsername.test(username) && !invalidChars.test(username))) {
        if (users.includes(username)) {
            error = "Lo username inserito è già in uso, sceglierne un altro.";
        } else {
            document.getElementById('username_r').classList.remove('invalid');
            document.getElementById('username_error_r').classList.add('hidden');
            return true;
        }
    } else {
        error = "Lo username inserito non è valido";
    }
    document.getElementById('username_r').classList.add('invalid');
    document.getElementById('username_error_r').innerHTML = error;
    document.getElementById('username_error_r').classList.remove('hidden');
    return false;
}
function checkPassword() {
    const password = document.getElementById('password_r').value;
    if (!invalidChars.test(password)) {
        document.getElementById('password_r').classList.remove('invalid');
        document.getElementById('password_error_r').classList.add('hidden');
        return true;
    }
    error = "La password inserita non è valida";
    document.getElementById('password_r').classList.add('invalid');
    document.getElementById('password_error_r').innerHTML = error;
    document.getElementById('password_error_r').classList.remove('hidden');
    return false;
}