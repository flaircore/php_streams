<?php

namespace Nick\PhpStreams;

use Aws\S3\S3Client;

class AwsS3Client {

    public $s3Client;

    public function __construct() {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => $_ENV['AWS_REGION'],
            'credentials' => [
                'key'    => $_ENV['AWS_API_KEY'],
                'secret' => $_ENV['AWS_API_SECRET_KEY'],
            ],
            //'endpoint' => AWS_ENDPOINT
        ]);
    }

}