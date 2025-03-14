document.addEventListener('DOMContentLoaded', (event) => { 
    const today = new Date().toISOString().split('T')[0]; 
    document.getElementById('data').setAttribute('min', today); 
    fetch('FetchUnavailableDates.php') 
        .then(response => response.json()) 
        .then(data => { 
            disableUnavailableDates(data.unavailableDates); 
        }); 
    fetch('getInfo.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text(); // Leggi la risposta come testo
        })
        .then(text => {
            if (text) {
                return JSON.parse(text); // Convertilo in JSON solo se non è vuoto
            } else {
                return {}; // Ritorna un oggetto vuoto se la risposta è vuota
            }
        })
        .then(data => {
            if (data &&  Object.keys(data).length > 0) {
                applyValue(data);
            }
            
        });
    document.getElementById('nome').addEventListener('change', checkName);
    document.getElementById('cognome').addEventListener('change', checkCognome);
    document.getElementById('CF').addEventListener('change', checkCF);
    document.getElementById('mail').addEventListener('change', checkMail);
    document.getElementById('phone').addEventListener('change', checkPhone);
    document.getElementById('note').addEventListener('change', checkNotes);
    
    document.getElementById('sede').addEventListener('change', updateTurns);
    document.getElementById('data').addEventListener('change', updateTurns);

    document.getElementById('Prenota').addEventListener('click', (event) => {
        if(!document.getElementById('turno').value) {
            event.preventDefault();
            document.getElementById('orari').classList.add('invalid');
            document.getElementById('turno_error').classList.remove('hidden');
            return;
        }       
    });
});

function updateTurns() {
    let sede = document.getElementById('sede').value;
    let data = document.getElementById('data').value;
    
    if (sede && data) {
        fetch('dateSelect.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body : 'date='+ data + '&sede='+ sede
        }) 
        .then(response => response.json()) 
        .then(data => { 
            console.log('Response data:', data);
            getTurni(data); 
        }); 
        
    } else if (!sede && data) {
        document.getElementById('orari').innerHTML = 'Seleziona una sede';
    } else if (sede && !data) {
        document.getElementById('orari').innerHTML = 'Seleziona una data';
    } else {
        document.getElementById('orari').innerHTML = 'Seleziona una sede e una data';
    }
}

function applyValue(data) {
    console.log(data);
    document.getElementById('nome').value = data['Nome'];
    document.getElementById('cognome').value = data['Cognome'];
    document.getElementById('mail').value = data['Mail'];
    document.getElementById('phone').value = data['Telefono'];
    document.getElementById('Birth').value = data['DataNascita'];
    document.getElementById('CF').value = data['CF'];
    document.getElementById('nome').ariaReadOnly=true;
    document.getElementById('nome').readOnly=true;
    document.getElementById('cognome').ariaReadOnly=true;
    document.getElementById('cognome').readOnly=true;
    document.getElementById('Birth').ariaReadOnly=true;
    document.getElementById('Birth').readOnly=true;
    document.getElementById('CF').ariaReadOnly=true;
    document.getElementById('CF').readOnly=true;
    document.getElementById('mail').ariaReadOnly=true;
    document.getElementById('mail').readOnly=true;
    document.getElementById('phone').ariaReadOnly=true;
    document.getElementById('phone').readOnly=true;
}

function disableUnavailableDates(unavailableDates) { 
    const dateInput = document.getElementById('data'); 
    dateInput.addEventListener('input', function() { 
        const selectedDate = new Date(this.value);
        const day = selectedDate.getDay();
        if (day === 0 || day === 6 || selectedDate <= new Date() || unavailableDates.includes(this.value)) {
            this.setCustomValidity("This date is unavailable."); 
            this.classList.add('invalid');
            this.reportValidity(); 
            this.value = '';
            document.getElementById('data_error').classList.remove('hidden');
        } else { 
            this.setCustomValidity('');
            this.classList.remove('invalid');
            document.getElementById('data_error').classList.add('hidden');
        } 
    }); 
}

function hideElementsAndShowMessage() {
    
}

function getTurni(data) {
    if(!document.getElementById('data_error').classList.contains('hidden')) {
        document.getElementById('orari').innerHTML = '<p>Prova a selezionare un\'altra data.</p>';
        return;
    }
    document.getElementById('orari').innerHTML = '';
    for (let i = 0; i < data.length; i++) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.classList.add('time-button');
        btn.textContent = data[i];
        
        btn.addEventListener('click', () => { 
            document.querySelectorAll('.time-button').forEach(btn => btn.classList.remove('selected')); 
            btn.classList.add('selected'); 
            switch (btn.textContent) {
                case '09:00':
                    document.getElementById('turno').value = 1;
                    break;
                case '10:00':
                    document.getElementById('turno').value = 2;
                    break;
                case '11:00':
                    document.getElementById('turno').value = 3;
                    break;
                case '12:00':
                    document.getElementById('turno').value = 4;
                    break;
                case '13:00':
                    document.getElementById('turno').value = 5;
                    break;
                case '14:00':
                    document.getElementById('turno').value = 6;
                    break;
                case '15:00':
                    document.getElementById('turno').value = 7;
                    break;
                case '16:00':
                    document.getElementById('turno').value = 8;
                    break;
                case '17:00':
                    document.getElementById('turno').value = 9;
                    break;
                case '18:00':
                    document.getElementById('turno').value = 10;
                    break;
            }
            document.getElementById('orari').classList.remove('invalid');
            document.getElementById('turno_error').classList.add('hidden');
        });
        document.getElementById('orari').appendChild(btn);
    }    
}

//chars not allowed
const invalidChars = /[!?<>\/\\{}*#%|[\]^~;:"]+/;

// functions

function transformToUpperCase(inputID) {
    const inputElement = document.getElementById(inputID);
    inputElement.value = inputElement.value.toUpperCase();
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
        document.getElementById('CF_error').classList.add('hidden');
        return true;
    }
    error = "Il codice fiscale inserito non è valido";
    document.getElementById('CF').classList.add('invalid');
    document.getElementById('CF_error').innerHTML = error;
    document.getElementById('CF_error').classList.remove('hidden');
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

function checkNotes() {
    const note = document.getElementById('note').value;
    if (!invalidChars.test(note)) {
        document.getElementById('note').classList.remove('invalid');
        document.getElementById('note_error').classList.add('hidden');
        return true;
    }
    error = "Il campo note non è valido";
    document.getElementById('note').classList.add('invalid');
    document.getElementById('note_error').innerHTML = error;
    document.getElementById('note_error').classList.remove('hidden');
    return false;
}