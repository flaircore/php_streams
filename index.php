<?php
$upload_dir = __DIR__ . '/uploads/';

if (isset($_FILES["fileUpload"])) {

	// Get file path
	$target_file = $upload_dir. basename($_FILES["fileUpload"]["name"]);

	$saved = move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $target_file);

	$data = array("Saved" => $saved  ,"Mango"=>95, "Cherry"=>120, "Kiwi"=>100, "Orange"=>55);

	header('Content-Type: application/json');
	echo json_encode($data);
	exit();

}
?>
 <!doctype html>
 <html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="dist/main.css">
  <title>A Web exp.!</title>
 </head>
 <body class="content-fluid">
 <header>

 </header>
<div>

    <form action="" method="post" enctype="multipart/form-data" class="mb-3">
        <h3 class="text-center mb-5">Upload File in PHP 8</h3>
        <div class="custom-file">
            <input type="file" name="fileUpload" class="custom-file-input" id="fileUpload">
            <label class="custom-file-label" for="chooseFile">Select file</label>
         <section id="custom-file-upload-progress">
          <div id="progress-bar">
           <div id="progress">Progress</div>
           <div id="percent">50%</div>
          </div>
         </section>
        </div>
        <button type="button" name="ajax_upload" class="btn btn-primary btn-block mt-4" onclick="saveDocumentToS3()">
            Ajax upload
        </button>

<!--     <button type="button" name="select-file" id="select-file">-->
<!--      Select file-->
<!--     </button>-->

    </form>
 <script src="dist/main.bundle.js"></script>
 <script>

        async function saveDocumentToS3() {
            const currentUrl = window.location.href
            let formData = new FormData()
            formData.append("fileUpload", fileUpload.files[0])
            const response = await fetch(currentUrl, {
                method: "POST",
                body: formData
            })

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const  data = await response.json()

            console.log(data)




        }

    </script>


    </div>
 <footer>
  Copyright@2023 FlairCore.com
 </footer>

 </body>
</html>
<?php
