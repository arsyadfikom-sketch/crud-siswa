<?php

function uploadFoto($s3, $bucket, $file){

    if($file['name'] == ''){

        return '';

    }

    $tmp  = $file['tmp_name'];

    $name = time().'_'.$file['name'];

    $result = $s3->putObject([

        'Bucket'     => $bucket,
        'Key'        => $name,
        'SourceFile' => $tmp

    ]);

    return $result['ObjectURL'];

}
?>
