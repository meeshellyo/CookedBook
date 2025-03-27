    <?php


        require_once("database.php");

        require_once("included_functions.php");

        if(isset($_POST["first_name"]) && $_POST["first_name"] !== "" && isset($_POST["last_name"]) && $_POST["last_name"] !== ""){

            // Getting the first and last name entered in form
            $first_name = $_POST["first_name"];
            $last_name = $_POST["last_name"];
            
            try{

        
                $mysqli = Database::dbConnect();
                
                $mysqli -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

                $stmt = $mysqli->prepare("insert into sample_user (user_fname,user_lname) values (:first_name,:last_name)"); 

                // binding the first and last name entered in form to the query values
                $stmt->bindParam(':first_name', $first_name);
                $stmt->bindParam(':last_name', $last_name);

            
                $stmt->execute(); 

            }
            catch(PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
           
    
        }else{
            
        }
        
        redirect('sample_user.php'); // go back to form
   
?>