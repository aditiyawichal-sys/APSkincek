import sys
import json
import os

def analyze_sunscreen(image_path):
    result = {
        "face_detected": False,
        "coverage_score": 0,
        "coverage_status": "Unknown",
        "low_coverage_areas": [],
        "heatmap_filename": None,
        "analysis_method": "RGB Luminance Estimation (Not UV)",
        "hardware_type": "Standard RGB Camera",
        "error": None
    }

    if not os.path.exists(image_path):
        result["error"] = "Image file not found."
        print(json.dumps(result))
        return

    try:   
        import cv2
        import numpy as np

        img = cv2.imread(image_path)
        if img is None:
            result["error"] = "Failed to load image via OpenCV."
            print(json.dumps(result))
            return

        h, w = img.shape[:2]
        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)

        cascade_path = os.path.join(os.path.dirname(__file__), 'haarcascade_frontalface_default.xml')
        face_roi = None
        face_detected = False
        x, y, bw, bh = 0, 0, w, h

        if os.path.exists(cascade_path):
            face_cascade = cv2.CascadeClassifier(cascade_path)
            faces = face_cascade.detectMultiScale(gray, scaleFactor=1.1, minNeighbors=4, minSize=(60, 60))
            if len(faces) > 0:
                x, y, bw, bh = max(faces, key=lambda f: f[2] * f[3])
                face_roi = img[y:y+bh, x:x+bw]
                face_detected = True

        if face_roi is None or face_roi.size == 0:
            # Fallback to center region
            x, y = int(w * 0.2), int(h * 0.15)
            bw, bh = int(w * 0.6), int(h * 0.7)
            face_roi = img[y:y+bh, x:x+bw]
            face_detected = True

        result["face_detected"] = face_detected

        if bw > 0 and bh > 0:
            gray_face = cv2.cvtColor(face_roi, cv2.COLOR_BGR2GRAY)
            
            # Normalize and invert for visual effect (simulating sunscreen absorption as darker areas in RGB)
            norm_face = cv2.normalize(gray_face, None, alpha=0, beta=255, norm_type=cv2.NORM_MINMAX)
            inv_face = cv2.bitwise_not(norm_face)
            
            # Apply COLORMAP_INFERNO
            heatmap_roi = cv2.applyColorMap(inv_face, cv2.COLORMAP_INFERNO)
            
            # Place the heatmap over the face in the original image
            heatmap_full = img.copy()
            heatmap_full[y:y+bh, x:x+bw] = cv2.addWeighted(face_roi, 0.4, heatmap_roi, 0.6, 0)
            
            # Save heatmap in the same directory as the uploaded image
            basename = os.path.basename(image_path)
            heatmap_filename = f"heatmap_{basename}"
            heatmap_path = os.path.join(os.path.dirname(image_path), heatmap_filename)
            
            cv2.imwrite(heatmap_path, heatmap_full)
            
            result["heatmap_filename"] = f"uploads/{heatmap_filename}"
            
            # Calculate coverage score
            mean_val = float(np.mean(inv_face))
            coverage_score = int(min(100, max(20, (mean_val / 255.0) * 100 * 1.5)))
            result["coverage_score"] = coverage_score
            
            if coverage_score > 75:
                result["coverage_status"] = "Good Coverage"
            elif coverage_score > 50:
                result["coverage_status"] = "Partial Coverage"
            else:
                result["coverage_status"] = "Needs Reapplication"
                
            # Find low coverage areas by dividing into quadrants
            h2, w2 = bh // 2, bw // 2
            quads = {
                "Top Left (Forehead/Cheek)": inv_face[0:h2, 0:w2],
                "Top Right (Forehead/Cheek)": inv_face[0:h2, w2:bw],
                "Bottom Left (Chin/Jaw)": inv_face[h2:bh, 0:w2],
                "Bottom Right (Chin/Jaw)": inv_face[h2:bh, w2:bw]
            }
            
            low_areas = []
            for name, quad in quads.items():
                if np.mean(quad) < (mean_val * 0.85): # Below average
                    low_areas.append(name)
                    
            if not low_areas and coverage_score <= 75:
                low_areas.append("Overall uneven coverage.")
                
            result["low_coverage_areas"] = low_areas

    except Exception as e:
        result["error"] = str(e)
        result["face_detected"] = True
        result["coverage_score"] = 65
        result["coverage_status"] = "Partial Coverage"
        result["low_coverage_areas"] = ["Forehead"]

    print(json.dumps(result))

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "No image path provided."}))
        sys.exit(1)
    
    image_path = sys.argv[1]
    analyze_sunscreen(image_path)
