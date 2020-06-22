<?php       
    if($_SERVER['REQUEST_METHOD'] == "POST") {

        if(isset($_POST['import'])){
            print '<script>alert("Data has been uploaded");</script>';

        }

        if(isset($_POST['upload'])){

            $emp_id = $_POST['ccmsid'];
            $emp_name = $_POST['name'];
            $emp_nt = $_POST['ntlogin'];
            $emp_access = $_POST['access'];

            $res = $mck_db->prepare('SELECT * FROM hawk_users WHERE ID="'.$emp_id.'"');
            $res->execute();
            $cnt = $res->fetch(PDO::FETCH_NUM);

            if($cnt > 0){
                print '<script>alert("User already exist");</script>'; 
            }else{
                $res = $mck_db->prepare('INSERT INTO hawk_users VALUES("'.$emp_id.'", "'.$emp_nt.'", "'.$emp_access.'", "'.$emp_name.'", "'.$_SESSION['emp_name'].'")');
                try{
                    $res->execute();
    
                    print '<script>alert("New user has been uploaded");</script>';
    
                }catch(PDOException $err) {
                    print $err->getMessage();
                }
            }
        }
		
		
		if(isset($_POST['update'])){
			
			$emp_id = $_POST['ccmsid'];
            $emp_nt = $_POST['ntlogin'];
			$emp_name = $_POST['name'];            
            $emp_access = $_POST['access'];

			$res = $mck_db->prepare('UPDATE hawk_users SET NTLogin="'.$emp_nt.'", UserAccess="'.$emp_access.'", Employee_Name="'.$emp_name.'", AddedBy="'.$_SESSION['emp_name'].'" WHERE ID="'.$emp_id.'" ');
			try{
				$res->execute();

				print '<script>alert("User has been updated");</script>';

			}catch(PDOException $err) {
				print $err->getMessage();
			}
        }
    }
?>

<table id="tableDefault" class="table table-bordered table-hover" cellspacing="0" width="100%">

<?php
    $res = $mck_db->prepare('SELECT * FROM hawk_users');
    $res->execute();
    
    $res = $res->fetchAll();

    print '<thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>CCMS</th>
            <th>Level</th>
            <th>Last Modified By</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody class="tbody my-auto">';

    foreach($res as $row){
        print '<tr class="record">';
            print '<td name="id">'.$row['ID'].'</td>';
            print '<td>'.$row['Employee_Name'].'</td>';
            print '<td>'.$row['NTLogin'].'</td>';
            print '<td>'.$row['UserAccess'].'</td>';
            print '<td>'.$row['AddedBy'].'</td>';
            print '<td>
                        <a id="'.$row['ID'].'" class="edit btn-link"><i class="fas fa-user-edit" style="font: 1.5em; color: blue;"></i></a>
                        <a id="'.$row['ID'].'" class="delete btn-link"><i class="fas fa-times-circle" style="font: 1.5em; color: red;"></i></a>
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
        <h4 id="title" class="modal-title">New User</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form method="POST">
        <div class="modal-body">
                <div class="form-group">
                    <label for="ccmsid">CCMS ID</label>
                    <input type="text" class="form-control form-control-sm" id="ccmsid" name="ccmsid" placeholder="ID">
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Name">
                </div>
                <div class="form-group">
                    <label for="ntlogin">NT Login</label>
                    <input type="text" class="form-control form-control-sm" id="ntlogin" name="ntlogin" placeholder="NT Login">
                </div>
                <div class="form-group">
                    <label for="accesslevel">Access Level</label>
                    <select class="form-control form-control-sm" name="access" id="accesslevel">
                        <option value="Viewer">Viewer</option>
                        <option value="Staff">Staff</option>
                        <option value="Administrator">Administrator</option>
                    </select>
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

<script type="text/javascript">
   
   $(function() {

        $(".delete").click(function(){

            var element = $(this);
            var table_id = 'hawk_users';
            var ref_id = 'ID';
            var del_id = element.attr("id");
            var info = 'id=' + del_id + '&table=' + table_id + '&ref=' + ref_id;

            if(confirm("Sure you want to delete user " + del_id + "?")) {

                $.ajax({
                    type: "POST",
                    data: info,
                    url: "lib/delete.php",
                    success: function(){
                        alert(del_id+' '+"has been deleted");
                    }
            
                });

                $(this).parents(".record").animate({ backgroundColor: "#fbc7c7" }, "fast")
        		.animate({ opacity: "hide" }, "fast");
            }

            return false;

        });

    });
</script>

<script type="text/javascript">  

    $(function() {
		
		$(".edit").click(function(){
			
			var title = document.getElementById("title");
			var element = $(this);
			var name = document.getElementById("name");
			var ntlogin = document.getElementById("ntlogin");
			var level = document.getElementById("accesslevel");	
			var table_id = 'hawk_users';
			var ref_id = 'ID';			
			var btn = document.getElementById("submitForm");			            
            var info = 'id=' + element.attr("id") + '&table=' + table_id + '&ref='+ ref_id;				
			
			$.ajax({
				type: "POST",
				data: info,
				dataType: "json",
				url: "lib/edit.php",
				success: function(data){									
					
					console.log(data);
					title.innerHTML = "Edit User";
					ccmsid.value = data['ID'];
					ccmsid.setAttribute("readonly", true);
					ntlogin.value = data['NTLogin'];
					name.value = data['Employee_Name'];
					level.value = data['UserAccess'];
					btn.value = "Update";
					btn.name = "update";
					
					$('#modalUpload').modal('show');									
					
			// 		remove data when modal submitted or closed
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

