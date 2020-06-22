<?php
    if($_SERVER['REQUEST_METHOD'] == "POST") {

        if(isset($_POST['upload'])){

			$ccms_id = $_POST['ccms_id'];
			$emp_name = $_POST['full_name'];
            $login_id = $_POST['login_id'];
            $mgr_name = $_POST['member_manager'];
            $acm_name = $_POST['member_accm'];
            $sup_name = $_POST['member_supervisor'];
			$emp_wave = $_POST['member_wave'];
			
            
            $res = $db->prepare('SELECT * FROM fntr_roster WHERE CCMS_ID="'.$ccms_id.'"');
            $res->execute();
            $cnt = $res->fetch(PDO::FETCH_NUM);

            if($cnt > 0){
                print '<script>alert("Employee already exist");</script>'; 
            }else{
                $res = $db->prepare('INSERT INTO fntr_roster VALUES("'.$ccms_id.'", "'.$emp_name.'", "'.$login_id.'", "'.$sup_name.'", "'.$acm_name.'", "'.$mgr_name.'", "'.$emp_wave.'")');
                try{
                    $res->execute();
    
                    print '<script>alert("New employee has been uploaded");</script>';
    
                }catch(PDOException $err) {
                    print $err->getMessage();
                }
            }
        }
        
		if(isset($_POST['update'])){

			$ccms_id = $_POST['ccms_id'];
			$emp_name = $_POST['full_name'];
            $login_id = $_POST['login_id'];
            $mgr_name = $_POST['member_manager'];
            $acm_name = $_POST['member_accm'];
            $sup_name = $_POST['member_supervisor'];
			$emp_wave = $_POST['member_wave'];

			$res = $db->prepare('UPDATE fntr_roster SET Emp_Name="'.$emp_name.'", Login_ID="'.$login_id.'", CM_Name="'.$mgr_name.'", ACCM_Name="'.$acm_name.'", Sup_Name="'.$sup_name.'", Ros_Wave="'.$emp_wave.'" WHERE CCMS_ID="'.$ccms_id.'" ');
			try{
				$res->execute();

				print '<script>alert("Employee has been updated");</script>';

			}catch(PDOException $err) {
				print $err->getMessage();
			}
        }

            
        
        if(isset($_POST['import'])){
            if(!empty($_FILES['file']['name'])){
                $file_name = $_FILES['file']['tmp_name'];
                $file_info = pathinfo($_FILES['file']['name']);
                
                if(strtolower($file_info['extension']) == 'csv'){
                    if($_FILES['file']['size'] > 0){

                        $file = fopen($file_name, 'r');
                        $imp = fgetcsv($file);

                        while(($imp = fgetcsv($file, 1000, ',')) !== FALSE){
                            
                            $res = $db->prepare('INSERT INTO tmob_roster VALUES("'.$imp[0].'", "'.$imp[1].'", "'.$imp[2].'", "'.$imp[3].'", "'.$imp[4].'", "'.$imp[5].'", "'.$imp[6].'", "'.$imp[7].'")');

                            try{
                                $res->execute();
                
                            }catch(PDOException $err) {
                                print $err->getMessage();
                            }
                        }

                        print '<script>alert("Roster has been imported");</script>';

                    }else{
                        print '<script>alert("CSV file was blank");</script>';
                    }
                }
            }else{
                print '<script>alert("No file selected");</script>';
            }
        }
    }
?>

<table id="tableDefault" class="table table-bordered table-hover" cellspacing="0" width="100%">

<?php
    date_default_timezone_set('America/Los_Angeles');
    $dDate = date('Y-m-d H:i:s');

    print '<thead>
        <tr>
            <th>CCMS ID</th>
            <th>Name</th>
			<th>Login ID</th>
            <th>Supervisor</th>
            <th>ACCM</th>
            <th>Manager</th>
			<th>Wave</th>
            <th>Actions</th>
        </tr>
    </thead>';

    $res = $db->prepare('SELECT *, COALESCE(ACCM_Name,"-") AS ACCM FROM fntr_roster');
    $res->execute();
    
    $res = $res->fetchAll();

    print'<tbody class="tbody">';

    foreach($res as $row){
        print '<tr>';
            print '<td>'.$row['CCMS_ID'].'</td>';
            print '<td>'.$row['Emp_Name'].'</td>';
            print '<td>'.$row['Login_ID'].'</td>';
            print '<td>'.$row['Sup_Name'].'</td>';
            print '<td>'.$row['ACCM'].'</td>';
            print '<td>'.$row['CM_Name'].'</td>';
            print '<td>'.$row['Ros_Wave'].'</td>';
            print '<td>
                        <a id="'.$row['CCMS_ID'].'" class="edit btn-link" data-toggle="modal" data-target="#modalUpload"><i class="fas fa-edit" style="font: 1.5em; color: blue;"></i></a>
                        <a id="'.$row['CCMS_ID'].'" class="delete btn-link"><i class="fas fa-times-circle" style="font: 1.5em; color: red;"></i></a>
                   </td>';
        print '</tr>';
    }

    print '</tbody>';
?>

</table>

<div class="modal" id="modalUpload">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h4 id="title" class="modal-title">New Member</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
        <form id="skill_form" method="POST">
            <div class="form-group">
                <label >CCMS ID</label>
                <input type="text" class="form-control form-control-sm" id="ccms_id" name="ccms_id" placeholder="Emp ID">
            </div>
            <div class="form-group">
                <label >Name</label>
                <input type="text" class="form-control form-control-sm" id="full_name" name="full_name" placeholder="Full Name">
            </div>
            <div class="form-group">
                <label >Login ID</label>
                <input type="text" class="form-control form-control-sm" id="login_id" name="login_id" placeholder="Login ID">
            </div>
            <div class="form-group">
                <label >Supervisor</label>
                <input type="text" class="form-control form-control-sm" id="member_supervisor" name="member_supervisor" placeholder="Supervisor">
            </div>
            <div class="form-group">
                <label >Assistant Manager</label>
                <input type="text" class="form-control form-control-sm" id="member_accm" name="member_accm" placeholder="ACCM">
            </div>
            <div class="form-group">
                <label >Manager</label>
                <input type="text" class="form-control form-control-sm" id="member_manager" name="member_manager" placeholder="Manager">
            </div>
			<div class="form-group">
                <label >Employee Wave</label>
                <input type="text" class="form-control form-control-sm" id="member_wave" name="member_wave" placeholder="Wave">
            </div>
      </div>

      <div class="modal-footer">
        <input id="submitForm" type="submit" name="upload" class="btn btn-sm btn-success" value="Submit">
        <button type="button" class="btn btn-sm btn-info" data-dismiss="modal">Close</button>
      </div>
        </form>

    </div>
  </div>
</div>

<script src="scripts/jquery.js"></script>
<!-- Delete -->
<script type="text/javascript">
    $(function() {

        $(".delete").click(function(){

            var element = $(this);
            var table_id = 'fntr_roster';
            var ref_id = 'CCMS_ID';
            var del_id = element.attr("id");
            var info = 'id=' + del_id + '&table=' + table_id + '&ref=' + ref_id;

            if(confirm("Sure you want to delete member " + del_id + " ?")) {

                $.ajax({
                    type: "POST",
                    data: info,
                    url: "lib/delete.php",
                    success: function(){
                        alert("Employee has been deleted");
                    }
            
                });

                $(this).parents(".record").animate({ backgroundColor: "#fbc7c7" }, "fast")
        		.animate({ opacity: "hide" }, "fast");
            }

            return false;

        });

    });
</script>


<!-- Edit -->
<script type="text/javascript">

	$(function() {
		
		$(".edit").click(function(){
			
			var title = document.getElementById("title");
			var element = $(this);
			var full_name = document.getElementById("full_name");
			var login_id = document.getElementById("login_id");
			var member_supervisor = document.getElementById("member_supervisor");
			var member_accm = document.getElementById("member_accm");	
			var member_manager = document.getElementById("member_manager");	
			var member_wave = document.getElementById("member_wave");
			var btn = document.getElementById("submitForm");			            
			
			var table_id = 'fntr_roster';
			var ref_id = 'CCMS_ID';  			
			var btn = document.getElementById("submitForm");			            
            var info = 'id=' + element.attr("id") + '&table=' + table_id + '&ref='+ ref_id;				
			
			$.ajax({
				type: "POST",
				data: info,
				dataType: "son",
				url: "lib/edit.php",
				success: function(data){									
					
					console.log(data);
					title.innerHTML = "Edit Roster";
					ccms_id.value = data['CCMS_ID'];
					ccms_id.setAttribute("readonly", true);					
					login_id.value = data['Login_ID'];					
					full_name.value = data['Emp_Name'];
					member_supervisor.value = data['Sup_Name'];
					member_accm.value = data['ACCM_Name'];
					member_manager.value = data['CM_Name'];
					member_wave.value = data['Ros_Wave'];
					
					btn.value = "Update";
					btn.name = "update";
					
					
					$('#modalUpload').modal('show');									
					
					//remove data when modal submitted or closed
					$('.modal').on('hidden.bs.modal', function (e) {
						$(this)
							.find("h4,h4[id='title']")
							   .html('New User')
							   .end()
							.find("input,textarea,select")
							   .val('')
							   .end()
							.find("input, input[id='ccmsid']")
							   .attr("readonly", false)
							   .end()
							.find("input[name='update']")
							   .val('Submit')
							   .end();
					});
					
				}
			});
			
		});
	});
		
</script>
