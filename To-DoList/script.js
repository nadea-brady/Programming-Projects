function addTask() {
    const taskInput = document.getElementById('task-input');
    const taskList = document.getElementById('task-list');
    if (taskInput.value.trim() !== '') {
        const li = document.createElement('li');
        li.textContent = taskInput.value;
        
        // Adding a delete button
        const deleteBtn = document.createElement('button');
        deleteBtn.textContent = 'Delete';
        deleteBtn.onclick = function() {
            taskList.removeChild(li);
        };
        li.appendChild(deleteBtn);

        // Adding a done button
        const doneBtn = document.createElement('button');
        doneBtn.textContent = 'Done';
        doneBtn.onclick = function() {
            li.classList.toggle('done');
        };
        li.appendChild(doneBtn);

        taskList.appendChild(li);
        taskInput.value = ''; // Clear input field after adding
    } else {
        alert('Please enter a task!');
    }
}
