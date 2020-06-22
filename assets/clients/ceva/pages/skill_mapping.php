<?php
    if($_SERVER['REQUEST_METHOD'] == "POST") {

        if(isset($_POST['upload'])){

            $skill_info = $_POST['skill_info'];
            $skill_lob = $_POST['skill_lob'];
            $skill_site = $_POST['skill_site'];
            
            $res = $db->prepare('SELECT * FROM fntr_skill WHERE Skill_ID="'.$skill_info.'"');
            $res->execute();
            $cnt = $res->fetch(PDO::FETCH_NUM);

            if($cnt > 0){
                print '<script>alert("Skill already exist");</script>'; 
            }else{
                $res = $db->prepare('INSERT INTO fntr_skill VALUES("'.$skill_info.'", "'.$skill_lob.'", "'.$skill_site.'")');
                try{
                    $res->execute();
    
                    print '<script>alert("New skill has been uploaded");</script>';
    
                }catch(PDOException $err) {
                    print $err->getMessage();
                }
            }
        }
		
		if(isset($_POST['update'])){
			
            $skill_info = $_POST['skill_info'];
            $skill_lob = $_POST['skill_lob'];
            $skill_site = $_POST['skill_site'];			

			$res = $db->prepare('UPDATE fntr_skill SET Skill_LOB="'.$skill_lob.'", Skill_Site="'.$skill_site.'" WHERE Skill_ID="'.$skill_info.'"');
			try{
				$res->execute();

				print '<script>alert("Skill has been updated");</script>';

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
                            
                            $res = $db->prepare('INSERT INTO fntr_skill VALUES("'.$imp[0].'", "'.$imp[1].'", "'.$imp[2].'", "'.$imp[3].'", "'.$imp[4].'", "'.$imp[5].'", "'.$imp[6].'", "'.$imp[7].'", "'.$imp[8].'")');

                            try{
                                $res->execute();
								
                            }catch(PDOException $err) {
                                print $err->getMessage();
                            }
                        }

                        print '<script>alert("Skills has been imported");</script>';

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
            <th>Skill</th>
            <th>LOB</th>
            <th>Skill Site</th>
			<th>Action</th>
        </tr>
    </thead>';

    $res = $db->prepare('SELECT * FROM fntr_skill');
    $res->execute();
    
    $res = $res->fetchAll();

    print'<tbody class="tbody">';

    foreach($res as $row){
        print '<tr class="record">';
            print '<td>'.$row['Skill_ID'].'</td>';
            print '<td>'.$row['Skill_LOB'].'</td>';
            print '<td>'.$row['Skill_Site'].'</td>';
            print '<td>
                        <a id="'.$row['Skill_ID'].'" class="edit btn-link"><i class="fas fa-edit" style="font: 1.5em; color: blue;"></i></a>
                        <a id="'.$row['Skill_ID'].'" class="delete btn-link"><i class="fas fa-times-circle" style="font: 1.5em; color: red;"></i></a>
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
        <h4 id="title" class="modal-title">New Skill</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

		<div class="modal-body">
			<form id="skill_form" method="POST">
				<div class="form-group">
					<label>Skill</label>
					<input type="text" class="form-control form-control-sm" name="skill_info" id="skill_info" placeholder="INFO SKILL">
				</div>
				<div class="form-group">
					<label>LOB</label>
					<input type="text" class="form-control form-control-sm" name="skill_lob" id="skill_lob" placeholder="LOB">
				</div>
				<div class="form-group">
					<label>Skill Site</label>
					<input type="text" class="form-control form-control-sm" name="skill_site" id="skill_site" placeholder="Site">
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
            var table_id = 'fntr_skill';
            var ref_id = 'Skill_ID';
            var del_id = element.attr("id");
            var info = 'id=' + del_id + '&table=' + table_id + '&ref=' + ref_id;

            if(confirm("Sure you want to delete skill " + del_id + " ?")) {

                $.ajax({
                    type: "POST",
                    data: info,
                    url: "lib/delete.php",
                    success: function(){
                        alert("Skill has been deleted");
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
			var skillInfo = document.getElementById("skill_info");
			var skillLob = document.getElementById("skill_lob");
			var skillSite = document.getElementById("skill_site");
			var btnUpdate = document.getElementById("submitForm");
			
			var element = $(this);
            var table_id = 'fntr_skill';
            var ref_id = 'Skill_ID';
            var edit_id = element.attr("id");
            var info = 'id=' + edit_id + '&table=' + table_id + '&ref=' + ref_id;				
			
			$.ajax({
				type: "POST",
				data: info,
				dataType: "json",
				url: "lib/edit.php",
				success: function(data){									
					
					title.innerHTML = "Edit Skill";				
					skillInfo.value = data['Skill_ID'];
					skillInfo.setAttribute("readonly", true);
					skillLob.value = data['Skill_LOB'];
					skillSite.value = data['Skill_Site'];
					btnUpdate.value = "Update";
					btnUpdate.name = "update";
					
					$('#modalUpload').modal('show');									
					
					//remove data when modal submitted or closed
					$('.modal').on('hidden.bs.modal', function (e) {
						$(this)
							.find("h4,h4[id='title']")
							   .html('New Skill')
							   .end()
							.find("input,textarea,select")
							   .val('')
							   .end()
							.find("input, input[id='skill_info']")
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
