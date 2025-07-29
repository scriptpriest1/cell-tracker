<?php
// Function to sanitize inputs
function clean_input($data)
{
  $data = trim($data); // Remove extra spaces, tabs, or newlines
  $data = stripslashes($data); // Remove backslashes
  $data = htmlspecialchars($data); // Convert special characters to HTML entities
  return $data;
}

// Check if user is logged in
function check_login($conn)
{
  if (isset($_SESSION['user_id'])) {
    $id = clean_input($_SESSION['user_id']);
    
    $stmt = $conn->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);

    $user_data = $stmt->fetch(); // Already returns false if nothing is found

    if ($user_data) {
      return $user_data;
    }
  }

  // Redirect to Login Page if user is not logged in
  header('Location: ' . BASE_URL . 'login.php');
  exit;
}
?>
