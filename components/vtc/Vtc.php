<?php
/**
 *
 * @copyright (c) 2018 William Escudero.
 * @package yii2-beutils
 * @version 1.0.0
 */
namespace yii\beutils\components\vtc;

use yii\base\Component;


/**
 * V.T.C - Variable Time Code
 *
 * This class provides functionality for one time security codes.
 * HOTP dynamic truncation is used to generate the codes.
 *
 * @package yii\beutils\components\vtc
 */
class Vtc extends Component
{
    /**
     * Hashing algorithm
     */
    const HASH_ALGO = 'sha256';

    /**
     * Cipher block length (bits)
     */
    const BLOCK_LEN = 1024;

    /**
     * Default length
     */
    const DEFAULT_LEN = 128;

    /**
     * False entropy used to generate random string. This is not required to be very secure.
     */
    private $falseEntropy = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+;:<>.";


    /**
     * This method generates a VTC
     *
     * @param $length
     * @param null $memo
     */
    public function genCode($length, $memo = null){

        $key = $this->createRandomKey();

        // Generate string block
        $block = $this->generateStr(self::DEFAULT_LEN);

        // Add memo (if available) to block and some salt
        $block .= ($memo != null && is_string($memo) ? $memo : "").@date('Y-m-d H:i:s').microtime();

        // Hash block and leave it binary (raw output)
        $hash_block = hash_hmac(self::HASH_ALGO, $block, $key, true);

        // Truncate binary hash block and return it
        return $this->hotpDynamicTruncation($hash_block, $length);
    }


    /**
     * Create random key
     */
    private function createRandomKey(){

        // Generate string
        $block = $this->generateStr(self::BLOCK_LEN);

        // Add salt to block
        $block .= @date('Y-m-d H:i:s').microtime();

        // Hash block of text
        return hash(self::HASH_ALGO, $block);
    }

    /**
     * This method implements HOTP dynamic truncation
     *
     * @param $binaryString The binary string that will be truncated
     * @param $length The length of the truncation
     * @return bool|string
     *
     * Based off https://tools.ietf.org/html/draft-mraihi-oath-hmac-otp-04#appendix-D
     */
    private function hotpDynamicTruncation($binaryString, $length){

        // Holds hex representation of binary string
        $hexGroup = [];

        // Convert binary string into hex representation
        for ($lp = 0; $lp < (strlen($binaryString)); $lp++)
            array_push($hexGroup, hexdec((bin2hex($binaryString[$lp]))));

        // Get offset by getting the lower 4 bits of the last byte of '$binaryString'
        $offset = ($hexGroup[(count($hexGroup)) -1] & 0xf);

        // Pack the next 4 bytes from offset onwards into a 32 bit binary string in big-endian,
        // unsigned binary string, masking the first bit to avoid ambiguity
        $data =
        (
            (($hexGroup[$offset] & 0x7f) << 24) |
            (($hexGroup[$offset + 1] & 0xff) << 16) |
            (($hexGroup[$offset + 2] & 0xff) << 8) |
            (($hexGroup[$offset + 3] & 0xff))
        );

        // Return truncation
        return (substr($data, -$length, $length));
    }


    /**
     * Generates a pseudo random string to be used with hashing methods.
     *
     * @param $size The length of the string to generate
     */
    private function generateStr(int $size){

        $data = "";

        for($lp = 0; $lp < $size; $lp++){
            $data .= $this->falseEntropy[(rand(0, (strlen($this->falseEntropy) -1)))];
        }

        return $data;
    }

}
