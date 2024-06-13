<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();


// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'hospital_db';

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $age = $_POST['age'] ?? '';
    $sex = $_POST['sex'] ?? '';
    $phone = $_POST['phone'] ?? '';

    // Validate form data
    if (empty($id) || empty($name) || empty($age) || empty($sex) || empty($phone)) {
        echo 'Error: Missing required form data.';
        exit();
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_file_path = $_FILES['image']['tmp_name'];


        // Send the image data to the Flask app for prediction
        $flask_url = 'http://localhost:5000/process_image'; // Flask app URL
        $ch = curl_init($flask_url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        
        // Send the image file using CURLFile
        $post_fields = ['image' => new CURLFile($image_file_path)];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

        // Execute the request and receive the response
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
            curl_close($ch);
            exit();
        }

        // Close cURL
        curl_close($ch);

        // Check the HTTP status code
        if ($http_code == 200) {
            // Parse the Flask app response
            $prediction = json_decode($response, true);

            // Check if the response contains a valid prediction
            if (isset($prediction['prediction'])) {
                $result = $prediction['prediction'];

                // Define the threshold value for tumor detection
                $threshold = 0.5;

                // Determine if a tumor was found based on the prediction value
                $result_message = $result >= $threshold ? 'Tumor found' : 'Tumor not found';


                // Convert the image data to base64
                $image_data = base64_encode(file_get_contents($image_file_path));
                
                // Store the data in the database
                // Insert patient data into the 'patient' table
                $stmt_patient = $conn->prepare('INSERT INTO patient (id, pname, age, sex, phone, result) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt_patient->bind_param('ssisss', $id, $name, $age, $sex, $phone, $result_message);

                if ($stmt_patient->execute()) {
                    echo 'Patient data inserted successfully.';

                    // Insert image data into the 'image' table
                    $stmt_image = $conn->prepare('INSERT INTO image (id, image_data, patient_id) VALUES (?, ?, ?)');
                    $stmt_image->bind_param('sss', $id, $image_data, $id);

                    if ($stmt_image->execute()) {
                        echo 'Image data inserted successfully.';

                        // Store the prediction, image, and name in session variables
                       // session_start();
                        $_SESSION['pname'] = $name;
                        $_SESSION['prediction'] = $result_message;
                        $_SESSION['image'] = $image_data;
                        
                        // Redirect to display_results.php after setting session variables
                        header("Location: result_1.html");
                        exit();

                    } else {
                        echo 'Error inserting image data: ' . $stmt_image->error;
                    }
                } else {
                    echo 'Error inserting patient data: ' . $stmt_patient->error;
                }

                // Close statements
                $stmt_patient->close();
                $stmt_image->close();
            } else {
                echo 'Invalid or missing prediction result.';
            }
        } else {
            echo "Error: Flask app responded with HTTP status code $http_code.";
        }
    } else {
        echo 'Error: Image upload failed.';
    }
}

// Close the database connection
$conn->close();
?>
