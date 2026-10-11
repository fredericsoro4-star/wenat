import face_recognition
import cv2

# Charger l'image de l'utilisateur enrôlé
known_image = face_recognition.load_image_file("images/utilisateur.jpg")
known_encoding = face_recognition.face_encodings(known_image)[0]

# Capturer une image via webcam
video_capture = cv2.VideoCapture(0)
ret, frame = video_capture.read()

# Convertir l'image capturée
rgb_frame = frame[:, :, ::-1]
face_locations = face_recognition.face_locations(rgb_frame)
face_encodings = face_recognition.face_encodings(rgb_frame, face_locations)

for face_encoding in face_encodings:
    # Comparer avec l'utilisateur enrôlé
    results = face_recognition.compare_faces([known_encoding], face_encoding)
    if results[0]:
        print("Utilisateur reconnu ✅")
    else:
        print("Utilisateur non reconnu ❌")

video_capture.release()
cv2.destroyAllWindows()
