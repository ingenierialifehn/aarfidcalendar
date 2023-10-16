
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<style>
#selectAll{
	top:0
}
</style>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Calendar</h3>
		<!-- <div class="card-tools">
			<a href="?page=individual/manage_individual" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div> -->
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<div class="row">
				<div class="col-md-8">
					<div id="calendar"></div>
				</div>
				<div class="col-md-4">
					<div class="callout border-0">
						<h5><b>New Absence</b></h5>
						<hr>
						
						<form action="" id="add_sched">
							<input type="hidden" name="id" value="">
							<div class="form-group">
								<label for="assembly_hall_id" class="control-label">Employee</label>
								<select name="assembly_hall_id" id="assembly_hall_id" class="custom-select select2" >
									<option value=""></option>
									<?php 
									$idcancha=$_settings->userdata('room_name');
									$hall_qry = $conn->query("SELECT * FROM `assembly_hall` WHERE id=$idcancha");
									while($row = $hall_qry->fetch_assoc()):
									?>
										<option value="<?php echo $row['id'] ?>" selected><?php echo $row['room_name'] ?></option>
									<?php endwhile; ?>
								</select>
							</div>
							<div class="form-group">
							<label for="reserved_by" class="control-label">Time OFF</label>
							<select name="reserved_by" id="reserved_by" class="custom-select select2" >
                                        <option selected>Choose Time</option>
                                        <option value="AM">AM</option>
                                        <option value="PM">PM</option>
                                        <option value="All Day">All Day</option>
                                    </select>
							</div>
							
							<div class="form-group">
								<label for="datetime_start" class="control-label">From: </label>
								<input type="date" class="form-control" name="datetime_start" id="datetime_start">
							</div>
							<div class="form-group">
								<label for="datetime_end" class="control-label">Until:</label>
								<input type="date" class="form-control" name="datetime_end" id="datetime_end">
							</div>
							<div class="form-group">
								<label for="schedule_remarks" class="control-label">Remarks</label>
								<textarea rows="3" class="form-control" name="schedule_remarks" id="schedule_remarks"></textarea>
							</div>
							<div class="form-group d-flex w-100 justify-content-end">
								<button class="btn btn-flat btn-primary btn-sm mr-2">Save</button>
								<button class="btn btn-flat btn-light btn-sm" type="reset">Reset</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
$idcancha=$_settings->userdata('room_name');
// if($idcancha==12){
// 	$sched_qry = $conn->query("SELECT s.*,a.room_name FROM `schedule_list` s inner join assembly_hall a on a.id = s.assembly_hall_id");
// }
// else {
// 	$sched_qry = $conn->query("SELECT s.*,a.room_name FROM `schedule_list` s inner join assembly_hall a on a.id = s.assembly_hall_id WHERE assembly_hall_id=$idcancha;");
// }
$sched_qry = $conn->query("SELECT s.*,a.room_name FROM `schedule_list` s inner join assembly_hall a on a.id = s.assembly_hall_id");
$sched_data = array();
while($row=$sched_qry->fetch_assoc()):
	$sched_data[]=$row;
endwhile;
$sched = json_encode($sched_data);
?>
<script>
	var scheds = $.parseJSON('<?php echo $sched ?>');
	$(function(){
		$('#add_sched').submit(function(e){
			e.preventDefault()
			start_loader()
			$('#add_sched .err-msg').remove()

			$.ajax({
				url:_base_url_+'classes/Master.php?f=save_schedule',
				method:"POST",
				data: $(this).serialize(),
				dataType:"json",
				error:err=>{
					console.log(err)
					end_loader()
					alert_toast("An error occured","success");
					location.reload();
				},
				success:function(resp){
					if(resp.status == 'success'){
						location.reload()
					}else if(resp.status == 'failed' && !!resp.err_msg){
						var el = $('<div class="err-msg alert alert-danger mb-1">')
							el.text(resp.err_msg)
						$('#add_sched').prepend(el)
							el.show('slow')
					}else{
						console.log(resp)
						alert_toast("An error occured","success");
					}
					end_loader();
				}
			})
		})
		$('.select2').select2({placeholder:"Please select an employee"})
		var Calendar = FullCalendar.Calendar;
        var date = new Date()
        var d    = date.getDate(),
            m    = date.getMonth(),
            y    = date.getFullYear()
		
		var calendarEl = document.getElementById('calendar');
		var calendar = new Calendar(calendarEl, {
						locale: 'en',
                        headerToolbar: {
                            left  : 'prev,next today',
                            center: 'title',
                            right : 'dayGridMonth,timeGridWeek,timeGridDay'
                        },
                        themeSystem: 'bootstrap',
                        //Random default events
                        events:function(event,successCallback){
                            var days = moment(event.end).diff(moment(event.start),'days')
                            var events = []
							Object.keys(scheds).map(k=>{
								if(scheds[k].room_name=="Fernando Cáceres"){
									var color='rgb(99, 57, 116 )'
								}
								if(scheds[k].room_name=="Matt Monnat"){
									var color='rgb(39, 174, 96)'
								}if(scheds[k].room_name=="Vaibhav Jain"){
									var color='rgb(245, 176, 65)'
								}if(scheds[k].room_name=="Chad Carpenter"){
									var color='rgb(211, 84, 0)'
								}
								if(scheds[k].room_name=="Arjun Dhawan"){
									var color='rgb(156, 100, 12)'
								}
								if(scheds[k].room_name=="Sandy Rawat"){
									var color='rgb(125, 206, 160)'
								}
								events.push({
									title          : scheds[k].room_name,
									start          : moment(scheds[k].datetime_start).format("YYYY-MM-DD"),
									end            : moment(scheds[k].datetime_end).format("YYYY-MM-DD"),
									backgroundColor: color, 
									borderColor    : 'var(--primary)',
									'data-id'      : scheds[k].id
								})
							})
							console.log(events)
                            successCallback(events)
                        },
                        eventClick:function(info){
							sched_id = info.event.extendedProps['data-id']
							console.log(sched_id)
                            uni_modal("Schedule Details","schedules/view_details.php?id="+sched_id)
                        },
                        editable  : true,
                        selectable: true,
				});

	calendar.render();
	})
</script>