document.addEventListener("DOMContentLoaded", function () {
    fetch("get_courses.php")
        .then(response => response.json())
        .then(courses => {
            displayCourses(courses);
        })
        .catch(error => console.error("خطأ في جلب البيانات:", error));
});

function displayCourses(courses) {
    const coursesContainer = document.querySelector(".courses");
    coursesContainer.innerHTML = "";
    
    courses.forEach(course => {
        const courseElement = document.createElement("div");
        courseElement.classList.add("course");

        courseElement.innerHTML = `
            <img src="${course.image}" alt="${course.title}">
            <h3>${course.title}</h3>
            <p>${course.description}</p>
            <p>المدة: ${course.duration}</p>
            <p>يبدأ من: ${course.start_date}</p>
            <p>المدرب: ${course.doctor}</p>
            <p>التقييم: ${course.rating}</p>
            <button onclick="registerCourse('${course.title}')">تسجيل</button>
        `;

        coursesContainer.appendChild(courseElement);
    });
}

function registerCourse(courseName) {
    alert(`✅ تم التسجيل في الدورة: ${courseName}`);
}
