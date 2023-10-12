<?php 
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: *");
	header("Access-Control-Allow-Methods: *");
	
	include 'DbConnect.php';
	$objDb = new DbConnect;
	$conn = $objDb->connect();
	
	
	$method = $_SERVER['REQUEST_METHOD'];
	switch($method){
		case "GET":
			$sql = "SELECT * FROM students";
			$path = explode('/', $_SERVER['REQUEST_URI']);
			if(isset($path[2]) && is_numeric($path[2])){
				$sql .= " WHERE id =".$path[2];
				$stmt = $conn->prepare($sql);
				//$stmt->bindParam(':id',$path[2]);
				$stmt->execute();
				$users = $stmt->fetch(PDO::FETCH_ASSOC);
				
				
			}
			else{
				$stmt = $conn->prepare($sql);
				$stmt->execute();
				$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
			}
			
			echo json_encode($users);
		break;	
		case "POST" :
			$students = json_decode(file_get_contents("php://input"));
			
			$sql = "INSERT INTO students(name,email,contact,address) VALUES(:name,:email,:contact,:address)";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':name',$students->name);
			$stmt->bindParam(':email',$students->email);
			$stmt->bindParam(':contact',$students->contact);
			$stmt->bindParam(':address',$students->address);
			//$res = $stmt->execute();
			if($stmt->execute()){
				$res = ['status'=>1,
				'message' => 'Record added Succesfully'
				];
			}else{
				$res = ['status'=>0,
				'message' => 'Failed to add record'
				];
			}
			echo json_encode($res);	
		break;
		
		case "PUT" :
			$students = json_decode(file_get_contents("php://input"));
			
			$sql = "UPDATE students SET name=:name,email=:email,contact=:contact,address=:address WHERE id=:id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':id',$students->id);
			$stmt->bindParam(':name',$students->name);
			$stmt->bindParam(':email',$students->email);
			$stmt->bindParam(':contact',$students->contact);
			$stmt->bindParam(':address',$students->address);
			//$res = $stmt->execute();
			if($stmt->execute()){
				$res = ['status'=>1,
				'message' => 'Record updated Succesfully'
				];
			}else{
				$res = ['status'=>0,
				'message' => 'Failed to update record'
				];
			}
		break;
		
		case "DELETE":
			
			$path = explode('/', $_SERVER['REQUEST_URI']);
			$sql = "DELETE FROM students WHERE id=".$path[3]; 
			$stmt = $conn->prepare($sql); 
			if($stmt->execute()){
				$res = ['status'=>1,
				'message' => 'Record deleted Succesfully'
				];
			}else{
				$res = ['status'=>0,
				'message' => 'Failed to delete record'
				];
			} 
			echo json_encode($res);	
		break;		
	}
	 
?>