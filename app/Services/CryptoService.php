<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use RuntimeException;

class CryptoService
{
    private const KEY_DIR = 'app/keys';

    private const PRIVATE_KEY_PATH = 'app/keys/private.pem';

    private const PUBLIC_KEY_PATH = 'app/keys/public.pem';

    private ?string $publicKeySpkiBase64 = null;

    public function getPublicKeySpkiBase64(): string
    {
        if ($this->publicKeySpkiBase64 !== null) {
            return $this->publicKeySpkiBase64;
        }

        $this->ensureKeysExist();

        $pem = file_get_contents(storage_path(self::PUBLIC_KEY_PATH));

        $lines = explode("\n", $pem);
        $content = '';

        foreach ($lines as $line) {
            $line = trim($line);

            if (str_starts_with($line, '-----')) {
                continue;
            }

            $content .= $line;
        }

        $this->publicKeySpkiBase64 = $content;

        return $content;
    }

    public function decryptRequest(string $binaryData): array
    {
        $this->ensureKeysExist();

        if (strlen($binaryData) < 524) {
            throw new RuntimeException('Invalid encrypted request: too short');
        }

        $encryptedAesKey = substr($binaryData, 0, 512);
        $iv = substr($binaryData, 512, 12);
        $ciphertextWithTag = substr($binaryData, 524);

        if (strlen($ciphertextWithTag) < 16) {
            throw new RuntimeException('Invalid encrypted request: missing auth tag');
        }

        $tag = substr($ciphertextWithTag, -16);
        $ciphertext = substr($ciphertextWithTag, 0, -16);

        $privateKey = openssl_pkey_get_private(
            file_get_contents(storage_path(self::PRIVATE_KEY_PATH))
        );

        if ($privateKey === false) {
            throw new RuntimeException('Failed to load private key');
        }

        if (! openssl_private_decrypt($encryptedAesKey, $aesKey, $privateKey, OPENSSL_PKCS1_OAEP_PADDING)) {
            throw new RuntimeException('Failed to decrypt AES key');
        }

        $decrypted = openssl_decrypt($ciphertext, 'aes-256-gcm', $aesKey, OPENSSL_RAW_DATA, $iv, $tag);

        if ($decrypted === false) {
            throw new RuntimeException('Failed to decrypt payload');
        }

        $data = json_decode($decrypted, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Invalid JSON in decrypted payload');
        }

        return [
            'data' => $data,
            'aes_key' => $aesKey,
        ];
    }

    public function encryptResponse(array $data, string $aesKey): string
    {
        $iv = random_bytes(12);
        $tag = '';
        $json = json_encode($data);

        $ciphertext = openssl_encrypt($json, 'aes-256-gcm', $aesKey, OPENSSL_RAW_DATA, $iv, $tag);

        if ($ciphertext === false) {
            throw new RuntimeException('Failed to encrypt response');
        }

        return $iv.$ciphertext.$tag;
    }

    private function ensureKeysExist(): void
    {
        if (File::exists(storage_path(self::PRIVATE_KEY_PATH))) {
            return;
        }

        File::makeDirectory(storage_path(self::KEY_DIR), 0755, true, true);

        $keyPair = openssl_pkey_new([
            'private_key_bits' => 4096,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        if ($keyPair === false) {
            throw new RuntimeException('Failed to generate RSA key pair');
        }

        openssl_pkey_export($keyPair, $privateKey);

        $publicKeyDetails = openssl_pkey_get_details($keyPair);
        $publicKey = $publicKeyDetails['key'];

        File::put(storage_path(self::PRIVATE_KEY_PATH), $privateKey);
        File::put(storage_path(self::PUBLIC_KEY_PATH), $publicKey);
    }
}
