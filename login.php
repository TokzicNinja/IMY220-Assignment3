<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	// Your database details might be different
	$mysqli = mysqli_connect("localhost", "root", "", "dbUser");
	$database = "dbUser";
	
	$email = isset($_POST["email"]) ? $_POST["email"] : false;
	$pass = isset($_POST["pass"]) ? $_POST["pass"] : false;	
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 3</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Mangaliso Mtembu">
</head>
<body>
	<div class="container">
		<?php
		
			$dir = "gallery/";
			
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				
				if($row = mysqli_fetch_array($res)){
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";


					echo 	"<form enctype='multipart/form-data' method='POST' >
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload[]' id='picToUpload' multiple='multiple'/><br/><br/>
									<input type='submit' class='btn btn-light' value='Upload Image' name='submit' />
								</div>
								<input type='hidden' value=".$_POST["email"]." name='email'/>
								<input type='hidden' value=".$_POST["pass"]." name='pass'/>
						  	</form>";
							
							//Checks whether the submit button has been clicked
							if(isset($_POST["submit"])){
								
								$files = $_FILES["picToUpload"];
								
								//For folder whether it exists or not
								if (!file_exists($dir)) 
									mkdir($dir, 0777, true);//create folder
							
								$user_id = $row["user_id"];//Fetch and store user_id
								$count = count($files["name"]);
								
								for($i = 0; $i<$count; $i++){
									
									//Conditions for uploading an image
									if(($files["type"][$i] == "image/jpeg")
										&& $files["size"][$i]<1000000){
											//Correct file type
											//Image can successfully upload
												
												$fileName = $files['name'][$i];
												
												$sqlQuery = "INSERT INTO tbgallery (user_id, filename) VALUES( '$user_id', '$fileName')";
												
												if($mysqli->query($sqlQuery))
													move_uploaded_file($files["tmp_name"][$i], $dir.$files["name"][$i]);
												else
													echo "Error". mysqli_error($mysqli);
									}
									else{ //Invalid image
									
											echo '<div class="alert alert-danger mt-3" role="alert">'.
													'File: '.$files["name"][$i].' either has exceeds 1MB '.
													'or file '.$files["type"][$i].' is incorrect'.
											   '</div>';
									}
								}
							}

							$user_id = $row['user_id'];
							
							$query = "SELECT filename FROM tbgallery WHERE user_id = '$user_id' ";
							$res = $mysqli->query($query);
							
							if($res->num_rows>0){
								echo "<h1>Image Gallery</h1>
								
									<div class='container'>
										<div class='row imageGallery'>";
										
												while($results = mysqli_fetch_array($res)){
													$name = $results["filename"];
													echo "<div class='col-3' style='background-image : url($dir/$name)'></div>";
												}
									echo "</div>
								  </div>";
							}
							else
							{
								echo '<div class="alert alert-danger mt-3" role="alert">
										No Images Found
									</div>';
							}
				}
				else{
						echo '<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			} 
			else{
					echo '<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>
