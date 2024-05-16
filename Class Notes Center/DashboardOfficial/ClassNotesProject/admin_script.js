document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addUserBtn').addEventListener('click', function() {
        const username = prompt('Enter the username:');
        const email = prompt('Enter the user email:');
        const password = prompt('Enter the user password:');
        const accountType = prompt('Enter the account type (student or teacher):');

        if (username && email && password && accountType) {
            addUserRow(username, email, password, accountType);
        } else {
            alert("All fields are required.");
        }
    });
});

function addUserRow(username, email, password, accountType) {
    const table = document.getElementById('userTable').getElementsByTagName('tbody')[0];
    const newRow = table.insertRow();

    newRow.insertCell(0).textContent = username;
    newRow.insertCell(1).textContent = email;
    newRow.insertCell(2).textContent = password;
    newRow.insertCell(3).textContent = accountType;

    const deleteCell = newRow.insertCell(4);
    const deleteButton = document.createElement('button');
    deleteButton.textContent = 'Delete';
    deleteButton.addEventListener('click', function() {
        table.deleteRow(newRow.rowIndex - 1);
    });
    deleteCell.appendChild(deleteButton);
}

