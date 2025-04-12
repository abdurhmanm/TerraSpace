const imagesContainer = document.querySelector('.carousel-container .images');
const prevBtn = document.querySelector('.prev-btn');
const nextBtn = document.querySelector('.next-btn');

let currentIndex = 0;

function updateCarousel() {
    const imageWidth = 300 + 20; // عرض الصورة + الهامش (10px لكل جانب)
    imagesContainer.style.transform = `translateX(${-currentIndex * imageWidth}px)`;
}

prevBtn.addEventListener('click', () => {
    if (currentIndex > 0) {
        currentIndex--;
        updateCarousel();
    }
});

nextBtn.addEventListener('click', () => {
    if (currentIndex < imagesContainer.children.length - 1) {
        currentIndex++;
        updateCarousel();
    }
});

function fetchCourses() {
    let categoryId = document.getElementById("category").value;
    let container = document.getElementById("courses-container");

    fetch(`fetch_courses.php?category_id=${categoryId}`)
        .then(response => response.json())
        .then(data => {
            container.innerHTML = "";
            data.forEach(course => {
                let courseDiv = document.createElement("div");
                courseDiv.classList.add("course");
                courseDiv.innerHTML = `
                    <h3>${course.title}</h3>
                    <p>الدكتور: ${course.instructor}</p>
                    <p>التقييم: ⭐ ${course.rating}</p>
                    <p>التاريخ: ${course.start_date}</p>
                `;
                container.appendChild(courseDiv);
            });
        })
        .catch(error => console.error("Error fetching courses:", error));
}

// تحميل الدورات عند فتح الصفحة
document.addEventListener("DOMContentLoaded", fetchCourses);

// التحقق من حالة الاتصال
function checkFirebaseConnection() {
    const connectedRef = rtdb.ref(".info/connected");
    connectedRef.on("value", (snap) => {
        if (snap.val() === true) {
            console.log("متصل بـ Firebase");
        } else {
            console.log("غير متصل بـ Firebase");
        }
    });
}

// استدعاء دالة التحقق
checkFirebaseConnection();
