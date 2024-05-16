function addCourse() {
    const courseNameInput = document.getElementById('course-name');
    const courseName = courseNameInput.value.trim();
    if (courseName) {
        const courseList = document.getElementById('course-list');
        const li = document.createElement('li');
        li.textContent = courseName;
        courseList.appendChild(li);
        courseNameInput.value = ''; // Reset input value
    } else {
        alert('Please enter a course name.');
    }
}

// Example initialization
document.addEventListener('DOMContentLoaded', () => {
    const initialCourses = ['Web Development', 'Data Science', 'Machine Learning'];
    initialCourses.forEach(courseName => {
        const courseList = document.getElementById('course-list');
        const li = document.createElement('li');
        li.textContent = courseName;
        courseList.appendChild(li);
    });
});
