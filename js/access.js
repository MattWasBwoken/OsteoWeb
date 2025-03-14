var users = [];

document.addEventListener("DOMContentLoaded", function () {
    fetch('getUsers.php')
        .then(response => response.json())
        .then(data => { 
            users = data;
        })
        .catch(error => { console.error('Error:', error); });
    document.getElementById("registrazione").classList.add('hidden');

    document.getElementById('go_reg').addEventListener('click', registrazione);
    document.getElementById('go_log').addEventListener('click', login);

    // Login
    document.getElementById('username_l').addEventListener('change', function () {
        checkUsername('username_l', 'username_error_l');
    });
    document.getElementById('password_l').addEventListener('change', function () {
        checkPassword('password_l', 'password_error_l');
    });
    // Registrazione
    document.getElementById('nome').addEventListener('change', checkName);
    document.getElementById('cognome').addEventListener('change', checkCognome); 
    document.getElementById('CF').addEventListener('change', checkCF);
    document.getElementById('CF').addEventListener('input', function() {
        transformToUpperCase('CF');
    });
    document.getElementById('mail').addEventListener('change', checkMail);
    document.getElementById('phone').addEventListener('change', checkPhone);
    document.getElementById('username_r').addEventListener('change', function () {
        checkUsername('username_r', 'username_error_r');
    });
    document.getElementById('password_r').addEventListener('change', function () {
        checkPassword('password_r', 'password_error_r');
    });
});

const invalidChars = /[<>\/\\{}*#%|[\]^~;:"]+/;

function registrazione() {
    document.getElementById("login").classList.add('hidden');
    document.getElementById("registrazione").classList.remove('hidden');
}
function login() {
    document.getElementById("registrazione").classList.add('hidden');
    document.getElementById("login").classList.remove('hidden');
}

function checkName() {
    const nome = document.getElementById('nome').value;
    const correctName = /^[a-zA-Z\s]+$/;
    if (correctName.test(nome) && !invalidChars.test(nome)) {
        document.getElementById('nome').classList.remove('invalid');
        document.getElementById('nome_error').classList.add('hidden');
        return true;
    }
    error = "Il nome inserito non è valido";
    document.getElementById('nome').classList.add('invalid');
    document.getElementById('nome_error').innerHTML = error;
    document.getElementById('nome_error').classList.remove('hidden');
    
    return false;
}

function checkCognome() {
    const cognome = document.getElementById('cognome').value;
    const correctCognome = /^[a-zA-Z\s]+$/;
    if (correctCognome.test(cognome) && !invalidChars.test(cognome)) {
        document.getElementById('cognome').classList.remove('invalid');
        document.getElementById('cognome_error').classList.add('hidden');
        return true;
    }
    error = "Il cognome inserito non è valido";
    document.getElementById('cognome').classList.add('invalid');
    document.getElementById('cognome_error').innerHTML = error;
    document.getElementById('cognome_error').classList.remove('hidden');
    return false;
}

function checkCF() {
    const cf = document.getElementById('CF').value;
    const correctCF = /^[A-Z]{6}\d{2}[A-Z]{1}\d{2}[A-Z]{1}\d{3}[A-Z]{1}$/;
    if (correctCF.test(cf) && !invalidChars.test(cf)) {
        document.getElementById('CF').classList.remove('invalid');
        document.getElementById('cf_error').classList.add('hidden');
        return true;
    }
    error = "Il codice fiscale inserito non è valido";
    document.getElementById('CF').classList.add('invalid');
    document.getElementById('cf_error').innerHTML = error;
    document.getElementById('cf_error').classList.remove('hidden');
    return false;
}

function checkMail() {
    const email = document.getElementById('mail').value;
    const correctEmail = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;
    if (correctEmail.test(email) && !invalidChars.test(email)) {
        document.getElementById('mail').classList.remove('invalid');
        document.getElementById('mail_error').classList.add('hidden');
        return true;
    }
    error = "L'email inserita non è valida";
    document.getElementById('mail').classList.add('invalid');
    document.getElementById('mail_error').innerHTML = error;
    document.getElementById('mail_error').classList.remove('hidden');
    return false;
}

function checkPhone() {
    const phone = document.getElementById('phone').value;
    const correctPhone = /^[0-9]{10}$/;
    if (correctPhone.test(phone) && !invalidChars.test(phone)) {
        document.getElementById('phone').classList.remove('invalid');
        document.getElementById('phone_error').classList.add('hidden');
        return true;
    }
    error = "Il numero di telefono inserito non è valido";
    document.getElementById('phone').classList.add('invalid');
    document.getElementById('phone_error').innerHTML = error;
    document.getElementById('phone_error').classList.remove('hidden');
    return false;
}

function checkUsername(inputID, errorID) {
    const username = document.getElementById(inputID).value;
    const correctUsername = /^[a-zA-Z0-9]+$/;
    if ((correctUsername.test(username) && !invalidChars.test(username))) {
        if (inputID == 'username_r' && users.includes(username)) {
            error = "Lo username inserito è già in uso, sceglierne un altro.";
        } else {
            document.getElementById(inputID).classList.remove('invalid');
            document.getElementById(errorID).classList.add('hidden');
            return true;
        }
    } else {
        error = "Lo username inserito non è valido";
    }
    document.getElementById(inputID).classList.add('invalid');
    document.getElementById(errorID).innerHTML = error;
    document.getElementById(errorID).classList.remove('hidden');
    return false;
}
function checkPassword(inputID, errorID) {
    const password = document.getElementById(inputID).value;
    if (!invalidChars.test(password)) {
        document.getElementById(inputID).classList.remove('invalid');
        document.getElementById(errorID).classList.add('hidden');
        return true;
    }
    error = "La password inserita non è valida";
    document.getElementById(inputID).classList.add('invalid');
    document.getElementById(errorID).innerHTML = error;
    document.getElementById(errorID).classList.remove('hidden');
    return false;
}

function transformToUpperCase(inputID) {
    const inputElement = document.getElementById(inputID);
    inputElement.value = inputElement.value.toUpperCase();
}