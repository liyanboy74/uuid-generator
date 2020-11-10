<?php
header('Content-Type: application/json');

$servername     = 	"localhost"	;
$username 	= 	"USERNAME"	;
$password 	= 	"PASSWORD"	;
$dbname 	= 	"DB_NAME"	;

// connect to the mysql database
$link = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$link)die("Connection failed: " . mysqli_connect_error());
//mysqli_set_charset
mysqli_set_charset($link,'utf8');

function create_token (){
	$uni_code=md5(uniqid(rand(), true));
	//$rand_code = bin2hex(random_bytes(8));
	//$token=(string)$uni_code.$rand_code;
	$token=$uni_code;
    return $token;
}

function test_input_sql ($value){
  if ($value==null) return null;
  $value=preg_replace("([*+<>?()=]+)",'', $value);
  $value = trim($value);
  $value = stripslashes($value);
  $value = htmlspecialchars($value);
  $value=mysqli_real_escape_string($GLOBALS['link'],(string)$value);
  return $value;
}

function error($code){

    $ans='';
    switch ($code) {
        case '001': $ans="Command not found !";break;
        case '002': $ans="Command not match !";break;
        case '003': $ans="Need more informations!";break;
        case '004': $ans="Mysqli error >>>".mysqli_error($GLOBALS['link']);http_response_code(404);break;
        case '005': $ans="UUID not found !";break;

        default : $ans="unknow error !";break;
    }
    echo json_encode(array('Result'=>'Error','Error' => array('Code' => $code,'Text'=>$ans)));
    die();
}


$method = $_SERVER['REQUEST_METHOD'];
parse_str(file_get_contents("php://input"),$input);
if (!$input) $input = array();
if($method=="GET") $input =$_GET;

$data = array('methode' => $method ,'input'=>$input);

$command='';
if(isset($data['input']['command']))$command=test_input_sql($data['input']['command']);

if($command=="guuid")
{
	if(isset($data['input']['name']))
  {
    $data['input']['name']=test_input_sql($data['input']['name']);
  	do{
		$token=create_token();
		$sql = "SELECT ID FROM uuid WHERE uuid='".$token."'";
		$result = mysqli_query($link,$sql);
		if (!$result) error("004");
		}while(mysqli_num_rows($result)>=1);

		$sql = "INSERT INTO uuid (name, uuid) VALUES ('".$data['input']['name']."', '".$token."')";
		$result = mysqli_query($link,$sql);
		if (!$result) error("004");

		echo json_encode(array('Result'=>'Success','Name'=>$data['input']['name'],'UUID'=>$token));
	}
	else error("003");
}
else if($command=="search")
{
	if(isset($data['input']['uuid']))
  	{
      $data['input']['uuid']=test_input_sql($data['input']['uuid']);
  		$sql = "SELECT * FROM uuid WHERE uuid='".$data['input']['uuid']."'";
  		$result = mysqli_query($link,$sql);
  		if (!$result) error("004");
  		if(mysqli_num_rows($result)>=1)
  		{
  			$rows=mysqli_fetch_assoc($result);
  			echo json_encode(array('Result'=>'Success','Name'=>$rows['name'],'UUID'=>$rows['uuid']));
  		}
  		else error("005");
  	}
}
else error("001");

?>