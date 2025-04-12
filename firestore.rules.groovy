rules_version = '2';
service cloud.firestore {
  match /databases/{database}/documents {
    // قواعد مجموعة المستخدمين
    match /users/{userId} {
      allow read: if request.auth != null && request.auth.uid == userId;
      allow write: if request.auth != null && request.auth.uid == userId;
    }

    // قواعد مجموعة الدورات
    match /Courses/{courseId} {
      allow read: if true;
      
      // قواعد مجموعة الدروس الفرعية
      match /lessons/{lessonId} {
        allow read: if isEnrolledInCourse(courseId);
      }
    }

    // قواعد مجموعة التسجيل في الدورات
    match /registrations/{registrationId} {
      allow read: if request.auth != null && (
        resource.data.userId == request.auth.uid
      );
      allow create: if request.auth != null && (
        request.resource.data.userId == request.auth.uid
      );
    }

    // قواعد مجموعة تقدم الطلاب
    match /progress/{progressId} {
      allow read: if request.auth != null && (
        progressId.matches(request.auth.uid + "_.*")
      );
      allow create, update: if request.auth != null && (
        progressId.matches(request.auth.uid + "_.*") &&
        request.resource.data.keys().hasAll(['completedLessons', 'totalLessons', 'lastCompletedLesson', 'lastAccess'])
      );
    }

    // دالة مساعدة للتحقق من تسجيل الطالب في الدورة
    function isEnrolledInCourse(courseId) {
      return request.auth != null && 
        exists(/databases/$(database)/documents/registrations/{registrationId}) &&
        get(/databases/$(database)/documents/registrations/{registrationId}).data.courseId == courseId &&
        get(/databases/$(database)/documents/registrations/{registrationId}).data.userId == request.auth.uid;
    }
  }
}