<?php

namespace DataService\Service;

use GuzzleHttp\Exception\RequestException;

class DriveService extends Service
{
    public function getFileData($fileId)
    {

        $url = 'https://www.googleapis.com/drive/v2/files/' . $fileId . '?key=' . $this->apiKey;

        // get the file
        try {
            $response = $this->httpClient->request('GET', $url);
        } catch (RequestException $e) {
            throw new \Exception('File not found correctly');
        }

        // json decode
        $data = json_decode($response->getBody());

        // check the owner
        $owner = reset($data->owners);
        if ($owner->emailAddress != $this->creatorEmail) {
            throw new \Exception('Invalid File');
        }

        return $data;
    }

    public function getFile($fileData)
    {
        $url = $fileData->webContentLink;
        try {
            $response = $this->httpClient->request('GET', $url);
        } catch (RequestException $e) {
            throw new \Exception('Could not fetch the source file');
        }

        return $response->getBody();
    }
}