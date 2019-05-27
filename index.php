<?php

require_once "vendor/autoload.php";
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=cibofstorage;AccountKey=ERxG3BqHgSTrjJ83QIZBbtQp4rKQd7QfM6YlkbjlVCx37mYuRO7FFsItm31QrQb7TxDopYPJh1nzvxo1e9xNxw==;EndpointSuffix=core.windows.net";
$blobClient = BlobRestProxy::createBlobService($connectionString);
$containerName = "cscontainer";

if (isset($_POST["submit"])) {
    $fileToUpload = $_FILES["fileToUpload"]["name"];
    $content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
    echo fread($content, filesize($fileToUpload));

    $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
    header("Location: index.php");
}

$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Blob with Computer Vision</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <div class="container" style="margin-top: 100px;">
        <form action="index.php" method="post" enctype="multipart/form-data">
            <div class="input-group">
                <div class="custom-file">
                    <input type="file" name="fileToUpload" class="custom-file-input" accept=".jpg, .jpeg, .png" required>
                    <label class="custom-file-label">Choose image</label>
                </div>
                <div class="input-group-append">
                    <input type="submit" name="submit" value="Upload" class="btn btn-outline-secondary">
                </div>
            </div>
        </form>

        <br />

        <table class="table table-bordered table-striped">
        <caption>List of images</caption>
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>URL Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    do {
                        foreach($result->getBlobs() as $blob) {
                ?>
                <tr>
                    <td><?php echo $blob->getName(); ?></td>
                    <td><?php echo $blob->getUrl(); ?></td>
                    <td>
                        <form action="analyze.php" method="post">
                            <input type="hidden" name="url" value="<?php echo $blob->getUrl(); ?>">
                            <input type="submit" name="submit" value="Analyze" class="btn btn-primary">
                        </form>
                    </td>
                </tr>
                <?php
                        } $listBlobsOptions->setContinuationToken($result->getContinuationToken());
                    } while($result->getContinuationToken());
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>