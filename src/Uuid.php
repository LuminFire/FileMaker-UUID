<?php

namespace BrilliantPackages\FileMakerUuid;

/**
 * FileMaker-compatible UUID.
 *
 * @since 1.0.0
 */
class Uuid
{

    /**
     * Generated UUID.
     *
     * @since 1.0.0
     *
     * @var string $uuid
     */
    public $uuid;

    /**
     * User ID for randomness.
     *
     * @var int|string $userId
     */
    protected $userId;

	/**
	 * Class instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Uuid
	 */
	private static $instance = null;

	/**
	 * Return only one instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return Uuid class.
	 */
    public static function getInstance($userId = 0)
    {
		if (null === self::$instance) {
			self::$instance = new Uuid($userId);
        }

        self::$instance->setUserId($userId);

		return self::$instance;
	}

	/**
	 * Register actions and hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return Uuid
	 */
    public function __construct($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Converts the UUID to a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Converts the UUID to a string for JSON serialization.
     *
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * Converts the UUID to a string for PHP serialization.
     *
     * @return string
     */
    public function serialize(): string
    {
        return $this->toString();
    }

    /**
     * Returns UUID as a string.
     *
     * @return string
     */
    public function toString(): string
    {
        return (string) $this->uuid;
    }

    /**
     * Generate a numeric UUID.
     *
     * @since 1.0.0
     *
     * @param int $userId
     *
     * @return Uuid
     */
    public static function numeric($userId = 0)
    {
        $uuid = self::getInstance($userId);
        return $uuid->generate();
    }

    /**
     * Generate FileMaker UUID.
     *
     * Generates a 41-digit delimited number of the form v-r-mmmmmmmmmmmmTsssssss-ccccc@nnnnnnnnnnnnnnn
	 * The sections of the UUID correspond to:
	 *   v: The UUID version (type) number: 1
	 *   r: A variant code reserved by the RFC 4122 standard: 2
	 *   m: The creation timestamp (seconds since 0001-01-01T00:00:00), or as close as we can get with PHP/Unix Epoch
	 *   s: PHP microseconds
	 *   c: Random bits ("session key" in FM)
	 *   n: IP Address as a long ("Device ID" in FM)
     *
     * @since 1.0.0
     *
     * @return Uuid
     */
    public function generate(): self
    {
        list($usec, $sec) = explode(' ', microtime());

        $sec = $sec + ((1970 * 365) * 24 * 60 * 60); // Seconds since year 0.
        $secf = str_pad($sec, 12, '0', STR_PAD_LEFT);
        $usecf = substr($usec, 2, 7);

        $c = $this->getRandomDigits(5);

        // This will only work on IPv4.
        $ip   = empty($_SERVER['REMOTE_ADDR']) ? gethostbyname(gethostname()) : $_SERVER['REMOTE_ADDR'];
        $node = str_pad(ip2long($ip), 15, '0', STR_PAD_LEFT);

        $uuid = '1-2-' . $secf . 'T' . $usecf . '-' . $c . '@' . $node;
        $uuid = trim(preg_replace('/[^0-9\s]/', '', strtolower($uuid)));

        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Generate random digits in place of a user ID.
     *
     * @since 1.0.0
     *
     * @param int $num Number of digits to return.
     *
     * @return string
     */
    protected function getRandomDigits($num)
    {
        if (! empty($this->userId)) {
            if (strlen($this->userId) > $num) {
                return substr($this->userId, (0 - $num), $num);
            } else {
                return str_pad($this->userId, $num, 0, STR_PAD_LEFT);
            }
        }

		$digits = 0;
		while (0 === $digits || strlen($digits) < $num) {
			$digits .= (int) sprintf('%F', hexdec(bin2hex(openssl_random_pseudo_bytes($num))));
		}
		return substr($digits, 0, $num);
    }
}
