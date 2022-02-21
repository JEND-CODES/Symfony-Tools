<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class EncryptDecrypt
{
    // Passphrase - Secret key to encrypt / decrypt
    // const SECRET_KEY = 'secret-key';

    // https://www.php.net/manual/fr/function.openssl-encrypt.php
    // https://www.php.net/manual/en/function.openssl-get-cipher-methods.php
    const METHOD = 'AES-128-ECB';

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function encrypt(string $message): string
    {
        $vector_length = openssl_cipher_iv_length($this::METHOD);

        // A non-null initialization vector
        $vector = openssl_random_pseudo_bytes($vector_length);
        
        $encrypted = openssl_encrypt($message, $this::METHOD, $this->container->getParameter('encryptKey'), 0, $vector);

        // dd($encrypted);

        return $encrypted;

    }

    public function decrypt(string $encrypted): string
    {
        $vector_length = openssl_cipher_iv_length($this::METHOD);

        $vector = openssl_random_pseudo_bytes($vector_length);

        $decrypted = openssl_decrypt($encrypted, $this::METHOD, $this->container->getParameter('encryptKey'), 0, $vector);

        dd($encrypted, $decrypted);

        return $decrypted;

    }

}