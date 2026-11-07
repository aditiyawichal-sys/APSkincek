<?php

class PythonAIService {
    public static function analyzeImage($imagePath) {
        // Build the absolute path to the python script
        $pythonScript = realpath(__DIR__ . '/../../python_ai/analyze.py');
        $absoluteImagePath = realpath($imagePath);

        if (!$pythonScript || !$absoluteImagePath) {
            return [
                'error' => 'Files not found.',
                'face_detected' => false,
                'model_status' => 'Error'
            ];
        }

        // Execute python script
        // Note: Assumes 'python' is in the system PATH.
        $command = escapeshellcmd("python \"$pythonScript\" \"$absoluteImagePath\"");
        
        // Use shell_exec to get the JSON output
        $output = shell_exec($command);
        
        if ($output === null) {
             return [
                'error' => 'Execution failed or no output returned.',
                'face_detected' => false,
                'model_status' => 'Execution Error'
            ];
        }

        // Parse JSON output
        $result = json_decode(trim($output), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => 'Invalid JSON returned from Python.',
                'raw_output' => $output,
                'face_detected' => false,
                'model_status' => 'JSON Error'
            ];
        }

        return $result;
    }

    public static function analyzeSunscreen($imagePath) {
        $pythonScript = realpath(__DIR__ . '/../../python_ai/sunscreen_analyze.py');
        $absoluteImagePath = realpath($imagePath);

        if (!$pythonScript || !$absoluteImagePath) {
            return [
                'error' => 'Files not found.',
                'face_detected' => false,
                'coverage_status' => 'Error'
            ];
        }

        $command = escapeshellcmd("python \"$pythonScript\" \"$absoluteImagePath\"");
        $output = shell_exec($command);
        
        if ($output === null) {
             return [
                'error' => 'Execution failed or no output returned.',
                'face_detected' => false,
                'coverage_status' => 'Execution Error'
            ];
        }

        $result = json_decode(trim($output), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => 'Invalid JSON returned from Python.',
                'raw_output' => $output,
                'face_detected' => false,
                'coverage_status' => 'JSON Error'
            ];
        }

        return $result;
    }
}
