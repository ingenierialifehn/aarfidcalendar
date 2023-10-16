<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Employees</h3>
		<div class="card-tools">
			<a href="?page=assembly_hall/manage_assembly" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> Add New Employee</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-bordered table-stripped">
				<colgroup>
					<col width="5%">
					<col width="30%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Name</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `assembly_hall` order by `room_name` asc ");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo $row['room_name'] ?></td>
							
							
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("¿Estás segur@ de eliminar esta Cancha?","delete_assembly_hall",[$(this).attr('data-id')])
		})
		$('.table').dataTable();
	})
	function delete_assembly_hall($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_assembly_hall",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("Ocurrió un error.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("Ocurrió un error.",'error');
					end_loader();
				}
			}
		})
	}
</script>