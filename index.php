<?php
$upload_dir = __DIR__ . '/uploads/';
?>
<div>

    <form action="" method="post" enctype="multipart/form-data" class="mb-3">
        <h3 class="text-center mb-5">Upload File in PHP 8</h3>
        <div class="custom-file">
            <input type="file" name="fileUpload" class="custom-file-input" id="fileUpload">
            <label class="custom-file-label" for="chooseFile">Select file</label>
        </div>
        <button type="button" name="ajax_upload" class="btn btn-primary btn-block mt-4" onclick="saveDocumentToS3()">
            Ajax upload
        </button>

    </form><script>

        async function saveDocumentToS3() {
            const currentUrl = window.location.href
            let formData = new FormData()
            formData.append("fileUpload", fileUpload.files[0])
            const uploadRequest = await fetch(currentUrl, {
                method: "POST",
                body: formData
            })


            const  data = await uploadRequest.json()


        }

    </script>


    </div>
<?php
if(isset($_FILES["fileUpload"])) {

    // Get file path
    $target_file = $upload_dir. basename($_FILES["fileUpload"]["name"]);

    $saved = move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $target_file);

    $data = array("saved" => $saved,"a" => "Apple", "b" => "Ball", "c" => "Cat");

    header("Content-Type: application/json");
    echo json_encode($data);
    exit();

}