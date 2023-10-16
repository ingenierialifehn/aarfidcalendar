<?php
require_once('../config.php');
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
					use PHPMailer\PHPMailer\SMTP;
					use PHPMailer\PHPMailer\Exception;
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			if(isset($sql))
			$resp['sql'] = $sql;
			return json_encode($resp);
			exit;
		}
	}
	function save_assembly(){
		extract($_POST);
		$data = "";
		$_POST['description'] = addslashes(htmlentities($_POST['description']));
		foreach($_POST as $k=> $v){
			if($k != 'id'){
				if(!empty($data)) $data.=", ";
				$data.=" {$k} = '{$v}'";
			}
		}
		$check = $this->conn->query("SELECT * FROM `assembly_hall` where `room_name` = '{$room_name}' ".(!empty($id) ? "and id != {$id}" : ''))->num_rows;
		$this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Esta cancha ya existe.";
		}else{
			if(empty($id)){
				$sql = "INSERT INTO `assembly_hall` set $data";
				$save = $this->conn->query($sql);
			}else{
				$sql = "UPDATE `assembly_hall` set $data where id = {$id}";
				$save = $this->conn->query($sql);
			}
			$this->capture_err();

			if($save){
				$resp['status'] = "success";
				$this->settings->set_flashdata('success'," Cancha registrada correctamente");
			}else{
				$resp['status'] = "failed";
				$resp['sql'] = $sql;
			}
		}
		return json_encode($resp);
	}

	function delete_assembly_hall(){
		$sql = "DELETE FROM `assembly_hall` where id = '{$_POST['id']}' ";
		$delete = $this->conn->query($sql);
		$this->capture_err();
		if($delete){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Assembly Hall/Room Successfully Deleted");
		}else{
			$resp['status'] = "failed";
			$resp['sql'] = $sql;
		}
		return json_encode($resp);
	}

	function save_schedule(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k=> $v){
			if($k != 'id'){
				if(!empty($data)) $data.=", ";
				$data.=" {$k} = '{$v}'";
			}
		}

		if(strtotime($datetime_end) < strtotime($datetime_start)){
			$resp['status'] = 'failed';
			$resp['err_msg'] = "Date and time are incorrect";
		}else{
			$d_start = strtotime($datetime_start);
			$d_end = strtotime($datetime_end);
			$chk = $this->conn->query("SELECT * FROM `schedule_list` where horario='{$horario}' and assembly_hall_id='{$assembly_hall_id}' and (('{$d_start}' Between unix_timestamp(datetime_start) and unix_timestamp(datetime_end)) or ('{$d_end}' Between unix_timestamp(datetime_start) and unix_timestamp(datetime_end))) ".(($id > 0) ? " and id !='{$id}'" : ""))->num_rows;
			
				if(empty($id)){
					$sql = "INSERT INTO `schedule_list` set {$data}";
					
				}else{
					$sql = "UPDATE `schedule_list` set {$data} where id = '{$id}'";
				}
				$save = $this->conn->query($sql);
				if($save){
					$resp['status'] = 'success';
					$this->settings->set_flashdata('success', " Saved");
					$nombre=$_POST["reserved_by"];
					$fecha=$_POST["datetime_start"];
					$horario=$_POST["horario"];
					$telefono=$_POST["telefono"];
					$obs=$_POST["schedule_remarks"];
					$canchaid=$_POST["assembly_hall_id"];
					if($canchaid=="12"){
						$canchaid="Matt Monnat";
					}
					if($canchaid=="13"){
						$canchaid="Fernando Caceres";
					}
					if($canchaid=="14"){
						$canchaid="Arjun Dhawan";
					}
					if($canchaid=="15"){
						$canchaid="Chad Carpenter";
					}
					if($canchaid=="16"){
						$canchaid="Vaibhav Jain";
					}
					if($canchaid=="17"){
						$canchaid="Sandy Rawat";
					}
					//DonBalon
				
					$mail = new PHPMailer(true);
					
						$mail->SMTPDebug = SMTP::DEBUG_SERVER;
						$mail->isSMTP();
						$mail->Host = 'smtp.hostinger.com';
						$mail->SMTPAuth = true;
						$mail->Username = 'absencecalendarapp@ingenierialife.com';
						$mail->Password = 'Admin2022_$';
						$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
						$mail->Port = 587;
					
						$mail->setFrom('absencecalendarapp@ingenierialife.com', 'Absence Calendar App');
						$mail->addAddress('matt.monnat@aarfid.com','Matt Monnat');
						$mail->addCC('fcaceres@aarfid.com');
						$mail->addCC('vaibhav.jain@aarfid.com');
						$mail->addCC('arjun.dhawan@aarfid.com');
						$mail->addCC('sandy.rawat@aarfid.com');
						//$mail->addCC('soporte@ingenierialife.com');
						//mail->addAttachment('docs/dashboard.png', 'Dashboard.png');
					
						$mail->isHTML(true);
						$mail->Subject = "New Absence from $canchaid";
						$mail->Body = "Hi, <br><br> $canchaid just registered a new absence for $nombre in this date: $fecha</b><br><br>See: https://ingenierialife.com/calendar-absence/admin/ <br>";
						$mail->send();
			
					// if($canchaid==5){
					// 	$valor=1038664124;
					// 	$sql2 = "INSERT INTO messages(incoming_msg_id, outgoing_msg_id,msg) VALUES(819963179,$valor,'¡Nueva Reservación!                                 Fecha: $fecha, Nombre: $nombre, Teléfono: $telefono, Horario: $horario, $obs')";
					// 	$save2 = $this->conn->query($sql2);
					// }

				}else{
					$resp['status'] = 'failed';
					$resp['sql'] = $sql;
					$resp['qry_error'] = $this->conn->error;
					$resp['err_msg'] = "There's an error while submitting the data.";
				}
			
		}
		return json_encode($resp);
	}
	function delete_sched(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM `schedule_list` where id = '{$id}'");
		if($delete){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success', "Schedule successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_assembly':
		echo $Master->save_assembly();
	break;
	case 'delete_assembly_hall':
		echo $Master->delete_assembly_hall();
	break;
	case 'save_schedule':
		echo $Master->save_schedule();
	break;
	case 'delete_sched':
		echo $Master->delete_sched();
	break;
	default:
		// echo $sysset->index();
		break;
}