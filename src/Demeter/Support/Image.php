<?php
namespace Demeter\Support;

class Image
{
    /**
     * Detects the MIME type of a file from its base64-encoded string representation.
     *
     * This method analyzes the binary data decoded from the base64 string
     * to determine the MIME type of the file. If the data cannot be decoded
     * or the MIME type cannot be determined, it defaults to 'image/jpeg'.
     *
     * @param string $base64String The base64-encoded string representing the file data.
     * @return string The detected MIME type of the file, defaults to 'image/jpeg'.
     */
    public static function detectMimeTypeFromBase64(string $base64String): string
    {
        $decoded = base64_decode($base64String, true);

        if($decoded === false){
            return 'image/jpeg';
        }

        $signatures = [
            "\xFF\xD8\xFF" => 'image/jpeg',
            "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A" => 'image/png',
            "GIF87a" => 'image/gif',
            "GIF89a" => 'image/gif',
            "\x42\x4D" => 'image/bmp',
        ];

        foreach($signatures as $signature => $mimeType){
            if(str_starts_with($decoded, $signature)){
                return $mimeType;
            }
        }

        if(str_starts_with($decoded, "RIFF") && str_contains($decoded, "WEBP")){
            return 'image/webp';
        }

        return 'image/jpeg';
    }

    /**
     * Checks if the provided input is a valid base64-encoded representation of an image.
     *
     * @param string $input The input string to be evaluated.
     * @return bool Returns true if the input string represents a valid base64-encoded image, false otherwise.
     */
    public static function isBase64Image(string $input): bool
    {
        if(preg_match('/^data:image\/[a-zA-Z]+;base64,/', $input)){
            return true;
        }

        if(strlen($input) > 50 && preg_match('/^[A-Za-z0-9+\/]+=*$/', $input) && base64_decode($input, true) !== false){
            $decoded = base64_decode($input);
            $imageInfo = @getimagesizefromstring($decoded);

            return $imageInfo !== false;
        }

        return false;
    }
}