const firebaseConfig = {
    apiKey: "AIzaSyAA6y8TFRWreu6Pd1AZyqvjAEh9BX4g6_E",
    authDomain: "teerra-space.firebaseapp.com",
    projectId: "teerra-space",
    storageBucket: "teerra-space.appspot.com",
    messagingSenderId: "416330751201",
    appId: "1:416330751201:web:233fb1abc5771548fe2ba0"
};

firebase.initializeApp(firebaseConfig);
const db = firebase.firestore();

const courses = [
    {
        id: "1",
        title: "تطوير تطبيقات الجوال",
        instructor: "محمد احمد",
        category: "تطوير تطبيقات",
        price: 349,
        rating: 4.7,
        reviews: 289,
        description: "تعلم كيفية تطوير تطبيقات الجوال باستخدام React Native و Flutter",
        lessons: [
            {
                title: "مقدمة في تطوير تطبيقات الجوال",
                description: "مقدمة شاملة عن تطوير التطبيقات وأساسياتها",
                videoUrl: "https://www.youtube.com/embed/VIDEO_ID_1",
                duration: "45 دقيقة"
            },
            {
                title: "React Native الأساسي",
                description: "تعلم أساسيات React Native",
                videoUrl: "https://www.youtube.com/embed/VIDEO_ID_2",
                duration: "60 دقيقة"
            }
        ]
    },
    {
        id: "2",
        title: "أساسيات إدارة المشاريع",
        instructor: "ليلى عمر",
        category: "إدارة",
        price: 0, // مجاني
        rating: 4.3,
        reviews: 215,
        description: "تعلم منهجيات إدارة المشاريع المختلفة وكيفية تطبيقها بفعالية في بيئة العمل",
        lessons: [
            {
                title: "مقدمة في إدارة المشاريع",
                description: "تعرف على أساسيات إدارة المشاريع",
                videoUrl: "https://www.youtube.com/embed/VIDEO_ID_3",
                duration: "50 دقيقة"
            },
            {
                title: "المنهجيات المختلفة",
                description: "دراسة المنهجيات المختلفة في إدارة المشاريع",
                videoUrl: "https://www.youtube.com/embed/VIDEO_ID_4",
                duration: "55 دقيقة"
            }
        ]
    }
    // يمكنك إضافة المزيد من الدورات هنا
];

async function addCoursesToFirestore() {
    try {
        for (const course of courses) {
            await db.collection('courses')
                .doc(course.id)
                .set(course);
            console.log(`تم إضافة الدورة: ${course.title}`);
        }
        console.log('تم إضافة جميع الدورات بنجاح');
    } catch (error) {
        console.error('خطأ في إضافة الدورات:', error);
    }
}

// تنفيذ الدالة عند تحميل الصفحة
addCoursesToFirestore();