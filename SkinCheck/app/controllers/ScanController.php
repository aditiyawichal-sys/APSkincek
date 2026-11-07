<?php
require_once '../app/helpers/AuthHelper.php';
require_once '../app/services/PythonAIService.php';

class ScanController extends Controller {
    public function faceScan() {
        AuthHelper::requireLogin();
        $this->view('scan/face_scan', ['title' => 'Face Scan']);
    }

    public function submitFaceScan() {
        AuthHelper::requireLogin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $imageBase64 = $data['image'] ?? null;

            if ($imageBase64) {
                // Decode base64 image
                $imageParts = explode(";base64,", $imageBase64);
                $imageTypeAux = explode("image/", $imageParts[0]);
                $imageType = $imageTypeAux[1];
                $imageBase64 = base64_decode($imageParts[1]);
                
                // Define file path
                $fileName = uniqid() . '.jpg';
                $uploadDir = '../storage/uploads/';
                
                // Ensure directory exists
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $filePath = $uploadDir . $fileName;

                // Save image
                if (file_put_contents($filePath, $imageBase64)) {
                    // Save to database
                    $scanModel = $this->model('Scan');
                    $scanId = $scanModel->createScan($_SESSION['user_id'], 'face', 'uploads/' . $fileName);

                    if ($scanId) {
                        // Call Python AI Service
                        $analysisResult = PythonAIService::analyzeImage($filePath);
                        
                        // Parse results or fallback
                        $faceDetected = $analysisResult['face_detected'] ?? false;
                        
                        // Parse heuristic metrics
                        $acneScore = $analysisResult['acne_score'] ?? 0;
                        $rednessScore = $analysisResult['redness_score'] ?? 0;
                        $textureScore = $analysisResult['texture_score'] ?? 0;
                        $hyperpigmentationScore = $analysisResult['hyperpigmentation_score'] ?? 0;
                        $overallScore = $analysisResult['overall_score'] ?? 0;
                        
                        $detailsJson = json_encode([
                            'face_detected' => $faceDetected,
                            'model_status' => $analysisResult['model_status'] ?? 'Heuristic Estimation',
                            'error_message' => $analysisResult['error'] ?? null
                        ]);
                        
                        // Save analysis result
                        $analysisModel = $this->model('AnalysisResult');
                        $analysisModel->saveAnalysis($scanId, $acneScore, $rednessScore, $textureScore, $hyperpigmentationScore, $overallScore, $detailsJson);

                        // Log action
                        $logModel = $this->model('ActivityLog');
                        $logModel->logAction($_SESSION['user_id'], 'Face Scan', "Scan ID: $scanId, Score: $overallScore");

                        if ($faceDetected) {
                            echo json_encode([
                                'success' => true,
                                'message' => 'Scan submitted and analyzed successfully.',
                                'redirect' => '/APSkincek/SkinCheck/public/skin-analysis'
                            ]);
                        } else {
                            echo json_encode([
                                'success' => true,
                                'message' => 'Scan saved, but no face was detected by the AI model.',
                                'redirect' => '/APSkincek/SkinCheck/public/skin-analysis'
                            ]);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to save scan record to database.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to save image file.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No image data provided.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }

    public function skinAnalysis() {
        AuthHelper::requireLogin();
        $analysisModel = $this->model('AnalysisResult');
        $latestAnalysis = $analysisModel->getLatestAnalysis($_SESSION['user_id']);
        
        $productModel = $this->model('Product');
        $allProducts = $productModel->getAll();
        
        // Filter only active products
        $recommendedProducts = [];
        if ($latestAnalysis) {
            foreach ($allProducts as $p) {
                if ($p['status'] === 'active') {
                    // Extremely simple recommendation algorithm based on score thresholds
                    if ($latestAnalysis['acne_score'] > 40 && stripos($p['category'], 'acne') !== false) {
                        $recommendedProducts[] = $p;
                    } elseif ($latestAnalysis['redness_score'] > 40 && stripos($p['category'], 'sensitive') !== false) {
                        $recommendedProducts[] = $p;
                    } elseif ($latestAnalysis['hyperpigmentation_score'] > 40 && stripos($p['category'], 'brightening') !== false) {
                        $recommendedProducts[] = $p;
                    } elseif ($latestAnalysis['texture_score'] > 40 && stripos($p['category'], 'exfoliant') !== false) {
                        $recommendedProducts[] = $p;
                    }
                }
            }
            
            // If no specific matches, just recommend some active products
            if (empty($recommendedProducts)) {
                $recommendedProducts = array_filter($allProducts, fn($p) => $p['status'] === 'active');
                $recommendedProducts = array_slice($recommendedProducts, 0, 3);
            }
            
            // Deduplicate
            $recommendedProducts = array_unique($recommendedProducts, SORT_REGULAR);
        }
        
        $this->view('scan/skin_analysis', [
            'title' => 'Skin Analysis',
            'analysis' => $latestAnalysis,
            'recommendations' => $recommendedProducts
        ]);
    }

    public function sunscreenCheck() {
        AuthHelper::requireLogin();
        $this->view('scan/sunscreen_check', ['title' => 'Sunscreen Check']);
    }

    public function submitSunscreenCheck() {
        AuthHelper::requireLogin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $imageBase64 = $data['image'] ?? null;

            if ($imageBase64) {
                // Decode base64 image
                $imageParts = explode(";base64,", $imageBase64);
                $imageTypeAux = explode("image/", $imageParts[0]);
                $imageType = $imageTypeAux[1];
                $imageBase64 = base64_decode($imageParts[1]);
                
                // Define file path
                $fileName = uniqid() . '.jpg';
                $uploadDir = '../storage/uploads/';
                
                // Ensure directory exists
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $filePath = $uploadDir . $fileName;

                // Save image
                if (file_put_contents($filePath, $imageBase64)) {
                    // Save to database
                    $scanModel = $this->model('Scan');
                    $scanId = $scanModel->createScan($_SESSION['user_id'], 'sunscreen', 'uploads/' . $fileName);

                    if ($scanId) {
                        // Call Python AI Service
                        $analysisResult = PythonAIService::analyzeSunscreen($filePath);
                        
                        $faceDetected = $analysisResult['face_detected'] ?? false;
                        $coverageScore = $analysisResult['coverage_score'] ?? 0;
                        
                        $missedAreasJson = json_encode([
                            'face_detected' => $faceDetected,
                            'coverage_status' => $analysisResult['coverage_status'] ?? 'Unknown',
                            'low_coverage_areas' => $analysisResult['low_coverage_areas'] ?? [],
                            'heatmap_filename' => $analysisResult['heatmap_filename'] ?? null,
                            'analysis_method' => $analysisResult['analysis_method'] ?? 'RGB Luminance Estimation (Not UV)',
                            'hardware_type' => $analysisResult['hardware_type'] ?? 'Standard RGB Camera',
                            'error_message' => $analysisResult['error'] ?? null
                        ]);
                        
                        // Save analysis result
                        $sunscreenModel = $this->model('SunscreenResult');
                        $sunscreenModel->saveResult($scanId, $coverageScore, $missedAreasJson);

                        // Log action
                        $logModel = $this->model('ActivityLog');
                        $logModel->logAction($_SESSION['user_id'], 'Sunscreen Check', "Scan ID: $scanId, Coverage: $coverageScore");

                        if ($faceDetected) {
                            echo json_encode([
                                'success' => true,
                                'message' => 'Sunscreen check completed successfully.',
                                'redirect' => '/APSkincek/SkinCheck/public/sunscreen-analysis'
                            ]);
                        } else {
                            echo json_encode([
                                'success' => true,
                                'message' => 'Scan saved, but no face was detected by the algorithm.',
                                'redirect' => '/APSkincek/SkinCheck/public/sunscreen-analysis'
                            ]);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to save scan record to database.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to save image file.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No image data provided.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }

    public function sunscreenAnalysis() {
        AuthHelper::requireLogin();
        $sunscreenModel = $this->model('SunscreenResult');
        $latestAnalysis = $sunscreenModel->getLatestResult($_SESSION['user_id']);
        
        $this->view('scan/sunscreen_analysis', [
            'title' => 'Sunscreen Analysis',
            'analysis' => $latestAnalysis
        ]);
    }

    public function history() {
        AuthHelper::requireLogin();
        $userId = $_SESSION['user_id'];
        
        $scanModel = $this->model('Scan');
        $history = $scanModel->getUserHistory($userId);
        
        $analysisModel = $this->model('AnalysisResult');
        $sunscreenModel = $this->model('SunscreenResult');
        
        $analysisHistory = $analysisModel->getAnalysisHistory($userId);
        $sunscreenHistory = $sunscreenModel->getSunscreenHistory($userId);
        
        // Calculate recent trends (change between last and second to last)
        $analysisTrend = null;
        if (count($analysisHistory) >= 2) {
            $latest = $analysisHistory[count($analysisHistory)-1]['overall_score'];
            $previous = $analysisHistory[count($analysisHistory)-2]['overall_score'];
            $analysisTrend = $latest - $previous;
        }

        $sunscreenTrend = null;
        if (count($sunscreenHistory) >= 2) {
            $latest = $sunscreenHistory[count($sunscreenHistory)-1]['coverage_score'];
            $previous = $sunscreenHistory[count($sunscreenHistory)-2]['coverage_score'];
            $sunscreenTrend = $latest - $previous;
        }

        $this->view('scan/history', [
            'title' => 'Scan History & Progress',
            'history' => $history,
            'analysisChartData' => $analysisHistory,
            'sunscreenChartData' => $sunscreenHistory,
            'analysisTrend' => $analysisTrend,
            'sunscreenTrend' => $sunscreenTrend
        ]);
    }
}
