import unittest
import os
import sys
import json
import py_compile

# Tambahkan path python_ai ke sys.path
BASE_DIR = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
PYTHON_AI_DIR = os.path.join(BASE_DIR, "SkinCheck", "python_ai")
if PYTHON_AI_DIR not in sys.path:
    sys.path.insert(0, PYTHON_AI_DIR)

class TestSkinCheckPythonAI(unittest.TestCase):

    def test_01_cascade_model_exists(self):
        """Memastikan model Haar Cascade tersedia di direktori python_ai"""
        cascade_file = os.path.join(PYTHON_AI_DIR, "haarcascade_frontalface_default.xml")
        self.assertTrue(os.path.exists(cascade_file), "File haarcascade_frontalface_default.xml harus tersedia")
        self.assertGreater(os.path.getsize(cascade_file), 500000, "Ukuran file XML cascade harus valid (>500KB)")

    def test_02_script_syntax_compilation(self):
        """Memastikan skrip analyze.py dan sunscreen_analyze.py lolos uji kompilasi sintaksis"""
        analyze_path = os.path.join(PYTHON_AI_DIR, "analyze.py")
        sunscreen_path = os.path.join(PYTHON_AI_DIR, "sunscreen_analyze.py")
        
        self.assertTrue(os.path.exists(analyze_path), "File analyze.py harus ada")
        self.assertTrue(os.path.exists(sunscreen_path), "File sunscreen_analyze.py harus ada")

        # Compile syntax check (tanpa eksekusi runtime penuh)
        py_compile.compile(analyze_path, doraise=True)
        py_compile.compile(sunscreen_path, doraise=True)

    def test_03_analyze_skin_missing_image(self):
        """Memastikan analyze.py menangani file gambar yang tidak ditemukan dengan error JSON terstruktur"""
        import subprocess

        analyze_path = os.path.join(PYTHON_AI_DIR, "analyze.py")
        proc = subprocess.run(
            [sys.executable, analyze_path, "gambar_fiktif_tidak_ada.jpg"],
            capture_output=True,
            text=True
        )

        output_raw = proc.stdout.strip()
        self.assertTrue(len(output_raw) > 0, "Output JSON harus dihasilkan")
        
        data = json.loads(output_raw)
        self.assertIn("error", data, "Output harus memiliki kunci 'error'")
        self.assertEqual(data["face_detected"], False, "face_detected harus bernilai False jika gambar tidak ada")
        self.assertIn("Image file not found", data["error"], "Pesan error harus mengindikasikan file tidak ditemukan")

    def test_04_schema_fields_compliance(self):
        """Memastikan seluruh field standar PRD tersedia pada respon output Computer Vision"""
        import subprocess

        analyze_path = os.path.join(PYTHON_AI_DIR, "analyze.py")
        proc = subprocess.run(
            [sys.executable, analyze_path, "missing.jpg"],
            capture_output=True,
            text=True
        )
        data = json.loads(proc.stdout.strip())
        
        expected_fields = [
            "face_detected",
            "acne_score",
            "redness_score",
            "texture_score",
            "hyperpigmentation_score",
            "overall_score",
            "model_status",
            "error"
        ]
        for field in expected_fields:
            self.assertIn(field, data, f"Field '{field}' harus ada pada output JSON")

if __name__ == "__main__":
    unittest.main()
