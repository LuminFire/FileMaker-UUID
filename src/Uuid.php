<?php

namespace BrilliantPackages\FileMakerUuid;

use JsonSerializable;
use Serializable;
use Stringable;

/**
 * FileMaker-compatible UUID.
 *
 * @since 1.0.0
 */
class Uuid implements Serializable, JsonSerializable, Stringable
{
    protected string $uuid;

    protected int|string $userId;

	private static $instance = null;

    public static function getInstance(int $userId = 0): self
    {
		if (null === self::$instance) {
			self::$instance = new Uuid($userId);
        }

        if (self::$instance->userId !== $userId) {
            self::$instance = new Uuid($userId);
        }

		return self::$instance;
	}

    public function __construct(int|string $userId)
    {
        $this->userId = $userId;
        $this->generate();
        return $this;
    }

    public function setUserId($userId): self
    {
        $this->userId = $userId;
        $this->generate();
        return $this;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function serialize(): string
    {
        return $this->toString();
    }

    public function unserialize($value): void
    {
        $this->uuid = $value;
    }

    public function toString(): string
    {
        return (string) $this->uuid;
    }

    public static function numeric(int $userId = 0): self
    {
        return self::getInstance($userId);
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

    protected function getRandomDigits(int $length): string
    {
        if (! empty($this->userId)) {
            if (strlen($this->userId) > $length) {
                return substr($this->userId, (0 - $length), $length);
            } else {
                return str_pad($this->userId, $length, 0, STR_PAD_LEFT);
            }
        }

		$digits = 0;
		while (0 === $digits || strlen($digits) < $length) {
			$digits .= (int) sprintf('%F', hexdec(bin2hex(openssl_random_pseudo_bytes($length))));
		}
		return substr($digits, 0, $length);
    }
}
