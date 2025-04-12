<script>
    // تحديث تفاصيل الدورة مع إضافة معلومات الطالب
    async function loadCourseDetails() {
        const urlParams = new URLSearchParams(window.location.search);
        const courseId = urlParams.get('id');
        const user = auth.currentUser;
        
        try {
            // التحقق من تسجيل الطالب في الدورة
            const registrationDoc = await db.collection('registrations')
                .where('userId', '==', user.uid)
                .where('courseId', '==', courseId)
                .get();

            if (registrationDoc.empty) {
                alert('عذراً، يجب التسجيل في الدورة أولاً');
                window.location.href = 'courses.html';
                return;
            }

            const courseDoc = await db.collection('Courses').doc(courseId).get();
            if (courseDoc.exists) {
                const courseData = courseDoc.data();
                
                // تحديث معلومات الدورة
                document.querySelector('#courseInfo h1').textContent = courseData.title;
                document.getElementById('instructorName').textContent = courseData.coach;
                document.getElementById('courseDescription').textContent = courseData.description;
                
                // تحديث الفيديو
                document.getElementById('courseVideo').src = courseData.course_url;
                
                // تحميل دروس الدورة
                await loadLessons(courseId);
                
                // تحميل تقدم الطالب
                await loadStudentProgress(courseId);
                
                // تحميل الملاحظات المحفوظة
                loadNotes();
            }
        } catch (error) {
            console.error("Error loading course:", error);
            alert("حدث خطأ في تحميل بيانات الدورة");
        }
    }

    // تحميل دروس الدورة
    async function loadLessons(courseId) {
        const lessonsContainer = document.getElementById('lessonsList');
        lessonsContainer.innerHTML = '';

        try {
            const lessonsSnapshot = await db.collection('Courses')
                .doc(courseId)
                .collection('lessons')
                .orderBy('order')
                .get();

            lessonsSnapshot.forEach((doc) => {
                const lesson = doc.data();
                const lessonElement = document.createElement('div');
                lessonElement.className = 'p-3 bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-600 transition-colors';
                lessonElement.innerHTML = `
                    <div class="flex items-center justify-between">
                        <span>${lesson.title}</span>
                        <span class="text-sm text-gray-400">${lesson.duration} دقيقة</span>
                    </div>
                `;
                lessonElement.onclick = () => loadLesson(doc.id, lesson);
                lessonsContainer.appendChild(lessonElement);
            });
        } catch (error) {
            console.error("Error loading lessons:", error);
        }
    }

    // تحميل تقدم الطالب
    async function loadStudentProgress(courseId) {
        const user = auth.currentUser;
        try {
            const progressDoc = await db.collection('progress')
                .doc(`${user.uid}_${courseId}`)
                .get();

            if (progressDoc.exists) {
                const progress = progressDoc.data();
                const percentage = Math.round((progress.completedLessons / progress.totalLessons) * 100);
                
                document.getElementById('progressBar').style.width = `${percentage}%`;
                document.getElementById('progressText').textContent = `${percentage}%`;
            }
        } catch (error) {
            console.error("Error loading progress:", error);
        }
    }

    // تحديث تقدم الطالب
    async function updateProgress(courseId, lessonId) {
        const user = auth.currentUser;
        const progressRef = db.collection('progress').doc(`${user.uid}_${courseId}`);
        
        try {
            await db.runTransaction(async (transaction) => {
                const progressDoc = await transaction.get(progressRef);
                
                if (!progressDoc.exists) {
                    // إنشاء وثيقة جديدة للتقدم
                    transaction.set(progressRef, {
                        completedLessons: 1,
                        totalLessons: await getLessonsCount(courseId),
                        lastCompletedLesson: lessonId,
                        lastAccess: firebase.firestore.FieldValue.serverTimestamp()
                    });
                } else {
                    // تحديث التقدم الحالي
                    const data = progressDoc.data();
                    if (!data.completedLessons.includes(lessonId)) {
                        transaction.update(progressRef, {
                            completedLessons: firebase.firestore.FieldValue.arrayUnion(lessonId),
                            lastCompletedLesson: lessonId,
                            lastAccess: firebase.firestore.FieldValue.serverTimestamp()
                        });
                    }
                }
            });
            
            // تحديث شريط التقدم
            await loadStudentProgress(courseId);
        } catch (error) {
            console.error("Error updating progress:", error);
        }
    }

    // الحصول على عدد الدروس
    async function getLessonsCount(courseId) {
        const lessonsSnapshot = await db.collection('Courses')
            .doc(courseId)
            .collection('lessons')
            .get();
        return lessonsSnapshot.size;
    }
</script>