<?php  
// Database configuration  
$host = 'localhost'; // Change if your database is hosted elsewhere  
$dbname = 'your_database_name'; // Replace with your database name  
$username = 'your_username'; // Replace with your database username  
$password = 'your_password'; // Replace with your database password  

// Create a new PDO instance  
try {  
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);  
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
} catch (PDOException $e) {  
    die("Could not connect to the database: " . $e->getMessage());  
}  

// Check if the form is submitted  
if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    // Collect and validate form data  
    $patientName = trim(htmlspecialchars($_POST['patientName']));  
    $email = trim(htmlspecialchars($_POST['email']));  
    $phone = trim(htmlspecialchars($_POST['phone']));  
    $doctor = trim(htmlspecialchars($_POST['doctor']));  
    $date = trim(htmlspecialchars($_POST['date']));  
    $time = trim(htmlspecialchars($_POST['time']));  
    $message = trim(htmlspecialchars($_POST['message']));  

    // Basic server-side validation  
    if (empty($patientName) || empty($email) || empty($phone) || empty($doctor) || empty($date) || empty($time)) {  
        die('Please fill in all required fields.');  
    }  
    
    // Prepare and bind the SQL statement  
    $stmt = $pdo->prepare("INSERT INTO appointments (patientName, email, phone, doctor, appointmentDate, appointmentTime, message)   
                            VALUES (?, ?, ?, ?, ?, ?, ?)");  
    $stmt->execute([$patientName, $email, $phone, $doctor, $date, $time, $message]);  

    // Send email notification to the user  
    $to = $email;  
    $subject = "Appointment Confirmation";  
    $body = "Dear $patientName,\n\nThank you for booking your appointment at Aga Khan University Hospital, Nairobi.\n\nHere are your appointment details:\n- Doctor: $doctor\n- Date: $date\n- Time: $time\n\nAdditional Information: $message\n\nWe look forward to seeing you!\n\nBest regards,\nAga Khan University Hospital";  

    // Email headers  
    $headers = "From: no-reply@aku.edu";  

    // Send the email  
    if (mail($to, $subject, $body, $headers)) {  
        // Output a confirmation message  
        echo "<!DOCTYPE html>  
        <html lang='en'>  
        <head>  
            <meta charset='UTF-8'>  
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>  
            <title>Appointment Confirmation</title>  
            <link rel='stylesheet' href='styles.css'>  
        </head>  
        <body>  
            <header>  
                <h1>Appointment Confirmation</h1>  
            </header>  
            <section>  
                <h2>Thank you for booking your appointment!</h2>  
                <p><strong>Patient Name:</strong> $patientName</p>  
                <p><strong>Email:</strong> $email</p>  
                <p><strong>Phone:</strong> $phone</p>  
                <p><strong>Doctor:</strong> $doctor</p>  
                <p><strong>Appointment Date:</strong> $date</p>  
                <p><strong>Appointment Time:</strong> $time</p>  
                <p><strong>Additional Information:</strong> $message</p>  
                <p>A confirmation email has been sent to your email address.</p>  
                <p>We look forward to seeing you!</p>  
            </section>  
            <footer>  
                <p>&copy; 2025 Aga Khan University Hospital, Nairobi. All rights reserved.</p>  
            </footer>  
        </body>  
        </html>";  
    } else {  
        echo "Error sending confirmation email.";  
    }  
} else {  
    // Redirect to appointment form if the page is accessed directly  
    header("Location: appointment.html");  
    exit();  
}  
?>  