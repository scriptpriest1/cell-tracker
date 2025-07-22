<?php

// Function to sanitize inputs
function clean_input($data)
{
  $data = trim($data); // Remove extra spaces, tabs, or newlines
  $data = stripslashes($data); // Remove backslashes
  $data = htmlspecialchars($data); // Convert special characters to HTML entities
  return $data;
}

//Check if user is logged in
function check_login($conn)
{
  if (isset($_SESSION['id'])) {
    $id = clean_input($_SESSION['id']);
    $query = $conn->prepare('SELECT * FROM cells WHERE id = ? limit 1');
    $query->bind_param('i', $id);
    $query->execute();

    $result = $query->get_result();
    if ($result && mysqli_num_rows($result) > 0) {
      $user_data = mysqli_fetch_assoc($result);
      return $user_data;
    }
  }

  //Redirect to Login Page if user is not logged in
  header('Location: ./login.php');
  die();
}
?>
